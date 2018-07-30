<?php

namespace app\common\model;


class Department extends CommonModel
{
	/**
	 * 获取全部部门
	 * @param int $pid 上级id，默认为0
	 * @param array $where 条件,默认为空
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getDepartmentListAll($pid = 0, $where = [])
	{
		$topDepartmentList = $this->where(["pid" => $pid])->where($where)->order("sort", "desc")->select();
		$departmentList = [];
		if ($topDepartmentList) {
			foreach ($topDepartmentList as $k_topDepartment => $v_topDepartment) {
				$childDepartment = $this->getDepartmentListAll($v_topDepartment->id, $where);
				$departmentList[] = [
					"id" => $v_topDepartment->id,
					"name" => $v_topDepartment->department_name,
					"child_list" => $childDepartment
				];
			}
		}
		return $departmentList;
	}
	
	/**
	 * 根据id获取单条部门信息
	 * @param int $id 部门id
	 * @param array $where 附加条件
	 * @return array 部门信息
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getDepartmentById($id, $where = [])
	{
		$department = $this->where(["id" => $id])->where($where)->find();
		if ($department) {
			return [
				"id" => $id,
				"sort" => $department->sort,
				"name" => $department->department_name,
			];
		}
		return [];
	}
	
	/**
	 * 根据id删除部门
	 * @param int $id 节点id
	 * @return bool
	 */
	public function deleteDepartmentById($id)
	{
		$idArr = $this->where(["pid" => $id])->column("id");
		if ($idArr) {
			foreach ($idArr as $v_id) {
				$this->deleteDepartmentById($v_id);
			}
		}
		self::destroy(["id" => $id]);
		return true;
	}
	
	/**
	 * 添加节点
	 * @param array $data
	 * @return bool|int|string
	 */
	public function add($data)
	{
		$insertData = [
			"pid" => (int)($data["pid"] ?? 0),
			"sort" => (int)($data["sort"] ?? 0),
			"department_name" => ($data["department_name"] ?? ""),
			"create_time" => time()
		];
		if (!$this->verifyMenu($insertData)) {
			return false;
		}
		$insertId = $this->insertGetId($insertData);
		return $insertId;
	}
	
	public function edit($id, $data)
	{
		$updateData = [
			"sort" => (int)($data["sort"] ?? 0),
			"department_name" => ($data["department_name"] ?? ""),
		];
		if (!$this->verifyMenu($updateData, $id)) {
			return false;
		}
		$updateResult = $this->where("id", $id)->update($updateData);
		if ($updateResult) {
			return true;
		} else {
			$this->error = "数据没有变化！";
			return false;
		}
	}
	
	
	/**
	 * 验证节点
	 * @param $data
	 * @param int $id
	 * @return bool
	 */
	private function verifyMenu($data, $id = 0)
	{
		$pid = $data["pid"] ?? 0;
		$sort = $data["sort"] ?? 0;
		$department_name = $data["department_name"] ?? "";
		if (!$department_name) {
			$this->error = "部门名称不能为空！";
			return false;
		}
		if ($sort < 0) {
			$this->error = "序号不能为负数！";
			return false;
		}
		if ($id > 0) {
			$pid = $this->where(["id" => $id])->value("pid", -1);
			if ($pid < 0) {
				$this->error = "部门不存在！";
				return false;
			}
		}
		return true;
	}
	
	public function getDepartmentStr($depList)
	{
		if (!$depList) return "";
		$str = '<ul style="margin-left: 20px;">';
		$foreHtml = "";
		$bacnHtml = "";
		foreach ($depList as $dep) {
			$childStr = $this->getDepartmentStr($dep["child_list"]);
			$unfoldIcon = $dep["child_list"] ? "<i class='layui-icon icon-hide' style='width: 20px;height: 20px;'>&#xe625;</i>" : "<i style='margin-left: 20px'></i>";
			$str .= "<li data-id='{$dep["id"]}'>
						{$unfoldIcon}
						<label>
						<input class='checkbox-dep' type='checkbox' data-id='{$dep["id"]}'>
						<a href='javascript:;' class='node-a'>{$dep["name"]}</a>
						</label>
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