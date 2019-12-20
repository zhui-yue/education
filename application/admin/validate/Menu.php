<?php
// +----------------------------------------------------------------------
// | Minishop [ Easy to handle for Micro businesses]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.qasl.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: tangtanglove <dai_hang_love@126.com> <http://www.ixiaoquan.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

use think\Validate;
use think\Db;

class Menu extends Validate
{
    protected $rule =   [
        'title'  => 'require'
    ];

    protected $message  =   [];

    // 加载语言包
    public function loadLang(){
        $this->message = [
	        'signname.require' => '菜单名称不可为空',
        ];
    }
    public function valirepeat($value)
    {
        return Db::name('authUser')->where(['signname'=>$value])->find()?'登录名不可重复':true;
    }
    

}