<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * @name 404
 */
class EmptyController extends Controller {
    public function index(){
       $this->error('功能开中...');
    }
}
