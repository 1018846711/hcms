<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:36:"E:\hcms\\theme\index\menu\index.html";i:1528964419;}*/ ?>
<body>
<style type="text/css">
    a {
        font-size: 18px;
    }

    i {
        cursor: pointer;
    }
</style>
<div style="float: left;margin: 10px 10px;width: 400px">
    <ul id="nav" style="overflow:scroll;overflow-x:hidden;height: 90%;">
        <li class="top-menu" data-id="0">
            <i class="layui-icon icon-hide" style="size: 18px">&#xe625;</i>
            <a href="javascript:;">主菜单</a>
            <i class="layui-icon icon-add" style="size: 18px">&#xe654;</i>
            <?php echo $menu_str; ?>
        </li>
    </ul>
</div>
<div style="float: left;margin: 50px 10px;width:640px;">
    <form class="layui-form" action="" id="node-info" style="display: none">
        <input type="hidden" name="type" value="">
        <input type="hidden" name="id" value="0">
        <div class="layui-form-item">
            <label class="layui-form-label">节点名称：</label>
            <div class="layui-input-inline">
                <input type="text" name="menu_name" required lay-verify="required" placeholder="请输入节点名称"
                       autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">菜单名称，最多10个字</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权限名称：</label>
            <div class="layui-input-inline">
                <input type="text" name="menu_node" placeholder="请输入权限名称" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">全小写，以点连接（如：模块名.控制器名.操作名）</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">序号：</label>
            <div class="layui-input-inline">
                <input type="text" name="sort" required lay-verify="required" placeholder="请输入序号" autocomplete="off"
                       class="layui-input" value="0">
            </div>
            <div class="layui-form-mid layui-word-aux">倒序</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权限类型：</label>
            <div class="layui-input-block type-radio">
                <input type="radio" name="menu_type" value="1" title="权限菜单" checked>
                <input type="radio" name="menu_type" value="2" title="公共菜单">
                <input type="radio" name="menu_type" value="3" title="权限操作">
                <input type="radio" name="menu_type" value="4" title="公共操作">
            </div>
            <div class="layui-form-mid type-text">公共操作</div>
        </div>
        <div class="layui-form-item sure-btn">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="sureBtn">立即提交</button>
            </div>
        </div>
    </form>
</div>
</body>
<script type="text/javascript" src="http://www.hcms.com/static/common//js/public.js"></script>
<script type="text/javascript">
    $(function () {
        layui.use('form', function () {
            var form = layui.form;
            form.on('submit(sureBtn)', function (data) {
                var field = data.field;
                var type = field.type;
                if (type == "add") {
                    field.pid = field.id;
                    var url = "http://www.hcms.com/index/menu/insert.html";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: field,
                        success: function (result) {
                            if (result.code == 1) {
                                layer.msg("添加成功！", {time: 1000}, function () {
                                    window.location.reload();
                                });
                            } else if (result.code == 0) {
                                layer.alert(result.msg);
                            }
                        }
                    });
                } else if (type == "edit") {
                    var url = "http://www.hcms.com/index/menu/update.html";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: field,
                        success: function (result) {
                            if (result.code == 1) {
                                layer.msg("修改成功！", {time: 1000});
                            } else if (result.code == 0) {
                                layer.alert(result.msg);
                            }
                        }
                    });
                }
                return false;
            });
        });

        $(document).on("click", ".icon-show", function () {
            $(this).nextAll("ul").show();
            $(this).addClass("icon-hide").removeClass("icon-show").html("&#xe625;");
        });

        $(document).on("click", ".icon-hide", function () {
            $(this).parent("li").find("ul").hide();
            $(this).parent("li").find(".icon-hide").addClass("icon-show").removeClass("icon-hide").html("&#xe623;");
        });

        $(document).on("click", ".icon-delete", function () {
            var id = $(this).parent("li").data("id");
            var url = "http://www.hcms.com/index/menu/delete.html";
            layer.confirm("你确定要删除该节点吗？", function () {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {id: id},
                    success: function (result) {
                        if (result.code == 1) {
                            layer.msg(result.msg, {time: 1000}, function () {
                                window.location.reload();
                            });
                        } else {
                            layer.alert(result.msg);
                        }
                    }
                });
            });
        });

        $(document).on("click", ".icon-add", function () {
            var id = $(this).parent("li").data("id");
            form_update($(this).prevAll(".node-a"), "add", id);
        });

        $(document).on("click", ".icon-edit", function () {
            var id = $(this).parent("li").data("id");
            form_update($(this).prevAll(".node-a"), "edit", id);
        });

        $(document).on("click", ".node-a", function () {
            var id = $(this).parent("li").data("id");
            form_update($(this), "select", id);
        });

        function form_update(a, type, id) {
            if (type == "add") {
                $("input[name='menu_name']").attr("disabled", false).val("");//无效去除并清空值
                $("input[name='menu_node']").attr("disabled", false).val("");//无效去除并清空值
                $("input[name='sort']").attr("disabled", false).val(0);//无效去除并清空值
                $(".type-text").hide();//单选文本隐藏
                $(".type-radio").show();//单选按钮显示
                $(".sure-btn").show();//提交按钮显示
                $("input:radio[name='menu_type'][value='1']").prop("checked", true);//默认单选第一个选中
                layui.form.render();//刷新单选
                $("#node-info").show();//表单显示
            } else if (type == "edit" || type == "select") {
                var waitIndex = layer.load(2);
                $.ajax({
                    data: {type: "get", id: id},
                    type: "POST",
                    success: function (result) {
                        layer.close(waitIndex);
                        if (result.code = 0) {
                            layer.alert(result.msg);
                            return false;
                        } else {
                            var data = result.data;
                            if (type == "edit") {
                                $("input[name='menu_name']").attr("disabled", false).val(data.name);//无效去除并赋值
                                $("input[name='menu_node']").attr("disabled", false).val(data.node);//无效去除并赋值
                                $("input[name='sort']").attr("disabled", false).val(data.sort);//无效去除并赋值
                                $(".type-text").hide();//单选文本隐藏
                                $(".type-radio").show();//单选按钮显示
                                $(".sure-btn").show();//提交按钮显示
                                $("input:radio[name='menu_type'][value=" + data.type + "]").prop("checked", true);
                                layui.form.render();//刷新单选
                                $("#node-info").show();//表单显示
                            } else if (type == "select") {
                                $("input[name='menu_name']").attr("disabled", true).val(data.name);//无效并赋值
                                $("input[name='menu_node']").attr("disabled", true).val(data.node);//无效并赋值
                                $("input[name='sort']").attr("disabled", true).val(data.sort);//无效并赋值
                                $(".type-radio").hide();//单选按钮隐藏
                                $(".sure-btn").hide();//提交按钮隐藏
                                $(".type-text").text(data.type_name).show();
                                $("#node-info").show();//表单显示
                            }
                        }
                    }
                });
            }
            $(".node-a").css("color", "#333");//所有节点默认颜色
            a.css("color", "#5FB878");//选中的变颜色
            $("input[name='type']").val(type);//类型赋值
            $("input[name='id']").val(id);//id赋值
        }
    });
</script>