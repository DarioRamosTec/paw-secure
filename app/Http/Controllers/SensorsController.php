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

    public function humidity (int $id)
    {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }   
        $humidity = Sensor::where([['space', $id], ['sensor_type', 2]])->get()->last();
        
        $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/humedad/data');
        if ($response->ok()) {
            $humidities = collect($response->json());
            $uid = 'U'.strval($id);
            foreach ($humidities as $sensor) {
                $value = $sensor['value'];
                if (str_contains($value, $uid)) {
                    $validate = Validator::make($sensor, [
                        "value" => ["regex:/([U][0-9]+[\-][0-9]+){1}/i"],
                    ]);
        
                    if (!$validate->fails()) {
                        $measure = explode('-', $value)[1];
                        $time = date("Y-m-d h:i:s", strtotime($sensor['created_at']));
                        if ($humidity->time !== $time) {
                            $humidity = new Sensor();
                            $humidity->measure = $measure;
                            $humidity->time = date("Y-m-d h:i:s", strtotime($time));
                            $humidity->space = $id;
                            $humidity->sensor_type = 2;
                            $humidity->save();
                            }
                        break;
                    }
                }
            }
            return response()->json([
                "msg" => __('paw.sensorinfo', ['sensor' => __('humidity') ]),
                "data" => $humidity,
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.ioerror'),
                    "data" => $humidity
            ], 206);
        }
    }

    public function sound (int $id)
    {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }   
        $sound = Sensor::where([['space', $id], ['sensor_type', 6]])->get()->last();
        
        $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/sonido/data');
        if ($response->ok()) {
            $temperatures = collect($response->json());
            $uid = 'U'.strval($id);
            foreach ($temperatures as $sensor) {
                $value = $sensor['value'];
                if (str_contains($value, $uid)) {
                    $validate = Validator::make($sensor, [
                        "value" => ["regex:/([U][0-9]+[\-][0-9]+){1}/i"],
                    ]);
        
                    if (!$validate->fails()) {
                        $measure = explode('-', $value)[1];
                        $time = date("Y-m-d h:i:s", strtotime($sensor['created_at']));
                        if ($sound->time !== $time) {
                            $sound = new Sensor();
                            $sound->measure = $measure;
                            $sound->time = date("Y-m-d h:i:s", strtotime($time));
                            $sound->space = $id;
                            $sound->sensor_type = 6;
                            $sound->save();
                        }
                        break;
                    }
                }
            }
            return response()->json([
                "msg" => __('paw.sensorinfo', ['sensor' => __('sound') ]),
                "data" => $sound,
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.ioerror'),
                    "data" => $sound
            ], 206);
        }
    }

    public function temperature (int $id)     
    {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }   
        $temperature = Sensor::where([['space', $id], ['sensor_type', 3]])->get()->last();
        
        $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/temperatura/data');
        if ($response->ok()) {
            $temperatures = collect($response->json());
            $uid = 'U'.strval($id);
            foreach ($temperatures as $sensor) {
                $value = $sensor['value'];
                if (str_contains($value, $uid)) {
                    $validate = Validator::make($sensor, [
                        "value" => ["regex:/([U][0-9]+[\-][0-9]+){1}/i"],
                    ]);
        
                    if (!$validate->fails()) {
                        $measure = explode('-', $value)[1];
                        $time = date("Y-m-d h:i:s", strtotime($sensor['created_at']));
                        if ($temperature->time !== $time) {
                            $temperature = new Sensor();
                            $temperature->measure = $measure;
                            $temperature->time = date("Y-m-d h:i:s", strtotime($time));
                            $temperature->space = $id;
                            $temperature->sensor_type = 3;
                            $temperature->save();
                        }
                        break;
                    }
                }
            }
            return response()->json([
                "msg" => __('paw.sensorinfo', ['sensor' => __('temperature') ]),
                "data" => $temperature,
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.ioerror'),
                    "data" => $temperature
            ], 206);
        }
    }

    public function gas (int $id)
    {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }   
        $gas = Sensor::where([['space', $id], ['sensor_type', 4]])->get()->last();
        
        $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/MQ2/data');
        if ($response->ok()) {
            $gases = collect($response->json());
            $uid = 'U'.strval($id);
            foreach ($gases as $sensor) {
                $value = $sensor['value'];
                if (str_contains($value, $uid)) {
                    $validate = Validator::make($sensor, [
                        "value" => ["regex:/([U][0-9]+[\-][0-9]+){1}/i"],
                    ]);
        
                    if (!$validate->fails()) {
                        $measure = explode('-', $value)[1];
                        $time = date("Y-m-d h:i:s", strtotime($sensor['created_at']));
                        if ($gas->time !== $time) {
                            $gas = new Sensor();
                            $gas->measure = $measure;
                            $gas->time = date("Y-m-d h:i:s", strtotime($time));
                            $gas->space = $id;
                            $gas->sensor_type = 4;
                            $gas->save();
                        }
                        break;
                    }
                }
            }
            return response()->json([
                "msg" => __('paw.sensorinfo', ['sensor' => __('gas') ]),
                "data" => $gas,
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.ioerror'),
                    "data" => $gas
            ], 206);
        }
    }

    public function motion (Request $request) {

    }

    public function position (int $id)
    {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }   
        $position = Sensor::where([['space', $id], ['sensor_type', 5]])->get()->last();
        
        $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/gps/data');
        if ($response->ok()) {
            $positions = collect($response->json());
            $uid = 'U'.strval($id);
            foreach ($positions as $sensor) {
                $value = $sensor['value'];
                if (str_contains($value, $uid)) {
                    $validate = Validator::make($sensor, [
                        "value" => ["regex:/([U][0-9]+[\-][0-9]+){1}/i"],
                    ]);
        
                    if (!$validate->fails()) {
                        $measure = explode('-', $value)[1];
                        $time = date("Y-m-d h:i:s", strtotime($sensor['created_at']));
                        if ($position->time !== $time) {
                            $position = new Sensor();
                            $position->measure = $measure;
                            $position->time = date("Y-m-d h:i:s", strtotime($time));
                            $position->space = $id;
                            $position->sensor_type = 5;
                            $position->save();
                        }
                        break;
                    }
                }
            }
            return response()->json([
                "msg" => __('paw.sensorinfo', ['sensor' => __('gps') ]),
                "data" => $position,
            ], 200);
        } else {
            return response()->json([
                "msg" => __('paw.ioerror'),
                    "data" => $position
            ], 206);
        }
    }

}
