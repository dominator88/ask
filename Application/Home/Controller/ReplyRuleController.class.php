<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class ReplyRuleController extends BaseController{
	

	
	
	public function validata(){
		$userid = session("USERID");
		
		$m = D('Admin/ReplyRule');
		$mcom = D('Admin/Company');
		$mrep = D('Admin/Reply');
		$questionid = I('get.questionid');
		$rid = I('get.rId');
		$com = $mcom->getInfo($userid,'now');
		$last = $mrep->getInfo($questionid,$userid,'last');
		$before = $mrep->getInfo($questionid,$userid,'before');
		$ruleData = $m->ruleList();
		$last = json_decode($last['reply'],true);
		$before = json_decode($before['reply'],true);

		$data = I();
		$outMsg = array();
		$data['91'] = json_decode(htmlspecialchars_decode($data['91']),true);
		$data['103'] = json_decode(htmlspecialchars_decode($data['103']),true);

		foreach($data as $key=>$value){
			if(is_array($value)){
				foreach($value as $k=>$v){
					if($v!=''){
						$tmpkey = 'now'.$key.$k;
						$$tmpkey = $v;
					}
				}
			}else{
				if($value!=''){
					$tmpkey = 'now'.$key;
					$$tmpkey = $value;
				}
			}
		}


		foreach($ruleData as $key=>$value){			
			$str .= 'if('.$value["exp"].'){$outMsg[] = array("code"=>"'.$value["code"].'","type"=>"'.substr($ruleData[$key]["msg"],0,strpos($ruleData[$key]["msg"],"，")).'","num"=>"'.$value['num'].'","msg"=>"'.str_replace(substr($ruleData[$key]["msg"],0,strpos($ruleData[$key]["msg"],"，")).'，','',$ruleData[$key]["msg"]).'");}';		
		}
		
		$str =   preg_replace('/IF\(/U','validata_if(',$str);
		$str =   preg_replace('/OR\(/U','validata_or(',$str);
		$str =   preg_replace('/AND\(/U','validata_and(',$str);
//		eval('if(validate_and(LEN($fddbr)>=2,NOTNULL($fddbr))){$outMsg[] = array("type"=>substr(错误，法人姓名非法！,0,strpos(错误，法人姓名非法！,"，")),"msg"=>错误，法人姓名非法！);}');
		 $str = str_replace(C('Q_SOURCE_FIELD'),C('Q_TARGET_FIELD'),$str);
		$str = preg_replace('/TQSJ\(\$now([\d\D]+)\)/U','$last[$1]',$str);
		$str = preg_replace('/BEFORE\(\$now([\d\D]+)\)/U','$before[$1]',$str);
//echo validata_if($now90==3,NOTNULL($now912));
//echo NOTNULL($now911);
		eval($str);		
			//备注信息
		if($rid){
			$remark = M('replyremark')->where('replyid='.$rid)->find();
			$remarks = json_decode(htmlspecialchars_decode($remark['content']),true);
		
			$this->assign('remarks',$remarks);
		}
		$this->assign('outmsg',$outMsg);
		$content = $this->fetch();
	//		echo validata_and();	
		$data = array();
		$data['status'] = 0;
		$cw = $jg = $ts = $flag =  0;
		foreach($outMsg as $k=>$v){
			//当有错误信息时，$falg设为2
			if($v['type'] == '错误'){$data['status'] =2;$flag = 2;$cw++;}
			//当存在警告信息,并且$flag未设置过，就将$falg设为1
				if($v['type'] == '警告'){$jg++;if(!$flag){$flag=1;}} 
				if($v['type'] == '提示'){$ts++;if(!$flag){$flag=1;} }
			
		
		}
	
		
		
		if($flag){
			$data['status'] = $flag;
		}
		$data['statusInfo'] = array('cw'=>$cw,'jg'=>$jg,'ts'=>$ts);
		
		$data['content'] = $content;
		//验证入库
/*		$m = M('Replyvalidata');
		
		$id = $m->where('rid='.$rid)->getField('id');
		if($id){
			$replydata['id'] = $id;
			$replydata['rid'] = $rid;
			$replydata['content'] = json_encode($content);
			$m->save($replydata);
			
		}else{
			$m->add($replydata);
		}
*/		
		$this->ajaxReturn($data);
		
		/*if($outMsg){
			$this->assign('outmsg',$outMsg);
			$content = $this->fetch();
			echo $content;
		}else{
			echo 'success';
		}
	*/
	}
	
}
?>