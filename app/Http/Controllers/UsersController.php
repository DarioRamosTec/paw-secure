<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Validator;
use App\Enums\GenreEnum;
use App\Mail\ActivateUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class UsersController extends Controller
{   
    public function index (int $id = null) {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                "msg"   => __('paw.found'),
                "data"  => $user->setHidden(['id', 'role', 'time_verification', 'created_at', 'updated_at'])
            ], 202);
        } else {
            return response()->json(
                [ "msg" => __('paw.usernotfound')]
            , 404);
        }
    }

    public function store (Request $request) {
        $content = new UserValidation();
        $error = $content->checkSignUp($request);
        if ($error != null) {return $error;}

        $user = new User();
        $content->signUp($user);
        $content->setLang();
        $signedUrl = URL::temporarySignedRoute(
            'activating', now()->addMinutes(10), ['email' => $user->email]
        );
        Mail::to($user)->send(new ActivateUser($user, $signedUrl));

        return response()->json([
            "msg"   => __('paw.usercreated'),
            "data"  => collect($user)->except(['id', 'role', 'time_verification', 'created_at', 'updated_at'])
        ], 202);
    }

    public function update (Request $request) {

    }

    public function delete () {

    }

    public function lang(Request $request, string $lang = null) {
        if (in_array($lang, ['en', 'es'])) {
            App::setLocale($lang);
            $request->user()->lang = $lang;
            return response()->json([
                "msg" => __('paw.langsuccess', ['lang' => $lang ])
            ], 202);
        }
        return response()->json([
            "msg" => __('paw.langfail', ['lang' => $lang ])
        ], 404);
        
    }

    public function login (Request $request) {
        $content = new UserValidation();
        $content->checkLogin($request);

        $user = User::where([['password', $request->password], ['email', $request->email]])->get();
        if ($user == null)
            return response()->json([
                "msg" => __('paw.usernotlogin')
            ], 404);

    }

    public function activate (Request $request, string $email) {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $user = User::where('email', $email)->get();
        if ($user != null) {
            $user[0]->is_active = true;
            $user[0]->save();
            App::setLocale($user[0]->lang);

            return response()->json([
                "msg"   => __('paw.useractivated', ['email' => $email]),
                "data"  => collect($user[0])->only(['name', 'email'])
            ], 200);
        } else {
            return response()->json([
                "msg"   => __('paw.usernotfound')
            ], 404);
        }
    }
}

class UserValidation {
    protected $name, $email, $middle_name, $last_name, $genre, $password, $birthday, $image, $lang;

    public function checkUpdate(Request $request) {
        $validate = Validator::make($request->all(), [
            "name"          => "required|min:3|max:40",
            "middle_name"   => "min:2|max:40",
            "last_name"     => "required|min:2|max:40",
            'genre'         => [new Enum(GenreEnum::class)],
            "password"      => "required|min:4|max:256",
            "image"         => "min:3",
            "birthday"      => "date"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.errorsfound'),
                "errors" => $validate->errors()
            ], 422);
            return false;
        } else {
            $this->image        = $request->image;
            $this->lang         = $request->lang;
            $this->name         = $request->name;
            $this->middle_name  = $request->middle_name;
            $this->last_name    = $request->last_name;
            $this->email        = $request->email;
            $this->genre        = $request->genre;
            $this->password     = $request->password;
            $this->birthday      = $request->birthday;
            return true;
        }
    }

    public function checkLogin(Request $request) {
        $validate = Validator::make($request->all(), [
            "email"         => "required|email|unique:App\Models\User,email",
            "password"      => "required|min:4|max:256"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.errorsfound'),
                "errors" =>$validate->errors()
            ], 422);
        }
        
    }

    public function checkSignUp(Request $request) {
        $validate = Validator::make($request->all(), [
            "email"             => "required|email|unique:App\Models\User,email",
            "password"          => "required|min:4|max:256",
            "password_again"    => "required|min:4|max:256|same:password",
            "name"              => "required|min:3|max:40",
            "lang"              => "required",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.errorsfound'),
                "errors" =>$validate->errors()
            ], 422);
        } else {
            $this->lang         = $request->lang;
            $this->name         = $request->name;
            $this->email        = $request->email;
            $this->password     = $request->password;
        }
    }

    public function signUp(&$model) {
        $model->name  = $this->name;
        $model->lang  = $this->lang;
        $model->email       = $this->email;
        $model->password    = Hash::make($this->password);
        $model->save();
    }

    public function update(&$model) {
        $model->name  = $this->name;
        $model->lang  = $this->lang;
        $model->image  = $this->image;
        $model->middle_name = $this->middle_name;
        $model->last_name   = $this->last_name;
        $model->email       = $this->email;
        $model->genre       = $this->genre;
        $model->password    = Hash::make($this->password);
        $model->birthday     = $this->birthday;
        $model->save();
    }

    public function setLang() {
        if (in_array($this->lang, ['en', 'es']))
            App::setLocale($this->lang); 
    }
}
