<?php 
namespace Admin\Model;
use Think\Model\ViewModel ;

class QuestionnairesModel extends ViewModel 
{	

	protected $viewFields  = array(
		'Questionnaires' =>array('id','aliasname','name','description','start_date','expire_date','cate','_type'=>'LEFT'),
		'Questions'	=>	array('id'=>'qid','alias'=>'tihao','name'=>'qname','options','sort','_on'=>"Questionnaires.id=Questions.questionnaire_id",'_type'=>'LEFT'),
	//	'Reply' => array('id'=>'rid','userId','reply','status','createTime','_on'=>'Questionnaires.id=Reply.questionnaire_id','_type'=>'LEFT'),
	//	'Users' => array('id'=>'uid','userName','realName','tjcyzgzh','_on'=>'Reply.userId=Users.id')
	);
	
	protected $_validate = array(
		array('type', 'require', '请选择问卷类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', 'require', '请输入问卷名字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('description', 'require', '请输入问卷描述', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('expire_date', 'require', '请选择问卷过期时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);
	
	protected $_auto = array(
		array('start_date', 'date', self::MODEL_INSERT, 'function', array('Y-m-d')),
	);

	
	
	public function getList(){
		$type = (int)I('type');
		$where['type'] = array('EQ',$type);
		$list = $this->where($where)->select();
		return $list;
	}
	
	public function getQuestionnaires($questionnaireId,$rId){
		$m = M('Reply');
		$where['id'] = array('EQ',$questionnaireId);
		
		$questionInfo = $this->where($where)->find();
		$where = array();
		$fristday =	 $questionInfo['start_date'];
		$lastday = $questionInfo['expire_date'];
		
		$day = date('d',time());
		$where['userId'] = array('EQ',session('USERID'));
		$where['questionnaire_id'] = array('EQ',$questionnaireId);
		$where['id'] = array('EQ',$rId);
		$reply = $m->where($where)->find(); 
		$questionInfo['reply'] = $reply;
		return $questionInfo;
	}
	
	public function getQuestion($questionnaireId){
		$where['Questions.questionnaire_id'] = array('EQ',$questionnaireId);
		
		$data = $this->where($where)->group('qid')->select();


		$option = array();
		foreach($data as $key=>$value){
		//	$option = unserialize($value['options']); 
			$option = json_decode($value['options'],true);
		//获取问题 题型	
			$data[$key]['qtype'] = $option[1]['type'];
			$data[$key]['options'] = $option;
			
		}
		
	
		return $data;
	}
	
	//获取指定问卷,指定用户的作答
	public function getReply($questionnaireId,$userId){
		$where['Questions.questionnaire_id'] = array('EQ',$questionnaireId);
		$where['Reply.userId'] = array('EQ',$userId);
		$data = $this->field('reply')->where($where)->find();
		return json_decode($data['reply'],true);
	}
}
?>