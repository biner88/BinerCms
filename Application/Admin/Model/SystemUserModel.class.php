<?php
namespace Admin\Model;
use Common\Model\CommonModel;

class SystemUserModel extends CommonModel {
	protected $_link = array(
		'group'=>array(
			'mapping_type' => self::BELONGS_TO,
			'class_name'   => 'SystemGroup',
			'foreign_key'  => 'group_id',
			'mapping_fields'=>'id,name'
            ),
    );
}
?>