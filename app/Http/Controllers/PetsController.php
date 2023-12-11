<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Storage;

class PetsController extends Controller
{
    public function store (Request $request) {
        $validation = new PetValidation();
        $error = $validation->createPet($request, auth()->user()->id);
        if ($error != null) {
            return $error;
        }
        if ($request->image != null) {            
            $link = 'images/'.auth()->user()->id.count(auth()->user()->pets).'.png';
            $imageContent = base64_decode(str_replace("\r\n", "", $request->image));
            Storage::disk('s3')->put($link, $imageContent);
            $validation->pet->image = $link;
        }

        $validation->save();
        return response()->json([
            "msg"   => __('paw.petcreated'),
            "data"  => collect($validation->pet)->except(['created_at', 'updated_at'])
        ], 201);
    }

    public function update (Request $request, int $id) {
        $pet = Pet::find($id);
        if ($pet == null || $pet->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }
        
        $validation = new PetValidation();
        $error = $validation->updatePet($request);
        if ($error != null) {
            return $error;
        }

        $validation->update($request, $pet);
        return response()->json([
            "msg"   => __('paw.updatepet'),
            "data"  => $pet
        ], 200);

    }

    public function index (int $id) {
        $pet = Pet::find($id);
        if ($pet == null || $pet->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        return response()->json([
            "msg"   => __('paw.indexpet'),
            "data"  => $pet
        ], 200);
    }
}

class PetValidation {
    public $pet;

    function createPet(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "name"          => "required|min:2|max:20",
            "race"          => "min:2|max:15",
            'sex'           => [new Enum(SexEnum::class)],
            "icon"          => "required|integer",
            "image"         => "max:2000000",
            "animal"        => "integer|required|exists:App\Models\Animal,id",
            "birthday"      => ["date_format:d/m/Y", "before_or_equal:".now()->format('Y-m-d')],
            "description"   => "required|min:5|max:45"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.petnotcreated'),
                "errors" => $validate->errors()
            ], 400);
        } else {
            $this->pet = new Pet();
            $this->pet->name        = $request->name;
            $this->pet->race        = $request->race;
            $this->pet->sex         = $request->sex;
            $this->pet->icon        = $request->icon;
            //$this->pet->image       = $request->image;
            $this->pet->birthday    = date("Y-m-d", strtotime($request->birthday));
            $this->pet->animal      = $request->animal;
            $this->pet->description = $request->description;
            $this->pet->user        = $id;
        }
    }

    function updatePet(Request $request) {
        $validate = Validator::make($request->all(), [
            "name"          => "min:3|max:40",
            "race"          => "min:2|max:40",
            'sex'           => [new Enum(SexEnum::class)],
            "icon"          => "integer",
            "image"         => "min:3",
            "animal"        => "exists:App\Models\Animal,id",
            "birthday"      => "date_format:m/d/Y",
            "description"   => "min:7|max:45"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.petnotcreated'),
                "errors" => $validate->errors()
            ], 400);
        }
    }

    function save() {
        if ($this->pet != null) {
            $this->pet->save();
        }
    }

    function update(Request $request, $pet) {
        $pet->name      = $request->get('name', $pet->name);
        $pet->race          = $request->get('race', $pet->race);
        $pet->sex           = $request->get('sex', $pet->sex);
        $pet->icon          = $request->get('icon', $pet->icon);
        $pet->image         = $request->get('image', $pet->image);
        $pet->birthday      = date("Y-m-d", strtotime($request->get('birthday', $pet->birthday)));
        $pet->animal        = $request->get('animal', $pet->animal);
        $pet->description   = $request->get('animal', $pet->description);
        $pet->save();
    }
}
