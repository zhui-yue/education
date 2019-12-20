<?php
// +----------------------------------------------------------------------
// | 首页框架控制器
// +----------------------------------------------------------------------
// | 包含后台首页所有功能
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Index extends Common
{
    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch('index');
    }
    /**
     * 首页-欢迎页
     */
    public function main()
    {
        return $this->fetch();
    }
   /*  public function test()
    {
        $url = 'http://audia6l.oonekj.com/api/audi.php';
        $arr = [
            'action' => 'addLocation',
            'lineId' => 1,
            'carId' => 2,
            'admin' => 1,
            'longitude' => '116.404556',
            'latitude' => '39.923789'
        ];
        $info = httpRequest($url, 'POST', $arr); //通过code获取用户的 openId 
        $info = json_decode($info);
        echo '<pre>';
        var_dump($info);
        die();
    } */
}
