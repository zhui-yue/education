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

class Index extends Common
{
    /**
     * 首页
     */
    public function index()
    {
        $userInfo = Db::name('student')->where(['Uid' => UID])->find();
        $this->assign('info', $userInfo);
        return $this->fetch();
    }
    /**
     * 日程详情
     */
    public function main()
    {
        $list = Db::name('activityDetails')->where(['Bid' => BID])->group('Sid')->column('Sid,Sid,Sname,addtime');
        $ids = array_keys($list);
        $time = time();
        foreach ($list as $key => $vo) {
            $list[$key]['type'] = $vo['addtime'] < $time ? 1 : 0;
            $list[$key]['type'] = $vo['addtime'] < $time ? 1 : 0;
            $list[$key]['link'] = '';
        }
        $list[min($ids)]['link'] = 'first';
        $list[max($ids)]['link'] = 'tail';
        $this->assign('list', $list);
        return $this->fetch();
    }
    /**
     * 项目列表
     */
    public function schedule($id = false)
    {
        if (!$id) {
            $data = Request::instance()->param();
            $id = $data['id'];
        }
        $temp = getTemp($id, BID);
        $list = Db::name('activityDetails')->where(['Bid' => BID, 'Sid' => $id])->column('Pid,Pid,Pname,Sname,icon,sign');
        $whe['Uid'] = UID;
        $whe['Sid'] = $id;
        $whe['Pid'] = ['in', array_keys($list)];
        $ids = Db::name('signin')->where($whe)->column('Pid');
        $i = 0;
        if (isset($list[46])) {
            $list[46]['show'] = in_array(46, $ids) ? 'active' : '';
            $day['Morning'] = $list[46];
            unset($list[46]);
        }
        if (isset($list[47])) {
            $list[47]['show'] = in_array(47, $ids) ? 'active' : '';
            $day['Night'] = $list[47];
            unset($list[47]);
        }
        foreach ($list as $key => $vo) {
            $i++;
            $list[$key]['key'] = $i;
            $list[$key]['show'] = in_array($key, $ids) ? 'active' : '';
            $list[$key]['arrow'] = $i % 2 ? 'arrow1' : 'arrow2';
            $name = $vo['Sname'];
        }
        $list[max(array_keys($list))]['type'] = 0;
        if (isset($day)) $this->assign('day', $day);
        $this->assign('list', $list);
        $this->assign('name', $name);
        return $this->fetch($temp);
    }
    /**
     * 扫码签到
     */
    public function scanSignIn()
    {
        $time = time();
        $data = Request::instance()->param();
        $add['Pid'] = $data['pid'];
        $add['Uid'] = UID;
        $add['Sid'] = Db::name('activityDetails')->where(['Bid' => BID, 'addtime' => ['lt', time()]])->group('Sid')->order('addtime desc')->value('Sid');
        $result = Db::name('signin')->insert($add);
        $id = $add['Sid'];
        if (!$id) {
            $data = Request::instance()->param();
            $id = $data['id'];
        }
        $temp = getTemp($id, BID);
        $list = Db::name('activityDetails')->where(['Bid' => BID, 'Sid' => $id])->column('Pid,Pid,Pname,Sname,icon,sign');
        $whe['Uid'] = UID;
        $whe['Sid'] = $id;
        $whe['Pid'] = ['in', array_keys($list)];
        $ids = Db::name('signin')->where($whe)->column('Pid');
        $i = 0;
        if (isset($list[46])) {
            $list[46]['show'] = in_array(46, $ids) ? 'active' : '';
            $day['Morning'] = $list[46];
            unset($list[46]);
        }
        if (isset($list[47])) {
            $list[47]['show'] = in_array(47, $ids) ? 'active' : '';
            $day['Night'] = $list[47];
            unset($list[47]);
        }
        foreach ($list as $key => $vo) {
            $i++;
            $list[$key]['key'] = $i;
            $list[$key]['show'] = in_array($key, $ids) ? 'active' : '';
            $list[$key]['arrow'] = $i % 2 ? 'arrow1' : 'arrow2';
            $name = $vo['Sname'];
        }
        $list[max(array_keys($list))]['type'] = 0;
        if (isset($day)) $this->assign('day', $day);
        $this->assign('list', $list);
        $this->assign('name', $name);
        return $this->fetch($temp);
    }
    /**
     * 成绩查询
     */
    public function scoreQuery()
    {
        $temp = Request::instance()->param('temp');
        if (!isset($temp) && empty($temp)) $temp = 'scoreQuery';
        return $this->fetch($temp);
    }
}
