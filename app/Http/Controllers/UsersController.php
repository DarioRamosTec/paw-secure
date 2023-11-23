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

class UsersController extends Controller
{   
    public function index (int $id = null) {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                "msg"   => __('paw.found'),
                "data"  => $user->setHidden(['role', 'time_verification', 'created_at', 'updated_at'])
            ], 202);
        } else {
            return response()->json(
                [ "msg" => __('paw.usernotfound')]
            , 404);
        }
    }

    public function store (Request $request) {
        $content = new UserValidation();
        if (!$content->checkForCreate($request)) return $content->fail;

        $user = new User();
        $content->writeValuesToModelAndSave($user);

        $signedUrl = URL::temporarySignedRoute(
            'activating', now()->addMinutes(10), ['email' => $user->email]
        );

        Mail::to($user)->send(new ActivateUser($user, $signedUrl));

        return response()->json([
            "msg"   => "Se creó un usuario, pero, aún no ha sido activado.",
            "data"  => collect($user)->except(['id', 'role', 'time_verification', 'created_at', 'updated_at'])
        ], 202);
    }

    public function update (Request $request) {

    }

    public function delete () {

    }

    public function login (Request $request) {
        $content = new IdoloValidacion();
        if (!$content->checkForLogin($request)) return $content->fail;

        $user = User::where([['password', $request->password], ['email', $request->email]])->get();
        if ($user != null) {
            
        } else {
            return response()->json([
                "msg" => "El correo electrónico o la contraseña son incorrectos."
            ], 404);            
        }

    }

    public function activate (Request $request, string $email) {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $user = User::where('email', $email)->get();
        if ($user != null) {
            $user[0]->is_active = true;
            $user[0]->save();
            return response()->json([
                "msg"   => "Se ha actualizado el usuario.",
                "data"  => collect($user[0])->except(['id', 'role', 'time_verification', 'created_at', 'updated_at'])
            ], 200);
        } else {
            return response()->json([
                "msg"   => "No se encontró el usuario buscado."
            ], 404);
        }
    }
}

class UserValidation {
    protected $name, $email, $middle_name, $last_name, $genre, $password, $country;
    public $fail = null;

    public function checkForCreate(Request $request) {
        $validate = Validator::make($request->all(), [
            "name"          => "required|min:3|max:40",
            "middle_name"   => "min:2|max:40",
            "last_name"     => "required|min:2|max:40",
            "email"         => "required|email|unique:App\Models\User,email",
            'genre'         => [new Enum(GenreEnum::class)],
            "password"      => "required|min:4|max:256",
            "country"       => "min:4|max:40"
        ]);

        if ($validate->fails()) {
            $this->fail = response()->json([
                "msg" => "Se han encontrado los siguientes errores de validación.",
                "errors" =>$validate->errors()
            ], 422);
            return false;
        } else {
            $this->name         = $request->name;
            $this->middle_name  = $request->middle_name;
            $this->last_name    = $request->last_name;
            $this->email        = $request->email;
            $this->genre        = $request->genre;
            $this->password     = $request->password;
            $this->country      = $request->country;
            return true;
        }
    }

    public function checkForLogin(Request $request) {
        $validate = Validator::make($request->all(), [
            "email"         => "required|email|unique:App\Models\User,email",
            "password"      => "required|min:4|max:256"
        ]);

        if ($validate->fails()) {
            $this->fail = response()->json([
                "msg" => "Se han encontrado los siguientes errores de validación.",
                "errors" =>$validate->errors()
            ], 422);
            return false;
        } else {
            return true;
        }
    }

    public function writeValuesToModelAndSave(&$model) {
        $model->name  = $this->name;
        $model->middle_name = $this->middle_name;
        $model->last_name   = $this->last_name;
        $model->email       = $this->email;
        $model->genre       = $this->genre;
        $model->password    = Hash::make($this->password);
        $model->country     = $this->country;
        $model->save();
    }
}
