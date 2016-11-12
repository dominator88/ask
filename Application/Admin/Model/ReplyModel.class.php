<?php 
namespace Admin\Model;
use Think\Model\ViewModel;

class ReplyModel extends ViewModel 
{
	protected $viewFields  = array(
		'Reply' => array('id'=>'rid','userId','reply','status','createTime','_type'=>'LEFT'),
		'Questionnaires' =>array('id','aliasname','name','description','start_date','expire_date','cate','_on'=>'Reply.questionnaire_id=Questionnaires.id'),
	//	'Questions'	=>	array('id'=>'qid','name'=>'qname','options','sort','_on'=>"Questionnaires.id=Questions.questionnaire_id",'_type'=>'LEFT'),
		
	//	'Users' => array('id'=>'uid','userName','realName','tjcyzgzh','_on'=>'Reply.userId=Users.id')
	);
	
	public function getInfo($questionnaire_id,$userid,$expectstr){
		
		$where['questionnaire_id'] = array('EQ',$questionnaire_id);
		$where['userId'] = array('EQ',$userid);
		
		switch($expectstr){
			case 'now':
				$fristday =	 strtotime(date('Y-m-01', time()));
				$lastday = strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')));
				break;
			case  'last':
				$time = strtotime('-1 month',time() );
				$fristday =	 strtotime(date('Y-m-01', $time));
				$lastday = strtotime(date('Y-m-d', strtotime(date('Y-m-01', $time) . ' +1 month -1 day')));
				
				break;
			case 'before':
				$time = strtotime('-2 month',time() );
				$fristday =	 strtotime(date('Y-m-01', $time));
				$lastday = strtotime(date('Y-m-d', strtotime(date('Y-m-01', $time) . ' +1 month -1 day')));
				break;
	
		}
		$where['createTime'] = array('BETWEEN',array($fristday,$lastday));

		$info = array();
		$info = $this->where($where)->find();
		return $info;
	}
}