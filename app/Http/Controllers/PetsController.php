<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class PetsController extends Controller
{
    public function store (Request $request) {
        $validation = new PetValidation();
        $error = $validation->createPet($request, auth()->user()->id);
        if ($error != null) {
            return $error;
        }

        $validation->save();
        return response()->json([
            "msg"   => __('paw.petcreated'),
            "data"  => collect($validation->pet)->except(['id', 'created_at', 'updated_at'])
        ], 202);
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
            "nickname"  => "required|min:3|max:40",
            "race"      => "min:2|max:40",
            'sex'       => [new Enum(SexEnum::class)],
            "icon"      => "required|integer",
            "image"     => "min:3",
            "animal"    => "required|exists:App\Models\Animal,id",
            "birthday"  => "required|date_format:m/d/Y"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.petnotcreated'),
                "errors" => $validate->errors()
            ], 400);
        } else {
            $this->pet = new Pet();
            $this->pet->nickname  = $request->nickname;
            $this->pet->race      = $request->race;
            $this->pet->sex       = $request->sex;
            $this->pet->icon      = $request->icon;
            $this->pet->image     = $request->image;
            $this->pet->birthday  = date("Y-m-d", strtotime($request->birthday));
            $this->pet->animal    = $request->animal;
            $this->pet->user      = $id;
        }
    }

    function updatePet(Request $request) {
        $validate = Validator::make($request->all(), [
            "nickname"  => "min:3|max:40",
            "race"      => "min:2|max:40",
            'sex'       => [new Enum(SexEnum::class)],
            "icon"      => "integer",
            "image"     => "min:3",
            "animal"    => "exists:App\Models\Animal,id",
            "birthday"  => "date_format:m/d/Y"
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
        $pet->nickname  = $request->get('nickname', $pet->nickname);
        $pet->race      = $request->get('race', $pet->race);
        $pet->sex       = $request->get('sex', $pet->sex);
        $pet->icon      = $request->get('icon', $pet->icon);
        $pet->image     = $request->get('image', $pet->image);
        $pet->birthday  = date("Y-m-d", strtotime($request->get('birthday', $pet->birthday)));
        $pet->animal    = $request->get('animal', $pet->animal);
        $pet->save();
    }
}
