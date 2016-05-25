<?php
namespace Common\Controller;

use Think\Controller;

class BaseController extends Controller{
  public function _initialize() {
	}
    /**
	 * 验证码
	 */
	public function verify() {
		$Verify = new \Think\Verify(array('imageH' => '32', 'imageW' => '110', 'fontSize' => 16, 'length' => 4, 'useNoise' => false,'useCurve'=>false));
		$Verify->entry();
	}
  /**
   * 记录日志
   */
  public function opLog($title='',$remark='', $type=0){
      if($title==''){
          $action_name = strtolower(ACTION_NAME);
          if ( $action_name=='delete' ||
          ($action_name=='add' && IS_POST) ||
          ($action_name=='edit' && IS_POST) ||
          ($action_name=='access' && IS_POST) ||
          ($action_name=='check') )
          {
              switch ($action_name) {
                  case 'delete':
                      $title = '删除数据';
                      break;
                  case 'add':
                      $title = '添加数据';
                      break;
                  case 'edit':
                      $title = '修改数据';
                      break;
                  case 'check':
                      $title = '审核数据';
                      break;
                  case 'access':
                      $title = '权限设置';
                      break;
                  case 'statuspass':
                      $title = '状态修改';
                      break;
                  case 'shelves':
                      $title = '上架/下架';
                      break;
                  default:
                      $title = '其他';
                      break;
              }
          }
      }
      if($title==''){
        return false;
      }
      if ($remark=='') {
          if ($_GET) {
              $requestData['GET'] = $_GET;
          }
          if ($_POST) {
              $requestData['POST'] = $_POST;
          }
          $requestData = preg_replace("/\"password\":\"(.*)\"/i",'"password":"******"',json_encode($requestData));

      }else{
          $requestData = $remark;
      }
      $logData = array(
              'uid'          =>session('?'.C('ADMIN_SESSION_KEY').'.id')?session(C('ADMIN_SESSION_KEY').'.id'):0,
              'm'            =>MODULE_NAME,
              'c'            =>CONTROLLER_NAME,
              'a'            =>ACTION_NAME,
              'ip'           =>get_client_ip(),
              'type'         =>$type,
              'created_time' =>time(),
              'title'        =>$title,
              'remark'       =>$requestData,
      );
      M('SystemLog')->add( $logData );
  }
  /**
   * [pageList 通用分页]
   * $data = pageList(M('Kindergarten'));
   * @param  [type] $model [description]
   * @param  array  $map   [description]
   * @return [type]        [description]
   */
  function pageList($model,$map = array()){
  	//排序方法
  	if(isset($_REQUEST['_order']) && isset($_REQUEST['_sort'])){
  		$order  = $_REQUEST['_order'];
  		$sort   = $_REQUEST['_sort'] ? 'asc' : 'desc';
  		$orders = $order . ' ' . $sort;
  	}elseif (isset($map['orders'])) {
  		$orders =  $map['orders'];
  	}else{
  		$order  = isset($map['order']) ? $map['order'] : $model->getPk();
  		$sort   = isset($map['asc']) ? 'asc' : 'desc';
  		$orders = $order . ' ' . $sort;
  	}
  	//连贯查询
  	$expect = array('find', 'join', 'field','cache', 'group', 'having', 'relation', 'table', 'where');
  	foreach ($expect as $value) {
  		if (isset($map[$value])) {
  			$model = $model->$value($map[$value]);
  		}
  	}

  	if (isset($map['_custom'])) {
  		foreach ($map['_custom'] as $key => $value) {
  			$model = $model->$key($value);
  		}
  	}
  	//返回sql
      if (isset($map['_sql'])) {
      	return $model->select(false);
      }
  	//取得满足条件的记录数
  	if (isset($map['count'])) {
  		$count = $map['count'];
  	}else{
  		if ($map['group']) {
  			$count = count($model->field('COUNT(*) AS tp_count')->select());
  		}else{
  			$count = $model->count();
  		}
  	}
  	//创建分页对象
  	if (isset($_REQUEST['_limit'])) {
  		$listRows = $_REQUEST['_limit'];
  	} else {
  		if(isset($map['limit'])){
  			$listRows = $map['limit'];
  		}else{
  			if(session('_PageNum')){
  				$listRows = session('_PageNum');
  			}else{
  				$listRows = 15;
  			}
  		}
  	}
  	//分页样式
  	if(empty($map['config']['theme'])){
  		$map['config']['theme'] = ' <li class=""><a>%totalRow% %header% %nowPage%/%totalPage% 页</a></li>  %first%  %prePage%  %linkPage%  %nextPage% %end%';
  	}
  	$p = new \Vendor\Page($count, $listRows);
  	if(is_array($map['config'])){
  		foreach($map['config'] as $ks=>$vs){
  			$p->setConfig($ks,$vs);
  		}
  	}
  	$expect = array('join', 'field',  'cache', 'group', 'relation', 'table', 'where');
  	foreach ($expect as $value) {
  		if (isset($map[$value])) {
  			if ($value == 'join' && is_array($map[$value])) {
  				foreach ($map['join'] as $join) {
  					$model = $model->join($join);
  				}
  			} else {
  				$model = $model->$value($map[$value]);
  			}
  		}
  	}

  	if (isset($map['_custom'])) {
  		foreach ($map['_custom'] as $key => $value) {
  			$model = $model->$key($value);
  		}
  	}
  	$model = $model->order($orders);
  	// 分页limit实现
  	$list = $model->limit($p->firstRow . ',' . $p->listRows)->select();
  	// echo $model->_sql();
  	if(isset($map['parameter'])){
  		$p->parameter = $map['parameter'];
  	}
  	//分页显示
  	$page = $p->show();
  	//列表排序显示
     $sortArrow = $sort == 'desc' ? '▼' : '▲'; //排序图标
     $sortAlt   = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
     $sort      = $sort == 'desc' ? 1 : 0; //排序方式
  	//返回数组
  	$_result = array(
  		'list'      =>$list,
  		'page'      =>$page,
  		'sort'      =>$sort,
  		'order'     =>$order,
  		'sortArrow' =>$sortArrow,
  		'sortAlt'   =>$sortAlt,
  	);
  	//cookie('_currentUrl_', __SELF__);
  	return $_result;
   }

}
