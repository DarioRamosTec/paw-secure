<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class MiscController extends Controller
{
    public function none (string $lang) {
        if (in_array($lang, ['en', 'es'])) {
            App::setLocale($lang);
        }

        return response()->json([
            "msg" => __('paw.nothing')
        ], 200);
    }

}
