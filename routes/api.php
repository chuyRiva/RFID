<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Models\Usuario;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
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
    return Usuario::where('usuario', $credentials->usuario)->where('password', $credentials->password)->first();
});

Route::get('/usuarios/', function() {
    return Usuario::all();
});

