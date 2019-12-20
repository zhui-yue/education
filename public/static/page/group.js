var $, tab, dataStr, layer;
layui.config({
    base: "/public/static/js/"
}).extend({
    "bodyTab": "bodyTab"
})
var hide = 0;
layui.use(['element', 'jquery', 'form', 'table'], function () {
    var element = layui.element;
    $ = layui.jquery;
    var form = layui.form;
    var table = layui.table;
    //表格渲染
    table.render({
        elem: '#demo',
        height: "full-125",
        method: 'POST',
        url: '/admin.php/System/group/', //数据接口
        page: false, //开启分页
        cols: [[ //表头
            { field: 'id', title: 'ID', width: 68, align: 'center', sort: true },
            { field: 'title', title: '分组名' },
            {
                field: 'status', title: '启用', align: 'center', width: 90, templet: function (d) {
                    var status = ''
                    if (d.status == 1) status = 'checked';
                    return '<input type="checkbox" lay-filter="status" lay-skin="switch" value="' + d.id + '" lay-text="是|否" ' + status + '>'
                }
            },
        ]]
    });
    //监听表格行点击
    table.on('row(test)', function (obj) {
        var data = obj.data;
        $('.layui-field-title legend').html(data.name);
        $('.layui-field-title input').val(data.id);
        //标注选中样式
        obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
        /* var index = layer.load(1, {
            shade: [0.1, '#000'] //0.1透明度的白色背景
        }); */
        var index = layer.msg('加载中，请稍候', { icon: 16, time: false, shade: [0.1, '#000'] });
        $.ajax({
            type: "POST",
            url: "/admin.php/System/group",
            dataType: 'json',
            data: {
                'id': data.id
            },
            success: function (res) {
                if (res.code == 0) {
                    layer.msg('加载成功');
                    $("form input").prop('checked', false);
                    $.each(res.rules, function () {
                        $("#" + this).prop('checked', true);
                        // console.log($("input#"+this));
                    })
                    form.render('checkbox');//重新渲染
                } else {
                    layer.msg('加载失败');
                }
                layer.close(index)
            },
            error: function () {
                layer.msg('AJAX错误', {
                    time: 1000
                });
            }
        });
    });
    //启用/禁用事件
    form.on('switch(status)', function (data) {
        var index = layer.msg('修改中，请稍候', { icon: 16, time: false, shade: 0.8 });
        $.ajax({
            type: "POST",
            url: "/admin.php/Ajax/changeState",
            dataType: 'json',
            data: {
                'gid': data.value,
                'state': data.elem.checked ? 1 : 0
            },
            success: function (data) {
                layer.msg(data.msg);
                if (data.res == 0) {
                    window.location.reload();
                }
                layer.close(index)
            },
            error: function () {
                layer.msg('AJAX错误', {
                    time: 1000
                });
            }
        });
        event.stopPropagation();
    });
    //阻止事件冒泡
    // $(".layui-form-checkbox").click(function (event) {
    //     event.stopPropagation();
    // })
    //监听表单内复选框
    form.on('checkbox()', function (data) {
        // console.log(data.elem); //得到checkbox原始DOM对象
        // console.log(data.elem.checked); //是否被选中，true或者false
        // console.log(data.value); //复选框value值，也可以通过data.elem.value得到
        // console.log(data.othis); //得到美化后的DOM对象
        if (data.elem.checked) {    //子集选中时,所有父级都选中
            $(this).parents('.layui-colla-item').children('div.layui-colla-title,div.layui-card-header').find('input').prop('checked', true);
        } else {  //父级取消所有子集一同取消
            if ($(this).parent('.layui-card-body').length == 0)
                $(this).parents('.layui-colla-item:first').find('input').prop('checked', false);
        }
        form.render('checkbox');//渲染复选框
        event.stopPropagation();// 阻止冒泡
    });
    //监听表单内复选框
    /* $("form .layui-form-checkbox").on('click',function(data){
        console.log(data);
        console.log(this);
    }) */
    //添加新分组
    $('.addGroup').on('click', function () {

    })
    //修改权限按钮
    $('.editRule').on('click', function () {
        var gid = $('.layui-field-title input').val();
        var ids = [];
        $.each($('form input'), function () {
            if ($(this).prop('checked')) {
                ids.push($(this).attr('id'));
            }
        })
        // console.log(ids);
        var index = layer.msg('修改中，请稍候', { icon: 16, time: false, shade: [0.1, '#000'] });
        if (gid) {
            $.ajax({
                type: "post",
                url: "/admin.php/Ajax/editRule",
                dataType: 'json',
                data: {
                    'gid': gid,
                    'ids': ids
                },
                success: function (data) {
                    layer.msg(data.msg);
                    if (data.res == 0)
                        window.location.reload();
                    layer.close(index)
                },
                error: function () {
                    layer.msg('AJAX错误', {
                        time: 1000
                    });
                }
            });
        } else
            layer.msg('未选中分组');


    })
    //表单初始赋值
    /* form.val("ruleList", {
        "1": true, // "name": "value"
    }) */
    //监听表单提交事件
    /* form.on('submit(editRule)', function (data) {
        var ajaxdata = data.field;
        $.ajax({
            type: "post",
            url: "/admin.php/Ajax/addmenu",
            dataType: 'json',
            data: ajaxdata,
            success: function (data) {
                layer.msg(data.msg, { time: 5000 });
                if (data.res) {
                    window.location.reload();
                }
            },
            error: function () {
                layer.msg('AJAX错误', {
                    time: 1000
                });
            }
        });
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    }); */
});
