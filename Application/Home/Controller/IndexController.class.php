<?php
namespace Home\Controller;
use Think\Controller;
/**
 * @name 前台首页
 * @description 前台默认页面
 * @author xu
 * @auth no
 * @date 2016-05-26
 */
class IndexController extends Controller {

	/**
	 * 首页
	 * @name WELCOME
	 * @showOnNav yes
	 */
	public function index() {
		$this->display();
	}
}
