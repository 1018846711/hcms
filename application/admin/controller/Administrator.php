<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use think\Request;
use think\Session;

class Administrator extends CommonController
{
	//每页的行数
	private $pageIndex = 10;
	
	/**
	 * 管理员详情界面
	 * @return mixed
	 */
	public function details()
	{
		$this->assign("type", "detail");
		$this->assign("user", $this->userInfo);
		return $this->fetch("form");
	}
	
	/**
	 * 管理员修改密码
	 * @return mixed
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function password()
	{
		if (request()->isPost()) {
			$id = $this->userInfo["id"];
			$oldPassword = input("post.old_password", "", "strip_tags");
			$newPassword = input("post.new_password", "", "strip_tags");
			$adminModel = new \app\common\model\Administrator();
			if ($adminModel->verifyPassword($id, $oldPassword) === false) {
				$this->error($adminModel->getError());
			}
			if ($adminModel->updatePassword($id, $newPassword) === false) {
				$this->error($adminModel->getError());
			}
			$this->userInfo = [];
			Session::delete("userId");
			$this->success("修改密码成功！");
		}
		return $this->fetch();
	}
	
	/**
	 * @return mixed
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function login()
	{
		if (request()->isPost()) {
			$captcha = input("post.captcha", "", "strip_tags");
			if (!captcha_check($captcha)) {
				$this->error("验证码错误！");
			}
			$jobNumber = input("post.job_number", "", "strip_tags");
			$password = input("post.password", "", "strip_tags");
			$adminModel = new \app\common\model\Administrator();
			$userId = $adminModel->login($jobNumber, $password);
			if ($userId === false) {
				$this->error("账号或密码错误！");
			}
			$this->success("登陆成功！");
		}
		return $this->fetch();
	}
	
	/**
	 * 管理员退出登录
	 * @return bool
	 */
	public function logout()
	{
		$this->userInfo = [];
		Session::delete("userId");
		return true;
	}
	
	/**
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		$type = input("get.type", "", "strip_tags");
		if ($type) {
			$depModel = new \app\common\model\Department();
			$type = input("post.type", "", "strip_tags");
			$depList = $depModel->getDepartmentListAll();
			$depStr = $depModel->getDepartmentStr($depList);
			$this->assign("department_str", $depStr);
			return $this->fetch("department/index");
		}
		$where = [];
		$jobNumber = trim(input("post.job_number", "", "strip_tags"));
		if ($jobNumber) $where["job_number"] = ["like", "%{$jobNumber}%"];
		$nickname = trim(input("post.nickname", "", "strip_tags"));
		if ($nickname) $where["nickname"] = ["like", "%{$nickname}%"];
		$mobile = trim(input("post.mobile", "", "strip_tags"));
		if ($mobile) $where["mobile"] = ["like", "%{$mobile}%"];
		$sex = trim(input("post.sex", "", "strip_tags"));
		if ($sex) $where["sex"] = $sex;
		$page = input("post.page/d", 1);
		$adminModel = new \app\common\model\Administrator();
		$adminList = $adminModel->getAdminList($where, false, $this->pageIndex, $page);
		$total = $adminModel->getAdminList($where, true);
		$this->assign("page_index", $this->pageIndex);
		$this->assign("total", $total);
		$this->assign("admin_list", $adminList);
		return $this->fetch();
	}
	
	public function delete()
	{
		$adminModel = new \app\common\model\Administrator();
		$id = input("post.id/d", 0, "strip_tags");
		$adminModel->deleteAdminById($id);
		$this->success("删除成功！");
	}
	
	/**
	 * 管理员添加
	 * @return mixed
	 */
	public function add()
	{
		$type = input("post.type", "", "strip_tags");
		if (request()->isPost() && $type == "add") {
			$data = input("param.", []);
			$adminModel = new \app\common\model\Administrator();
			$result = $adminModel->saveAdmin(0, $data);
			if ($result === false) {
				$this->error($adminModel->getError());
			} else {
				$this->success("添加管理员成功！");
			}
		}
		$this->assign("type", "add");
		return $this->fetch("form");
	}
	
	/**
	 * @return mixed
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function edit()
	{
		$type = input("post.type", "", "strip_tags");
		$adminModel = new \app\common\model\Administrator();
		if (request()->isPost() && $type == "edit") {
			$data = input("param.", []);
			$id = $data["id"] ?? 0;
			$result = $adminModel->saveAdmin($id, $data);
			if ($result === false) {
				$this->error($adminModel->getError());
			} else {
				$this->success("管理员修改成功！");
			}
		}
		$id = input("param.id/d", "", "strip_tags");
		if (!$id) {
			$this->error("请选择需要编辑的管理员！");
		}
		$userInfo = $adminModel->getAdminById($id);
		$this->assign("user", $userInfo);
		$this->assign("type", "edit");
		return $this->fetch("form");
	}
	
}