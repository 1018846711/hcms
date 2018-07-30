<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:44:"E:\hcms\\theme\admin\administrator\form.html";i:1532922557;}*/ ?>
<form class="layui-form" action="" style="width: 300px;height: 300px;padding-right: 100px;margin-top: 20px">
    <input type="hidden" name="type" value="<?php echo $type; ?>">
    <input type="hidden" name="id" value="<?php echo isset($user['id'])?$user['id']:0; ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">工号：</label>
        <?php if($type == 'detail'): ?>
        <div class="layui-form-mid"><?php echo $user['job_number']; ?></div>
        <?php endif; if(in_array(($type), explode(',',"add,edit"))): ?>
        <div class="layui-input-block person-input">
            <input type="text" name="job_number" required lay-verify="required" placeholder="请输入工号"
                   value="<?php echo isset($user['job_number'])?$user['job_number']:''; ?>" autocomplete="off" class="layui-input">
        </div>
        <?php endif; ?>
    </div>
    <?php if(in_array(($type), explode(',',"add"))): ?>
    <div class="layui-form-item">
        <label class="layui-form-label">密码：</label>
        <div class="layui-input-block person-input">
            <input type="password" name="password" required lay-verify="required" placeholder="请输入密码"
                   value="<?php echo isset($user['password'])?$user['password']:''; ?>" autocomplete="off" class="layui-input">
        </div>
    </div>
    <?php endif; ?>
    <div class="layui-form-item">
        <label class="layui-form-label">昵称：</label>
        <?php if($type == 'detail'): ?>
        <div class="layui-form-mid person-text"><?php echo $user['nickname']; ?></div>
        <?php endif; if(in_array(($type), explode(',',"add,edit"))): ?>
        <div class="layui-input-block person-input">
            <input type="text" name="nickname" required lay-verify="required" placeholder="请输入昵称"
                   value="<?php echo isset($user['nickname'])?$user['nickname']:''; ?>" autocomplete="off" class="layui-input">
        </div>
        <?php endif; ?>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机：</label>
        <?php if($type == 'detail'): ?>
        <div class="layui-form-mid person-text"><?php echo $user['mobile']; ?></div>
        <?php endif; if(in_array(($type), explode(',',"add,edit"))): ?>
        <div class="layui-input-block person-input">
            <input type="text" name="mobile" required lay-verify="required" placeholder="请输入手机号"
                   value="<?php echo isset($user['mobile'])?$user['mobile']:''; ?>" autocomplete="off" class="layui-input">
        </div>
        <?php endif; ?>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">性别：</label>
        <?php if($type == 'detail'): ?>
        <div class="layui-form-mid person-text"><?php if($user['sex'] == '1'): ?>男<?php else: ?>女<?php endif; ?></div>
        <?php endif; if(in_array(($type), explode(',',"add,edit"))): $sex = $user["sex"]??1; ?>
        <div class="layui-input-block person-input">
            <input type="radio" value="1" name="sex" title="男" <?php if($sex == '1'): ?>checked<?php endif; ?>>
            <input type="radio" value="2" name="sex" title="女" <?php if($sex == '2'): ?>checked<?php endif; ?>>
        </div>
        <?php endif; ?>
    </div>
    <?php if(in_array(($type), explode(',',"add,edit"))): ?>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn sure-btn" data-type="0" lay-submit lay-filter="sure">确定提交</button>
        </div>
    </div>
    <?php endif; ?>
</form>
<script type="text/javascript" src="http://www.hcms.com/static/common//js/public.js"></script>
<script type="text/javascript">
    layui.use('form', function () {
        var form = layui.form;
        //监听提交
        form.on('submit(sure)', function (data) {
            $.ajax({
                type: "POST",
                data: data.field,
                success: function (result) {
                    if (result.code == 1) {
                        layer.msg(result.msg, {time: 1000}, function () {
                            parent.window.location.reload();
                        });
                    } else {
                        layer.alert(result.msg);
                    }
                }
            });
            return false;
        });
    });
</script>