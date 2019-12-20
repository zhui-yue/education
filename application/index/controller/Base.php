<?php
// +----------------------------------------------------------------------
// | Minishop [ Easy to handle for Micro businesses ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.qasl.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: tangtanglove <dai_hang_love@126.com> <http://www.ixiaoquan.com>
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Loader;
use think\Request;

/**
 * 系统基础控制器：不需登录
 * @author  tangtanglove <dai_hang_love@126.com>
 */
class Base extends Controller
{
    private $APPID  = 'wx538b08110815d813';
    private $SECRET = 'f42ee74c944f91ae120c595972002009';
    /**
     * 用户登录方法
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function login()
    {
        if (Request::instance()->isPost()) {
            $data = input('');
            $where['Uid'] = trim($data['uid']); //登录名
            $userInfo = Db::name('student')->where($where)->find();
            if ($userInfo) {
                if (empty($userInfo['openid'])) {
                    Db::name('student')->where($where)->update(['openid' => session('index_user_auth.openid')]);
                    // $session['openid']    =  session('index_user_auth.openid'); //用户ID
                    // $session['Uid']       = $userInfo['Uid']; //用户ID
                    // $session['Bid']       = $userInfo['Bid']; //用户ID
                    // // 记录用户登录信息
                    // session('index_user_auth', $session);
                    return $this->success('登陆成功！', url('index/index/index'));
                } else {
                    return $this->error('该学员已绑定微信！');
                }
            } else {
                return $this->error('该学员不存在！');
            }
        } else {
            $data = getSignPackage($this->APPID, $this->SECRET, request()->url());
            $this->assign('data', $data);
            return $this->fetch('login');
        }
    }
    public function loginOut()
    {
        session(null);
        return $this->fetch('login');
    }

    /**
     * 系统验证码方法
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function captcha()
    {
        $captcha = new \org\Captcha(config('captcha'));
        $captcha->entry();
    }
}
