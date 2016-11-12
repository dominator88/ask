<?php 
namespace Admin\Model;
use Think\Model;

class CompanyModel extends Model{
	
	//可以获取今年,去年，前年的企业填表信息
	public function getInfo($userid,$expectstr){
	
		$where['userId'] = array('EQ',$userid);
		$expect = array('now','last','before');
		if(in_array($expectstr,$expect)){
			switch ($expectstr){
				case 'now':
					$time = date('Y',time());
					$where['updateTime'] = array('BETWEEN',$time.'-01-01'.','.date('Y-m-d',strtotime(' +1year',strtotime($time.'-01-01'))));
					break;
				case 'last':
					$time = date('Y',time()) - 1;
					$where['updateTime'] = array('BETWEEN',$time.'-01-01'.','.date('Y-m-d',strtotime(' +1year',strtotime($time.'-01-01'))));
					break;
				case 'before':
					$time = date('Y',time()) - 2;
					$where['updateTime'] = array('BETWEEN',$time.'-01-01'.','.date('Y-m-d',strtotime(' +1year',strtotime($time.'-01-01'))));
					break;
			}
			
		}
		$reuslt = $this->where($where)->find();

		return $reuslt;
		
	}
	
	public function getInfoById($id){
		$info =  $this->where('id='.$id)->find();
		return $info;
	}
	
	
	
}