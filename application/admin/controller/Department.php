<?php
/**
 * Created by PhpStorm.
 * User: 穆建华
 * Date: 2018/5/25
 * Time: 9:43
 */

namespace app\admin\controller;


use app\common\controller\CommonController;

class Department extends CommonController
{
	/**
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		$depModel = new \app\common\model\Department();
		$type = input("post.type", "", "strip_tags");
		if (request()->isPost() && $type == "get") {
			$id = input("post.id/d", 0, "strip_tags");
			if ($id <= 0) $this->error("请求参数错误！");
			$department = $depModel->getDepartmentById($id);
			if (!$department) $this->error("没有找到该部门！");
			$this->success("获取成功！", null, $department);
		}
		$depList = $depModel->getDepartmentListAll();
		$depStr = $depModel->getDepartmentStr($depList);
		$this->assign("department_str", $depStr);
		return $this->fetch();
	}
	
	public function delete()
	{
		$id = input("post.id/d", 0, "strip_tags");
		if (!$id) $this->error("请选择需要删除的节点！");
		$depModel = new \app\common\model\Department();
		if ($depModel->deleteDepartmentById($id)) {
			$this->success("删除成功！");
		}
		$this->error("删除失败！" . $depModel->getError());
	}
	
	public function insert()
	{
		$id = input("post.id/d", 0, "strip_tags");
		$sort = input("post.sort/d", 0, "strip_tags");
		$department_name = input("post.department_name", "", "strip_tags");
		$depModel = new \app\common\model\Department();
		$result = $depModel->add([
			"pid" => $id,
			"sort" => $sort,
			"department_name" => $department_name,
		]);
		if ($result) {
			$this->success("添加成功！");
		} else {
			$this->error($depModel->getError());
		}
	}
	
	public function update()
	{
		$id = input("post.id/d", 0, "strip_tags");
		$sort = input("post.sort/d", 0, "strip_tags");
		$department_name = input("post.department_name", "", "strip_tags");
		$depModel = new \app\common\model\Department();
		$result = $depModel->edit($id, [
			"sort" => $sort,
			"department_name" => $department_name,
		]);
		if ($result) {
			$this->success("修改成功！");
		} else {
			$this->error($depModel->getError());
		}
	}
}