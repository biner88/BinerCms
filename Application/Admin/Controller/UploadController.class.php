<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

//import('UploadFile',MYLIB_PATH);
set_time_limit(0);
/**
 * @name 文件上传
 * @desc 文件上传
 * @author biner
 * @auth yes
 * @date 2015-08-18
 */
class UploadController extends AdminbaseController {
    public function _initialize() {
        parent::_initialize();
    }
    /**
     * @name 上传
     */
    public function uploadOne() {
        $config = array(
            'maxSize'  =>    314572800,
            'rootPath' =>    './data/',
            'saveName' =>    'md5',//array('uniqid',''),
            'exts'     =>    array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'  =>    true,
            'subName'  =>    array('date','Ymd'),
            'replace'  => true,
        );
        if ($_POST['folder']=='') {
            $config['savePath'] = 'public/';
        }else{
            $config['savePath'] = $_POST['folder'].'/';
        }
        $upload = new \Think\Upload($config);
        // 实例化上传类
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {
            // 上传错误提示错误信息
            //print_r($upload->getError());
            echo json_encode(array('err'=>$upload->getError()));
        }else{
            $file = $info['filename'];
            //{"err":"","msg":"base\/app\/apk\/home4.0.apk","info":{"name":"home4.0.apk","type":"application\/octet-stream","size":4088898,"key":"filename","ext":"apk","md5":"74a5f98e48141a5bc7f41ce634602d0d","sha1":"a854ebf58ab8bf7a11524553d1addec404a92c9f","savename":"home4.0.apk","savepath":"base\/app\/apk\/"}}
            // if ($file['ext'] == 'apk') {
            //      $ApkParser = new \Vendor\ApkParser();
            //      $res                     = $ApkParser->open( $config['rootPath'].$file['savepath'].$file['savename'] );
            //      $file['package']     = $ApkParser->getPackage();
            //      $file['versionName'] = $ApkParser->getVersionName();
            //      //$file['versionCode'] = $ApkParser->getVersionCode();
            //  }
            $realpath = $_SERVER['DOCUMENT_ROOT'].C('TMPL_PARSE_STRING.__FILE__').'/'.$file['savepath'].$file['savename'];
            if (in_array($file['ext'],array('jpg', 'gif', 'png', 'jpeg'))) {
              $info  =  _getImageInfo($realpath);
              $file['width']  = $info['width'];
              $file['height'] = $info['height'];
            }
            $file['size'] = filesize($realpath);
            if ($file) {
                   echo json_encode(array('err'=>'','info'=>$file));
            }else{
                echo json_encode(array('err'=>'上传失败'));
            }
        }
    }
}
?>
