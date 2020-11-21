<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\SpecificationController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/v1/packaging/specification/materials', [SpecificationController::class, 'getMaterials']);
Route::get('/v1/packaging/specification/shapes', [SpecificationController::class, 'getShapes']);
Route::get('/v1/packaging/specification/inputs', [SpecificationController::class, 'getInputs']);
Route::get('/v1/packaging/specification/results', [SpecificationController::class, 'getEmptyResults']);
Route::post('/v1/packaging/specification/results', [SpecificationController::class, 'getResults']);
