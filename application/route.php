<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::domain('adminapi', function () {
    Route::resource('goods', 'api/goods');
    \think\Route::get('captcha/:id', "\\think\\captcha\\CaptchaController@index");
    Route::get('captcha', 'api/loginRegister/captcha');
    Route::post('login', 'api/loginRegister/login');
    Route::get('logout', 'api/loginRegister/logout');
    Route::post('logo', 'api/upload/logo');
    Route::post('images', 'api/upload/images');
    Route::resource('categorys', 'api/category');
    Route::resource('brands', 'api/brand');
    Route::resource('types', 'api/type');
    Route::resource('auths', 'api/auth');
    Route::resource('roles', 'api/role');
    Route::resource('admins', 'api/admin');
    Route::delete('delpics/:id', 'api/goods/delpics');
    Route::get('nav', 'api/auth/nav');
});
Route::get('index','home/index/index');

Route::group('user',function(){
    Route::rule('login','home/loginRegister/login','GET|POST');
    Route::rule('register','home/loginRegister/register','GET|POST');
    Route::rule('code','home/loginRegister/code','POST|GET');
    Route::get('logout','home/loginRegister/logout');
    Route::get('is_reg','home/loginRegister/is_reg');
});
Route::group('home/login',function(){
    Route::get('qqcallback','home/thirdLogin/qq');
    Route::get('alicallback','home/thirdLogin/alipay');
    Route::rule('relevance','home/thirdLogin/relevance','GET|POST');
});

Route::group('order',function(){
    Route::get('settlement','home/order/settlement');
    Route::post('add','home/order/add');
});

Route::group('pay',function(){
    Route::rule('index','home/pay/index','GET');
    Route::post('alinotify','home/pay/alinotify');
    Route::get('alicallback','home/pay/alicallback');
    Route::post('submit','home/pay/pay');
});

Route::resource('goods', 'home/goods');
// Route::Group('home/login',function(){
//     Route::get('qqcallback','home1/thirdLogin/qq');
//     Route::get('alicallback','home1/thirdLogin/alipay');
// });




