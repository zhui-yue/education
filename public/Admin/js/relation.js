layui.use(['form', 'laydate', 'table', 'laytpl', 'jquery'], function () {
    var form = layui.form,
        $ = layui.jquery,
        laydate = layui.laydate,
        table = layui.table;
    var SchType = ProType = false, SchPid = ProPid = 0;

    //批次列表
    var tableIns = table.render({
        elem: '#infoList',
        method: 'POST',
        url: '/admin.php/Activity/batch/', //数据接口
        cellMinWidth: 50,
        id: "infoList",
        cols: [[
            // { type: "checkbox", width: 50 },
            { field: 'id', title: 'ID', width: 120, align: "center" },
            { field: 'name', title: '批次名称', align: "center" }
        ]]
    });
    getRelation('Schedule');
    getRelation('Project');
    var loadTips = layer.load(1, { shade: [0.7, '#000'] });//0.1透明度的白色背景
    //批次关联日程（监听表格行点击）
    table.on('row(infoList)', function (obj) {
        var data = obj.data;
        // layer.load(1, { time: 500, shade: [0.7, '#000'] });//0.1透明度的白色背景
        $('.schedule legend').html('日程列表：' + data.name);
        $('.schedule .editSch').attr('data-pid', data.id);
        SchPid = data.id;
        $(".schedule").fadeIn('slow');
        $(".project").fadeOut('slow');
        //标注选中样式
        obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
        //清除选中日程
        $(".schedule form").find("input").prop('checked', false);
        // console.log('id=' + data.id)
        $.each(list, function (key, item) {
            if (item.Bid == data.id) {
                //添加选中日程
                $(".schedule form").find("input[data-id='" + item.Sid + "']").prop('checked', true);
            }
        })
        form.render('checkbox');//渲染复选框

    });
    //日程关联项目
    $(".schedule").on('click', '.panel_foot', function () {
        var name = $(this).prev('div').find('input').attr('title'),
            id = $(this).attr('data-id');
        // layer.load(1, { time: 500, shade: [0.7, '#000'] });//0.1透明度的白色背景
        // console.log(name);
        $('.project legend').html('项目列表：' + name);
        $('.project .editPro').attr('data-pid', id);
        ProPid = id;
        $(".project").fadeIn('slow');
        //清除选中日程
        $(".project form").find("input").prop('checked', false);
        // console.log(id);        
        $.each(list, function (key, item) {
            if (item.Bid == SchPid && item.Sid == id) {
                //添加选中日程
                $(".project form").find("input[data-id='" + item.Pid + "']").prop('checked', true);
            }
        })
        form.render('checkbox');//渲染复选框
    })
    //修改日程
    $(".schedule").on('click', '.editSch', function () {
        var ids = [], pid = $('.editSch').data('pid');
        //将页面全部复选框选中的值拼接到一个数组中
        $(".schedule form input[type=checkbox]:checked").each(function () {
            ids.push($(this).data('id'));
        });
        //数组
        // console.log('pid=' + SchPid);
        // console.log(ids);
        editRelation(SchPid, ids, 0, 'BatSch');
    })
    //修改项目
    $(".project").on('click', '.editPro', function () {
        var ids = [], pid = $('.editPro').data('pid');
        //将页面全部复选框选中的值拼接到一个数组中
        $(".project form input[type=checkbox]:checked").each(function () {
            ids.push($(this).data('id'));
        });
        //数组
        // console.log('pid=' + ProPid);
        // console.log(ids);
        editRelation(SchPid, ProPid, ids, 'SchPro');
    })
    //---------------------------------------------------------------------------------------------
    function getRelation(type, id = 0) {
        $.ajax({
            type: "post",
            url: "/admin.php/Activity/relation",
            dataType: 'json',
            data: {
                type: type,
                id: id
            },
            success: function (html) {
                if (type == 'Schedule') {
                    $(".schedule form").html(html);
                    //初始化渲染表单
                    form.render();
                    SchType = true;
                }
                if (type == 'Project') {
                    $(".project form").html(html);
                    //初始化渲染表单
                    form.render();
                    ProType = true;
                }
                if (SchType && ProType) layer.close(loadTips);
            },
            error: function () {
                layer.close(index);
                layer.msg('AJAX错误', {
                    time: 1000
                });
                // window.location.reload();
            }
        });
    }
    function editRelation(Bid, Sid, Pid, type) {
        var index = layer.msg('修改中，请稍候', { icon: 16, time: false, shade: 0.8 });
        if (Sid.length < 1 || Pid.length < 1) {
            layer.close(index);
            layer.msg('至少选择一项！');
            return false;
        }
        $.ajax({
            type: "post",
            url: "/admin.php/Activity/relationEdit",
            dataType: 'json',
            data: {
                Bid: Bid,
                Sid: Sid,
                Pid: Pid,
                type: type
            },
            success: function (res) {
                layer.close(index);
                list = res.list
                if (res.code == 1) {
                    layer.msg('修改成功！');
                } else {
                    layer.msg('修改失败！');
                }
                // console.log(list);
            },
            error: function () {
                layer.close(index);
                layer.msg('AJAX错误', {
                    time: 1000
                });
                // window.location.reload();
            }
        });
    }
})
