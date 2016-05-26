<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
/**
 * @name 用户组管理
 * @auth true
 * @icons users
 * @author biner
 * @date 2016-04-13
 */
class GroupController extends AdminbaseController {
    public function _initialize() {
        parent::_initialize();
        $this->obj = D('SystemGroup');
    }
	/**
	 * @name 列表
	 * @showOnNav yes
	 */
    public function index(){
      $tdTitle = array(
  			'id'              =>array('title'=>'编号','width'=>80,'class'=>'hidden-sm hidden-xs'),
  			'name'         =>array('title'=>'组名','width'=>120,'sort'=>false),
  			'remark'        =>array('title'=>'说明','sort'=>false,'class'=>'hidden-sm hidden-xs'),
  			'status'          =>array('title'=>'状态','sort'=>false),
        'admin'        =>array('title'=>'操作','width'=>140,'sort'=>false),
  		);
  		$this->assign("tdTitle",$tdTitle);
    	$formdata = I();
  		if ($formdata) {
  			if (!empty($formdata['name'])) {
  				$map['where']['name'] = array('like','%'.$formdata['name'].'%');
  			}
  		}
      $data = $this->pageList($this->obj,$map);
      $this->assign('data',$data);
      $this->assign('formget',$formdata);
  	  $this->display();
    }
	/**
	 * @name 新增
	 * @showOnNav yes
	 */
    public function add(){
    	if(IS_POST){
            //$data = I('post.');
            if ($this->obj->create()) {
                $result = $this->obj->add();
                if (false !== $result) {
                    $this->success("保存成功！",U("index"));
                }else{
                    $this->error("保存失败！");
                }
            }else{
            	$this->error($this->obj->getError());
            }
    	}else{
	        $this->display('edit');
    	}
    }
	/**
	 * @name 编辑
	 */
    public function edit(){
    	if(IS_POST){
            $data = I('post.');
            if ($this->obj->create()) {
                $result = $this->obj->save();
                if (false !== $result) {
                    $this->success("保存成功！", U("index"));
                }else{
                    $this->error("保存失败！");
                }
            }else{
            	$this->error($this->obj->getError());
            }
    	}else{
	        $vo = $this->obj->where(array('id'=>I('get.id')))->find();
	        $this->assign('vo',$vo);
	    	$this->display();
    	}
    }
	/**
	 * @name 删除
	 */
    public function delete(){
        $id = I('id');
        $checkUser = M('SystemUser')->where(array('group_id'=> array('in', $id) ))->find();
        if ($checkUser) {
            $this->error('删除失败,该用户组下有用户,请先删除用户');
        }else{
            if (false !== $this->obj->where(array($this->obj->getPk() => array('in', $id)))->delete()) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }
	/**
	 * @name 权限管理
	 * @description 该权限可以对用户组进行授权,请谨慎选择
	 */
    public function access(){
        if (IS_POST) {
            $data = I('post.');
            M('SystemAccess')->where(array('t'=>$data['t'],'id'=>$data['id']))->delete();
            session('_ACCESS_ALLOW_LIST',null);
            $access = array();
            foreach ($data['nodes'] as $key => $value) {
                $cma = explode('/', $value['aciton']);
                $access[] = array(
                    'id' =>$data['id'],
                    't'  =>$data['t'],
                    'm'  =>$cma[0],
                    'c'  =>$cma[1],
                    'a'  =>$cma[2],
                );
            }
            if ($access) {
                M('SystemAccess')->addAll($access);
                $this->success('权限已更新');
            }else{
                $this->error('权限已禁用');
            }
        }else{
            $auth = new \Vendor\Rbac;
            $AccessList = $auth->getAccessListByGroup(I('get.id'));
            if ($AccessList['_ACCESS_LIST']) {
              foreach ($AccessList['_ACCESS_LIST'] as $key => $value) {
                $AccessList['_ACCESS_LIST'][$key]['name'] = '【'.$value['name'].'】';//'<span style="color:#888;">'.$value['description'].'</span>';
              }
              $AccessList = $AccessList['_ACCESS_LIST'];
            }else{
              $AccessList = array();
            }
            $this->assign('accesslist',json_encode($AccessList));
            $this->display();
        }
    }
}
