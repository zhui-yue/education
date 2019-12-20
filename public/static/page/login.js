layui.use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer
        $ = layui.jquery;

    $(".loginBody .seraph").click(function(){
        layer.msg("这只是做个样式，至于功能，你见过哪个后台能这样登录的？还是老老实实的找管理员去注册吧",{
            time:5000
        });
    })

    //登录按钮
    form.on("submit(login)",function(data){
        $(this).text("登录中...").attr("disabled","disabled").addClass("layui-disabled");
        var obj = $(this);
        // setTimeout(function(){
        	$.ajax({
				type: "post",
				url: "/admin.php/Base/Login",
				dataType: 'json',
				data: {
					'userName': $("#userName").val(),
					'passWord': $("#passWord").val(),
					'code': $("#code").val(),
				},
				success: function(data) {
//					alert(data);
					if(data.code == 0) {
						layer.msg(data.msg,{
				            time:1000
				        });
				        $("#code").val("");
				        $("#codeImg").attr('src','/admin.php/base/captcha.html?rand='+Math.random());
				        obj.text("登录").attr("disabled",false).removeClass("layui-disabled");
					}
					if(data.code == 1) {
						layer.msg(data.msg,{
				            time:1000
				        });
						window.location.href = data.url;
					}
					obj.text("登录").attr("disabled",false).removeClass("layui-disabled");//改变按钮状态
				},
				error: function() {
					obj.text("登录").attr("disabled",false).removeClass("layui-disabled");//改变按钮状态
					alert('AJAX错误');
				}
			});
            //window.location.href = "/layuicms2.0";
        // },1000);
        return false;
    })

    //表单输入效果
    $(".loginBody .input-item").click(function(e){
        e.stopPropagation();
        $(this).addClass("layui-input-focus").find(".layui-input").focus();
    })
    $(".loginBody .layui-form-item .layui-input").focus(function(){
        $(this).parent().addClass("layui-input-focus");
    })
    $(".loginBody .layui-form-item .layui-input").blur(function(){
        $(this).parent().removeClass("layui-input-focus");
        if($(this).val() != ''){
            $(this).parent().addClass("layui-input-active");
        }else{
            $(this).parent().removeClass("layui-input-active");
        }
    })
    //showNotice()//系统公告函数
})
