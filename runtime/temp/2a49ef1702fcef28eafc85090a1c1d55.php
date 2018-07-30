<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:45:"E:\hcms\\theme\admin\administrator\index.html";i:1532922886;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form class="layui-form" method="post" action="">
    <input type="hidden" name="page" value="<?php echo input('post.page',1); ?>">
    <div style="margin-top: 15px;margin-bottom: 10px;">
        <div class="layui-form-item" style="display: inline-block">
            <label class="layui-form-label">工号：</label>
            <div class="layui-input-block">
                <input type="text" name="job_number" class="layui-input" value="<?php echo input('post.job_number',''); ?>"
                       style="width: 100px">
            </div>
        </div>
        <div class="layui-form-item" style="display: inline-block">
            <label class="layui-form-label">昵称：</label>
            <div class="layui-input-block">
                <input type="text" name="nickname" class="layui-input" style="width: 100px"
                       value="<?php echo input('post.nickname',''); ?>">
            </div>
        </div>
        <div class="layui-form-item" style="display: inline-block">
            <label class="layui-form-label">手机号码：</label>
            <div class="layui-input-block">
                <input type="text" name="mobile" class="layui-input" style="width: 200px"
                       value="<?php echo input('post.mobile',''); ?>">
            </div>
        </div>
        <div class="layui-form-item" style="width: 200px;display: inline-block">
            <label class="layui-form-label">性别</label>
            <div class="layui-input-block">
                <select name="sex">
                    <option value=""></option>
                    <option <?php if(input('post.sex','') == '1'): ?>selected<?php endif; ?> value="1" >男</option>
                    <option <?php if(input('post.sex','') == '2'): ?>selected<?php endif; ?> value="2">女</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display: inline-block">
            <label class="layui-form-label">部门：</label>
            <div class="layui-input-block">
                <input type="text" name="dep" class="layui-input" style="width: 200px">
            </div>
        </div>
        <button class="layui-btn layui-btn-normal"><i class="layui-icon">&#xe615;</i>搜索</button>
        <button class="layui-btn layui-btn-normal admin-add" style="margin:10px;">
            <i class="layui-icon">&#xe654;</i>添加管理员
        </button>
    </div>
</form>
<table class="layui-table" style="margin-right: 20px;width: 100%">
    <thead>
    <tr>
        <th>工号</th>
        <th>昵称</th>
        <th>手机号码</th>
        <th>性别</th>
        <th>所在部门</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($admin_list as $key=>$value): ?>
    <tr>
        <td><?php echo $value['job_number']; ?></td>
        <td><?php echo $value['nickname']; ?></td>
        <td><?php echo $value['mobile']; ?></td>
        <td><?php echo $value['sex']; ?></td>
        <td>部门，部门</td>
        <td data-id="<?php echo $value['id']; ?>">
            <a class="admin-edit btn-link" href="javascript:;">编辑</a>
            <a class="admin-department btn-link" href="javascript:;">部门管理</a>
            <a class="admin-delete btn-link" href="javascript:;">删除</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div id="page-div" style="float: right;margin-right: 10px"></div>
</body>
<script type="text/javascript" src="http://www.hcms.com/static/common//js/public.js"></script>
<script type="text/javascript">
    layui.use('laypage', function () {
        layui.laypage.render({
            elem: 'page-div',
            curr: "<?php echo input('post.page',1); ?>",
            limit: "<?php echo $page_index; ?>",
            count: "<?php echo $total; ?>",
            jump: function (obj, first) {
                var curr = obj.curr;
                $("input[name='page']").val(curr);
                if (first) return false;
                $("form").submit();
            }
        });
    });

    $(".admin-add").on("click", function () {
        var url = "http://www.hcms.com/admin/administrator/add";
        layer.open({
            title: "管理员添加",
            type: 2,
            content: [url, "no"],
            area: ['350px', '400px']
        });
        return false;
    });

    $(".admin-edit").on("click", function () {
        var id = $(this).parents("td").data("id");
        var url = "http://www.hcms.com/admin/administrator/edit/id/" + id;
        layer.open({
            title: "管理员编辑",
            type: 2,
            content: [url, "no"],
            area: ['350px', '370px']
        });
    });

    $(".admin-delete").on("click", function () {
        var url = "http://www.hcms.com/admin/administrator/delete";
        var id = $(this).parents("td").data("id");
        layer.confirm("确定删除该管理员么？", function () {
            $.ajax({
                type: "POST",
                url: url,
                data: {id: id},
                success: function (result) {
                    if (result.code == 1) {
                        layer.msg("删除成功！", {time: 1000}, function () {
                            window.location.reload();
                        });
                    } else {
                        layer.alert(result.msg);
                    }
                }
            });
        });
    });

    $(".admin-department").on("click", function () {
        var url = "http://www.hcms.com/admin/Administrator/index?type=dep";
        layer.open({
            type: 2,
            content: url,
            area: ['430px', '700px'],
        });
    });
</script>
</html>