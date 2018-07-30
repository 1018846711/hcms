<?php

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class CommonModel extends Model
{
	use SoftDelete;
	protected $insert = ['create_time'];
	
	public function setCreateTimeAttr()
	{
		return time();
	}
}