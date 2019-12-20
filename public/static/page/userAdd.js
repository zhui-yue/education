layui.use(['form', 'layer', 'layedit', 'laydate', 'upload', 'jquery'], function () {
    var form = layui.form
    layer = parent.layer === undefined ? layui.layer : top.layer,
        laypage = layui.laypage,
        upload = layui.upload,
        layedit = layui.layedit,
        laydate = layui.laydate,
        $ = layui.jquery;
    //初始化渲染表单
    form.render();
    //用于同步编辑器内容到textarea
    layedit.sync(editIndex);

    //上传缩略图
    upload.render({
        elem: '.thumbBox',
        url: '../../json/userface.json',
        method: "get",  //此处是为了演示之用，实际使用中请将此删除，默认用post方式提交
        done: function (res, index, upload) {
            var num = parseInt(4 * Math.random());  //生成0-4的随机数，随机显示一个头像信息
            $('.thumbImg').attr('src', res.data[num].src);
            $('.thumbBox').css("background", "#fff");
        }
    });

    //格式化时间
    function filterTime(val) {
        if (val < 10) {
            return "0" + val;
        } else {
            return val;
        }
    }
    //默认头像
    var imgSrc = $('.thumbImg').attr('src');
    if (isNaN(imgSrc)) {
        $('.thumbImg').attr('src', '/public/static/images/face.jpg');
    }
    //添加时间
    var timeStatus = $('.addtime').val() ? false : true;//判断是否给初始值
    laydate.render({
        elem: '.addtime',
        type: 'datetime',
        max: 0,//日期不得超过今天
        // trigger : "click",//自定义弹出控件的事件
        // range :true,//开启左右面板范围选择
        format: 'yyyy-MM-dd HH:mm',//自定义格式
        isInitValue: timeStatus,
        value: new Date(),
        done: function (value, date, endDate) {
            console.log(value);
        }
    });
    //定时发布
    /* form.on("radio(release)",function(data){
        if(data.elem.title == "定时发布"){
            $(".releaseDate").removeClass("layui-hide");
            $(".releaseDate #release").attr("lay-verify","required");
        }else{
            $(".releaseDate").addClass("layui-hide");
            $(".releaseDate #release").removeAttr("lay-verify");
            submitTime = time.getFullYear()+'-'+(time.getMonth()+1)+'-'+time.getDate()+' '+time.getHours()+':'+time.getMinutes()+':'+time.getSeconds();
        }
    }); */
    //表单验证
    form.verify({
        password: function (val) {
            if($('input[name=userid]').val() == ''){
                if(val.trim() == '') return "密码不能为空";
            }
        },
        confirmpw: function (val) {
            if (val.trim() != $('.password').val().trim()) return "两次输入密码必须一致！";
        }
    })
    //提交数据
    form.on("submit(addUser)", function (data) {
        //截取文章内容中的一部分文字放入文章摘要
        // var abstract = layedit.getText(editIndex).substring(0,50);
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候', { icon: 16, time: false, shade: 0.8 });
        // 实际使用时的提交信息
        console.log(data);

        $.post("/admin.php/System/useradd", {
            userid: $("input[name=userid]").val().trim(),  //登录名
            signname: $(".signname").val().trim(),  //登录名
            password: $(".password").val().trim(),  //密码
            // content : layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0],  //个人介绍
            // newsImg : $(".thumbImg").attr("src"),  //头像
            nickname: $(".nickname").val().trim(),    //昵称
            groupid: $('.userGroup select').val(),    //权限组
            addtime: $(".addtime").val(),    //添加时间
            status: data.field.status == "on" ? "1" : "0",    //是否启用
        }, function (res) {
            top.layer.close(index);
            if (res.code) {
                layer.closeAll("iframe");
                //刷新父页面
                parent.location.reload();
            }
            top.layer.msg(res.msg);
        })
        /* setTimeout(function(){
            top.layer.close(index);
            top.layer.msg("文章添加成功！");
            // layer.closeAll("iframe");
            // //刷新父页面
            // parent.location.reload();
        },500); */
        return false;
    })

    //预览
    form.on("submit(look)", function () {
        layer.alert("此功能需要前台展示，实际开发中传入对应的必要参数进行文章内容页面访问");
        return false;
    })

    //创建一个编辑器
    var editIndex = layedit.build('userContent', {
        height: 'full-125',
        uploadImage: {
            url: "../../json/newsImg.json"
        }
    });

})