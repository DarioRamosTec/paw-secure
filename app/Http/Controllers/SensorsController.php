<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SensorsController extends Controller
{
    public function presence (int $id)
    {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        $presence = Sensor::where([['space', $id], ['sensor_type', 1]])->get()->last();

        $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/movimiento/data');
        if ($response->ok()) {
            $presences = collect($response->json());
            $uid = 'U'.strval($id);
            foreach ($presences as $sensor) {
                $value = $sensor['value'];
                if (str_contains($value, $uid)) {
                    $validate = Validator::make($sensor, [
                        "value" => ["regex:/([U][0-9]+[\-][0-9]+){1}/i"],
                    ]);

                    if (!$validate->fails()) {
                        $measure = explode('-', $value)[1];
                        $time = date("Y-m-d h:i:s", strtotime($sensor['created_at']));
                        if ($presence->time !== $time) {
                            $presence = new Sensor();
                            $presence->measure = $measure;
                            $presence->time = date("Y-m-d h:i:s", strtotime($time));
                            $presence->space = $id;
                            $presence->sensor_type = 1;
                            $presence->save();
                        }
                        break;
                    }
                }
            }
            return response()->json([
                "msg" => __('paw.sensorinfo', ['sensor' => __('presence') ]),
                "data" => $presence,
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.ioerror'),
                "data" => $presence
            ], 206);
        }
    }

    public function humidity (Request $request) {

    }

    public function sound (Request $request) {

    }

    public function temperature (Request $request) {

    }

    public function gas (Request $request) {

    }

    public function motion (Request $request) {

    }

    public function position (Request $request) {

    }

}
