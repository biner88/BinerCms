<?php
namespace Admin\Model;
use Common\Model\CommonModel;

class SystemMenuModel extends CommonModel {
	public function opNode($map){
		switch($map['type']){
		//下移,上移
		case 'next':case 'prev':
			//获取目标sort值
			$tar = $this->Field('parent_id,sort')->where( array('id'=>$map['targetid']) )->find();
			//编号初始化
			if($this->where( array('parent_id'=>$map['pid']) )->min('sort')==$tar['sort']){
				$tar['sort']=1;
			}
			//设置本身为目标tar
			$this->where( array('id'=>$map['id']) )->setField($tar);
			//更新所有目标
			if($map['type']=='next'){
				$info = $this->where(array('parent_id'=>$tar['parent_id'],'sort'=>array('elt',$tar['sort']),'id'=>array('neq',$map['id'])))->order('sort desc')->select();
				foreach($info as $k=>$v){
					$this->where( array('id'=>$map['id']) )->setField('sort',($tar['sort']-($k+1)));
				}
			}else{
				$info = $this->where(array('parent_id'=>$tar['parent_id'],'sort'=>array('egt',$tar['sort']),'id'=>array('neq',$map['id'])))->order('sort asc')->select();
				foreach($info as $k=>$v){
					$model->where(array('id'=>$map['id']))->setField('sort',($tar['sort']+($k+1)));
				}
			}
			return true;
		break;
		//隐显,禁用启用
		case 'display':
			$this->where(array('id'=>$map['id']))->setField($map['type'],($map['st']?0:1));
			return true;
		break;
		case 'inner':
			$this->where(array('id'=>$map['id']))->setField('parent_id',$map['targetid']);
			return true;
		break;
		}
		return false;
	}
}
?>
