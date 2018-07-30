<?php

namespace app\index\controller;


use app\common\controller\CommonController;

class Menu extends CommonController
{
	/**
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		$type = input("post.type", "", "strip_tags");
		$nodeModel = new \app\common\model\Menu();
		if (request()->isPost() && $type == "get") {
			$id = input("post.id/d", 0, "strip_tags");
			if ($id <= 0) $this->error("请求参数错误！");
			$node = $nodeModel->getMenuById($id);
			if (!$node) $this->error("没有找到该节点！");
			$this->success("获取成功！", null, $node);
		}
		$nodeList = $nodeModel->getMenuListAll();
		$menuStr = $this->getMenuStr($nodeList);
		$this->assign("menu_str", $menuStr);
		return $this->fetch();
	}
	
	public function delete()
	{
		$id = input("post.id/d", 0, "strip_tags");
		if (!$id) $this->error("请选择需要删除的节点！");
		$menuModel = new \app\common\model\Menu();
		if ($menuModel->deleteMenuById($id)) {
			$this->success("删除成功！");
		}
		$this->error("删除失败！" . $menuModel->getError());
	}
	
	public function insert()
	{
		$pid = input("post.pid/d", 0, "strip_tags");
		$sort = input("post.sort/d", 0, "strip_tags");
		$menu_node = input("post.menu_node", "", "strip_tags");
		$menu_name = input("post.menu_name", "", "strip_tags");
		$menu_type = input("post.menu_type/d", 0, "strip_tags");
		$nodeModel = new \app\common\model\Menu();
		$result = $nodeModel->add([
			"pid" => $pid,
			"sort" => $sort,
			"menu_node" => $menu_node,
			"menu_name" => $menu_name,
			"menu_type" => $menu_type
		]);
		if ($result) {
			$this->success("添加成功！");
		} else {
			$this->error($nodeModel->getError());
		}
	}
	
	public function update()
	{
		$id = input("post.id/d", 0, "strip_tags");
		$sort = input("post.sort/d", 0, "strip_tags");
		$menu_node = input("post.menu_node", "", "strip_tags");
		$menu_name = input("post.menu_name", "", "strip_tags");
		$menu_type = input("post.menu_type/d", 0, "strip_tags");
		$nodeModel = new \app\common\model\Menu();
		$result = $nodeModel->edit($id, [
			"sort" => $sort,
			"menu_node" => $menu_node,
			"menu_name" => $menu_name,
			"menu_type" => $menu_type
		]);
		if ($result) {
			$this->success("修改成功！");
		} else {
			$this->error($nodeModel->getError());
		}
	}
	
	private function getMenuStr($menuList)
	{
		if (!$menuList) return "";
		$str = '<ul style="margin-left: 20px;">';
		foreach ($menuList as $menu) {
			$childStr = $this->getMenuStr($menu["child_list"]);
			$unfoldIcon = $menu["child_list"] ? "<i class='layui-icon icon-hide' style='width: 20px;height: 20px;'>&#xe625;</i>" : "<i style='margin-left: 20px'></i>";
			$explainColor = in_array($menu["type"], [1, 2]) ? "normal" : "primary";
			$str .= "<li data-id='{$menu["id"]}'>
						{$unfoldIcon}
						<a href='javascript:;' class='node-a'>{$menu["name"]}</a>
						<span class='layui-btn layui-btn-xs layui-btn-radius layui-btn-{$explainColor}'>{$menu["type_name"]}</span>
						<i class='layui-icon icon-delete' style='size: 18px'>&#xe640;</i>
						<i class='layui-icon icon-edit' style='size: 18px'>&#xe642;</i>
						<i class='layui-icon icon-add' style='size: 18px'>&#xe654;</i>
						{$childStr}
					</li>";
		}
		$str .= '</ul>';
		return $str;
	}
}