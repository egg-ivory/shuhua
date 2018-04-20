<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        echo 22;
        return $this->fetch('index');
    }
}
