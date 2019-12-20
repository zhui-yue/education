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
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 生成子集菜单树
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_sontree($tree)
{
    // 创建Arr
    $resu = array();
    foreach ($tree as $title) {
        foreach ($title as $data) {
            if (is_array($data)) {
                $resu[$title['href']] = $data;
            }
        }
    }
    return json_encode($resu);
}
/**
 * tree转换为select
 * @author  tangtanglove
 */
function tree_to_select($list, $level = 0, $repeat = "--")
{
    $data = '';
    foreach ($list as $key => $value) {
        $data = $data . "<option value='" . $value['id'] . "'>" . str_repeat($repeat, $level) . $value['title'] . "</option>";
        if (!empty($value['children'])) {
            $data = $data . tree_to_select($value['children'], $level + 1);
        }
    }
    return $data;
}
/**
 * tree转换为view
 * @author  tangtanglove
 */
function tree_to_view($list, $level = 1)
{
    $data = '';
    foreach ($list as $key => $value) {
        if($level < 3){
            $data .= "  <div class='layui-colla-item'>
                            <div class='layui-colla-title'>
                                <input type='checkbox' id='{$value['id']}' title='{$value['title']}' lay-skin='primary'>
                            </div>
                            <div class='layui-colla-content'>";
                                if (isset($value['children'])) {
                                    $level++;
                                    $data .= tree_to_view($value['children'],$level);
                                    $level--;
                                }
            $data .=        "</div>
                        </div>";
        }else{
            $data .=   "<div class='layui-card layui-colla-item'>
                            <div class='layui-card-header'>
                                <input type='checkbox' id='{$value['id']}' title='{$value['title']}' lay-skin='primary'>
                            </div>
                            <div class='layui-card-body'>";
            if(isset($value['children'])){
                    // $data .= "<div class='layui-colla-item'>";
                foreach ($value['children'] as $key => $io) {      
                    $data .= "<input type='checkbox' id='{$io['id']}'  title='{$io['title']}'>";     
                }
                    // $data .= "</div>";
            }
            $data .=       "</div>
                        </div>";
        }
    }
    $data =  "<div class='layui-collapse' lay-accordion=''>" . $data . "</div>";
    return $data;
}
