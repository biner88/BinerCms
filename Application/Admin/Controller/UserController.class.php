<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
/**
 * @name 用户管理
 * @author biner
 * @auth true
 * @icons user
 * @date 2015-08-18
 */
class UserController extends AdminbaseController {
	/**
	 * @name 列表
   * @showOnNav yes
	 */
	public function index() {
    $tdTitle = array(
			'id'              =>array('title'=>'编号','width'=>80,'class'=>'hidden-sm hidden-xs'),
			'account'         =>array('title'=>'账户','width'=>120,'sort'=>false),
			'group_id'        =>array('title'=>'角色','sort'=>false),
			'name'            =>array('title'=>'姓名','sort'=>false,'class'=>'hidden-sm hidden-xs'),
			'status'          =>array('title'=>'状态','sort'=>false),
      'created_time' =>array('title'=>'注册时间','class'=>'hidden-sm hidden-xs'),
      'admin'        =>array('title'=>'操作','width'=>200,'sort'=>false),
		);
		$this->assign("tdTitle",$tdTitle);
		$map = array();
    $map['relation'] = true;
		$formdata = I();
		if ($formdata) {
			if (!empty($formdata['name'])) {
				$map['where']['account|name|telphone|email'] = array('like','%'.$formdata['name'].'%');
			}
		}
		$data = $this->pageList(D('SystemUser'),$map);
		$this->assign('data', $data);
		$this->assign('formdata',$formdata);
		$this->display();
	}
  /**
	 * @name 详情
	 */
   public function item() {
     $id = I('get.id');
     $vo = D('SystemUser')->relation(true)->where(array('id'=>$id))->find();
     if ($vo) {
       $this->assign("vo",$vo);
       $this->display();
     }else{
       $this->error("用户不存在");
     }

 	}
  /**
	 * @name 新增
	 * @showOnNav yes
	 */
    public function add(){
			$SystemUser = D('SystemUser');
    	if(IS_POST){
            $data = I('post.');
            if ($SystemUser->create()) {
								if($SystemUser->pass){
									$pw = pw_encode($SystemUser->pass);
									$SystemUser->pass      = $pw['pass'];
									$SystemUser->pass_salt = $pw['pass_salt'];
									$SystemUser->pass_type  = $pw['pass_ver'];
								}
                $result = $SystemUser->add();
                if (false !== $result) {
                    $this->success("保存成功！", U("index"));
                }else{
                    $this->error("保存失败！");
                }
            }else{
            	$this->error($SystemUser->getError());
            }
    	}else{
    		$group = M('SystemGroup')->where(array('status'=>1))->select();
    		$this->assign('group',$group);
	      $this->display('edit');
    	}
    }
  	/**
	   * @name 编辑
	   */
    public function edit(){
      $SystemUser = D('SystemUser');
    	if(IS_POST){
            $data = I('post.');
            if ($SystemUser->create()) {
								if($SystemUser->pass){
									$pw = pw_encode($SystemUser->pass);
									$SystemUser->pass      = $pw['pass'];
									$SystemUser->pass_salt = $pw['pass_salt'];
									$SystemUser->pass_type  = $pw['pass_ver'];
								}
                $result = $SystemUser->save();
                if (false !== $result) {
                  $this->success("保存成功！", U("index"));
                }else{
                  $this->error("保存失败！");
                }
            }else{
            	$this->error($SystemUser->getError());
            }
    	}else{
        $vo = $SystemUser->where(array('id'=>I('get.id')))->find();
        $this->assign('vo',$vo);
    		$group = M('SystemGroup')->where(array('status'=>1))->select();
    		$this->assign('group',$group);
	    	$this->display();
    	}
    }
    /**
  	 * @name 权限管理
  	 * @description 该权限可以对用户进行授权,请谨慎选择
  	 */
    public function access(){
        if (IS_POST) {
            $data = I('post.');
            M('SystemAccess')->where(array('t'=>$data['t'],'id'=>$data['id']))->delete();
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
								session('_ACCESS_ALLOW_LIST',null);
                $this->success('权限已更新');
            }else{
                $this->error('权限已禁用');
            }
        }else{
            $auth = new \Vendor\Rbac;
            $AccessList = $auth->getAccessListByUser(I('get.id'));
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
  /**
   * @name 设置默认权限
   */
  public function setDefaultAccess(){
      $id = I('id');
      if (false !== D('SystemAccess')->where(array('id' => $id,'t'=>1))->delete()) {
          $this->success('设置成功');
      } else {
          $this->error('设置失败');
      }
  }
  /**
	 * @name 删除
	 */
    public function delete(){
        $id = I('id');
				if ($id==C('SUPER_USER')) {
					$this->opLog('试图删除超级管理员');
					$this->error('删除失败,超级管理员不允许删除');
				}
        if (false !== D('SystemUser')->where(array('id' => array('in', $id)))->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
?>
