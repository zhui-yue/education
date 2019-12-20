<?php
// +----------------------------------------------------------------------
// | 首页框架控制器
// +----------------------------------------------------------------------
// | 包含后台首页所有功能
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Ajax extends Controller
{
    public function addMessage()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $userInfo = session('suerInfo');
            $add['userid'] = isset($userInfo['id']) ? $userInfo['id'] : 0;
            $add['type'] = $data['type'];
            $add['company'] = $data['company'];
            $add['department'] = $data['department'];
            $add['name'] = $data['name'];
            $add['telephone'] = $data['telephone'];
            $add['time'] = time();
            $result = Db::name('nationalMessage')->insert($add) ? true : false;
            if ($result) {
                $where['id'] = $userInfo['recordId'];
                $edit['status'] = 1;
                Db::name('nationalRecord')->where($where)->update($edit);
            }
            return $result;
        }
        return false;
    }
    public function addAogeMessage()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $add['name'] = $data['name'];
            $add['tel'] = $data['tel'];
            $add['email'] = $data['email'];
            $add['address'] = $data['address'];
            $add['message'] = $data['message'];
            $add['time'] = time();
            $result = Db::name('aogeMessage')->insert($add) ? 1 : 0;
            //跨源请求（同源策略）
            header("Access-Control-Allow-Origin: *");
            return $result;
        }
    }
    public function checkIn()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $add['name'] = $data['name'];
            $add['tele'] = $data['tele'];
            $add['comp'] = $data['comp'];
            $add['time'] = time();
            $result = Db::name('checkin')->insert($add) ? 1 : 0;
            return $result;
        }
    }
}
