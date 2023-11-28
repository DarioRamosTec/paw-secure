namespace App/Https/Controllers/Api;

use App/Https/Controllers;
use Illuminate/Http/Request;
use Illuminate/Support/Facades/Validator;
use App/Models/Api/Persona;



class EjemploApiController extends EjemploApiController
{
    public function adafruit() {
      //  https://io.adafruit.com/ricardo_sanchz/feeds/welcome-feed// ejemplo de ruta
        $response =Http::withHeaders ([
            'X-AIO-Key'=> 'TOKEN DE ADAFRUIT',]) ->get 
        ('https://io.adafruit.com/api/v2/ricardo_sanchz/feeds/welcome-feed/data');
        if ($response->ok())
   {
    return response()->json([ 
    "msg"=> "Consumo satisfactorio...",
    "data"=> $response->json() 
    ], 200);
   }
   else { return response()->json([ 
    "msg"=> "Ocurrio un error en la api de adafruit",
    "data"=> $response->body()
    ], 200);}
    }
   
    public function apiHttp(){
     $response =Http::get('https://rickandmortyapi.com./api/character'); //Api a consumir
   if ($response->ok())
   {
    return response()->json([ 
    "msg"=> "Consumo satisfactorio...",
    "data"=> $response->json('results') //Lo que quiero me regrese
    ], 200);
   }
   else { return response()->json([ 
    "msg"=> "Ocurrio un error en la api ",
    "data"=> $response->body()
    ], 200);}
    }
    public function update(Request $request, int $id){}
   
    public function index(){ 
        return response()->json([
            "msg"=> "Datos encontrados satisfactoriamente",
    "data"=> Persona::all() 
    ], 200);}

    publix function store(Request $request) {}
    public function destroy (int $id){}
    }


