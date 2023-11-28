//Este es otro ejemplo bellaco aqui en imsomnia pondras como ruta
https:127.0.0.1:8000/api/v2/adafruit
//y en lugar de header un JSON esto sirve para guardar en la base de datos lo que nos de imsomnia
{
    "ioA": ""TOKEN DE ADAFRUIT
}
//utiliza get para consumir o post para dar




public function adafruit(Request $request) {
        $response =Http::withHeaders ([
            'X-AIO-Key'=> $request->ioA,]) 
            ->get 
        ('https://io.adafruit.com/api/v2/ricardo_sanchz/feeds/welcome-feed/data');
        if ($response->ok())
   {
//AQUI SE PUEDE USAR LOGICA PARA GUARDAR LO RECIBIDO EN LA DB

    return response()->json([ 
    "msg"=> "Consumo satisfactorio...",
    "data"=> $response->json() 
    ], 200);
   }
   else { return response()->json([ 
    "msg"=> "Ocurrio un error en la api de adafruit",
    "data"=> $response->body()
    ], $response->status());
}
    }
   