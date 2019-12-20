layui.use(['form', 'layer', 'laydate', 'table', 'laytpl', 'jquery'], function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;

    //用户列表
    var tableIns = table.render({
        elem: '#numblist',
        method: 'POST',
        url: '/admin.php/Moothfest/number/', //数据接口
        cellMinWidth: 95,
        page: true,
        height: "full-125",
        limit: 10,
        limits: [10, 15, 20, 25],
        id: "userListTable",
        cols: [[
            // { type: "checkbox", width: 50 },
            { field: 'id', title: 'ID', width: 60, align: "center" },
            { field: 'time', title: '时间', width: 200, align: "center" },
            /* {
                field: 'time', title: '时间', width: 170, align: 'center', templet: function (d) {
                    return timeTodate(d.time)
                }
            }, */
            { field: 'count', title: '浏览人数', align: 'center' },
            { field: 'luck', title: '留资人数', align: 'center' },
            // { title: '操作', width: 80, templet: '#numblistBar', align: "center" }
        ]]
    });
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
    //是否启用
    form.on('switch(status)', function (data) {
        var index = layer.msg('修改中，请稍候', { icon: 16, time: false, shade: 0.8 });

        // var checkStatus = table.checkStatus('userListTable'); //idTest 即为基础参数 id 对应的值
        // var data = checkStatus.data;
        console.log(data);
        $.ajax({
            type: "post",
            url: "/admin.php/Ajax/toStatue",
            dataType: 'json',
            data: {
                'id': data.value,
                'status': data.elem.checked ? 1 : 0
            },
            success: function (res) {
                layer.msg(res.msg);
                table.reload('userListTable');//重载表格
                layer.close(index)
            },
            error: function () {
                layer.msg('AJAX错误', {
                    time: 1000
                });
                window.location.reload();
            }
        });
    })
    //更改权限组
    form.on('select(userGgroup)', function (data) {
        console.log($(this).data('id'));
        console.log(data.value);
    })
    //搜索【此功能需要后台配合，所以暂时没有动态效果演示】
    $(".search_btn").on("click", function () {
        if ($(".searchVal").val() != '') {
            table.reload("userListTable", {
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: {
                    key: $(".searchVal").val()  //搜索的关键字
                }
            })
        } else {
            layer.msg("请输入搜索的内容");
        }
    });

    $(".addUserBtn").click(function () {
        addUser();
    })

    //批量删除
    $(".exportAll").click(function () {
        layer.confirm('确定全部导出？', { icon: 3, title: '提示信息' }, function (index) {
            window.location.replace("/admin.php/Moothfest/exportNumb");
            layer.close(index);
        })

    })

    //列表操作
    table.on('tool(userList)', function (obj) {
        var layEvent = obj.event,
            data = obj.data;

        if (layEvent === 'edit') { //编辑
            addUser(data);
        } else if (layEvent === 'del') { //删除
            layer.confirm('确定删除此文章？', { icon: 3, title: '提示信息' }, function (index) {
                // $.get("删除文章接口",{
                //     newsId : data.newsId  //将需要删除的newsId作为参数传入
                // },function(data){
                tableIns.reload();
                layer.close(index);
                // })
            });
        } else if (layEvent === 'look') { //预览
            layer.alert("此功能需要前台展示，实际开发中传入对应的必要参数进行文章内容页面访问")
        }
    });

})