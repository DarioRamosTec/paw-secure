<?php

namespace App\Http\Controllers;

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
            "data"  => collect($validation->space)->except(['created_at', 'updated_at'])
        ], 202);
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
            "msg"   => __('paw.indexSpace'),
            "data"  => $space
        ], 200);
    }
}

class SpaceValidation {
    public $space;

    function createSpace(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "name"          => "min:3|max:20",
            "description"   => "min:5|max:45"
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
            "name"          => "min:3|max:40",
            "description"   => "min:5|max:45"
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