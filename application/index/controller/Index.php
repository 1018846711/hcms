<?php

namespace app\index\controller;


use app\common\controller\CommonController;

class Index extends CommonController
{
	/**
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		$menuModel = new \app\common\model\Menu();
		$menuList = $menuModel->getMenuListAll(0, ["menu_type" => ["in", [1, 2]]]);
		$this->assign("menu_list", $menuList);
		$this->assign("user", $this->userInfo);
		return $this->fetch();
	}
	
	public function test()
	{
		exit("OK");
	}
}