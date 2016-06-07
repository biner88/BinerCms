<?php
namespace Common\Controller;
use Common\Controller\BaseController;

class AdminbaseController extends BaseController{

	public function _initialize() {
		//后台判断标识
		define('IS_ADMIN', 1);
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
					$result[] = array(
						'id'      =>$value['id'],
						'name'    =>$value['name'],
						'icon'    =>$value['icon']?$value['icon']:'fa-folder',
						'url'     =>$value['module']?U($value['module'].'/'.$value['controller'].'/'.$value['action']).($value['query_string']?'&'.$value['query_string']:''):'javascript:;',
						'li_name' =>$value['module']?'':' class="showSubNav"',
						'a_name'  =>strtoupper(MODULE_NAME) == strtoupper($value['module'])?' class="active"':'',
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
	/**
	* 保存标签
	*/
	function saveTag($source_tags, $id, $controller = CONTROLLER_NAME,$module=MODULE_NAME) {
		if (!empty($source_tags) && !empty($id)) {
			$Tag                   = D(ucfirst($module)."Tag");
			$TagLog                = D(ucfirst($module)."TagLog");
			// 记录已经存在的标签
			$exists_tags           = $TagLog->where(array('module'=>$module.'_'.$controller,'item_id'=>$id))->getField("id,tag_id");
			if ($exists_tags) {
				$TagLog->where(array('module'=>$module.'_'.$controller,'item_id'=>$id))->delete();
			}
			//标签分析
			if (stripos($source_tags, ',')) {
				$tags                  = explode(',', $source_tags);
			}elseif (stripos($source_tags, ' ')) {
				$tags                  = explode(' ', $source_tags);
			}else{
				$tags = array($source_tags);
			}

			foreach ($tags as $key => $val) {
				$val = trim($val);
				if (!empty($val)) {
					$tag = $Tag->where(array('module'=>$module.'_'.$controller,'name'=>$val))->find();
					if ($tag) {
						// 标签已经存在
						if (!in_array($tag['id'], $exists_tags)) {
							$Tag->where(array('id'=>$tag['id']))->setInc('count');
						}
					} else {
						// 不存在则添加
						$tag = array(
							'name'   => $val,
							'count'  => 1,
							'module' => $module.'_'.$controller,
						);
						$tag['id']     = $Tag->add($tag);
					}
					// 记录tag关联信息
					$t = array(
						'module'      => $module.'_'.$controller,
						'item_id'     => $id,
						'create_time' => time(),
						'tag_id'      => $tag['id'],
					);
					$TagLog->add($t);
				}
			}
		}
	}
}
