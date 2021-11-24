<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Models\Usuario;
Use App\Models\Activo;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|	C-0
|	bimbo sa de cv
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login/', function(Request $request) {
	$credentials = $request->only('usuario', 'password');
	$datau1 = Usuario::where('usuario', $credentials['usuario'])
		->where('password', $credentials['password'])
    	->leftjoin('empresas', 'empresas.id', 'usuarios.empresa_id')
    	->select('usuarios.*', 'empresas.nombre AS nombre_empresa','empresas.color_primario','empresas.color_secundario','empresas.logo')
    	->first();
    
    if($datau1){
		Usuario::where('usuario', $credentials['usuario'])->where('password', $credentials['password'])
		    ->leftjoin('empresas', 'empresas.id', 'usuarios.empresa_id')
		    ->select('usuarios.*', 'empresas.nombre AS nombre_empresa','empresas.color_primario','empresas.color_secundario','empresas.logo')
		    ->first()->update(array('token' => getToken(16)));
    } 	
    

    $datau = Usuario::where('usuario', $credentials['usuario'])->where('password', $credentials['password'])
    ->leftjoin('empresas', 'empresas.id', 'usuarios.empresa_id')
    ->select('usuarios.nombre','usuarios.email','usuarios.tipo_usuario','usuarios.activo','usuarios.token', 'empresas.nombre AS nombre_empresa','empresas.color_primario','empresas.color_secundario','empresas.logo')
    ->first();

    $response = getResponse($datau,"Usuario correcto","Las credenciales no coinciden con ningún usuario");
    //$obj = json_decode($response)
    //echo $response['code'];
    $obj_r = json_encode($response);
    return response($obj_r, $response['code'])
    	->header('Content-Type', 'application/json');
});

Route::get('/usuarios/', function() {
    return Usuario::all();
});


Route::get('/activos/', function(Request $request) {
    $vars = $request->only('token');
    $dataa = Activo::all();
    $datau1 = Usuario::where('token', $vars)
        ->leftjoin('empresas', 'empresas.id', 'usuarios.empresa_id')
        ->select('usuarios.*', 'empresas.nombre AS nombre_empresa','empresas.color_primario','empresas.color_secundario','empresas.logo');

    if($datau1){

    }else{
        $response = getResponse(null,"ok","Usuario inactivo");
        $obj_r = json_encode($response);
    return response($obj_r, $response['code'])
        ->header('Content-Type', 'application/json');
    }    
    $response = getResponse($dataa,"ok","Algo salío mal al consultar los activos");
    $obj_r = json_encode($response);
    return response($obj_r, $response['code'])
        ->header('Content-Type', 'application/json');
});

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
    }

    return $token;
}


function getResponse($data,$msgs,$msgf){
	$msg = "";
	$code = 200;
	if($data){
		$json_array  = json_decode($data, true);
		if(count($json_array)>0){
			$msg = $msgs;
		}else{
			$msg = $msgf;
		}
	}else{
		$msg = $msgf;
		$code = 400;
	}
	


	//$obj_r = new array();
	$obj_r['data'] = $data;
	$obj_r['msg'] = $msg;
	$obj_r['code'] = $code;

	$myJSON = $obj_r;

	return $myJSON;
}