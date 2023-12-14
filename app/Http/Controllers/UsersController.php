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
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuthController;
use App\Models\Pet;
use App\Models\PetSpace;
use App\Models\Space;
use Illuminate\Support\Facades\Storage;

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
        if (in_array($request->lang, ['en', 'es'])) {
            App::setLocale($request->lang);
        }
        
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
        $content = new UserValidation();
        $error = $content->checkUpdate($request);
        if ($error != null) {return $error;}

        $user = User::find(auth()->user()->id)
        $content->update($user);
        $user = User::find(auth()->user()->id)
        
        return response()->json([
            "msg"   => __('paw.found'),
            "data"  => $user,
        ], 200);
    }

    public function delete () {

    }

    public function lang(Request $request, string $lang = null) {
        if ($request->isMethod('put')) {
            if (in_array($lang, ['en', 'es'])) {
                App::setLocale($lang);
                $user = User::find($request->user()->id);
                $user->lang = $lang;
                $user->save();
                return response()->json([
                    "msg" => __('paw.langsuccess', ['lang' => $lang ])
                ], 202);
            }
            return response()->json([
                "msg" => __('paw.langfail', ['lang' => $lang ])
            ], 404);
        } else if ($request->isMethod('get')) {
            $lang = auth()->user()->lang;
            if ($lang == null) {
                return response()->json([
                    "msg" => __('paw.notlang')
                ], 404);
            } else {
                return response()->json([
                    "msg" => __('paw.langfound', ['lang' => $lang ]),
                    "data" => $lang
                ], 200);
            }
        } else {
            return response()->json([
                "msg" => __('paw.routenothing')
            ], 404);
        }        
    }

    public function login (Request $request) {
        $content = new UserValidation();
        $error = $content->checkLogin($request);
        if ($error != null) {return $error;}

        $user = User::where([['password', $request->password], ['email', $request->email]])->get();
        if ($user == null)
            $content->setLang();
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

            return View::make('activate', [
                'name' => $user[0]->name,
            ]);
        } else {
            return View::make('404');
        }
    }

    public function spaces (Request $request) {
        $spaces = $request->user()->spaces;
        $count = count($spaces);
        if ($count == 0) {
            return response()->json([
                "msg" => __('paw.anycages'),
                "data" => []
            ], 200);
        } else {
            return response()->json([
                "msg" => trans_choice('paw.foundcages', $count),
                "data" => $spaces
            ], 200);
        }
    }

    public function pets (Request $request) {
        $pets = $request->user()->pets;
        $count = count($pets);
        if ($count == 0) {
            return response()->json([
                "msg" => __('paw.anypets'),
                "data" => []
            ], 200);
        } else {
            return response()->json([
                "msg" => trans_choice('paw.foundpets', $count),
                "data" => $pets
            ], 200);
        }
    }

    public function mypets (Request $request, int $id) {
        $mypet = auth()->user()->pets->find($id);
        if ($mypet != null && $mypet->image != null) {
            return Storage::disk('s3')->response($mypet->image);
        }
    }
}

class UserValidation {
    protected $name, $email, $middle_name, $last_name, $genre, $password, $birthday, $image, $lang;

    public function checkUpdate(Request $request, $user) {
        $validate = Validator::make($request->all(), [
            "name"          => "min:3|max:40",
            "middle_name"   => "min:2|max:40",
            "last_name"     => "min:2|max:40",
            'genre'         => [new Enum(GenreEnum::class)],
            "password"      => "min:4|max:256",
            "image"         => "min:3",
            "birthday"      => "date_format:m/d/Y"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.errorsfound'),
                "errors" => $validate->errors()
            ], 400);
        } else {
            $this->image        = $request->get('image', $user->image);
            $this->lang         = $request->get('lang', $user->lang);
            $this->name         = $request->get('name', $user->name);
            $this->middle_name  = $request->get('middle_name', $user->middle_name);
            $this->last_name    = $request->get('last_name', $user->last_name);
            $this->email        = $request->get('email', $user->email);
            $this->genre        = $request->get('genre', $user->genre);
            $this->password     = $request->get('password', $user->password);
            $this->birthday     = date("d-m-Y", strtotime($request->get('birthday', $user->birthday)));
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
            ], 400);
        }
        
    }

    public function checkSignUp(Request $request) {
        $validate = Validator::make($request->all(), [
            "email"             => "required|email|unique:App\Models\User,email",
            "password"          => "required|min:4|max:256",
            "password_again"    => "required|min:4|max:256|same:password",
            "name"              => "required|min:3|max:40",
            "lang"              => "required|min:2",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.errorsfound'),
                "errors" =>$validate->errors()
            ], 400);
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
        $model->name        = $this->name;
        $model->lang        = $this->lang;
        $model->image       = $this->image;
        $model->middle_name = $this->middle_name;
        $model->last_name   = $this->last_name;
        $model->email       = $this->email;
        $model->genre       = $this->genre;
        $model->password    = Hash::make($this->password);
        $model->birthday    = $this->birthday;
        $model->save();
    }

    public function setLang() {
        if (in_array($this->lang, ['en', 'es']))
            App::setLocale($this->lang); 
    }
}
