<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function movimiento()
    {
        $client = new Client();

        try {
            $response = $client->request('GET', 'https://io.adafruit.com/PawSecure/feeds/movimiento');
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            // Manejo de la respuesta
            if ($statusCode === 200) {
                return response()->json(['data' => $body], $statusCode);
            } else {
                return response()->json(['error' => 'Hubo un problema con la solicitud. Código de estado: ' . $statusCode], $statusCode);
            }
        } catch (RequestException $e) {
            return response()->json(['error' => 'Excepción de solicitud: ' . $e->getMessage()], 500);
        }
    }
}
