<?php 
namespace Admin\Model;
use Think\Model;


// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.tpask.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: MR.Z <327778155@qq.com>
// +----------------------------------------------------------------------
class AnnouncementModel extends Model{
	
	function lists(){
		//$where = array();
		$lists = $this->select();
		return $lists;
	}
	
	public function addData($data){
		
		$this->create();
		//$data['content'] = htmlspecialchars($data['content']);
		$id = $this->add($data);
		return $id;
	}
	
	public function edit($data){
		//$data['content'] = htmlspecialchars($data['content']);
	
		$flag = $this->save($data);
		return $flag;
	
	}
	
	public function deleteId($id){
		$flag = $this->delete($id);
		return $flag;
	}
	
	public function getinfoById($id){
		$info = $this->where('id='.$id)->find();
		return $info;
	}
	
	
	
	
}
?>