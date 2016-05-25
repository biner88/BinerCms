<?php
namespace Admin\Model;
use Common\Model\CommonModel;

class SystemGroupModel extends CommonModel {
	//根据id获取角色名称
	function getGroupNameById($group_id){
		return $this->cache(true)->getFieldById($group_id,'name');
	}
}
?>