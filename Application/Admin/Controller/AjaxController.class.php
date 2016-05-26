<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
use Think\Cache;
/**
  * @name 公共模块
  * @description Ajax模块,无权限控制,但需要登录
  * @author biner
  * @auth false
  * @date 2016-05-18
  */
class AjaxController extends AdminbaseController {
  public function index() {
    send_http_status(404);
	}
  /**
   * @name 清理缓存
   */
   public function clearCache(){
     $task = array_diff(array('Cache', 'Data', 'Temp'), array());
     if (defined('RUNTIME_PATH')) {
         if (is_dir(RUNTIME_PATH)) {
             $dirs = scandir(RUNTIME_PATH);

             foreach ($dirs as $dir) {
                 if ($dir == '.' || $dir == '..') {
                     continue;
                 }

                 if (in_array($dir, $task)) {
                     self::_removeDirectory(RUNTIME_PATH . $dir);
                 }
             }
         }
     }
     Cache::getInstance()->clear();
     $this->success("清空缓存成功");
   }
   public static function _removeDirectory($dir){
       if (IS_WIN) {
           if (rmdir($dir) == false && is_dir($dir)) {
               // 递归删除目录下的文件
               $dh = opendir($dir);
               while ($file = readdir($dh)) {
                   if ($file != "." && $file != "..") {
                       $full = $dir . DIRECTORY_SEPARATOR . $file;
                       if(!is_dir($full)) {
                           unlink($full);
                       } else {
                           self::_removeDirectory($full);
                       }
                   }
               }

               closedir($dh);
               if(rmdir($dir)) {
                   return true;
               }
           }
       } else {
           exec("rm -Rf $dir");
           return true;
       }

       return false;
   }
   /**
    * @name 系统使用开源组件
    */
   public function openSource(){
     $openSource_list = array(
         array(
           'name'=>'jQuery',
           'icon'=>'fa-github',
           'url'=>'https://github.com/jquery/jquery',
           'version'=>'1.9.1',
           'description'=>'jQuery是一个兼容多浏览器的javascript库，核心理念是write less,do more(写得更少,做得更多)。',
         ),
         array(
           'name'=>'Bootstrap',
           'icon'=>'fa-github',
           'url'=>'https://getbootstrap.com/',
           'version'=>'3.0',
           'description'=>'Bootstrap是基于HTML5和CSS3开发的，它在jQuery的基础上进行了更为个性化和人性化的完善，形成一套自己独有的网站风格，并兼容大部分jQuery插件。',
         ),
         array(
           'name'=>'datetimepicker',
           'icon'=>'fa-github',
           'url'=>'https://github.com/Eonasdan/bootstrap-datetimepicker',
           'version'=>'4.17.37',
           'description'=>'DateTimePicker控件，控件一般用于让用户可以从日期列表中选择单个值。运行时，单击控件边上的下拉箭头，会显示为两个部分：一个下拉列表，一个用于选择日期的。',
         ),
         array(
           'name'=>'ztree',
           'icon'=>'fa-github',
           'url'=>'https://github.com/zTree/zTree_v3',
           'version'=>'3.5.23',
           'description'=>'zTree 是一个依靠 jQuery 实现的多功能 “树插件”。优异的性能、灵活的配置、多种功能的组合是 zTree 最大优点。专门适合项目开发，尤其是 树状菜单、树状数据的Web显示、权限管理等等。',
         ),
         array(
           'name'=>'artDialog v6',
           'icon'=>'fa-github',
           'url'=>'http://aui.github.io/artDialog/',
           'version'=>'6.0.2',
           'description'=>'artDialog —— 经典的网页对话框组件，内外皆用心雕琢。',
         ),
         array(
           'name'=>'validation',
           'icon'=>'fa-github',
           'url'=>'https://github.com/1000hz/bootstrap-validator',
           'version'=>'0.10.2',
           'description'=>'The Validator plugin offers automatic form validation configurable via mostly HTML5 standard attributes. It also provides an unobtrusive user experience, because nobody likes a naggy form.',
         ),
         array(
           'name'=>'fontawesome',
           'icon'=>'fa-github',
           'url'=>'https://github.com/FortAwesome/Font-Awesome',
           'version'=>'4.6.3',
           'description'=>'Font Awesome为您提供可缩放的矢量图标，您可以使用CSS所提供的所有特性对它们进行更改，包括：大小、颜色、阴影或者其它任何支持的效果。',
         ),
         array(
           'name'=>'thinkphp',
           'icon'=>'fa-github',
           'url'=>'https://github.com/liu21st/thinkphp',
           'version'=>'3.2',
           'description'=>'ThinkPHP是为了简化企业级应用开发和敏捷WEB应用开发而诞生的。',
         ),
     );
     $this->assign("openSource_list",$openSource_list);
     $this->display();
   }
  /**
   * @name 获取二级,三级目录树
   */
   public function getMenu(){
     $pid = I('id');
     if(empty($pid)){
       return false;
     }
     $menu_list = array();
     if($pid=='member'){
       $menu_list[] = array(
         'name' =>'个人资料',
         'icon' =>'fa-folder-open-o',
         'url'  =>'javascript:;',
         'color'=>'#3c8cad',
         'lev3'=>array(
          //  array(
          //    'name'=>'修改资料',
          //    'icon'=>'fa-user',
          //    'url'=>U('admin/user/info'),
          //  ),
          //  array(
          //    'name'=>'修改密码',
          //    'icon'=>'fa-key',
          //    'url'=>U('admin/user/passwod'),
          //  ),
           array(
             'name'=>'退出登录',
             'icon'=>'fa-sign-out',
             'url'=>U('admin/public/logout'),
             'color'=>'',
           ),
         ),
       );
     }else{
       $lev2 = M('SystemMenu')->cache(true)->where(array('parent_id'=>$pid,'display'=>1))->order('sort asc')->select();
       if($lev2){
         foreach ($lev2 as $key => $value) {
           $menu_list[$key] = array(
             'id'   =>$value['id'],
             'name' =>$value['name'],
             'icon' =>$value['icon']?$value['icon']:'fa-folder-open-o',
             'url'  =>'javascript:;',
             'color'=>'#3c8cad',
           );
           if($value['module'] && $value['controller'] && $value['action']){
             $menu_list[$key]['url'] = U($value['module'].'/'.$value['controller'].'/'.$value['action']).($value['query_string']?'&'.$value['query_string']:'');
           }
            $lev3= M('SystemMenu')->cache(true)->where(array('parent_id'=>$value['id'],'display'=>1))->order('sort asc')->select();
            if($lev3){
              foreach ($lev3 as $key3 => $value3) {
                if (checkAccess($value3['module'].'/'.$value3['controller'].'/'.$value3['action'])) {
                  $url = U($value3['module'].'/'.$value3['controller'].'/'.$value3['action']).($value3['query_string']?'&'.$value3['query_string']:'');
                  if ($url) {
                    $menu_list[$key]['lev3'][$key3] = array(
                      'name' =>$value3['name'],
                      'icon' =>$value3['icon']?$value3['icon']:'fa-file-text-o',
                      'url'  =>$url,
                      'color'=>'#3c8cad',
                    );
                  }
                }
              }
            }
         }
       }
     }
     return $this->ajaxReturn($menu_list);
   }
   /**
    * @name 检查后台添加用户名是否存在
    */
   public function checkuser(){

   }

}
