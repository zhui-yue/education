<?php
// +----------------------------------------------------------------------
// | Minishop [ Easy to handle for Micro businesses ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.qasl.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: tangtanglove <dai_hang_love@126.com> <http://www.ixiaoquan.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

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
    /**
     * 初始化方法
     * @author tangtanglove
     */
    protected function _initialize()
    {
        // 检测程序安装
        /* if(!is_file(ROOT_PATH . 'data/install.lock')){
            $this->redirect(url('install/index/index'));
        }
        load_config();// 加载接口配置 */
    }

    /**
     * 用户登录方法
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function login()
    {
        if (Request::instance()->isPost()) {
            $datares = input('');
            $username = input('post.userName');
            $password = input('post.passWord');
            $captcha  = input('post.code');
            // 实例化验证器
            $validate = Loader::validate('Login');
            // 验证数据
            $data = ['username' => $username, 'password' => $password, 'captcha' => $captcha];
            // 加载语言包
            $validate->loadLang();
            // 验证
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            $where['signname'] = trim($username); //登录名
            $where['status']   = 1;
            $userInfo = Db::name('authUser')->where($where)->find();
            if ($userInfo && $userInfo['password'] === minishop_md5(trim($password), $userInfo['salt'])) {
                $session['uid']       = $userInfo['userid']; //用户ID
                $session['signname']  = $userInfo['signname']; //登录名
                $session['nickname']  = $userInfo['nickname']; //用户昵称
                // $session['email']     = $userInfo['email'];
                // $session['mobile']    = $userInfo['mobile'];
                // 记录用户登录信息
                session('admin_user_auth', $session);
                return $this->success('登陆成功！', url('admin/index/index'));
            } else {
                return $this->error('密码错误！' . minishop_md5($password, $userInfo['salt']) . '/' . $userInfo['password']);
            }
        } else {
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
