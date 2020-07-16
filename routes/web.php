<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return redirect('https://motionwares.com/p/olaleye.osunsanya');    
});

$router->group(['namespace' => 'V1'], function() use ($router){
    //Post controller
    $router->group(['namespace' => 'Post'], function() use ($router){
        $router->get('/v1/posts/limit/{limit}', 'PostController@show');
    });

});
