<?php 
namespace Admin\Model;
use Think\Model\RelationModel;
class UsersModel extends RelationModel{
	 protected $_link = array(
		'Company' => array(
			'mapping_type'  => self::HAS_ONE  ,
			'foreign_key'   => 'userId',
			
		)
	 
	 );
	 
	 
	 
	 
	 public function getList(){
		 $where = array();
		 
		$userList =  $this->where($where)->select();
		return $userList;	
	 }

	 
	
}




?>