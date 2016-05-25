<?php
/**
 * 根据节点检查权限
 * checkAccess('Base/Group/index');
 * @param  [type] $CMA [description]
 * @return [布尔]      [description]
 */
function checkAccess($cma='',$uid=0){
   $auth = new \Vendor\Rbac;
   return  $auth->checkUserAuth($cma,$uid);
}
/**
 * *********底部按钮,不带id参数,初始化为input:button标签*********
 * {:showLink(array('action'=>'pass','type'=>'fun','text'=>'草稿箱/已发布','value'=>'审核'))} 是否转换,action是方法名
 * {:showLink(array('action'=>'recycle','type'=>'single','ext'=>array('field'=>'is_pass','value'=>'2'),'text'=>'回收'))} 把字段改为value内值,并从列表移除
 * {:showLink(array('action'=>'delete','type'=>'delete','text'=>'删除'))} 删除,会弹窗提示
 * *********每条信息的按钮,带id参数,初始化为a标签*********
 * {:showLink(array('action'=>'recycle','type'=>'single','ext'=>array('field'=>'is_pass','value'=>'0'),'id'=>$vo['id'],'text'=>'恢复'))} 把字段改为value内值,并从列表移除
 * {:showLink(array('action'=>'pass','status'=>$vo['is_pass'],'id'=>$vo['id'],'text'=>'草稿箱/已发布'))} 是否转换,action是方法名
 * {:showLink(array('action'=>'edit','id'=>$vo['id'],'type'=>'dialog','text'=>'修改'))} 使用默认的方法
 * {:showLink(array('action'=>'commentlist','id'=>$vo['id'],'type'=>'dialog','text'=>'评论'))} 自定义方法
 * {:showLink(array('action'=>'delete','type'=>'delete','id'=>$vo['id'],'text'=>'删除'))} 删除,会弹窗提示
 */
function showLink($obj){
    $Auth = checkAccess($obj['action']);
    //根据action或者名称
    if($Auth===true){
      	if (isset($obj['type'])) {
        	switch($obj['type']){
						case 'delete':
						//删除
							$html = '<button type="button" class="btn btn-danger btn-xs ajax-del" data-url="'.$obj['action'].'">'.$obj['text'].'</button>';
						break;
						//自定义
            case 'fun':
							$ext = $obj['ext']?json_encode($obj['ext']):'';
							$html = '<button type="button" orgin-data="'.$ext.'" class="btn btn-danger btn-xs btn-fun" data-url="'.$obj['action'].'">'.$obj['text'].'</button>';
            break;
						case 'link':
							//链接
							$html = "<a href='".$obj['action']."' class='btn btn-primary btn-xs' role='button'>".$obj['text']."</a>";
						break;
						case 'submit':
						//提交
							$html = "<input type='submit' class='btn btn-info' value='".$text."' />";
						break;
						default:
						//其他
							$html = "<input type='button' onclick='".$obj['type']."()' class='btn btn-primary btn-xs' value='".$obj['text']."' data-url='".$obj['action']."'/>";
						break;
					}
      	}else{
					//其他
					$ext = $obj['ext']?json_encode($obj['ext']):'';
      		$html = "<input type='button' onclick='".$obj['fun']."(".$ext.")' data-url='".$obj['action']."' class='btn btn-primary btn-xs' value='".$obj['text']."' />";
      	}
        return $html;
    }
}
//时间戳格式化
function fdate($format,$time){
	if (!$time || $time==0) {
		return '--';
	}else{
		return date($format,$time);
	}
}
//性别格式化
function getSex($sex){
	if ($sex==0) {
		return '保密';
	}elseif ($sex==1) {
		return '男';
	}elseif ($sex==2) {
		return '女';
	}else{
		return '未知';
	}
}
/**
 * 随机密码加密规则 by biner 2012-05-31
 * var password	//明文密码
 * var salt		//干扰码
 * var ver		//加密规则编号
 * 若只传$password参数,则生成加密后password并返回salt,ver
 * @return array
*/
function pw_encode($password,$salt='',$ver=''){
	$salt1 = $salt?$salt:substr(uniqid(rand()), -6);
	$ver  = $ver?$ver:rand(1,7);
	//随机加密算法
	switch($ver){
		case 1:
			$pass = sha1(md5($password).$salt1);
		break;
		case 2:
			$pass = md5($salt1.sha1($password).$password);
		break;
		case 3:
			$pass = sha1($salt1.md5($password).$salt1);
		break;
		case 4:
			$pass = md5(md5($salt1).$salt1.sha1($password));
		break;
		case 5:
			$pass = md5($salt1.$salt1.md5($password));
		break;
		case 6:
			$pass = sha1($salt1.$password.$salt1);
		break;
		case 7:
			$pass = md5(md5($password).$salt1);
		break;

	}
	if($salt && $ver){
		$result = $pass;
	}else{
		$result = array(
			'pass'       => $pass,
			'pass_salt'  => $salt1,
			'pass_ver'   => $ver,
		);
	}
	return $result;
}
function getFileList($path = '') {
	if ($path === '') {
		$path = dirname(__FILE__);
	}
	$begin = scandir($path);
	if (substr($path, 0, -1) != '/' && substr($path, 0, -1) != '\\') {
		$path .= '/';
	}
	$return = array();
	foreach ($begin as $value) {
		if ($value == '.' || $value == '..' || $value == '.DS_Store' ||  $value == '.svn') {
			continue;
		}
		if (is_file($path.$value)) {
			$return[] = $value;
		}
	}

	return $return;
}
//获取文件夹列表
function getDirList($path = '') {
	if ($path === '') {
		$path = dirname(__FILE__);
	}
	$begin = scandir($path);
	if (substr($path, 0, -1) != '/' && substr($path, 0, -1) != '\\') {
		$path .= '/';
	}
	foreach ($begin as $value) {
		if ($value == '.' || $value == '..') {
			continue;
		}
		if (is_dir($path.$value)) {
			$return[] = $value;
		}
	}

	return $return;
}
