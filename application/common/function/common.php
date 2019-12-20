<?php
// +----------------------------------------------------------------------
// | Minishop [ Easy to handle for Micro businesses ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.qasl.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: tangtanglove <dai_hang_love@126.com> <http://www.ixiaoquan.com>
// +----------------------------------------------------------------------

use think\Db;

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 系统根目录
 */
function root_path()
{
    return __ROOT__;
}

/**
 * 创建盐
 * @author tangtanglove <dai_hang_love@126.com>
 */
function create_salt($length = -6)
{
    return $salt = substr(uniqid(rand()), $length);
}
/**
 * minishop md5加密方法
 * @author tangtanglove <dai_hang_love@126.com>
 */
function minishop_md5($string, $salt)
{
    return md5(md5($string) . $salt);
}
/* 
*
 */
function getUserRelus($uid)
{
    $wheUser['userid'] = $uid;
    $groupId = Db::name('authUser')->where($wheUser)->value('groupid');
    return Db::name('authGroup')->where(['id' => $groupId])->value('rules');
}
function getUserSalt($uid)
{
    $wheUser['userid'] = $uid;
    return Db::name('authUser')->where($wheUser)->value('salt');
}
function getTemp($id, $Bid)
{
    $ids = Db::name('activityDetails')->where(['Bid' => BID])->group('Sid')->column('Sid');
    if ($id == min($ids)) $temp = 'schedule_first';
    elseif ($id == max($ids)) $temp = 'schedule_tail';
    else $temp = 'schedule';
    return $temp;
}
