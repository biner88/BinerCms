<?php
namespace Admin\Model;
use Common\Model\CommonModel;

class SystemLogModel extends CommonModel {
	protected $_link = array(
		'user'=>array(
			'mapping_type' => self::BELONGS_TO,
			'class_name'   => 'SystemUser',
			'foreign_key'  => 'uid',
			'mapping_fields'=>'id,name'
            ),
    );
}
?>
