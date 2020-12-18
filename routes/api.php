<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\SpecificationController;
use App\Http\Controllers\API\V1\SpaceController;

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


Route::prefix('v1/packaging/specification')->group(function () {
    Route::get('/materials', [SpecificationController::class, 'getMaterials']);
    Route::get('/shapes', [SpecificationController::class, 'getShapes']);
    Route::get('/inputs', [SpecificationController::class, 'getInputs']);
    Route::get('/results', [SpecificationController::class, 'getEmptyResults']);
    Route::post('/results', [SpecificationController::class, 'getResults']);
});

Route::prefix('v1/packaging/area')->group(function () {
    Route::get('/areas', [SpaceController::class, 'getAreas']);
    Route::get('/areaInputs', [SpaceController::class, 'getAreaInputs']);
    Route::get('/partInputs', [SpaceController::class, 'getPartInputs']);
    Route::post('/calculateArea', [SpaceController::class, 'calculate']);
});
