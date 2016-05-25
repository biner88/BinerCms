<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
/**
 * @name 菜单管理
 * @author biner
 * @auth yes
 * @date 2015-08-18
 */
class MenuController extends AdminbaseController {
    protected $obj;

    public function _initialize()  {
        parent::_initialize();
        $this->obj = D('SystemMenu');
    }
    /**
     * @name 列表
     * @showOnNav yes
     */
    public function index(){
      //目录管理
  		if($_GET['t'] == 'catalogTreeJson'){
  			$Id = I('post.id')?I('post.id'):0;
        $catalogTree = array();
        $list = $this->obj->where( array('parent_id'=>$Id) )->order('sort asc')->select();
         foreach ($list as $key => $value) {
           $catalogTree[] = array(
             'id'       =>$value['id'],
             'name'     =>$value['name'],
             'pid'      =>$value['parent_id'],
             'isParent' =>$this->obj->field("id")->where('parent_id='.$value['id'])->cache(true)->find()?true:false,
             'is_show'  =>$value['display'],
             'sort'     =>$value['sort'],
             'expand'   =>1,//$Id>0?0:1,
           );
         }
  		   echo json_encode(array_values($catalogTree));
  		}else{
  			$this->display();
  		}
    }
    /**
     * @name 添加
     * @showOnNav yes
     */
    public function add() {
      		if (I('get.t')=='save') {
      			$data              = I('post.');
            $add_data = array(
              'name'         =>$data['name'],
              'module'       =>$data['module'],
              'controller'   =>$data['controller'],
              'action'       =>$data['action'],
              'query_string' =>$data['query_string'],
              'icon'         =>$data['icon'],
              'display'      =>$data['display'],
              'parent_id'    =>$data['id'],
              'sort'         =>intval($data['sort']),
            );
      		  if($newid = $this->obj->add($add_data)) {
              $this->success('添加成功');
      			}else{
              $this->error('添加失败');
      		  }
      		}else{
      			$this->display('edit');
      		}
    }
    /**
     * @name 修改
     */
     public function edit() {
        if(I('get.t')=='selectList'){
          $result = array();
          foreach ($permissions as $module => $moduleNode) {
              $result['module'][] = $module;
              foreach ($moduleNode as $controller => $node) {
                  $result['controller'][$module][] = $controller;
                  foreach ($node as $method => $auth) {
                      $result['method'][$module][$controller][] = $method;
                  }
              }
          }

          $this->ajaxReturn($result);
     		}elseif(I('get.t')=='show'){
     			//显示修改页面
     			$vo = $this->obj->where(array('id'=>I('get.id')))->find();
     			$this->assign('vo', $vo);
     			$this->display();
     		}elseif(I('get.t')=='drop'){
     			//拖动,隐藏/显示,启用/禁用
     			$data = array(
     				'id'       =>I('get.id'),
     				'type'     =>I('get.type'),
     				'targetid' =>intval(I('get.targetid')),
     				'pid'      =>intval(I('get.pid')),
     				'st'       =>intval(I('get.st')),
     			);
     			$r = $this->obj->opNode($data);
     			if($r){
            $this->success("操作成功");
     			}else{
            $this->success("操作失败",$data);
     			}
     		}else{
     			$data              = I('post.');
     			$data['display']   = $data['display']?1:0;
     			$data['sort']      = intval($data['sort']);
     		  	if(false !== $this->obj->save($data)) {
     				    $this->success("操作成功");
     		 	  }else{
     			      $this->success("操作失败");
     		  	}
     		}
     	}
    /**
     * @name 删除
     */
    public function delete() {
        $id = I('get.id');
        if (is_array($id)) {
            if (false !== $this->obj->where(array('id', array('in', implode(',', $id))))->delete()) {
                $this->success("删除成功！", U("index"));
            } else {
                $this->error('删除失败');
            }
        } elseif (is_numeric($id)) {
            if (false !== $this->obj->delete($id)) {
                $this->success("删除成功！", U("index"));
            } else {
                $this->error("删除失败！");
            }
        }
    }
}
