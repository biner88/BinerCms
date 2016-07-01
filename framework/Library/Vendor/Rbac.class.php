<?php
namespace Vendor;
//use ReflectionMethod;
//use ReflectionClass;
/**
 * @name 利用反射获取权限树
 * @date 2016-04-22
 * @author [biner] <[i@biner.me]>
 */
class Rbac {

	public function __construct() {
		// 默认认证类型 1 登录认证 2 实时认证
		 $this->model = C('USER_AUTH_TYPE');
		 //当前用户id
		 $this->uid = (int) session(C('ADMIN_SESSION_KEY').'.id');
	}
	/**
	 * 获取用户顶部导航
	 * @return array 权限方法列表数组M/C/A
	 */
	 public function actionsMenu(){
	 		$rf = new \ReflectionClass(MODULE_NAME.'\\Controller\\'.CONTROLLER_NAME.'Controller');
	 		$controller_name = preg_match('/@name\s+(\w+)/u', $rf->getDocComment(), $catch)
	 				? $catch[1]
	 				: strtoupper(CONTROLLER_NAME);
	 		$controller_icons = preg_match('/@icons\s+(\w+)/u', $rf->getDocComment(), $catch)
	 				? $catch[1]
	 				: 'list';
	 		foreach ($rf->getMethods() as $key=>$method) {
	 			//和权限进行比对
	 			$cma = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.$method->name;
	 			if(checkAccess($cma)==true){
	 				$doc = $method->getDocComment();
	 				//是否在导航显示
	 				$showOnNav = preg_match('/@showOnNav\s+(\w+)/u', $doc, $catch)
	 					 ? ($catch[1]=='yes'?true:false )
	 					 : false;
	 				 //获取名称
	 					if (!preg_match('/@name\s+(\w+)/u', $doc, $catch)) continue;
	 					$name = $catch[1];
	 					if ($showOnNav==true) {
	 						$show_action[] = array(
	 						 'name'=>$name,
	 						 'cma'=>U($cma),
	 						 'checked'=>$method->name==ACTION_NAME?1:0,
	 						);
	 					}else{
	 						if ($method->name==ACTION_NAME) {
	 							$hide_action = array(
	 							 'name'=>$name,
	 							 'cma'=>$_SERVER['REQUEST_URI'],
	 							);
	 						}
	 					}
	 			}
	 		}
	 		$result = array(
	 			'controller_name'  =>$controller_name,
	 			'controller_icons' =>$controller_icons,
	 			'show_action'      =>$show_action,
	 			'hide_action'      =>$hide_action,
	 		);

	 	return $result;
	 }
	/**
	 * 获取用户被授权的方法列表
	 * @return array 权限方法列表数组M/C/A
	 */
	 public function accessAllowList($uid=0){
		$group_access     = array();
		$group_access_all = array();
		$user_access      = array();
		$user_access_all  = array();
		$uid              = $uid>0?$uid:$this->uid;
		$group_id     = M('SystemUser')->where(array('id'=>$uid,'status'=>1))->getField('group_id');
		if(!$group_id){
			return false;
		}
		$group_access = M('SystemAccess')->where(array('id'=>$group_id,'t'=>0))->select();
		if ($group_access) {
			foreach ($group_access as $key => $value) {
				$group_access_all[] = strtolower($value['m'].'/'.$value['c'].'/'.$value['a']);
			}
		}
		$user_access  = M('SystemAccess')->where(array('id'=>$uid,'t'=>1))->select();
		if ($user_access) {
			foreach ($user_access as $key => $value) {
				$user_access_all[] = strtolower($value['m'].'/'.$value['c'].'/'.$value['a']);
			}
		}
		//去掉被禁止的用户特殊权限
		if ($user_access_all) {
			foreach ($group_access_all as $key => $value) {
				if (in_array($value, $user_access_all)) {
					$access[] = $value;
				}
			}
		}else{
			$access = $group_access_all;
		}
		//加上用户特殊权限被允许的
		if ($user_access_all) {
			foreach ($user_access_all as $key => $value) {
				if (!in_array($value, $group_access_all)) {
					$access[] = $value;
				}
			}
		}
		return $access;
	 }
	/**
	 * 检查用户权限
	 * @return array 权限列表数组
	 */
    public function checkUserAuth($cma='',$uid=0){
			$uid = $uid>0?$uid:$this->uid;
    	//超级管理员不验证权限
    	if ( $uid == C('SUPER_USER') ) {
    		return true;
    	}
			// 获取类名和控制器名
			 $className = '\\'.MODULE_NAME . '\Controller\\' .CONTROLLER_NAME.'Controller';
			// 反射类
			 $rf = new \ReflectionClass($className);
			 $doc = $rf->getDocComment();
			 $auth = preg_match('/@auth\s+(\w+)/u', $doc, $catch)
						? ($catch[1]=='true'?true:false)
						: false;
			 if (false===$auth ) {
					 return true;
			 }
    	//登录验证
			if ($this->model==1) {
				$action_access = session('_ACCESS_ALLOW_LIST');
				if (!$action_access) {
					$action_access =  $this->accessAllowList($uid);
					session('_ACCESS_ALLOW_LIST',$action_access);
				}
			}elseif ($this->model==2){
				$action_access =  $this->accessAllowList($uid);
			}
			//没有权限禁止
			if (!$action_access) {
				return false;
			}
			if ($cma) {
		       $cma       = explode("/", $cma);
		       switch(count($cma)){
			        case 1:default:
				        $cma = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.$cma[0]);
			        break;
			        case 2:
			        	$cma = strtolower(MODULE_NAME.'/'.$cma[0].'/'.$cma[1]);
			        break;
			        case 3:
			        	$cma = strtolower($cma[0].'/'.$cma[1].'/'.$cma[2]);
			        break;
		        }
			}else{
				$cma = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
			}
			if (in_array($cma, $action_access)) {
				return true;
			}else{
				return false;
			}

    }
    /**
     * [getAccessListByUid 获取指定用户的权限]
     * @return [type] [description]
     */
    public function getAccessListByUser($uid=0){
			$uid = $uid>0?$uid:$this->uid;
    	return $this->getAccessList($uid);
    }
    /**
     * [getAccessListByUid 获取指定角色的权限]
     * @return [type] [description]
     */
    public function getAccessListByGroup($group_id){
    	return $this->getAccessList(0,$group_id);
    }
	/**
	 * 获取权限树,系统默认权限, for ztree
	 * @return array 权限列表数组
	 */
	public function getAccessList($uid=0,$group_id=0) {
			$auth_nodes   = array();
			$access = array();
      if ( $uid>0 ) {
        $group_access     = array();
        $group_access_all = array();
        $user_access      = array();
        $user_access_all  = array();
				$group_id     = M('SystemUser')->where(array('id'=>$uid,'status'=>1))->getField('group_id');
				if(!$group_id){
					return false;
				}
				$group_access = M('SystemAccess')->where(array('id'=>$group_id,'t'=>0))->select();
				if ($group_access) {
					foreach ($group_access as $key => $value) {
						$group_access_all[] = strtolower($value['m'].'/'.$value['c'].'/'.$value['a']);
					}
				}
				$user_access  = M('SystemAccess')->where(array('id'=>$uid,'t'=>1))->select();
				if ($user_access) {
					foreach ($user_access as $key => $value) {
						$user_access_all[] = strtolower($value['m'].'/'.$value['c'].'/'.$value['a']);
					}
				}
				//去掉用户特殊权限被禁止的
				if ($user_access_all) {
					foreach ($group_access_all as $key => $value) {
						if (in_array($value, $user_access_all)) {
							$access[] = $value;
						}
					}
				}else{
					$access = $group_access_all;
				}
				//加上用户特殊权限被允许的
				if ($user_access_all) {
					foreach ($user_access_all as $key => $value) {
						if (!in_array($value, $group_access_all)) {
							$access[] = $value;
						}
					}
				}
	      }elseif ( $group_id>0 ) {
	        	$group_access = M('SystemAccess')->where(array('id'=>$group_id,'t'=>0))->select();
	        	if($group_access){
							foreach ($group_access as $key => $value) {
								$access[] = strtolower($value['m'].'/'.$value['c'].'/'.$value['a']);
							}
						}
      }
      // 获取APP目录下所有模块
      foreach (getDirList(APP_PATH) as $key=>$module) {
          // 为保证模块是涉及到需要设置权限的模块，则通过读取模块描述信息获得
          // 模块目录下拥有模块的权限配置文件则代表该模块需要权限验证
          // 反之，不包含该文件的不验证权限
					$config_file_path =APP_PATH . $module . '/Config.php';
          if (is_file($config_file_path)) {
          	  $module_index = strtolower($module) ;
              // 获取模块信息描述文件内容
              $info = include $config_file_path;
							if ( $info['auth']==false || empty($info['auth']) ) {
								continue;
							}
              $auth_nodes[] = array(
								'id'          =>$module_index,
								'pId'         =>'root',
								'name'        =>$info['name'],
								'description' =>$info['description'],
								'open'        =>true,
								'icons'       =>$info['icons'],
								'ac_level'    =>1,
              );
              // 获取模块目录下的控制器文件列表
              foreach (getFileList(APP_PATH . $module . '/Controller') as $key2=>$file) {
                  // 获取类名和控制器名
									if (substr($file, -20, 20)=='Controller.class.php') {
 										$className = '\\'.$module . '\Controller\\' . substr($file, 0, -10);
 									}else{
 										continue;
 									}
                  // 反射类
                   $rf = new \ReflectionClass($className);

                  // 获取其注释内容，通过这种方式获得控制器别名
                   $doc = $rf->getDocComment();
									$auth = preg_match('/@auth\s+(\w+)/u', $doc, $catch)
											? ($catch[1]=='true'?true:false)
											: false;
									if (false===$auth ) {
											continue;
									}
									$controller = substr($file, 0, -20);
                  $controller_index = strtolower($module_index.'/'.$controller);
                  $name = preg_match('/@name\s+(\w+)/u', $doc, $catch)
                      ? $catch[1]
                      : ($description?$description:$controller);

									$description = preg_match('/@description\s+(\w+\S*)/u', $doc, $catch)
											? $catch[1]
											: '';

                  $icons = preg_match('/@icons\s+(\w+)/u', $doc, $catch)
                      ? $catch[1]
                      : '';

                  $auth_nodes[] = array(
										'id'          => $controller_index,
										'pId'         => $module_index,
										'name'        => $name,
										'description' => $description,
										//'icons'        => $icons,
										'open'        => false,
										'ac_level'       => 2,
                  );
                  // 获取控制器内所有方法，即获得所有操作
                  foreach ($rf->getMethods() as $key3=>$method) {
                  	$action_index = strtolower($controller_index.'/'.$method->name);
                      if ($method->class == $module . '\Controller\\' . substr($file, 0, -10)) {
                          $doc = $method->getDocComment();
													//控制器名称
													if (!preg_match('/@name\s+(\w+)/u', $doc, $catch)) continue;
													$name = $catch[1];
													//控制器描述
													$description = preg_match('/@description\s+(\w+\S*)/u', $doc, $catch)
													   ? $catch[1]
													   : '';
													// $showOnNav = preg_match('/@showOnNav\s+(\w+)/u', $doc, $catch)
													//    ? ($catch[1]=='yes'?true:false )
													//    : false;
													// $showOnNavName = preg_match('/@showOnNavName\s+(\w+)/u', $doc, $catch)
													//    ? $catch[1]
													//    : '';
                           if (in_array($action_index, $access)) {
                           	 $allow = true;
                           }else{
														 $allow = false;
													 }
	                        $auth_nodes[] = array(
														'id'          => $action_index,
														'pId'         => $controller_index,
														'name'        => $name,
														'description' => $description,
														// 'showOnNav'   => $showOnNav,
														// 'showOnNavName'=> $showOnNavName,
														//'icon'        => $icon,
														'checked'     => $allow,
														'ac_level'    =>3,
	                        );
                      }
                  }
              }
          }
      }

      foreach ($auth_nodes as $key => $value) {
      	if ($value['ac_level']==2) {
        	 if ($this->_getAccessChecked($value['id'],3,$auth_nodes)) {
        	 	$auth_nodes[$key]['checked'] = true;
        	 }
      	}
      }
      foreach ($auth_nodes as $key => $value) {
      	//去掉没有控制器的模块
         if ($value['ac_level']==1) {
        	 if (!$this->_getAccessKey($value['id'],2,$auth_nodes)) {
        	 	unset($auth_nodes[$key]);
        	 }
        	 if ($this->_getAccessChecked($value['id'],2,$auth_nodes)) {
        	 	$auth_nodes[$key]['checked'] = true;
        	 }
         }
         //去掉没有操作的控制器
         if ($value['ac_level']==2) {
        	 if (!$this->_getAccessKey($value['id'],3,$auth_nodes)) {
        	 	unset($auth_nodes[$key]);
        	 }
         }
      }
      $result['_ACCESS_LIST'] = array_values($auth_nodes);

    return $result;
	}
	/**
	 * [_getAccessKey 检查目录树是否完整(是否包括M,C,A)]
	 * @param  [type] $fv    [description]
	 * @param  [type] $level [description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	private function _getAccessKey($fv,$level,$array){
		foreach ($array as $key => $value) {
			if ( $value['pId']==$fv && $value['ac_level']==$level ) {
				return true;
			}else{
				continue;
			}
		}
	}
	/**
	 * [_getAccessChecked 检查目录树是否完整(是否包括M,C,A)]
	 * @param  [type] $fv    [description]
	 * @param  [type] $level [description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	private function _getAccessChecked($fv,$level,$array){
		foreach ($array as $key => $value) {
			if ( $value['pId']==$fv && $value['checked']==true ) {
				return true;
			}else{
				continue;
			}
		}
	}
}
?>
