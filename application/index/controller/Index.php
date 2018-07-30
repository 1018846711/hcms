<?php

namespace app\index\controller;

use think\Controller;

class Index extends Controller {
    public function index() {
        echo request()->ip(0);
        echo "</br>";
        echo phpinfo();
    }

    public function test() {


    }
}
