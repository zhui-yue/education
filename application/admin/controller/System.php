<?php
// +----------------------------------------------------------------------
// | 首页框架控制器
// +----------------------------------------------------------------------
// | 包含后台首页所有功能
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Db;
use think\Request;
use think\Loader;

class System extends Common
{
    /**
     * 首页
     */
    public function menu()
    {
        // $wheNav['hide'] = 0;
        $menuList = Db::name('authRule')->order('sort asc,id asc')->select();
        $menutree = list_to_tree($menuList, 'id', 'pid', 'children');
        $menuselect = tree_to_select($menutree);
        $this->assign('menutree', json_encode($menutree));
        $this->assign('option', $menuselect);
        return $this->fetch();
    }
    //权限分组
    public function group()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            if (isset($data['id'])) {
                $list = Db::name('authGroup')->where(['id' => $data['id']])->value('rules');
                $result['rules'] = explode(',', $list);
            } else $list = Db::name('authGroup')->order('id asc')->select();
            $result['code'] = 0;
            $result['msg'] = '';
            $result['count'] = is_array($list)?count($list):0;
            $result['data'] = $list;
            return $result;
        }
        $rule = getUserRelus(UID);
        if (UID != 1) {
            $rule = getUserRelus(UID);
            $wheNav['id'] = ['in', $rule];
        }
        $wheNav['status'] = 1;
        $menuList = Db::name('authRule')->where($wheNav)->order('sort asc,id asc')->select();
        $menutree = list_to_tree($menuList);
        $menuView = tree_to_view($menutree);
        $this->assign('menuView', $menuView);
        return $this->fetch();
    }
    //用户列表
    public function userlist()
    {
        if (Request::instance()->isPost()) {
            $list = Db::name('authUser')->order('userid asc')->select();
            $result['code'] = 0;
            $result['msg'] = '';
            $result['count'] = count($list);
            $result['data'] = $list;
            return $result;
        }
        $groupList = Db::name('authGroup')->where(['status' => 1])->column('id,title');
        $this->assign('groupList', $groupList);
        return $this->fetch();
    }
    //添加用户
    public function useradd()
    {
        if (Request::instance()->isPost()) {
            $data = input('');
            $data['addtime'] = strtotime($data['addtime']);
            $validate = Loader::validate('System');
            // 加载语言包
            $validate->loadLang();
            // 验证字段
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            if($data['userid']){
                $where['userid'] = $data['userid'];
                if($data['password']){
                    $salt = getUserSalt($data['userid']);
                    $data['password'] = minishop_md5($data['password'],$salt);
                }else unset($data['password']);
                unset($data['userid']);
                unset($data['addtime']);
                $result['code'] = Db::name('authUser')->where($where)->update($data)?1:0;
                $result['msg'] = $result['code']?'修改成功！':'修改失败！';
            }else{
                $data['salt'] = create_salt();
                $data['password'] = minishop_md5($data['password'],$data['salt']);
                $result['code'] = Db::name('authUser')->insert($data)?1:0;
                $result['msg'] = $result['code']?'添加成功！':'添加失败！';
            }
            return $result;
        }
        if(UID != 1){
            $wheGro['id'] = 1;
        }
        $wheGro['status'] = 1;
        $groupList = Db::name('authGroup')->where($wheGro)->column('id,title');
        $this->assign('groupList', $groupList);
        return $this->fetch();
    }
    //个人资料
    public function userdel()
    {
        return $this->fetch();
    }
    //个人资料
    public function selfdata()
    {
        return $this->fetch();
    }
}
