<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;


class SpacesController extends Controller
{
    public function store (Request $request) {
        $validation = new SpaceValidation();
        $error = $validation->createSpace($request, auth()->user()->id);
        if ($error != null) {
            return $error;
        }

        $validation->save();
        return response()->json([
            "msg"   => __('paw.spacecreated'),
            "data"  => [collect($validation->space)->except(['created_at', 'updated_at'])]
        ], 201);
    }

    public function update (Request $request, int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }
        
        $validation = new SpaceValidation();
        $error = $validation->updateSpace($request);
        if ($error != null) {
            return $error;
        }

        $validation->update($request, $space);
        return response()->json([
            "msg"   => __('paw.updateSpace'),
            "data"  => $space
        ], 200);

    }

    public function index (int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        return response()->json([
            "msg"   => __('paw.indexspace'),
            "data"  => [$space]
        ], 200);
    }

    public function link (Request $request, int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }
        
        $validate = Validator::make($request->all(), [
            "mac"      => "required|mac_address"
        ]);
        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.spacenotlinked'),
                "errors" => $validate->errors()
            ], 400);
        }
        $space->mac = $request->mac;
        $space->linked = true;
        $space->save();

        return response()->json([
            "msg"   => __('paw.spacelink'),
            "data"  => [$space]
        ], 200);
    }

    public function target (Request $request, int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            "pet"          => "integer|exists:App\Models\Pet,id",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.spacenotcreated'),
                "errors" => $validate->errors()
            ], 400);
        }

        if ($request->pet != null) {
            $pet = $space->pets->firstWhere('id', $request->pet);
            if ($pet == null || $pet->user != auth()->user()->id) {
                return response()->json([
                    "msg"   => __('paw.403'),
                    "data"  => []
                ], 403);
            }
            $space->target = $pet->id;
        } else {
            $space->target = null;
        }
        $space->save();

        return response()->json([
            "msg"   => __('paw.spacetarget'),
            "data"  => [$space]
        ], 200);
    }
}

class SpaceValidation {
    public $space;

    function createSpace(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "name"          => "required|min:3|max:20",
            "description"   => "required|min:5|max:45"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.spacenotcreated'),
                "errors" => $validate->errors()
            ], 400);
        } else {
            $this->space = new Space();
            $this->space->name        = $request->name;
            $this->space->description = $request->description;
            $this->space->user        = $id;
        }
    }

    function updateSpace(Request $request) {
        $validate = Validator::make($request->all(), [
            "name"          => "required|min:3|max:40",
            "description"   => "required|min:5|max:45"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.spacenotcreated'),
                "errors" => $validate->errors()
            ], 400);
        }
    }

    function save() {
        if ($this->space != null) {
            $this->space->save();
        }
    }

    function update(Request $request, $space) {
        $space->name      = $request->get('name', $space->name);
        $space->race          = $request->get('race', $space->race);
        $space->sex           = $request->get('sex', $space->sex);
        $space->icon          = $request->get('icon', $space->icon);
        $space->image         = $request->get('image', $space->image);
        $space->birthday      = date("Y-m-d", strtotime($request->get('birthday', $space->birthday)));
        $space->animal        = $request->get('animal', $space->animal);
        $space->description   = $request->get('animal', $space->description);
        $space->save();
    }
}
