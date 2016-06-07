<?php
namespace Common\Controller;
use Common\Controller\BaseController;

class HomebaseController extends BaseController{

  public function _initialize() {
		parent::_initialize();
		define('IS_ADMIN', 0);
	}
  }
}
