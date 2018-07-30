<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:37:"E:\hcms\\theme\index\index\index.html";i:1528451978;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>这是网页标题</title>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <a class="layui-logo" href="" style="font-size: 24px">后台首页</a>
        <ul class="layui-nav layui-layout-left" style="padding:0;">
            <li style="line-height: 60px;font-size: 16px">菜单1-操作1</li>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;"><?php echo $user['nickname']; ?></a>
                <dl class="layui-nav-child">
                    <dd><a href="javascript:;" class="person-details">基本资料</a></dd>
                    <dd><a href="javascript:;" class="update-password">修改密码</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item log-out"><a href="javascript:;">退出登录</a></li>
        </ul>
    </div>
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" lay-filter="test">
                <?php foreach($menu_list as $menu): ?>
                    <li class="layui-nav-item <?php if(empty($menu['child_list']) || (($menu['child_list'] instanceof \think\Collection || $menu['child_list'] instanceof \think\Paginator ) && $menu['child_list']->isEmpty())): ?>bottom-none<?php endif; ?>">
                        <a href="javascript:;" data-url="<?php echo $menu['url']; ?>"><?php echo $menu['name']; ?></a>
                        <?php if(!(empty($menu['child_list']) || (($menu['child_list'] instanceof \think\Collection || $menu['child_list'] instanceof \think\Paginator ) && $menu['child_list']->isEmpty()))): ?>
                        <dl class="layui-nav-child">
                            <?php foreach($menu['child_list'] as $child): ?>
                                <dd class="bottom-none"><a href="javascript:;" data-url="<?php echo $child['url']; ?>"><?php echo $child['name']; ?></a></dd>
                            <?php endforeach; ?>
                        </dl>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="layui-body">
        内容主体区域
    </div>

    <div class="layui-footer">
        © layui.com - 底部固定区域
    </div>
</div>
<script type="text/javascript" src="http://www.hcms.com/static/common//js/public.js"></script>
<script type="text/javascript">
    $(function () {
        /**
         * 点击左边菜单切换主体内容
         * */
        $(".bottom-none").on("click", function () {
            var url = $(this).find("a").data("url");
            var html = '<iframe src="' + url + '" width="100%" height="99%"></iframe>';
            $(".layui-body").empty();
            $(".layui-body").append(html);
        });

        /**
         * 管理员退出登录
         */
        $(".log-out").on("click", function () {
            var url = "http://www.hcms.com/admin/administrator/logout";
            $.ajax({
                type: 'POST',
                url: url,
                success: function () {
                    layer.msg("退出登录成功！", {time: 1000}, function () {
                        window.location.reload();
                    });
                }
            });
        });

        /**
         * 管理员详细资料弹出
         */
        $(".person-details").on("click", function () {
            var url = "http://www.hcms.com/admin/administrator/details";
            layer.open({
                title: "个人信息",
                type: 2,
                content: [url, "no"],
                area: ['300px', '300px']
            });
        });


        /**
         * 管理员修改密码窗口
         */
        $(".update-password").on("click", function () {
            var url = "http://www.hcms.com/admin/administrator/password";
            layer.open({
                title: "修改密码",
                type: 2,
                content: [url, "no"],
                area: ['300px', '250px']
            });
        });
    });
</script>
</body>
</html>