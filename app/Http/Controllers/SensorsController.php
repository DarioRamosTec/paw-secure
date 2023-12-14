<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\SensorType;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SensorsController extends Controller
{
    public $_num = "[\+|\-]?[0-9]+([\.][0-9]+)?";
    public $_uid = "[U][0-9]+";

    public function sensor (int $id, string $sensor) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        $sensor_type = SensorType::firstWhere('name', $sensor);
        if ($sensor_type != null) {
            if ($sensor_type->name != 'motion' && $sensor_type->name != 'position' ) {
                $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                    ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/'.$sensor_type->feed.'/data');
                
                if ($response->ok()) {
                    $magnitude = Sensor::where([['space', $id], ['sensor_type', $sensor_type->id]])->get()->last();
                    $feed = collect($response->json());
                    $uid = 'U'.strval($id);
                    foreach ($feed as $data) {
                        $value = $data['value'];
                        if (str_contains($value, $uid)) {
                            $validate = Validator::make($data, [
                                "value" => ["regex:/(".$this->_uid."[:]".$this->_num."){1}/i"],
                            ]);
        
                            if (!$validate->fails()) {
                                $measure = explode(':', $value)[1];
                                $time = date("Y-m-d h:i:s", strtotime($data['created_at']));
                                if ($magnitude == null || ($magnitude->time !== $time && $magnitude->measure != $measure)) {
                                    $magnitude = new Sensor();
                                    $magnitude->measure = $measure;
                                    $magnitude->time = date("Y-m-d h:i:s", strtotime($time));
                                    $magnitude->space = $id;
                                    $magnitude->sensor_type = $sensor_type->id;
                                    $magnitude->pet = $space->target;
                                    $magnitude->save();
                                    $magnitude = Sensor::find($magnitude->id);
                                }
                                break;
                            }
                        }
                    }
                    if ($magnitude == null) {
                        return response()->json([
                            "msg" => __('paw.sensorinfo'),
                            "data" => [],
                        ], 202);   
                    } else {
                        return response()->json([
                            "msg" => __('paw.sensorinfo'),
                            "data" => $magnitude,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        "msg" => __('paw.ioerror'),
                        "data" => []
                    ], 404);
                }
            } else {
                return response()->json([
                    "msg" => __('paw.sensornotgeneral'),
                    "data" => []
                ], 400);
            }
        } else {
            return response()->json([
                "msg" => __('paw.sensornotfound'),
                "data" => []
            ], 404);
        }
    }

    public function motion (Request $request, int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        $sensor_type = SensorType::firstWhere('name', 'motion');
        if ($sensor_type != null) {
            $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/'.$sensor_type->feed.'/data');
            
            if ($response->ok()) {
                $magnitude = Sensor::where([['space', $id], ['sensor_type', $sensor_type->id]])->get()->last();
                $feed = collect($response->json());
                $uid = 'U'.strval($id);
                foreach ($feed as $data) {
                    $value = $data['value'];
                    if (str_contains($value, $uid)) {
                        $validate = Validator::make($data, [
                            "value" => ["regex:/(".$this->_uid."[:][x]".$this->_num."[y]".$this->_num."[z]".$this->_num."){1}/i"],
                        ]);
    
                        if (!$validate->fails()) {
                            $measure = 1;
                            $vls = explode(':', $value)[1];
                            $time = date("Y-m-d h:i:s", strtotime($data['created_at']));
                            if ($magnitude == null || ($magnitude->time !== $time)) {
                                $magnitude = new Sensor();
                                $magnitude->measure = $measure;
                                $magnitude->data = $vls;
                                $magnitude->time = date("Y-m-d h:i:s", strtotime($time));
                                $magnitude->space = $id;
                                $magnitude->sensor_type = $sensor_type->id;
                                $magnitude->pet = $space->target;
                                $magnitude->save();
                                $magnitude = Sensor::find($magnitude->id);
                            }
                            break;
                        }
                    }
                }
                if ($magnitude == null) {
                    return response()->json([
                        "msg" => __('paw.sensorinfo'),
                        "data" => [],
                    ], 202);   
                } else {
                    return response()->json([
                        "msg" => __('paw.sensorinfo'),
                        "data" => $magnitude,
                    ], 200);
                }
            } else {
                return response()->json([
                    "msg" => __('paw.ioerror'),
                    "data" => []
                ], 404);
            }
        } else {
            return response()->json([
                "msg" => __('paw.sensornotfound'),
                "data" => []
            ], 404);
        }
    }

    public function position (Request $request, int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        $sensor_type = SensorType::firstWhere('name', 'position');
        if ($sensor_type != null) {
            $response = Http::withHeaders(['x-aio-key'=> config('paw.adafruit')])
                ->get('https://io.adafruit.com/api/v2/PawSecure/feeds/'.$sensor_type->feed.'/data');
            
            if ($response->ok()) {
                $magnitude = Sensor::where([['space', $id], ['sensor_type', $sensor_type->id]])->get()->last();
                $feed = collect($response->json());
                $uid = 'U'.strval($id);
                foreach ($feed as $data) {
                    $value = $data['value'];
                    if (str_contains($value, $uid)) {
                        $validate = Validator::make($data, [
                            "value" => ["regex:/(".$this->_uid."[:]".$this->_num."[\,]".$this->_num."){1}/i"],
                        ]);
    
                        if (!$validate->fails()) {
                            $measure = 1;
                            $vls = explode(':', $value)[1];
                            $time = date("Y-m-d h:i:s", strtotime($data['created_at']));
                            if ($magnitude == null || $magnitude->time !== $time) {
                                $magnitude = new Sensor();
                                $magnitude->measure = $measure;
                                $magnitude->data = $vls;
                                $magnitude->time = date("Y-m-d h:i:s", strtotime($time));
                                $magnitude->space = $id;
                                $magnitude->sensor_type = $sensor_type->id;
                                $magnitude->pet = $space->target;
                                $magnitude->save();
                                $magnitude = Sensor::find($magnitude->id);
                            }
                            break;
                        }
                    }
                }
                if ($magnitude == null) {
                    return response()->json([
                        "msg" => __('paw.sensorinfo'),
                        "data" => [],
                    ], 202);   
                } else {
                    return response()->json([
                        "msg" => __('paw.sensorinfo'),
                        "data" => $magnitude,
                    ], 200);
                }
            } else {
                return response()->json([
                    "msg" => __('paw.ioerror'),
                    "data" => []
                ], 404);
            }
        } else {
            return response()->json([
                "msg" => __('paw.sensornotfound'),
                "data" => []
            ], 404);
        }
    }

    public function index (int $id) {
        $space = Space::find($id);
        if ($space == null || $space->user != auth()->user()->id) {
            return response()->json([
                "msg"   => __('paw.403'),
                "data"  => []
            ], 403);
        }

        $pets = $space->pets;
        $sensorTypes = SensorType::all('id');
        $allPetSensors = collect();
        foreach ($pets as $pet) {
                $petSensor = [];
                foreach ($sensorTypes as $sensor_type) {
                    $sensorData = Sensor::where([['space', $id], ['sensor_type', $sensor_type->id], ['pet', $pet->id]])
                    ->get()
                    ->last();
                    array_push($petSensor, $sensorData);
                }
                $allPetSensors = $allPetSensors->put($pet->id, $petSensor);
        }
        $petSensor = [];
        foreach ($sensorTypes as $sensor_type) {
        $sensorData = Sensor::where([['space', $id], ['sensor_type', $sensor_type->id], ['pet', null]])
            ->get()->last();
            array_push($petSensor, $sensorData);
        }
        $allPetSensors = $allPetSensors->put('0', $petSensor);
        
        return response()->json([
            "msg" => __('paw.sensorinfo'),
            "data" => [$allPetSensors]
        ], 200);
    }

}
