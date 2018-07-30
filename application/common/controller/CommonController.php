<?php

namespace app\common\controller;

use app\common\model\Administrator;
use think\Controller;
use think\Request;
use think\Session;

class CommonController extends Controller
{
	protected $userInfo = [];
	
	
	/**
	 * CommonController constructor.
	 * @param Request|null $request
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$module = request()->module();
		$controller = request()->controller();
		$action = request()->action();
		$userId = Session::get("userId");
		$adminModel = new Administrator();
		$userInfo = $adminModel->getUserInfo($userId);
		if ($module == "admin" && $controller == "Administrator" && $action == "login") {
			if ($userInfo === false) return;
			$this->redirect("index/index/index");
		}
		if ($userInfo === false) {
			if (request()->isAjax()) {
				$this->error($adminModel->getError());
			}
			$this->redirect("/admin/administrator/login");
		}
		$this->userInfo = $userInfo;
	}
}