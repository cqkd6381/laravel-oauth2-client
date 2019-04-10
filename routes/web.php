<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//authorization_code 授权码模式(即先登录获取code,再获取token)
Route::get('/redirect', function () {
    $query = http_build_query([
        'client_id' => env('CLIENT_ID', null),
        'redirect_uri' => env('REDIRECT_URI', null),
        'response_type' => 'code',
        'scope' => 'check-status',
    ]);

    return redirect('http://laravel-oauth2-server.test/oauth/authorize?'.$query);
});

Route::get('/login/laravel-oauth2-server/callback', function (\Illuminate\Http\Request $request) {
    $http = new \GuzzleHttp\Client();
    $params = [
        'headers' => [
            'Accept' => 'application/json'
        ],
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => env('CLIENT_ID', null),
            'client_secret' => env('CLIENT_SECRET', null),
            'redirect_uri' => env('REDIRECT_URI', null),
            'code' => $request->code,
        ],
    ];
    $response = $http->post('http://laravel-oauth2-server.test/oauth/token', $params);
    return json_decode((string) $response->getBody(), true);
});

//implicit 简化模式(在redirect_uri 的Hash传递token; Auth客户端运行在浏览器中,如JS,Flash)
Route::get('/redirect1', function () {
    $query = http_build_query([
        'client_id' => env('CLIENT_ID2', null),
        'redirect_uri' => env('REDIRECT_URI2', null),
        'response_type' => 'token',
        'scope' => '',
    ]);
    return redirect('http://laravel-oauth2-server.test/oauth/authorize?'.$query);
});

Route::get('/implicit/callback', function (\Illuminate\Http\Request $request) {
    echo "参数可从URL中#后面获取\n";
    echo $request->url();
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
