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

class PetSpacesController extends Controller
{
    public function store (Request $request, int $id) {
        $space = Space::find($id);
        $petspace = PetSpace::where('space', $id)->get();
        if ($space != null && $space->user == auth()->user()->id && count($petspace) == 0) {
            $validate = Validator::make($request->all(), [
                'pets'      => 'required|array',
                'pets.*'    => 'required|integer|distinct'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    "msg" => __('paw.petspacesnotarray'),
                    "errors" => $validate->errors()
                ], 400);
            }
            
            $pets_ids = [];
            foreach ($request->get('pets') as $pet_id) {
                $pet = Pet::find($pet_id);
                if ($pet != null && $pet->user == auth()->user()->id) {
                    array_push($pets_ids, $pet_id);
                } else {
                    return response()->json([
                        "msg" => __('paw.petspacespetnotfound'),
                        "errors" => $validate->errors()
                    ], 404);
                }
            }

            foreach ($pets_ids as $pet_id) {
                PetSpace::create([
                    'pet' => $pet_id,
                    'space' => $space->id
                ]);
            }

            $space = Space::find($id);
            return response()->json([
                "msg" => __('paw.petspacecreated'),
                "data" => [$space]
            ], 201);
        } else {
            return response()->json([
                "msg" => __('paw.petspacesnotpets'),
                "data" => $petspace
            ], 404);
        }
    }

    public function update (Request $request, int $id) {
        $space = Space::find($id);
        $petspace = PetSpace::where('space', $id)->get();
        if ($space != null && $space->user == auth()->user()->id && count($petspace) > 0) {
            $validate = Validator::make($request->all(), [
                'pets'      => 'required|array',
                'pets.*'    => 'required|integer|distinct|exists:App\Models\Pet,id'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    "msg" => __('paw.petspacesnotarray'),
                    "errors" => $validate->errors()
                ], 400);
            }
            
            $pets_ids = [];
            foreach ($request->get('pets') as $pet_id) {
                $pet = Pet::find($pet_id);
                if ($pet != null && $pet->user == auth()->user()->id) {
                    array_push($pets_ids, $pet_id);
                } else {
                    return response()->json([
                        "msg" => __('paw.petspacespetnotfound'),
                        "errors" => $validate->errors()
                    ], 404);
                }
            }

            foreach ($petspace as $pet_space) {
                $pet_space->delete();
            }

            foreach ($pets_ids as $pet_id) {
                PetSpace::create([
                    'pet' => $pet_id,
                    'space' => $space->id
                ]);
            }

            $space = Space::find($id);
            return response()->json([
                "msg" => __('paw.petspaceupdated'),
                "data" => $space
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.petspacesempty'),
                "data" => $petspace
            ], 404);
        }
    }
}
