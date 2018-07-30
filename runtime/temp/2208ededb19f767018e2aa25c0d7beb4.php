<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:48:"E:\hcms\\theme\admin\administrator\password.html";i:1528185936;}*/ ?>
<form class="layui-form" action="" style="padding-right: 20px;margin-top: 35px">
    <div class="layui-form-item">
        <label class="layui-form-label">原密码：</label>
        <div class="layui-input-block person-input">
            <input type="password" name="password-old" required lay-verify="required" placeholder="请输入原密码"
                   autocomplete="new-password" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">新密码：</label>
        <div class="layui-input-block person-input">
            <input type="password" name="password-new" required lay-verify="required" placeholder="请输入新密码"
                   autocomplete="new-password" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn sure-btn" data-type="0" lay-submit lay-filter="formDemo">修改密码</button>
        </div>
    </div>
</form>
<script type="text/javascript" src="http://www.hcms.com/static/common//js/public.js"></script>
<script type="text/javascript">
    layui.use('form', function () {
        var form = layui.form;
        //监听提交
        form.on('submit(formDemo)', function (data) {
            var oldPassword = data['field']['password-old'];
            var newPassword = data['field']['password-new'];
            $.ajax({
                data: {old_password: oldPassword, new_password: newPassword},
                type: 'POST',
                url: "http://www.hcms.com/admin/administrator/password",
                success: function (data) {
                    if (data.code == 1) {
                        layer.msg(data.msg, {time: 1000}, function () {
                            parent.window.location.reload();
                        });
                    } else {
                        layer.alert(data.msg);
                    }
                }
            });
            return false;
        });
    });
</script>