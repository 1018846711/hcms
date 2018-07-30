<?php
/**
 * Created by PhpStorm.
 * User: 穆建华
 * Date: 2018/4/27
 * Time: 15:51
 */

namespace app\common\model;

use think\Session;

class Administrator extends CommonModel
{
	
	/**
	 * 获取登陆管理员信息
	 * @param int $userId 管理员id
	 * @return array|bool 成功返回用户信息，失败返回false
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getUserInfo($userId)
	{
		$result = $this->where(["id" => $userId])->find();
		if (!$result) {
			$this->error = "没有找到用户信息！";
			return false;
		}
		$result = $result->toArray();
		if (session_id() != $result["session_id"]) {
			Session::delete("userId");
			$this->error = "该账号已在异地登陆，强制下线！";
			$result = false;
		}
		return $result;
	}
	
	/**
	 * 修改密码
	 * @param int $userId 管理员id
	 * @param string $password 管理员修改后的密码
	 * @return bool 是否成功
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function updatePassword($userId, $password)
	{
		$userInfo = $this->getUserInfo($userId);
		if (!$userInfo) return false;
		$newPassword = $this->getPassword($password, $userInfo);
		$this->where(["id" => $userId])->setField("password", $newPassword);
		return true;
	}
	
	/**
	 * 验证用户密码是否正确，正确返回id，错误返回false
	 * @param string $identify 用户id或者工号
	 * @param string $password 验证的密码
	 * @return bool
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function verifyPassword($identify, $password)
	{
		$result = $this->where(["job_number|id" => $identify])->field("id,job_number,password")->find();
		if (!$result) {
			$this->error = "没有找到用户信息！";
			return false;
		}
		$adminArray = $result->toArray();
		if ($this->getPassword($password, $adminArray) === $adminArray["password"]) {
			return $result["id"];
		}
		$this->error = "密码不正确！";
		return false;
	}
	
	/**
	 * 用户登陆
	 * @param string $jobNumber 工号
	 * @param string $password 密码
	 * @return array|bool 失败返回false,成功返回用户id
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function login($jobNumber, $password)
	{
		$id = $this->verifyPassword($jobNumber, $password);
		if ($id === false) return false;
		Session::set('userId', $id);
		$this->where(['id' => $id])->update(['session_id' => session_id(), "login_ip" => request()->ip(1)]);
		return true;
	}
	
	/**
	 * 获取根据用户信息获取规则密码（当前规则：md5(密码+工号+id)、如果没有工号或id则返回md5(密码)）
	 * @param string $password 密码
	 * @param array $userInfo 用户信息
	 * @return string 规则编译后的密码
	 */
	private function getPassword($password, $userInfo = [])
	{
		if (isset($userInfo["job_number"]) && isset($userInfo["id"])) {
			return md5($password . $userInfo["job_number"] . $userInfo["id"]);
		}
		return md5($password);
	}
	
	/**
	 * @param array $where 附加条件
	 * @param bool $isCount 是否返回行数，默认不返回行数
	 * @param integer $pageIndex 每页显示多少条数据
	 * @param integer $page 页数
	 * @return array|int|string 管理员列表或者行数
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getAdminList($where = [], $isCount = false, $pageIndex = 0, $page = 1)
	{
		if ($isCount) {
			return $this->where($where)->count();
		}
		$this->where($where);
		if ($pageIndex > 0) {
			$this->page($page, $pageIndex);
		}
		$result = $this->select();
		$adminList = [];
		foreach ($result as $key => $value) {
			$admin = $value->toArray();
			$admin["sex"] = ["未知", "男", "女"][$admin["sex"]] ?? "未知";
			$adminList[] = $admin;
		}
		return $adminList;
	}
	
	/**
	 * @param int $id 管理员id
	 * @return array|bool
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getAdminById($id)
	{
		$result = $this->where(["id" => $id])->find();
		if (!$result) {
			$this->error = "没有找到该管理员！";
			return false;
		}
		$admin = $result->toArray();
		return $admin;
	}
	
	
	public function saveAdmin($id, $data)
	{

		$id = (int)$id;
		$adminArray = [
			"job_number" => $data["job_number"] ?? "",
			"password" => $data["password"] ?? "",
			"nickname" => $data["nickname"] ?? "",
			"sex" => $data["sex"] ?? "",
			"mobile" => $data["mobile"] ?? ""
		];
		if (!$adminArray["job_number"]) {
			$this->error = "工号不能为空！";
			return false;
		}
		if (!$adminArray["nickname"]) {
			$this->error = "昵称不能为空！";
			return false;
		}
		if (!in_array($adminArray["sex"], [1, 2])) {
			$this->error = "性别错误！";
			return false;
		}
		if (!$adminArray["mobile"]) {
			$this->error = "电话号码不能为空！";
			return false;
		}
		if ($id > 0) {
			$resultCount = $this->where(["id" => $id])->count();
			if ($resultCount == 0) {
				$this->error = "管理员不存在！";
				return false;
			}
			unset($adminArray["password"]);
			$result = $this->where(["id" => $id])->update($adminArray);
			if ($result) {
				return true;
			}
            $this->error = "修改无变化！";
            return false;
		}
		if (!$adminArray["password"]) {
			$this->error = "密码不能为空！";
			return false;
		}

		$addId = $this->save($adminArray);
		if (!$addId) {
			return false;
		}
		$addId = $this->id;
		$newPassword = $this->getPassword($adminArray["password"], ["id" => $addId, "job_number" => $adminArray["job_number"]]);
		$this->where("id", $addId)->setField("password", $newPassword);
		return true;
	}
	
	/**
	 * 根据id删除管理员
	 * @param int $id 管理员id
	 * @return bool
	 */
	public function deleteAdminById($id)
	{
		self::destroy(["id" => $id]);
		return true;
	}
}