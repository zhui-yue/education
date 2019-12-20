<?php
// +----------------------------------------------------------------------
// | 首页框架控制器
// +----------------------------------------------------------------------
// | 包含后台首页所有功能
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Db;
use app\common\model\CommonModel;


class Base extends CommonModel
{
    /**
     * 题目总数
     */
    public function count($where = 1)
    {
        return Db::name('problem')->where($where)->count();
    }
}
