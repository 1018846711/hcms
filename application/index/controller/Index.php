<?php

namespace app\index\controller;

use think\Controller;

class Index extends Controller {
    public function index() {
        echo request()->ip(1);
        echo "</br>";
        echo phpinfo();
    }
}
