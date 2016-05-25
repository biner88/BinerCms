<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
/**
 * @name 系统日志
 * @description 系统日志
 * @author biner
 * @auth yes
 * @icons file-text-o
 * @date 2015-08-18
 */
class LogController extends AdminbaseController {
    protected $obj;
    public function _initialize() {
        parent::_initialize();
        $this->obj = D('SystemLog');
    }
	/**
	 * @name 列表
     * @showOnNav yes
	 */
	public function index() {
    $tdTitle = array(
			'id'             =>array('title'=>'编号','width'=>80),
			'user_id'        =>array('title'=>'操作者','width'=>120,'sort'=>false),
			'title'          =>array('title'=>'事件','sort'=>false),
			'm'              =>array('title'=>'模块','sort'=>false),
			'c'              =>array('title'=>'控制器','sort'=>false),
			'a'              =>array('title'=>'方法','sort'=>false),
      'created_time' =>array('title'=>'时间'),
      'ip'          =>array('title'=>'IP','sort'=>false),
      'admin'       =>array('title'=>'操作','width'=>50,'sort'=>false),
		);
		$this->assign("tdTitle",$tdTitle);
		$map = array();
		$map['relation'] = true;
		// if ( session('login_user.userid') != 1 ) {
		// 	$map['where']['uid'] = array('neq',1);
		// }
		$formdata = I();
		if ($formdata) {
			if (!empty($formdata['username'])) {
				$uid = D('SystemUser')->where(array('name'=>$formdata['username']))->getField('id');
				if ($uid) {
					$map['where']['uid'] = $uid;
				}
			}
      if(!empty($formdata['s_time']) && !empty($formdata['e_time'])){
				$map['where']['created_time'] = array('between',array(strtotime($formdata['s_time'].' 00:00:00'),strtotime($formdata['e_time'].' 23:59:59')));
			}
			// if (!empty($formdata['created_time'])) {
			// 	$map['where']['_string']= "FROM_UNIXTIME(`created_time`,'%Y-%m-%d')  ='".$formdata['created_time']."'";
			// 	$formdata['created_time'] = strtotime($formdata['created_time']);
			// }
		}else{
			$formdata['created_time'] = time();
		}
		$data = $this->pageList($this->obj,$map);
		$this->assign('data', $data);
		$this->assign('formdata',$formdata);
		$this->display();
	}
  /**
	 * @name 详情
	 */
	public function item() {
		$id = I('get.id');
		$vo = $this->obj->relation(true)->where(array('id' => $id,))->find();
		//dump(json_decode($vo['remark'],true));
		$this->assign('vo', $vo);
		$this->display();
	}
}
?>
