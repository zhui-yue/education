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
use think\Lang;
use think\Request;
use think\Loader;
use think\Db;

/**
 * 系统通用控制器：需登录
 * @author  tangtanglove <dai_hang_love@126.com>
 */
class Common extends Controller
{
    /**
     * 初始化方法
     * @author tangtanglove
     */
    protected function _initialize()
    {
        if (session('admin_user_auth')) {
            if (!defined('UID')) {
                define('UID', session('admin_user_auth.uid'));
            }
            // 初始化导航
            $this->navbar();
        } else {
            define('UID', null);
        }
        if (!UID) {
            $this->redirect(url('base/login'));
        }
        $data = request()->module() . '/' . request()->controller() . '/' . lcfirst(request()->action());
        if (UID != 1) {
            $this->auth(); //验证权限
        }
        //load_config();//加载接口配置      
    }

    /* 退出登录 */
    public function logout()
    {
        //退出登录，注销session
        session('admin_user_auth', null);
        $this->redirect(url('base/login'));
    }

    /**
     * 后台菜单
     * @author tangtanglove
     */
    protected function navbar()
    {
        if (UID != 1) {
            $rule = getUserRelus(UID);
            $wheNav['id'] = ['in', $rule];
        }
        $wheNav['status'] = 1;
        $menuList = Db::name('authRule')->where($wheNav)->order('sort asc,id asc')->select();
        // 顶级菜单列表
        foreach ($menuList as $vo) {
            if ($vo['pid'] == 0) {
                $navlist[] = $vo;
            }
        }
        $this->assign('navlist', $navlist);
        // 生成子集菜单树
        $menuTree = list_to_tree($menuList);
        $menuJson = list_to_sontree($menuTree);
        $this->assign('menuJson', $menuJson);
    }

    /**
     * 权限判断
     * @return [type] [description]
     */
    protected function auth()
    {
        //实例化验证器
        $auth = Loader::validate('Auth');
        $this->current_action = request()->module() . '/' . request()->controller() . '/' . lcfirst(request()->action());
        if (!in_array($this->current_action, $auth->_Exclude)) {
            if (!$auth->check($this->current_action, UID)) $this->error('没有权限','/admin.php/index/main');
        }
    }
}
