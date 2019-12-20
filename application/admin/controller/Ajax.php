<?php
// +----------------------------------------------------------------------
// | Ajax控制器
// +----------------------------------------------------------------------
// | 负责后端所有Ajax操作
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Db;
use think\Loader;
use think\Validate;

class Ajax extends Common
{
    public function menuList()
    {
        /* if(UID == 1){
            $wheMenu = 1;
        }else{
            $wheGroup['id'] = Db::name('adminUser')->where(['userid'=>UID])->value('group');
            $userRule = Db::name('adminGroup')->where($wheGroup)->value('rule');
            $wheMenu['id'] = ['in',$userRule];
        }        
        $menuList = Db::name('Menu')->where($wheMenu)->order('sort asc,id asc')->select(); */
        /* $wheGroup['id'] = input('id');
        $groupRule = Db::name('authGroup')->where($wheGroup)->value('rules');
        if ($groupRule) {
            $res['code'] = 1;
            $res['rules'] = explode(',', $groupRule);
        } else {
            $res['code'] = 0;
        }
        return $res; */ 
    }
    public function menuadd()
    {
        $data = input('');
        // 实例化验证器
        $validate = Loader::validate('Menu');
        // 加载语言包
        $validate->loadLang();
        // 验证字段
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        if (isset($data['id']) && !empty($data['id'])) {
            $res = Db::name('authRule')->update($data) ? 1 : 0;
        } else {
            $res = Db::name('authRule')->insert($data) ? 1 : 0;
        }
        $resul['res'] = $res;
        if ($res == 1) {
            $resul['msg'] = '添加成功！！！';
            $wheNav['status'] = 0;
            $menuList = Db::name('authRule')->where($wheNav)->order('sort asc,id asc')->select();
            $menutree = list_to_tree($menuList, 'id', 'pid', 'children');
            $resul['json'] = json_encode($menutree);
        } else {
            $resul['msg'] = '添加失败！！！';
        }
        return $resul;
    }
    public function menudel()
    {
        $id = input('menuId');
        // 实例化验证器
        $validate = new Validate([
            'id'  => 'require',
        ]);
        $data = [
            'id'  => $id
        ];
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        $res = Db::name('authRule')->delete($id) ? 1 : 0;
        $result['res'] = $res;
        if ($res == 1) {
            $result['msg'] = '删除成功！！！';
        } else {
            $result['msg'] = '删除失败！！！';
        }
        return $result;
    }
    public function editRule()
    {
        $data = input('');
        $gid = $data['gid'];
        $ids = implode(',', $data['ids']);
        // 实例化验证器
        $validate = new Validate([
            'gid'  => 'require',
            'ids'  => 'require',
        ]);
        $data = [
            'gid'  => $gid,
            'ids'  => $ids
        ];
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        //--验证完成--------------------------------------------------------
        $result['res'] = Db::name('authGroup')->where(['id' => $gid])->update(['rules' => $ids]) ? 1 : 0;
        if ($result['res']) $result['msg'] = '修改成功！';
        else $result['msg'] = '修改失败！';
        return $result;
    }
    public function changeState()
    {
        $data = input('');
        $gid = $data['gid'];
        $state = $data['state'];
        // 实例化验证器
        $validate = new Validate([
            'gid'  => 'require',
            'state'  => 'require',
        ]);
        $data = [
            'gid'  => $gid,
            'state'  => $state
        ];
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        //--验证完成--------------------------------------------------------
        $result['res'] = Db::name('authGroup')->where(['id' => $gid])->update(['status' => $state]) ? 1 : 0;
        if ($result['res']) $result['msg'] = '修改成功！';
        else $result['msg'] = '修改失败！';
        return $result;
    }
    public function toStatue()
    {
        $data = input('');
        $id = $data['id'];
        $status = $data['status'];
        // 实例化验证器
        $validate = new Validate([
            'id'  => 'require',
            'status'  => 'require',
        ]);
        $data = [
            'id'  => $id,
            'status'  => $status
        ];
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        //--验证完成--------------------------------------------------------
        $result['code'] = Db::name('authUser')->where(['userid' => $id])->update(['status' => $status]) ? 1 : 0;
        if ($result['code']) $result['msg'] = '修改成功！';
        else $result['msg'] = '修改失败！';
        return $result;
    }
    public function userdel()
    {
        $data = input('');
        $id = $data['id'];
        $status = $data['status'];
        // 实例化验证器
        $validate = new Validate([
            'id'  => 'require',
            'status'  => 'require',
        ]);
        $data = [
            'id'  => $id,
            'status'  => $status
        ];
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        //--验证完成--------------------------------------------------------
        $result['code'] = Db::name('authUser')->where(['userid' => $id])->update(['status' => $status]) ? 1 : 0;
        if ($result['code']) $result['msg'] = '修改成功！';
        else $result['msg'] = '修改失败！';
        return $result;
    }
    public function commonOperation()
    {
        $tabArr = ['authUser', 'authGroup', 'authRule'];
        $operArr = [
            'edit' => [0 => '修改失败!', 1 => '修改成功!'],
            'del' => [0 => '删除失败!', 1 => '删除成功!']
        ];
        $data = Request::instance()->param();
        if($data){

        }



    }
    public function chanPassFun()
    {
        
    }
}
