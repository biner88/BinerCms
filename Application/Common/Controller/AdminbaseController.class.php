<?php
namespace Common\Controller;
use Common\Controller\BaseController;

class AdminbaseController extends BaseController{

	public function _initialize() {
		//后台判断标识
		define('IS_ADMIN', true);
		//其他后台分组加载admin配置文件
		if(MODULE_NAME!='Admin'){
			include_once APP_PATH.C('APP_GROUP_PATH').'Admin/Common/function.php';
		}
		$user = session(C('ADMIN_SESSION_KEY'));
		$this->assign("user",$user);
		$this->user = $user;
		 if (!$user) {
		 		$this->redirect('Admin/Public/login');
		 }else{
        $auth = new \Vendor\Rbac;
        $access = $auth->checkUserAuth();
        if (!$access) {
        	$this->error("您没有权限访问！");
        }
				$this->assign("_action_menu",$auth->actionsMenu());
		 }
		$this->opLog();
		$this->assign("navlist",$this->navlist());
	}
	/**
	 * @name 菜单目录
	 */
	function navlist(){
		$list = M('SystemMenu')->cache(true)->where(array('display'=>1,'parent_id'=>0))->select();
		$result = array();
		if ($list) {
			foreach ($list as $key => $value) {
				$show = 0;
				//检查一级
				if( $value['module'] && $value['controller'] && $value['action'] && checkAccess($value['module'].'/'.$value['controller'].'/'.$value['action'])){
					$show ++;
				}
				if($show==0){
					//检查二级
					$lev2 = M('SystemMenu')->cache(true)->where(array('display'=>1,'parent_id'=>$value['id']))->select();
					//检查三级
					if($lev2){
						if( $lev2['module'] && $lev2['controller'] && $lev2['action'] && checkAccess($value2['module'].'/'.$value2['controller'].'/'.$value2['action'])){
							$show ++;
						}
						if($show==0){
							foreach ($lev2 as $key2 => $value2) {
								$lev3 = M('SystemMenu')->cache(true)->where(array('display'=>1,'parent_id'=>$value2['id']))->select();
								if ($lev3 && checkAccess($value2['module'].'/'.$value2['controller'].'/'.$value2['action'])) {
									$show ++;
								}
							}
						}
					}
				}

				if($show>0){
					if( $value['module'] && $value['controller'] && $value['action'] ){
						$url = U($value['module'].'/'.$value['controller'].'/'.$value['action']).($value['query_string']?'&'.$value['query_string']:'');
						$li_name = '';
						if(strtoupper(MODULE_NAME) == strtoupper($value['module']) && strtoupper(CONTROLLER_NAME) == strtoupper($value['controller']) ){
							$a_name = ' class="active"';
						}else{
							$a_name = '';
						}
					}else{
						$url = 'javascript:;';
						$li_name = ' class="showSubNav"';
						$a_name = '';
					}
					$result[] = array(
						'id'      =>$value['id'],
						'name'    =>$value['name'],
						'icon'    =>$value['icon']?$value['icon']:'fa-folder',
						'url'     =>$url,
						'li_name' =>$li_name,
						'a_name'  =>$a_name,
					);
				}
			}
		}
		return $result;
	}
	/**
	 * 获取返回地址
	 */
	function getReturnUrl() {
		if(cookie('?_currentUrl_')){
			$url = cookie('_currentUrl_');
		}else{
			$url = __ROOT__.'/index.php?'.C('VAR_MODULE'). '=' . MODULE_NAME . '&'. C('VAR_CONTROLLER') . '=' . CONTROLLER_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
		}
		return $url;
	}
	/**
	* 状态改变
	*/
	public function statusChange($model='',$field='is_pass',$value=1){
		if(isset($model) && I('id') ){
			$id = I('id');
			$model     = D($model);
			$pk = $model->getPk();
			//$map[$pk] = array('in',$id);
			$ids=array();
			$name      = $model->field($pk.','.$field)->where(array($pk=>array('in',$id)))->select();
			foreach($name as $k=>$v){
				if (I('get.field') && I('get.value') ) {
					$field  = I('get.field');
					$status = I('get.value');
				}else{
					$status  = $v[$field]==1?0:1;
				}
				$ids[$k]['id'] = $v[$pk];
				if($model->where(array($pk=>$v[$pk]))->setField($field,$status)){
					$ids[$k]['id'] = $v[$pk];
					$ids[$k]['status'] = $status;
				}
			}
			if ($ids) {
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
		}
	}
}
