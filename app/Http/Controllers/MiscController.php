<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MiscController extends Controller
{
    public function lang(string $lang = null) {
        if (in_array($lang, ['en', 'es'])) {
            App::setLocale($lang);            
            return response()->json([
                "msg" => __('paw.langsuccess', ['lang' => $lang ])
            ], 202);
        }
        return response()->json([
            "msg" => __('paw.langfail', ['lang' => $lang ])
        ], 404);
        
    }
}
