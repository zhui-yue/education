<?php
// +----------------------------------------------------------------------
// | 首页框架控制器
// +----------------------------------------------------------------------
// | 包含后台首页所有功能
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Request;
use think\Db;
use think\Loader;

class Activity extends Common
{
    /**
     * 活动批次
     */
    public function batch()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $count = Db::name('activityBatch')->count();
            $list = Db::name('activityBatch')->order('statime asc')->select();
            $result['code'] = 0;
            $result['msg'] = '';
            $result['count'] = $count;
            $result['data'] = $list;
            return $result;
        }
        return $this->fetch();
    }
    /**
     * 添加修改批次
     */
    public function batchAdd()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            if (isset($data['time'])) {
                $time = explode('-', $data['time']);
                $data['statime'] = strtotime($time[0]);
                $data['endtime'] = strtotime($time[1]);
                unset($data['time']);
            }
            if (isset($data['id'])) {
                $result['code'] = Db::name('activityBatch')->where(['id' => $data['id']])->update($data);
                $result['msg'] =  $result['code'] ? '修改成功！' : '修改失败！';
            } else {
                $result['code'] = Db::name('activityBatch')->insert($data);
                $result['msg'] =  $result['code'] ? '添加成功！' : '添加失败！';
            }
            return $result;
        }
    }
    /**
     * 删除批次
     */
    public function batchDel()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $result['code'] = Db::name('activityBatch')->where(['id' => $data['id']])->delete();
            $result['msg'] =  $result['code'] ? '删除成功！' : '删除失败！';
            return $result;
        }
    }
    /**
     * 活动日程
     */
    public function schedule()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            if (isset($data['page']) && isset($data['limit'])) {
                $page = ($data['page'] - 1) * 10;
                $limit = $data['limit'];
            }
            if (isset($data['key'])) {
                $keywords =  $data['key'];
                $where['name|seo'] = ['like', "%$keywords%"];
            } else {
                $where = 1;
            }
            $count = Db::name('activitySchedule')->where($where)->count();
            $list = Db::name('activitySchedule')->where($where)->limit($page, $limit)->order('statime desc')->select();
            $result['code'] = 0;
            $result['msg'] = '';
            $result['count'] = $count;
            $result['data'] = $list;
            return $result;
        }
        return $this->fetch();
    }
    /**
     * 添加日程
     */
    public function scheduleAdd()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $time = explode(':', $data['addTime']);
            $time = $time[0] * 3600 + $time[1] * 60;
            $data['statime'] = $data['addDay'] * 24 * 60 * 60 + $time;
            unset($data['addDay']);
            unset($data['addTime']);
            $result['code'] = Db::name('activitySchedule')->insert($data);
            $result['msg'] =  $result['code'] ? '添加成功！' : '添加失败！';
            return $result;
        }
    }
    /**
     * 修改日程
     */
    public function scheduleEdit()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $result['code'] = Db::name('activitySchedule')->where(['id' => $data['id']])->update([$data['field'] => $data['value']]);
            $result['msg'] =  $result['code'] ? '修改成功！' : '修改失败！';
            return $result;
        }
    }
    /**
     * 删除日程
     */
    public function scheduleDel()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $result['code'] = Db::name('activitySchedule')->where(['id' => $data['id']])->delete();
            $result['msg'] =  $result['code'] ? '删除成功！' : '删除失败！';
            return $result;
        }
    }
    /**
     * 活动项目
     */
    public function project()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            if (isset($data['page']) && isset($data['limit'])) {
                $page = ($data['page'] - 1) * 10;
                $limit = $data['limit'];
            }
            if (isset($data['key'])) {
                $keywords =  $data['key'];
                $where['name'] = ['like', "%$keywords%"];
            } else {
                $where = 1;
            }
            $count = Db::name('activityProject')->where($where)->count();
            $list = Db::name('activityProject')->where($where)->limit($page, $limit)->order('id desc')->select();
            $result['code'] = 0;
            $result['msg'] = '';
            $result['count'] = $count;
            $result['data'] = $list;
            return $result;
        }
        return $this->fetch();
    }
    /**
     * 添加项目
     */
    public function projectAdd()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            if (isset($data['icon'][0]) || isset($data['icon'][1])) $field = $data['icon'];
            unset($data['icon']);
            unset($data['file']);
            if (isset($field[0]) && !empty($field[0])) {
                $icon = $this->base64_image_content($field[0], 'public/uploads/icon');
                $data['icon'] = $icon['file'];
            }
            if (isset($field[1]) && !empty($field[1])) {
                $sign = $this->base64_image_content($field[1], 'public/uploads/icon', $icon ? $icon['name'] . '-sign' : false);
                $data['sign'] = $sign['file'];
            }
            $data['option'] = implode(',', array_keys($data['option']));
            $result['code'] = Db::name('activityProject')->insert($data);
            $result['msg'] =  $result['code'] ? '添加成功！' : '添加失败！';
            return $result;
        }
    }
    /**
     * 修改项目
     */
    public function projectEdit()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $result['code'] = Db::name('activityProject')->where(['id' => $data['id']])->update([$data['field'] => $data['value']]);
            $result['msg'] =  $result['code'] ? '修改成功！' : '修改失败！';
            return $result;
        }
    }
    /**
     * 删除项目
     */
    public function projectDel()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $result['code'] = Db::name('activityProject')->where(['id' => $data['id']])->delete();
            $result['msg'] =  $result['code'] ? '删除成功！' : '删除失败！';
            return $result;
        }
    }
    /**
     * 活动项目
     */
    public function relation()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $str = $HTML = '';
            if ($data['type'] == 'Schedule') {
                $Schedule = Db::name('activitySchedule')->order('statime asc')->column('id,name,seo');
                foreach ($Schedule as $key => $vo) {
                    $title = $vo['name'];
                    $title .= $vo['seo'] ? '（' . $vo['seo'] . '）' : '';
                    $HTML .=  '<div class="panel_body">
                                    <div class="panel_head">
                                        <input type="checkbox" data-id="' . $key . '" name="option[' . $key . ']"  title="' . $title . '">
                                    </div>
                                    <div class="panel_foot layui-btn" data-id="' . $key . '">
                                        <i class="iconfont icon-sanjiaoxing"></i>
                                    </div>
                                </div>';
                }
                return $HTML;
            }
            if ($data['type'] == 'Project') {
                $Project = Db::name('activityProject')->order('sort asc,id desc')->column('id,name');
                foreach ($Project as $key => $vo) {
                    $HTML .=  '<input type="checkbox" data-id="' . $key . '" name="option[' . $key . ']"  title="' . $vo . '">';
                }
                return $HTML;
            }
        }
        $list = Db::name('relation')->select();
        $this->assign('list', json_encode($list));
        return $this->fetch();
    }
    public function relationEdit()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            if ($data['type'] == 'BatSch') {
                $result['del'] = Db::name('relation')->where(['Bid' => $data['Bid'], 'Sid' => ['not in', $data['Sid']]])->delete();
                $ids = Db::name('relation')->where(['Bid' => $data['Bid'], 'Sid' => ['in', $data['Sid']]])->column('Sid');
                $data['Sid'] = array_diff($data['Sid'], $ids);
                foreach ($data['Sid'] as $key => $vo) {
                    $add[$key]['Sid'] = $vo;
                    $add[$key]['Bid'] = $data['Bid'];
                }
            }
            if ($data['type'] == 'SchPro') {
                $result['del'] = Db::name('relation')->where(['Bid' => $data['Bid'], 'Sid' => $data['Sid']])->delete();
                foreach ($data['Pid'] as $key => $vo) {
                    $add[$key]['Pid'] = $vo;
                    $add[$key]['Sid'] = $data['Sid'];
                    $add[$key]['Bid'] = $data['Bid'];
                }
            }
            $result['add'] = isset($add) ? Db::name('relation')->insertAll($add) : 0;
            $result['code'] =  $result['add'] || $result['del'] ? 1 : 0;
            $result['list'] = Db::name('relation')->select();
            return $result;
        }
    }

    //存储base64图片
    public function base64_image_content($base64_image_content, $path, $name = false)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new['file'] = $path . "/" . date('Ymd', time()) . "/";
            if (!file_exists($new['file'])) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new['file'], 0700);
            }
            $new['name'] = $name ? $name : time();
            $new['file'] .= $new['name'] . ".{$type}";
            if (file_put_contents($new['file'], base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return $new;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
