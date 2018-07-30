<?php

namespace app\common\model;


class Menu extends CommonModel
{
	/**
	 * 获取全部节点
	 * @param int $pid 上级id，默认为0
	 * @param array $where 条件,默认为空
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getMenuListAll($pid = 0, $where = [])
	{
		$topNodeList = $this->where(["pid" => $pid])->where($where)->order("sort", "desc")->select();
		$nodeList = [];
		if ($topNodeList) {
			foreach ($topNodeList as $k_topNode => $v_topNode) {
				$childNode = $this->getMenuListAll($v_topNode->id, $where);
				$url = "";
				if ($v_topNode->menu_node) {
					$nodeArray = explode(".", $v_topNode->menu_node);
					$url = request()->domain() . "/";
					foreach ($nodeArray as $node) {
						$url .= "{$node}/";
					}
				}
				$nodeList[] = [
					"id" => $v_topNode->id,
					"name" => $v_topNode->menu_name,
					"url" => rtrim($url, "/"),
					"type" => $v_topNode->menu_type,
					"type_name" => $v_topNode->type_name,
					"child_list" => $childNode
				];
			}
		}
		return $nodeList;
	}
	
	/**
	 * 根据id获取单条节点信息
	 * @param int $id 节点id
	 * @param array $where 附加条件
	 * @return array 节点信息
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getMenuById($id, $where = [])
	{
		$node = $this->where(["id" => $id])->where($where)->find();
		if ($node) {
			$url = "";
			if ($node->menu_node) {
				$nodeArray = explode(".", $node->menu_node);
				$url = request()->domain() . "/";
				foreach ($nodeArray as $v_node) {
					$url .= "{$v_node}/";
				}
			}
			return [
				"id" => $id,
				"sort" => $node->sort,
				"node" => $node->menu_node,
				"name" => $node->menu_name,
				"url" => rtrim($url, "/"),
				"type" => $node->menu_type,
				"type_name" => $node->type_name,
			];
		}
		return [];
	}
	
	/**
	 * 根据id删除节点
	 * @param int $id 节点id
	 * @return bool
	 */
	public function deleteMenuById($id)
	{
		$idArr = $this->where(["pid" => $id])->column("id");
		if ($idArr) {
			foreach ($idArr as $v_id) {
				$this->deleteMenuById($v_id);
			}
		}
		self::destroy(["id" => $id]);
		return true;
	}
	
	/**
	 * 根据节点id获取上级id，没有找到则返回-1
	 * @param integer $id 节点id
	 * @return mixed
	 */
	public function getPidById($id)
	{
		$pid = $this->where(["id" => $id])->value("pid", -1);
		return $pid;
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
			"menu_node" => ($data["menu_node"] ?? ""),
			"menu_name" => ($data["menu_name"] ?? ""),
			"menu_type" => (int)($data["menu_type"] ?? 0),
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
			"menu_node" => ($data["menu_node"] ?? ""),
			"menu_name" => ($data["menu_name"] ?? ""),
			"menu_type" => (int)($data["menu_type"] ?? 0),
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
		$menu_type = $data["menu_type"] ?? 0;
		$menu_node = $data["menu_node"] ?? "";
		$menu_name = $data["menu_name"] ?? "";
		if (!$menu_name) {
			$this->error = "菜单名称不能为空！";
			return false;
		}
		if ($sort < 0) {
			$this->error = "序号不能为负数！";
			return false;
		}
		if (!in_array($menu_type, [1, 2, 3, 4])) {
			$this->error = "节点类型错误！";
			return false;
		}
		if ($id > 0) {
			$pid = $this->getPidById($id);
			if ($pid < 0) {
				$this->error = "节点不存在！";
				return false;
			}
		}
		if ($pid != 0) {
			if (!$menu_node) {
				$this->error = "非一级菜单的地址不能为空！";
				return false;
			}
			$ppId = $this->getPidById($pid);
			if (in_array($menu_type, [1, 2]) && $ppId > 0) {
				$this->error = "菜单只能存在第二级！";
				return false;
			}
		}
		return true;
	}
	
	
	/**
	 * 节点类型名称获取器
	 * @param $value
	 * @param $data
	 * @return mixed|string
	 */
	public function getTypeNameAttr($value, $data)
	{
		$type = $data["menu_type"] ?? 0;
		return ["未知类型", "权限菜单", "公共菜单", "权限操作", "公共操作"][$type] ?? "未知类型";
	}
}