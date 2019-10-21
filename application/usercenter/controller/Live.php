<?php

namespace app\usercenter\controller;


class Live extends Common
{
    public function index(){
        dump(session('home_userinfo'));die;
        return view('index',['list'=>[]]);
    }
}
