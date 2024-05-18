<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return $router->app->version();
});

// 微信服务路由
$router->group(['namespace' => 'Wechat', 'middleware' => ['web', 'wechat.oauth']], function () use ($router) {
    //微信服务 注意：在laravel要使用 Route::any, 由于lumen没有any路由，只能使用GET和POST两个路由，因为微信服务端认证的时候是 GET, 接收用户消息时是 POST
    $router->get('/wechat', 'WeChatController@serve');
    $router->post('/wechat', 'WeChatController@serve');
    $router->get('/user', function () {
        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料

        dd($user);
    });
});

// 腾讯企点服务路由
$router->group(['namespace' => 'Qidian', 'prefix' => 'qidian', 'middleware' => []], function () use ($router) {
    //服务器地址URL
    $router->get('/serve', 'QidianController@serve');
    $router->post('/serve', 'QidianController@serve');
    //授权事件接收URL
    $router->get('/event', 'QidianController@event');
    $router->post('/event', 'QidianController@event');
    //应用授权地址（指令回调URL）
    $router->get('/appAuth', 'QidianController@appAuth');
});
