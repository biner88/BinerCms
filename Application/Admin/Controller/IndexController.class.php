<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
/**
 * @name 系统首页
 * @description 登录后看到的第一个界面,必选~!
 * @author biner
 * @auth yes
 * @date 2015-08-18
 */
class IndexController extends AdminbaseController {

	/**
	 * 主界面
	 * @name WELCOME
	 * @showOnNav yes
	 */
	public function index() {
		$this->display();
	}
	/**
	* @name 清理缓存
	 */
  public function cacheClear()  {
        clearCache();
        $this->success("清空缓存成功", U('Admin/index/index'));
    }
}
