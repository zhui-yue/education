layui.use(['form', 'laydate', 'table', 'laytpl', 'upload', 'jquery'], function () {
    var form = layui.form,
        $ = layui.jquery,
        laydate = layui.laydate,
        upload = layui.upload,
        table = layui.table;

    //用户列表
    var tableIns = table.render({
        elem: '#infoList',
        method: 'POST',
        url: '/admin.php/Activity/project/', //数据接口
        cellMinWidth: 50,
        id: "infoList",
        page: true,
        limit: 10,
        limits: [10, 20, 30, 40, 50],
        cols: [[
            { type: "checkbox", width: 50 },
            { field: 'id', title: 'ID', width: 80, align: "center", sort: true },
            { field: 'name', title: '项目名称', align: "center", edit: 'text' },
            {
                field: 'option', title: '选项', align: 'center', width: 180, templet: function (d) {
                    var arr = d.option.split(','), but = str = '';
                    for (var i in arr) {
                        // console.log(arr[i]);
                        switch (arr[i]) {
                            case '1':
                                str = '签到';
                                break;
                            case '2':
                                str = '打分';
                                break;
                            default:
                                str = '未知';
                                break;
                        }
                        but += '<a class="layui-btn layui-btn-xs">' + str + '</a>';
                        // console.log(but);
                    }
                    return but;
                }
            },
            { field: 'sort', title: '排序', width: 80, align: "center", sort: true, edit: 'text' },
            { title: '操作', width: 90, templet: '#listBar', align: "center" }
        ]],
    });
    upload.render({
        elem: '.thumbBox',
        accept: 'images', //视频,
        auto: false,
        choose: function (obj) {
            var item = $(this.item);
            //读取本地文件
            obj.preview(function (index, file, result) {
                var img = $([
                    '<img id="upload-' + index + '" class="layui-upload-img thumbImg" src="' + result + '">' +
                    '<input type="hidden" name="icon[]" value="' + result + '">'
                ].join(''));
                item.html(img);
                $(".boxTwwo").css('display', 'block');
            });
        }
    });
    //日期时间范围选择
    laydate.render({
        elem: '#addTime',
        type: 'time',
        trigger: 'click',
        format: 'HH:mm',
        ready: formatminutes,
        value: '00:00' //必须遵循format参数设定的格式
    });
    //监听排序事件 
    table.on('sort(infoList)', function (obj) { //注：sort 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
        // console.log(obj.field); //当前排序的字段名
        // console.log(obj.type); //当前排序类型：desc（降序）、asc（升序）、null（空对象，默认排序）
        // console.log(this); //当前排序的 th 对象
        //尽管我们的 table 自带排序功能，但并没有请求服务端。
        //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
        table.reload('infoList', {
            initSort: obj, //记录初始排序，如果不设的话，将无法标记表头的排序状态。
            where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                field: obj.field, //排序字段
                order: obj.type, //排序方式
                key: $(".searchVal").val(),  //搜索的关键字
                uid: '',//搜索的关键字
                Prize: '', //搜索的关键字
            }
        });
        // layer.msg('服务端排序。order by ' + obj.field + ' ' + obj.type);
    });
    //单元编辑
    table.on('edit(infoList)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
        // console.log(obj.value); //得到修改后的值
        // console.log(obj.field); //当前编辑的字段名
        // console.log(obj.data); //所在行的所有相关数据          
        var index = layer.msg('修改中，请稍候', { icon: 16, time: false, shade: 0.8 });
        $.ajax({
            type: "post",
            url: "/admin.php/Activity/projectEdit",
            dataType: 'json',
            data: {
                id: obj.data.id,
                field: obj.field,
                value: obj.value
            },
            success: function (res) {
                layer.close(index);
                if (res.code) {
                    table.reload('infoList');//重载表格
                    $(".addInput").val("");
                }
                layer.msg(res.msg);
            },
            error: function () {
                layer.close(index);
                layer.msg('AJAX错误', {
                    time: 1000
                });
                window.location.reload();
            }
        });
    });
    //监听排序事件 
    table.on('tool(infoList)', function (obj) { //注：sort 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
        // console.log(obj.value); //得到修改后的值
        // console.log(obj.field); //当前编辑的字段名
        // console.log(obj.data); //所在行的所有相关数据  
        //尽管我们的 table 自带排序功能，但并没有请求服务端。
        //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
        var layEvent = obj.event,
            data = obj.data;
        if (layEvent === 'edit') { //编辑
            var tipsTpl = $("#tipsTpl").html();
            var tipsItem = layer.confirm(tipsTpl, {
                title: "提示信息",
                btn: ['确认', '取消'] //按钮
            }, function () {
                layer.close(tipsItem)
                addData(data);
            }, function () {
                layer.close(tipsItem)
            });
        }
    });
    //监听工具条 
    table.on('tool(infoList)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）

        if (layEvent === 'detail') { //查看
            //do somehing
        } else if (layEvent === 'del') { //删除
            var index = layer.confirm('真的删除行么', function (index) {
                layer.close(index);
                var index = layer.msg('删除中，请稍候', { icon: 16, time: false, shade: 0.8 });
                $.ajax({
                    type: "post",
                    url: "/admin.php/Activity/projectDel",
                    dataType: 'json',
                    data: obj.data,
                    success: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('infoList');//重载表格
                            // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            // layer.close(index);
                        }
                        layer.msg(res.msg);
                    },
                    error: function () {
                        layer.close(index);
                        layer.msg('AJAX错误', {
                            time: 1000
                        });
                        window.location.reload();
                    }
                });
                //向服务端发送删除指令
            });
        } else if (layEvent === 'edit') { //编辑
            //do something
            //同步更新缓存对应的值
            obj.update({
                username: '123'
                , title: 'xxx'
            });
        } else if (layEvent === 'LAYTABLE_TIPS') {
            layer.alert('Hi，头部工具栏扩展的右侧图标。');
        }
    });
    //添加项目
    form.on('submit(addproject)', function (data) {
        var len = $("input:checked").length;
        if (len < 1) {
            layer.msg('选项至少选择一个！', { icon: 2 });
            return false;
        }
        var index = layer.msg('添加中，请稍候', { icon: 16, time: false, shade: 0.8 });
        // console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
        $.ajax({
            type: "post",
            url: "/admin.php/Activity/projectAdd",
            dataType: 'json',
            data: data.field,
            success: function (res) {
                layer.close(index);
                if (res.code) {
                    table.reload('infoList');//重载表格
                    $(".addInput").val("");
                    // $(".addInputTime").val(0);
                    laydate.render({
                        elem: '#addTime',
                        value: '00:00' //必须遵循format参数设定的格式
                    });
                }
                layer.msg(res.msg);
            },
            error: function () {
                layer.close(index);
                layer.msg('AJAX错误', {
                    time: 1000
                });
                // window.location.reload();
            }
        });
    })
    //搜索【此功能需要后台配合，所以暂时没有动态效果演示】
    $(".search_btn").on("click", function () {
        if ($(".searchVal").val() != '') {
            tableReload(1, $(".searchVal").val());
        } else {
            layer.msg("请输入搜索的内容");
        }
    });
    //---------------------------------------------------------------------------------------------

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
        return filterTime(Y) + '/' + filterTime(m) + '/' + filterTime(d) + ' ' + filterTime(H) + ':' + filterTime(i) + ':' + filterTime(s);
    }
    //将秒数转换成时间格式
    function getLocalTime(time) {
        let str, D = Math.floor(time / 86400), H = Math.floor((time - D * 86400) / 3600), M = Math.floor((time - D * 86400 - H * 3600) / 60);
        switch (D) {
            case 0:
                str = '第一天 ';
                break;
            case 1:
                str = '第二天 ';
                break;
            case 2:
                str = '第三天 ';
                break;
            case 3:
                str = '第四天 ';
                break;
            case 4:
                str = '第五天 ';
                break;
            case 5:
                str = '第六天 ';
                break;
            case 6:
                str = '第七天 ';
                break;
            default:
                str = '第一天 ';
                break;
        }
        return str + ' ' + filterTime(H) + ':' + filterTime(M);
    }
    function formatminutes(date) {
        var aa = $(".laydate-time-list li ol")[1];
        var showtime = $($(".laydate-time-list li ol")[1]).find("li");
        for (var i = 0; i < showtime.length; i++) {
            var t00 = showtime[i].innerText;
            if (t00 != "00" && t00 != "10" && t00 != "20" && t00 != "30" && t00 != "40" && t00 != "50") {
                showtime[i].remove()
            }
        }
        $($(".laydate-time-list li ol")[2]).find("li").remove();  //清空秒
    }
})
