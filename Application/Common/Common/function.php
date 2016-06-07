<?php
//验证
function verify($preg,$str){
    switch($preg){
    case 'email':
        $preg = '/^[\w\.-]+?@([\w\-]+\.){1,2}[a-zA-Z]{2,3}$/';// /^1[3578]{1}[0-9]{9}$/
    break;
    case 'mobile':
        $preg = '/^1\d{10}$/';
    break;
    case 'url':
        $preg = '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/';
    break;
    case 'idcard':
        $preg = '/^\d{15}(\d{2}[A-Za-z0-9])?$/';
    break;
    case 'chinese':
        $preg = '/^[".chr(0xa1)."-".chr(0xff)."]+$/';
    break;
    case 'chinese2':
        $preg = '/^[\x7f-\xff]+$/';//全部中文
    break;
    case 'chinese3':
        $preg = '/[\x7f-\xff]/';//含有中文
    break;
    case 'color':
        $preg = '/^#[a-fA-F0-9]{6}$/';//含有中文
    break;
    }
    if (!preg_match($preg,$str)) {
        return false;
    }else{
        return true;
    }
}
//格式化状态显示
function getStatus($status){
	switch ($status) {
		case -1:
			return '<span class="badge badge-default">回收</span>';
			break;
		case 0:
			return '<span class="badge badge-danger">禁用</span>';
			break;
		case 1:
			return '<span class="badge badge-success">启用</span>';
			break;
		case 2:
			return '<span class="badge badge-info">未激活</span>';
			break;
		default:
			# code...
			break;
	}
}
 //循环创建目录
function mk_dir($dir, $mode = 0777){
  if (is_dir($dir) || @mkdir($dir,$mode,true)) return true;
  if (!mk_dir(dirname($dir),$mode)) return false;
  return @mkdir($dir,$mode,true);
}

/* 文件大小格式化 */
function byte_format($size, $dec = 2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}
?>
