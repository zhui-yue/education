var $, tab, dataStr, layer;
layui.config({
    base: "/public/static/js/"
}).extend({
    "bodyTab": "bodyTab"
})
var hide = 0;
layui.use(['tree', 'jquery', 'form', 'bodyTab'], function () {
    var tree = layui.tree;
    $ = layui.jquery;
    var form = layui.form;
    tab = layui.bodyTab({
        openTabNum: "50",  //最大可打开窗口数量
        url: "/admin.php/index/sonnav" //获取菜单json地址
    });
    //渲染
    var inst1 = tree.render({
        elem: '#navlist',  //绑定元素
        data: dataArr,
        id: 'demoId',
        edit: ['del'], //操作节点的图标
        click: function (obj) {
            $('.addmenu').text("确认修改").addClass("layui-btn-warm");
            form.val("addform", {
                "id": obj.data.id, // "name": "value"
                "title": obj.data.title, // "name": "value"
                "href": obj.data.href,
                "controller": obj.data.controller,
                "icon": obj.data.icon,
                "sort": obj.data.sort,
                "pid": obj.data.pid,
                "type": obj.data.type,
                "status": obj.data.status ? true : false,
            })
        },
        operate: function (obj) {
            var type = obj.type; //得到操作类型：add、edit、del
            var data = obj.data; //得到当前节点的数据
            var elem = obj.elem; //得到当前节点元素

            //Ajax 操作
            var id = data.id; //得到节点索引
            if (type === 'update') { //修改节点
                console.log(elem); //得到修改后的内容
                console.log(elem.find('.layui-tree-entry .layui-tree-txt').html()); //得到修改后的内容
                return false;
            } else if (type === 'del') { //删除节点
                var menuId = obj.data.id;
                $.ajax({
                    type: "post",
                    url: "/admin.php/Ajax/menudel",
                    dataType: 'json',
                    data: {
                        'menuId': menuId
                    },
                    success: function (data) {
                        layer.msg(data.msg, { time: 5000 });
                        if (data.res == 0) {
                            window.location.reload();
                        }
                    },
                    error: function () {
                        layer.msg('AJAX错误', {
                            time: 1000
                        });
                    }
                });
            };
        }
    });
    form.val("addform", {
        "sort": "0",
        "pid": "0",
    })
    form.on('submit(addmenu)', function (data) {
        var ajaxdata = data.field;
        $.ajax({
            type: "post",
            url: "/admin.php/Ajax/menuadd",
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
    });
    $('.btn-reset').on('click',function(){
        $('input[name=id]').val('');
        $('.addmenu').text("立即提交").removeClass('layui-btn-warm');
    })
});
