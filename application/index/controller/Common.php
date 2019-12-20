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
use think\Request;
use think\Db;

/**
 * 系统通用控制器：需登录
 * @author  tangtanglove <dai_hang_love@126.com>
 */
class Common extends Controller
{
    private $APPID  = 'wx538b08110815d813';
    private $SECRET = 'f42ee74c944f91ae120c595972002009';
    /**
     * 初始化方法
     * @author tangtanglove
     */
    protected function _initialize()
    {
        $data = getSignPackage($this->APPID, $this->SECRET, request()->url());
        $this->assign('data', $data);
        if (session('index_user_auth.openid')) {
            if (!defined('OPENID')) {
                define('OPENID', session('index_user_auth.openid'));
            }
            if (!+('UID')) {
                define('UID', session('index_user_auth.Uid'));
            }
            if (!defined('BID')) {
                define('BID', session('index_user_auth.Bid'));
            }
        } else {
            $code = Request::instance()->param('code');
            if (!$code) {
                $local = 'http://hq.oonekj.com/index.php';
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->APPID . "&redirect_uri=" . urlencode($local) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect");
                exit;
            }
            $info = get_user_openid($this->APPID, $this->SECRET, $code); //获取用户的OPENID
            session('index_user_auth.openid', $info['openid']);
            define('OPENID', session('index_user_auth.openid'));
        }
        if (!UID || !BID) {
            $userInfo = Db::name('student')->where(['openid' => OPENID])->find();
            if ($userInfo) {
                session('index_user_auth', $userInfo);
                if (!defined('UID')) {
                    define('UID', $userInfo['Uid']);
                }
                if (!defined('BID')) {
                    define('BID', $userInfo['Bid']);
                }
            } else {
                $this->redirect('/index.php/Base/login');
            }
        }
    }

    /* 退出登录 */
    public function logout()
    {
        //退出登录，注销session
        session('index_user_auth', null);
        $this->redirect(url('index/login'));
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
}
