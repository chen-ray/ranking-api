<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/index', [Controller::class, 'api']);
Route::get('index', [Controller::class, 'api']);
Route::get('breakdowns/{rankingId}', [Controller::class, 'breakdowns'])->name('apiBreakdowns');
Route::get('publication-ids', [Controller::class, 'publicationIds']);
Route::get('crawler/{action}', [Controller::class, 'crawler']);
Route::get('hand-country-url', [Controller::class, 'countryUrl']);
Route::get('weeks', [Controller::class, 'weeks']);
