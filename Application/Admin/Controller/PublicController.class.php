<?php
/**
  * @name 公共模块
  * @description 无权限控制
  * @author biner
  * @auth false
  * @date 2016-05-18
  */
namespace Admin\Controller;
use Common\Controller\BaseController;
class PublicController extends BaseController {
  public function index() {
    send_http_status(404);
	}
  public function logout() {
		session('login_account', session( C('ADMIN_SESSION_KEY').'.account') );
		session(C('ADMIN_SESSION_KEY'), null);
		$this->success('已成功注销', U('Admin/public/login'));
	}

	public function login() {
		if (IS_POST) {
			$account = strtolower(I('post.account'));
			if(!$account){
				$res = array(
					'msg'   =>'邮箱或用户名不能为空',
					'errortype' => 2,
					'formid' =>'account',
					);
				$this->error($res);
			}
			//密码是否为空
			if(empty($_POST['password'])) {
				$res = array(
					'msg'   =>'密码不能为空',
					'errortype'   => 2,
					'formid' =>'password',
					);
				$this->error($res);
			}
			//验证码验证
			if($_SESSION['login_error_num'] > 3){
				if(empty($_POST['verify']) || '' === trim($_POST['verify'])) {
					$res = array(
						'msg'   => '验证码不能为空',
						'errortype'   => 1,
						'formid' =>'verify'
						);
					$this->error($res);
				}else{
					$verify = new \Think\Verify();
					if(!$verify->check(I('post.verify'))){
						$res = array(
							'msg'   =>'验证码错误',
							'errortype'   => 2,
							'formid' =>'verify'
							);
						$this->error($res);
					}
				}
		    }
		    // 要求判断status值，为1才是正常的账户
		    $vo = D('SystemUser')->where(array('account|email|telphone'=>$account))->find();
		    if ($vo === false) {
		    	$res = array(
		    		'info' => '登录过程中出现错误',
		    		'errortype' => 2,
		    	);$this->error($res);
		    } elseif ($vo === null) {
		    	$res = array(
					'msg'   =>'账户不存在',
					'errortype' => 2,
					'formid' =>'account',
				);$this->error($res);
		    }elseif ($vo['status'] == 0) {
		    	$res = array(
					'msg'   =>'该账户已被禁止登录',
					'errortype' => 2,
					'formid' =>'account',
					);$this->error($res);
		    } else {
				//验证密码
				if($vo['pass'] != pw_encode(I('post.password'),$vo['pass_salt'],$vo['pass_type'])) {
					$res = array(
						'msg'   =>'密码错误',
						'errortype'   =>3,
						'formid' =>'password',
						'errornum'=>$this->errorNum($account),
					);
					$this->error($res);
				}else{
          //是否有后台权限
          $cma = checkAccess('admin/index/index',$vo['id']);

          if ($cma === false) {
            $this->opLog($account.'成功登录后台,但无后台权限,被限制登录');
            session(C('ADMIN_SESSION_KEY'), null);
            $res = array(
							'msg'   =>'无后台登录权限',
							'errortype'   => 2,
							'formid' =>'account'
							);
						$this->error($res);
          }
          //需要session的信息
					$userSession = array(
						'id'           =>$vo['id'],
						'account'      =>$vo['account'],
            'name'   =>$vo['name'],
						'email'        =>$vo['email'],
						'group_id'     =>$vo['id']==C('SUPER_USER')?0:$vo['group_id'],
            'avatar' =>$vo['avatar']?$vo['avatar']:'/static/images/avatar.png',
						//'theme' => $vo['theme']?$vo['theme']:'000000',
					);
					session(C('ADMIN_SESSION_KEY'),$userSession);
					//更新登录信息
					$data = array(
						'last_login_time' =>time(),
						'login_count'     =>array('exp','login_count+1'),
						'last_login_ip'   =>get_client_ip(),
					);
					D('SystemUser')->where(array('id'=>$vo['id']))->save($data);

					unset($_SESSION['login_error']);
					$this->opLog($account.'成功登录后台');
					$this->success(array('msg'=>'登录成功','type'=>2,'href'=>U('Admin/index/index')));
				}
		    }
		    }else{
            $this->display();
        }
	}
	/**
	 * 登录错误计数
	 */
	public function errorNum($account){
		if ( session('login_account') == $account) {
			$error_num =  session('login_error_num');
			session('login_error_num',$error_num+1 );
		}else{
			$error_num =   1;
			session('login_account',$account);
			session('login_error_num',$error_num);
		}
		$this->opLog($account.'尝试登录后台失败'.$error_num.'次');
		return $error_num;
	}
}
