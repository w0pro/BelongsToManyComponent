<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Wopro\BelongsToManyComponent\Controllers\BelongsToManyController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. You're free to add
| as many additional routes to this file as your tool may require.
|
*/

Route::get('/models',[BelongsToManyController::class, 'getRelations']);
Route::get('/search-model',[BelongsToManyController::class, 'search']);
Route::get('/search-all-model',[BelongsToManyController::class, 'searchAll']);
Route::post('/set-relations',[BelongsToManyController::class, 'store']);
Route::get('/get-relations/{id}',[BelongsToManyController::class, 'show']);
Route::get('/get-all-relations/{id}',[BelongsToManyController::class, 'index']);
Route::delete('/delete-relations/{id}',[BelongsToManyController::class, 'destroy']);
Route::post('/sort-relations',[BelongsToManyController::class, 'sort']);
Route::get('/get-rows/{id}',[BelongsToManyController::class, 'getRows']);

Route::post('/update-model/{id}/{objectName}/{subjectModel}',[BelongsToManyController::class, 'update']);







