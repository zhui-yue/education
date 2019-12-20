layui.use(['form', 'layer', 'laydate', 'table', 'laytpl', 'jquery'], function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;

    //用户列表
    var tableIns = table.render({
        elem: '#userList',
        method: 'POST',
        url: '/admin.php/System/userlist/', //数据接口
        cellMinWidth: 95,
        page: true,
        height: "full-125",
        limit: 10,
        limits: [10, 15, 20, 25],
        id: "userListTable",
        cols: [[
            { type: "checkbox", width: 50 },
            { field: 'userid', title: 'ID', width: 60, align: "center" },
            { field: 'signname', title: '登录名', width: 150, align: 'center' },
            { field: 'nickname', title: '用户昵称', align: 'center' },
            {
                field: 'addtime', title: '添加时间', width: 170, align: 'center', templet: function (d) {
                    return timeTodate(d.addtime)
                }
            },
            {
                field: 'status', title: '状态', width: 100, align: 'center', templet: function (d) {
                    var status = ''
                    if (d.status == 1) status = 'checked';
                    return '<input type="checkbox" lay-filter="status" lay-skin="switch" value="' + d.userid + '" lay-text="是|否" ' + status + '>'
                }
            },
            { field: 'group', title: '权限分组', width: 170, align: 'center', templet: "#userGgroup" },
            // { field: 'group', title: '权限分组', width: 170, align: 'center', templet: "#userGgroups" },
            { title: '操作', width: 170, templet: '#userListBar', align: "center" }
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
        // console.log(data);
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

    //添加/修改用户
    function addUser(edit) {
        var index = layui.layer.open({
            title: "添加用户",
            type: 2,
            content: "/admin.php/System/useradd",
            success: function (layero, index) {
                var body = layui.layer.getChildFrame('body', index);
                if (edit) {
                    body.find(".layui-layer-title").html('修改用户');
                    body.find("input[name=userid]").val(edit.userid);//ID
                    body.find(".signname").val(edit.signname);//登录名
                    body.find(".password").attr('disabled', true);//密码
                    body.find(".confirmpw").attr('disabled', true);//确认密码
                    body.find(".nickname").val(edit.nickname);//昵称
                    // body.find(".thumbImg").attr("src",edit.newsImg);//头像
                    body.find(".userGroup select").val(edit.groupid);//权限组
                    body.find(".addtime").val(timeTodate(edit.addtime));//添加时间
                    body.find(".userStatus input[name='status']").prop("checked", edit.status ? 'checked' : '');//启用
                    // form.render();//将渲染放到add页，可防止switch失效的bug
                }
                setTimeout(function () {
                    layui.layer.tips('点击此处返回用户列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                }, 500)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize", function () {
            layui.layer.full(index);
        })
    }
    $(".addUserBtn").click(function () {
        addUser();
    })

    //批量删除
    $(".delAllBtn").click(function () {
        var checkStatus = table.checkStatus('newsListTable'),
            data = checkStatus.data,
            newsId = [];
        if (data.length > 0) {
            for (var i in data) {
                newsId.push(data[i].newsId);
            }
            layer.confirm('确定删除选中的文章？', { icon: 3, title: '提示信息' }, function (index) {
                // $.get("删除文章接口",{
                //     newsId : newsId  //将需要删除的newsId作为参数传入
                // },function(data){
                tableIns.reload();
                layer.close(index);
                // })
            })
        } else {
            layer.msg("请选择需要删除的文章");
        }
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
            // console.log(data);
            var itemtpl = $('#changePassword').html();
            itemtpl = itemtpl.replace(/\{userid\}/g, data.userid);
            layer.open({
                type: 1,
                title: '修改密码',
                skin: 'layui-layer-rim', //加上边框
                area: ['420px', '240px'], //宽高
                content: itemtpl
            });
        }
    });
    $('.chanPassBut').on('click', chanPassFun);
    function chanPassFun() {
        alert('111');
        /* if ($('.password').val() == $('.confirmpw').val()) {
            $.ajax({
                type: "post",
                url: "/admin.php/Ajax/chanPassFun",
                data: $('#chanPassForm').serialize(),
                success: function (res) {
                    layer.msg('修改成功', {
                        time: 1000
                    });
                },
                error: function () {
                    layer.msg('AJAX错误', {
                        time: 1000
                    });
                    // window.location.reload();
                }
            });

        } else {
            layer.msg('两次密码输入不对', {
                time: 1000
            });
        } */
    }
    /*  $('body').on('click', '.chanPassBut', chanPassFun);
     function chanPassFun() {
         if ($('.password').val() == $('.confirmpw').val()) {
             $.ajax({
                 type: "post",
                 url: "/admin.php/Ajax/chanPassFun",
                 data: $('#chanPassForm').serialize(),
                 success: function (res) {
                     layer.msg('修改成功', {
                         time: 1000
                     });
                 },
                 error: function () {
                     layer.msg('AJAX错误', {
                         time: 1000
                     });
                     // window.location.reload();
                 }
             });
 
         } else {
             layer.msg('两次密码输入不对', {
                 time: 1000
             });
         }
     } */
   /*  $('.chanPassBut').on('click', function () {
        alert(2);
        if ($('.password').val() == $('.confirmpw').val()) {
            $.ajax({
                type: "post",
                url: "/admin.php/Ajax/chanPassFun",
                data: $('#chanPassForm').serialize(),
                success: function (res) {
                    layer.msg('修改成功', {
                        time: 1000
                    });
                },
                error: function () {
                    layer.msg('AJAX错误', {
                        time: 1000
                    });
                    // window.location.reload();
                }
            });

        } else {
            alert(1);
            layer.msg('两次密码输入不对', {
                time: 1000
            });
        }

    }) */

})