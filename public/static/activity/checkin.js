layui.use(['form', 'layer', 'laydate', 'table', 'laytpl', 'jquery'], function () {
    var layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        table = layui.table;

    //用户列表
    var tableIns = table.render({
        elem: '#infolist',
        method: 'POST',
        url: '/admin.php/Activity/payment/', //数据接口
        cellMinWidth: 95,
        page: true,
        height: "full-125",
        limit: 10,
        limits: [10, 15, 20, 25],
        id: "userListTable",
        cols: [[
            // { type: "checkbox", width: 50 },
            { field: 'id', title: 'ID', width: 60, align: "center" },
            { field: 'name', title: '姓名', align: "center" },
            { field: 'tele', title: '电话', align: 'center' },
            { field: 'comp', title: '公司', align: 'center' },
            {
                field: 'time', title: '添加时间', width: 170, align: 'center', templet: function (d) {
                    return timeTodate(d.time)
                }
            },
            { title: '操作', width: 170, templet: '#listBar', align: "center" }
        ]]
    });
    //全部导出
    $(".exportAll").click(function () {
        layer.confirm('确定全部导出？', { icon: 3, title: '提示信息' }, function (index) {
            window.location.replace("/admin.php/Activity/exportCheckin");
            layer.close(index);
        })

    })
    //格式化时间
    function filterTime(val) {
        if (val < 10) {
            return "0" + val;
        } else {
            return val;
        }
    }
    //时间戳转换
    function timeTodate(time) {
        date = time ? new Date(time * 1000) : new Date();
        Y = date.getFullYear();
        m = (date.getMonth() + 1 < 10 ? date.getMonth() + 1 : date.getMonth() + 1);
        d = date.getDate();
        H = date.getHours();
        i = date.getMinutes();
        s = date.getSeconds();
        return filterTime(Y) + '-' + filterTime(m) + '-' + filterTime(d) + ' ' + filterTime(H) + ':' + filterTime(i);
    }
    //列表操作
    table.on('tool(infolist)', function (obj) {
        var layEvent = obj.event,
            data = obj.data;

        if (layEvent === 'del') { //删除
            layer.confirm('确定删除此条签到信息？', { icon: 3, title: '提示信息' }, function (index) {
                $.post("/admin.php/Activity/delCheckin", {
                    ids: data.id  //将需要删除的newsId作为参数传入
                }, function (data) {
                    layer.close(index);
                    if (data) {
                        tableIns.reload();
                        layer.msg('删除成功！',{time:1000});
                    } else {
                        layer.msg('删除失败！',{time:1000});
                    }
                })
            });
        }
    });
})