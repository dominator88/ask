<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class CompanyRuleController extends BaseController{
	
	
	
	
	public function validata(){
		$userid = session("USERID");
	
		$m = D('Admin/CompanyRule');
		$mcom = D('Admin/Company');
		//行业分类集合
		$codearr = M('Class')->getField('id,code');
		//行政区域代码集合
		$qharr = C('QHDM');
		
		$last = $mcom->getInfo($userid,'last');
		if($last){
			$last['zyywhd'] = json_decode(htmlspecialchars_decode($last['zyywhd']),true);
			$last['dwszd'] = json_decode(htmlspecialchars_decode($last['dwszd']),true);
			$last['zyjjzb'] = json_decode(htmlspecialchars_decode($last['zyjjzb']),true);
		}

	//去年数据	
		foreach($last as $key=>$value){
			if(is_array($value)){
				foreach($value as $k=>$v){
					$keytmp = $key.$k;
					$last[$keytmp] = $v;
				}
			}
		}		
	
	//	$com = $mcom->getInfo($userid,'now');
		$com = array();	
		$com = I();
		
		//测试
		
		
		//获取excel表规则
		$ruleData = $m->ruleList(); 
		//$data = R('Admin/Reply/getReplyInfo');
		$com['zyywhd'] = json_decode(htmlspecialchars_decode($com['zyywhd']),true);
		$com['dwszd'] = json_decode(htmlspecialchars_decode($com['dwszd']),true);
		$com['zyjjzb'] = json_decode(htmlspecialchars_decode($com['zyjjzb']),true);

		foreach($com as $key=>$value){
			if(is_array($value)){
				foreach($value as $k=>$v){
					$keytmp = $key.$k;
					$$keytmp = $v;
				}
			}else{
				$$key = $value;
			}
		}

		$outMsg = array();
		//print_r(I());

		
		
		
		foreach($ruleData as $key=>$value){			
		
			$str .= 'if('.$value["exp"].'){$outMsg[] = array("code"=>"'.$value["code"].'","type"=>"'.substr($ruleData[$key]["msg"],0,strpos($ruleData[$key]["msg"],"，")).'","num"=>"'.$value['num'].'","msg"=>"'.$ruleData[$key]["msg"].'","arr"=>\''.$value['arr'].'\');}';	
			
						
		}
		
		
		$str =   preg_replace('/IF\(/U','validata_if(',$str);
		$str =   preg_replace('/OR\(/U','validata_or(',$str);
		$str =   preg_replace('/AND\(/U','validata_and(',$str);
		
//		eval('if(validate_and(LEN($fddbr)>=2,NOTNULL($fddbr))){$outMsg[] = array("type"=>substr(错误，法人姓名非法！,0,strpos(错误，法人姓名非法！,"，")),"msg"=>错误，法人姓名非法！);}');
		 $str = str_replace(C('COM_SOURCE_FIELD'),C('COM_TARGET_FIELD'),$str);
		 $str = preg_replace('/TQSJ\(\$([\d\D]+)\)/U','$last[$1]',$str);
//echo validata_if($now89!=3,$now98!=6);
		//echo validata_if((int)LEFT($dianhua,1)==0);
		//echo LEFT($dianhua,1)==2;
//echo validata_and($zyjjzb2<10*$last['zyjjzb2'],$zyjjzb2>0.1*$last['zyjjzb2']);
		
		// if(LEFT($dianhua,1)==1){echo 'true';}else{echo 'false';};
		//validata_if(LEFT($dianhua,1)!=0);
//echo validata_if(NOTNULL($zyywhd1),INARRAY($zyywhd1,$codearr));
//echo validata_and(ABS($qmcyrs - $last['qmcyrs'])<500);
		eval($str);	
		//print_r(validata_if(NOTNULL($hydm),INARRAY($hydm,$codearr) == 1));

//	echo (int)($zyjjzb0< $qmcyrs*2);
		$userid = session("USERID");
		$date = F('date');

		$map['userId'] = array('EQ', $userid);
		$map['updateTime'] = array('BETWEEN',$date['start_date'].','.$date['expire_date']);

		//	$user = $m->where($where)->find();
		
		//查找在填报时间填报过的企业
		$remark = $mcom->where($map)->getField('shuoming');	
		$remarks = json_decode(htmlspecialchars_decode($remark),true);
		$this->assign('remarks',$remarks);


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

		if($flag && $data['status']!=2){
			$data['status'] = 1;
		}
		$data['statusInfo'] = array('cw'=>$cw,'jg'=>$jg,'ts'=>$ts);
		
//echo $last[zyjjzb2];echo $zyjjzb2;
		
		$data['content'] = $content;
	
		$this->ajaxReturn($data);
	
	}
	
}