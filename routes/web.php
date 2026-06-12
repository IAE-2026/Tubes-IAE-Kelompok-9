<?php

use App\Http\Controllers\GraphqlController;
use App\Http\Middleware\EnsureIaeApiKey;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/documentation');
});

Route::view('/api/documentation', 'docs.swagger');
Route::get('/docs/openapi.json', function () {
    return response()->json(json_decode(file_get_contents(public_path('docs/openapi.json')), true));
});
Route::view('/graphiql', 'docs.graphiql');
Route::post('/graphql', [GraphqlController::class, 'handle'])
    ->middleware(EnsureIaeApiKey::class);
