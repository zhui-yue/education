<?php
// +----------------------------------------------------------------------
// | 首页框架控制器
// +----------------------------------------------------------------------
// | 包含后台首页所有功能
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Controller;
use think\Loader;
use think\Request;
use think\Cache;

class Photograph extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch();
    }  
}
