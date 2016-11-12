<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class IndexController extends BaseController{
	
	public function _initialize (){
		$userid = session('USERID');
		if(__SELF__!= '/index.php/Home/Index/logout.html'){
			
		
			if(empty($userid) && strtolower(__SELF__) != strtolower('/index.php/Home/Index/Login.html')){$this->error('请登录后再进行操作',U('/Home/Index/Login'));}
			$where['id'] = array('EQ',$userid);
			$user = M('Users')->where($where)->find();
			if($user && strtolower(__SELF__) != strtolower('/index.php/Home/Index/userCenter.html') ){
		//		print_r($user);
				if($user['userpassword'] == md5($user['username'])){ $this->error('请先更改密码再进行操作',U('/Home/Index/userCenter'));}
			}
		}
		
	}
	
	//默认主页
	public function index(){
		$userId = session("USERID");
		
//print_r($class->arr);
		if(isset($_SESSION["USERID"])){

			
		
			$this->display();
		}else{
			$this->redirect('Home/Index/login');
		}	
	}
	
	public function help(){
		$this->display();
		
	}
	
	
	//用户中心  个人资料
	public function userCenter(){
		
		
		$m = D("Admin/Users");
		
		$userid = session("USERID");
		$where['id']  = $userid;
	
	//	$this->assign('class',$strout);
		
	if(IS_GET){										
			

		$user = $m->where($where)->find();
		//如果
		//print_r($company);
	//	print_r($user['Company']);
					
		$this->assign('user',$user);
		$this->display();
	}elseif(IS_POST){
			
			
			$password = I('password');
			$confrimPassword = I('confrimPassword');
			//$m->field('realName,email,telephone,subphone,mobilephone,tjcyzgzh,userPassword')->create();print_r( $where);exit;
			$data['realname'] = I('realname');
			$data['email'] = I('email');
			$data['telephone'] = I('telephone');
			$data['subphone'] = I('subphone');
			$data['mobilephone'] = I('mobilephone');
			$data['tjcyzgzh'] = I('tjcyzgzh');
			
			
			
			//获取本年度的企业基本信息是否有填写
		//	$time = date('Y',time());
		//	$where['updateTime'] = array('BETWEEN',$time.'-01-01'.','.date('Y-m-d',strtotime(' +1year',strtotime($time.'-01-01'))));
			
		//	$user = $m->relation(true)->where($where)->find();
		
			
			//验证密码
			if(!empty($password)   ){
				if($password === $confrimPassword){$data['userPassword'] = md5($password);}else{$this->error('两次输入密码不相同');}
				
				if(strlen($password) < 8){$this->error('密码长度不得少于8位');}
			}
			//验证姓名为中文
			if(!preg_match("/^[\x7f-\xff]+$/",$data['realname']))$this->error("您的真实姓名必须为中文，请重新填写");
			//验证固话
			if(!preg_match("/^(0(10|21|22|23|[1-9][0-9]{2})(-|))?[0-9]{7,8}$/",$data['telephone'])){
				$this->error("固话填写不正确，请重新填写");
			}
			//验证手机号
			if(!preg_match('/^1[0-9]{10}$$/',$data['mobilephone']) && !empty($data['mobilephone'])){
				$this->error("手机号填写不正确,请重新填写");
			}
			
			
			//验证用户名
			if(empty($data['realname'])){$this->error('必填字段 真实姓名 不能为空');}
			if(empty($data['telephone'])){$this->error('必填字段 电话 不能为空');}
			if(!isset($data['tjcyzgzh'] ) || $data['tjcyzgzh']==''){$this->error('必填字段 统计从业资格证号 不能为空');}
			
	
			$flag = $m->where($where)->save($data);
			$msg = '个人资料更新成功';
				
			
			
			if($flag ){
				$this->success($msg);
			}else{
				$this->error('个人资料更新失败');
			}
		}
	}
	
	
	public function company(){
		$m = D("Admin/Company");
		
		$userid = session("USERID");
		$where['userId']  = $userid;
	
	//填报时间区间
		$date = F('date');
		$this->assign('date',$date);
		
		
		$map['userId'] = array('EQ', $userid);
		$map['updateTime'] = array('BETWEEN',$date['start_date'].','.$date['expire_date']);

		//	$user = $m->where($where)->find();
		
		//查找在填报时间填报过的企业
		$company = $m->where($map)->find();	

	/***************分类代码start********************/
		$arr = M('Class')->select();
		
		$class =  new \Think\Tree($arr);
		
	
		$classarr = $class->getsubcat(); 
		$parent = array();
	//	$sub = array();
		$i = 0;
		$tmpp = array();
		foreach($classarr[1]['childs'] as $key=>$value){
			$tmpp[$value['code']] = $value['catname'];//等到有行业代码了，会将id改为code
			$parent[] = json_encode($tmpp);

			$tmp = array();		
			foreach($value['childs'] as $k=>$v){

				$tmp[$v['code']] = $v['catname'];
				$sub[$i][] =json_encode($tmp);
				$tmp = array();
				
			}
			$tmpp = array();
			$sub[$i] = '['.implode(',',$sub[$i]).'],';
			$i++;
		}

		$parentstr = '['.implode(',',$parent).']';

		foreach($sub as $k=>$v){
			$substr .= "'a_".$k."':".$v;
		}
		$this->assign('parentstr',$parentstr);
		$this->assign('substr',$substr);
/***************分类代码end********************/
	
	//	$this->assign('class',$strout);
		
		if(IS_GET){
			
		
			

			if(!empty($company)){
				$user['Company'] = $company;
				//格式化企业信息中的json字段
				$user['Company']['dwszd'] = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
				$user['Company']['zyjjzb'] = json_decode(htmlspecialchars_decode($user['Company']['zyjjzb']),true);
				$user['Company']['zyywhd'] = json_decode(htmlspecialchars_decode($user['Company']['zyywhd']),true);
				$user['Company']['yupdatetime'] = date('Y',time());
				
			}else{
				
			//如果company 为空，则说明今年未填企业信息，便取最近一年的信息进行预填		
				$company = M('Company')->where('userid='.$userid)->order('updateTime desc')->find();
				$user['Company']['zzjgdm'] = $company['zzjgdm'];
				$user['Company']['dwxxmc'] = $company['dwxxmc'];
				$user['Company']['shxydm'] = $company['shxydm'];
				$user['Company']['qydm'] = $company['qydm'];
				$user['Company']['hydm'] = $company['hydm'];
				$duser = M('Users')->where('id='.$userid)->find();
				$user['Company']['lxrxm'] = $duser['realname'];
				$user['Company']['lxdianhua'] = $duser['telephone'];
				
			}

	
			
		
			$this->assign('QHDM', C('QHDM'));
			$this->assign('user',$user);
			$this->display();
		}elseif(IS_POST){		
		//更新公司信息
				
				if(date('Y-m-d',time()) < $date['start_date'] || date('Y-m-d',time()) > $date['expire_date']){ $this->error("当前时间不在填报时间内，现在不能填报表！");}
				$submitStatus = I('get.status')?I('get.status'):1;

				//$data = $m->where($where)->find();
				
				$data['Company']['status'] = $submitStatus;
				$data['Company']['dwxxmc'] = I('dwxxmc');
				$data['Company']['zzjgdm'] = I('zzjgdm');
				$data['Company']['hydm'] = I('hydm');
				$data['Company']['shxydm'] = I('shxydm');
				$data['Company']['fddbr'] = I('fddbr');
				$data['Company']['dianhua'] = I('dianhua');
				$data['Company']['qydm'] = I('qydm');
				$data['Company']['yzbm'] = I('yzbm');
				$data['Company']['qmcyrs'] = I('qmcyrs');
				$data['Company']['dwszd'] = I('dwszd');
				$data['Company']['zyjjzb'] = I('zyjjzb');
				$data['Company']['sfck'] = I('sfck');
				$data['Company']['sfss'] = I('sfss');
				$data['Company']['sfgykg'] = I('sfgykg');
				$data['Company']['dwgm'] = I('dwgm');
				$data['Company']['djzclx'] = I('djzclx');
				$data['Company']['zyywhd'] = I('zyywhd');
				$data['Company']['lxrxm'] = I('lxrxm');
				$data['Company']['zw'] = I('zw');
				$data['Company']['lxdianhua'] = I('lxdianhua');
				//验证说明信息
				$data['Company']['shuoming'] = I('shuoming');
				//验证信息
				$data['Company']['validata'] = I('validatas');
				$data['Company']['updateTime'] = date('Y-m-d',time());
				$data['Company']['yupdatetime'] = date('Y',time());
			
			
		//获取本年度的企业基本信息是否有填写

		
			
			//如果企业信息存在
				if($company){
					
					$flag = $m->where($map)->save($data['Company']);
					
				}else{
					$data['Company']['userId'] = $userid;
					$flag = $m->add($data['Company']);
					
				}
				if($submitStatus==1){			
						$msg = '年报资料保存成功';
				}elseif($submitStatus==2){
						$msg = '年报资料提交成功';
				}
			
			
			if($flag ){
				$this->success($msg);
			}else{
				$this->error('更新失败');
			}
		}
	}
	
	

	//ajax验证说明信息的字段长度  已在前端处理
	public function validatashuoming(){
		$shuoming =	 json_decode(htmlspecialchars_decode( I('post.shuoming')),true);
		$data['status'] = 0;
		foreach($shuoming as $k=>$v){
			$flag = preg_match("/^[\x{4e00}-\x{9fa5}]{6,}/u",$v);
			if(!$flag){$data['status'] = 1;break;}
		}
		$this->ajaxReturn($data);
	}
	
	public function cancelCompany(){
		$date = F('date');
		$userid = session('USERID');
		$map['userId'] = array('EQ', $userid);
		$map['updateTime'] = array('BETWEEN',$date['start_date'].','.$date['expire_date']);
		
		$data['status'] = 1;
		$m = M('Company');
		$flag = $m->where($map)->save($data);
		if($flag){
			$this->success("撤销成功",U('Home/Index/Company'));
		}else{
			$this->error("撤销失败",U('Home/Index/Company'));
		}
	}
	
/*	public function edit(){
		$userId = session("USERID");
		$data['realName'] = I('realName');
		$data['email'] = I('email');
		$data['telephone'] = I('telephone');
		$data['subphone'] = I('subphone');
		$data['mobilephone'] = I('mobilephone');
		$data['tjcyzgzh'] = I('tjcyzgzh');
		$m = D('Admin/Users');
		$flag = $m->relation(true)->where(array('id'=>$userId))->save($data);
		
		 if($flag){
			 $this->success('更新成功');			 
		 }else{
			 $this->error('更新失败');
		 }
	}
*/	
	
	//用户登录页面
	public function login(){
		$m = D('Admin/Users');
		
		if(IS_GET){	
			$userId = session('USERID');		
			if(isset($_SESSION["USERID"])){
				$this->redirect('Home/Index/userCenter');
			}else{
				$this->display();
			}
		}elseif(IS_POST){
			$userName = I('userName');
			$password = I('password');
			$where['userName'] = array('EQ',$userName);
			$where['userPassword'] = array('EQ',md5($password));
			$user = $m->where($where)->find();
			
			if($user){
				session('USERID',$user['id']);
				session('USERNAME',$userName);
				$md5UserName = md5($userName);
				
				if($user['userpassword'] == $md5UserName){
					$this->success('请先修改密码再进行操作',U('Home/Index/userCenter'));
				}else{
					$this->success('登录成功',U('Home/Index/newQuestionnaires'));
				}
			}else{
				$this->error('登录失败');
			}
		}
	}
	
	public function logout(){
		session('USERID',null);
		redirect('/index.php/Home/Index','2','页面跳转中');
		
	}
	
	//用户注册
	public function regist(){
		
	}
	
	//修改密码
	public function editPass(){
		
	}
	
	
	
/*******************试卷部分*************************/
	
	//最新试卷列表
	public function newQuestionnaires(){
		//header("Location: /index.php/Home/Index/index", true,302);
		
		$m = D('Admin/Questionnaires');
		$userid = session('USERID');
		//获取当月第一天和最后一天
	/*	$day = array();
		$day['frist'] =	 strtotime(date('Y-m-01', time()));
		$day['last'] = strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')));
	*/	$date = F('date');	
		$subQuery = $m->order('id desc')->select(false);
		
		$count = count(M()->table($subQuery.' question')->group('id')->select());
		$pager = new \Think\Page($count,10);
		$pages = $pager->show(); 
		
		
		$data = M()->table($subQuery.' question')->group('id')->order('id desc')->limit($pager->firstRow,$pager->listRows)->getField('id,aliasname,name,start_date,expire_date'); 
	
		$where['userId'] = array('EQ',$userid);
		$fristday =	 strtotime(date('Y-m-01', time()));
		$lastday = strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')));
		$where['createTime'] = array('BETWEEN',$fristday.','.$lastday);
		$answer = M('Reply')->where($where)->getField('questionnaire_id,id as rid,reply,status,createTime');
		$nowtime = date('Y-m-d',time());
	//	$newarr = array();
		foreach($data as $k=>$v){
			$data[$k]['reply'] = $answer[$k]['reply'];
			$data[$k]['status'] = $answer[$k]['status'];
			$data[$k]['createtime'] = $answer[$k]['createtime'];
			$data[$k]['rid'] = $answer[$k]['rid'];
		//	$newarr[$v['id']]['reply'] = $newanswer[$v['id']]
		}
		

		//获取年报信息
		$u = D('Admin/Users');
		$com = D('Admin/Company');
		$where = array();
	$where['updateTime'] = array('BETWEEN',$date[start_date].','.$date[expire_date]);
		$where['userId'] = $userid;
		$user['Company'] = $com->where($where)->find();
	//	$user = $u->relation(true)->where($where)->find();
	
	//公告
		$manno = D('Admin/Announcement');
		$annolist = $manno->order('top desc,recommend desc,createtime desc')->select();
		
		$this->assign('annolist',$annolist);
	
	
	
		$this->assign('user',$user);
		$this->assign('pages',$pages);
		$this->assign('date',$date);
		$this->assign('data',$data);
		$this->assign('nowtime',$nowtime);
		$this->display();
	}
	
	//历史试卷列表
	public function historyQuestionnaires(){
		$m = D('Admin/Questionnaires');
		$subQuery = $m->order('id desc')->select(false);
		
		$count = count(M()->table($subQuery.' question')->group('id')->select());
		$pager = new \Think\Page($count,2);
		$pages = $pager->show(); 
		
		$data = M()->table($subQuery.' question')->group('id')->order('id desc')->limit($pager->firstRow,$pager->listRows)->getField('id,aliasname,name,start_date,expire_date'); 
		$this->assign('pages',$pages);	
		$this->assign('data',$data);


		$this->display();
	}
	
	public function listHistory(){
		$m = D('Admin/Reply');
		
		$questionnaireId = (int)I('get.questionnaireId');
		$where['Reply.userId'] = array('EQ',session("USERID"));
	//	$where['Reply.status'] = array('EQ',2);
		$where['Questionnaires.id'] = array('EQ',$questionnaireId);
	
		$time = I('post.createTime'); 
		$season = $this->countSeason('2016-06',date('Y-m',time()));
		rsort($season);
		if(IS_POST){
			$where['createTime'] = array('BETWEEN',strtotime($time).','.strtotime($time." +1month"));			
		}
		
		$count = count($m->where($where)->group('rid')->select());

		$pager = new \Think\Page($count,10);
		$pages = $pager->show();
		
		$page = $m->where($where)->limit($pager->firstRow,$pager->listRows)->select();
	
		$this->assign('season',$season);
		$this->assign('Page',$page);
		$this->assign('pages',$pages);
		$this->display();
	}
	
	
	public function listHistoryCompany(){
		
		$mcom = M('Company');
		$where['userId'] = array('EQ',session("USERID"));
		$time = I('post.updateTime'); 
		$season = $this->countYear('2015',date('Y',time()));
		rsort($season);
		if(IS_POST){
			$where['updateTime'] = array('BETWEEN',$time.'-01-01,'.date('Y-m-d',strtotime($time."-01 +1year")));			
		}
		
		$count = count($mcom->where($where)->select());
	//	echo $count;
		$pager = new \Think\Page($count,10);
		$pages = $pager->show();
		
		$page = $mcom->where($where)->limit($pager->firstRow,$pager->listRows)->select();
		$this->assign('season',$season);
		$this->assign('Page',$page);
		$this->assign('pages',$pages);
		$this->display();
	}
	
	public function HistoryCompany(){
	//	$m = D("Admin/Users");
		$where['id'] = array('EQ',I('id'));
		$user['Company'] = M('Company')->where($where)->find();

		$user['Company']['dwszd'] = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
		$user['Company']['zyjjzb'] = json_decode(htmlspecialchars_decode($user['Company']['zyjjzb']),true);
		$user['Company']['zyywhd'] = json_decode(htmlspecialchars_decode($user['Company']['zyywhd']),true);
		$user['Company']['yupdatetime'] = date('Y',strtotime($user['Company']['updatetime']));
		$users = M('Users')->where('id='.$user['Company']['userid'])->find();
		$user = array_merge($users,$user);
		$this->assign('user',$user);
		$this->display();
	}
	
	public function exportCompany(){
		$where['id'] = array('EQ',I('id'));
		$user['Company'] = M('Company')->where($where)->find();

		$user['Company']['dwszd'] = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
		$user['Company']['zyjjzb'] = json_decode(htmlspecialchars_decode($user['Company']['zyjjzb']),true);
		$user['Company']['zyywhd'] = json_decode(htmlspecialchars_decode($user['Company']['zyywhd']),true);
		$user['Company']['yupdatetime'] = date('Y',strtotime($user['Company']['updatetime']));
		$users = M('Users')->where('id='.$user['Company']['userid'])->find();
		$user = array_merge($users,$user);
		$this->assign('user',$user);
		
		$content = $this->fetch('form/company');
		$fileContent = getWordDocument($content);
		$filename = "./Public/company_".I('id').".doc";
		$fp = fopen($filename, 'w+') or die("文件打开失败");
		fwrite($fp, $fileContent);
		fclose($fp);
		

		$fp = fopen($filename, 'r') or die("文件打开失败");
		$size = filesize($filename);
		
		header("Content-type: application/doc");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".$size);
		header("Content-Disposition:attachment;filename=企业基本情况表.doc"); 
		echo  fread($fp, $size);
		
		
	}
	
	
	private function countSeason($start,$end){
		$temp = date("Y-m",strtotime("$start +1month"));
		while ($temp <= $end){
			$time[] = $temp;
			$temp = date("Y-m",strtotime("$temp +1month"));
		}
		
		return $time;
	}
	
	private function countYear($start,$end){
		$temp = date("Y",strtotime("$start-01 +1year"));

		while ($temp <= $end){
			$time[] = $temp;
			$temp = date("Y",strtotime("$temp-01 +1year"));
		}
		
		return $time;
	}
	
	
	//进行答题
	public function toQuestion(){
		//如果来源页面是从历史问卷中进入，则只显示一个返回的按钮
		$refurl =  I('server.HTTP_REFERER');
		if(strpos($refurl,'listHistory')!==false){
			$this->assign('history',1);
		}
		
		
		$questionnaireId = (int)I('questionnaireId');
		$rId = (int)I('rId');
		$userId = session('USERID');
		
	//	$userId = 9; //测试数据userId = 9;
		$m = D('Admin/Questionnaires');
		$mreply = M('Reply');
		$where['Questions.questionnaire_id'] = $questionnaireId;
		$data = $m->getQuestion($questionnaireId);
	
		$questionInfo = $m->getQuestionnaires($questionnaireId,$rId);

		$mu = D('Admin/Users');
		$user = $mu->relation(true)->where("id=".$userId)->find();
		$dwszdarr = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
		$user['Company']['dwszd'] = $dwszdarr[0].$dwszdarr[1].$dwszdarr[2].$dwszdarr[3].$dwszdarr[4].$dwszdarr[5];
		$map['id'] = array('EQ',$rId);
		$answer = json_decode($mreply->where($map)->getField('reply'),true); 
		$remark = M('replyremark')->where('replyid='.$rId)->find();
		$remarks = json_decode(htmlspecialchars_decode($remark['content']),true);

		$questionInfo['remark'] = htmlspecialchars_decode($remark['content']);
		if($answer){
	//如果指定问题已经设置了答题，就将答案列在相应的选项内	
			foreach($data as $k=>$v){
				if($v['qtype'] == 'checkbox' || $v['qtype'] == 'radio'){
				//获取到被选中答案的键值	
				//	print_r( $v);
					$indexarr = explode(',',$answer[$v['qid']]);
					$data[$k]['answer'] = $answer[$v['qid']];
				//给指定的键值添加一个属性selected	
					foreach($indexarr as $key=>$index){
						//指定第九项为特殊项
						if($index!=9){
							$data[$k]['options'][$index]['selected'] = 1;
						}else{
							
							$index = str_replace('other','',$index);
							$data[$k]['options'][$index]['text'] = $answer[othertext.$v['qid']];
							$data[$k]['options'][$index]['selected'] = 1;
						}
					}
					
					
				}elseif($v['qtype'] == 'text'){
				//填空题答案赋予	
					$indexarr = json_decode(htmlspecialchars_decode($answer[$v['qid']]),true);
//		print_r($indexarr);
					$data[$k]['answer'] = json_encode($indexarr,JSON_FORCE_OBJECT);
				//	foreach($indexarr as $key=>$index){
				//		$data[$k]['options'][$key]['value'] = $index;
						
				//	}
					foreach($data[$k]['options'] as $key=>$value){
						$data[$k]['options'][$key]['value'] = $indexarr[$key];
					}
					
				}
			//	if($v['tihao']){$data[$k]['remark'] = $remarks[$v['tihao']];}
			}
		}
		$nowdate = date('d',time());
		$questionInfo['nowdate'] = $nowdate;
		$this->assign('questionInfo',$questionInfo);
		$this->assign('user',$user);
		$this->assign('data',$data);
		$this->display();
	
	//	$fileContent = $this->fetch();
	//	$fp = fopen("test.doc", 'w');
	//	fwrite($fp, $fileContent);
	//	fclose($fp);

	}

	public function exportReply(){
		
		$questionnaireId = (int)I('questionnaireId');
		$rId = (int)I('rId');
		$userId = session('USERID');
		
	//	$userId = 9; //测试数据userId = 9;
		$m = D('Admin/Questionnaires');
		$mreply = M('Reply');
		$where['Questions.questionnaire_id'] = $questionnaireId;
		$data = $m->getQuestion($questionnaireId);
	
		$questionInfo = $m->getQuestionnaires($questionnaireId,$rId);

		$mu = D('Admin/Users');
		$user = $mu->relation(true)->where("id=".$userId)->find();
		$dwszdarr = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
		$user['Company']['dwszd'] = $dwszdarr[0].$dwszdarr[1].$dwszdarr[2].$dwszdarr[3].$dwszdarr[4].$dwszdarr[5];
		$map['id'] = array('EQ',$rId);
		$answer = json_decode($mreply->where($map)->getField('reply'),true); 
		$remark = M('replyremark')->where('replyid='.$rId)->find();
		$remarks = json_decode(htmlspecialchars_decode($remark['content']),true);

		$questionInfo['remark'] = htmlspecialchars_decode($remark['content']);
		if($answer){
	//如果指定问题已经设置了答题，就将答案列在相应的选项内	
			foreach($data as $k=>$v){
				if($v['qtype'] == 'checkbox' || $v['qtype'] == 'radio'){
				//获取到被选中答案的键值	
				//	print_r( $v);
					$indexarr = explode(',',$answer[$v['qid']]);
					$data[$k]['answer'] = $answer[$v['qid']];
				//给指定的键值添加一个属性selected	
					foreach($indexarr as $key=>$index){
						//指定第九项为特殊项
						if($index!=9){
							$data[$k]['options'][$index]['selected'] = 1;
						}else{
							
							$index = str_replace('other','',$index);
							$data[$k]['options'][$index]['text'] = $answer[othertext.$v['qid']];
							$data[$k]['options'][$index]['selected'] = 1;
						}
					}
					
					
				}elseif($v['qtype'] == 'text'){
				//填空题答案赋予	
					$indexarr = json_decode(htmlspecialchars_decode($answer[$v['qid']]),true);
//		print_r($indexarr);
					$data[$k]['answer'] = json_encode($indexarr,JSON_FORCE_OBJECT);
				//	foreach($indexarr as $key=>$index){
				//		$data[$k]['options'][$key]['value'] = $index;
						
				//	}
					foreach($data[$k]['options'] as $key=>$value){
						$data[$k]['options'][$key]['value'] = $indexarr[$key];
					}
					
				}
			//	if($v['tihao']){$data[$k]['remark'] = $remarks[$v['tihao']];}
			}
		}
		
		$this->assign('questionInfo',$questionInfo);
		$this->assign('user',$user);
		$this->assign('data',$data);
	
		$content = $this->fetch('form/reply_10');
		$fileContent = getWordDocument($content);

		$filename = "./Public/reply_".$userId.'_'.$questionnaireId.'_'.$rId.".doc";
		$fp = fopen($filename, 'w+') or die("文件打开失败");
		fwrite($fp, $fileContent);
		fclose($fp);
		
		$fp = fopen($filename, 'r') or die("文件打开失败");
		$size = filesize($filename);
		
		header("Content-type: application/doc");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".$size);
		header("Content-Disposition:attachment;filename=N241_制造业采购经理调查问卷.doc"); 
		echo  fread($fp, $size);
		
		
		

		

		
	}
	
	
	//保存试卷  提交试卷
	public function submitQuestion(){

		$questionnaireId = (int)I('get.questionnaireId');
		$submitStatus = I('get.status');
		$data = array(
			'questionnaire_id'	=>	$questionnaireId,
			'userId' => session('USERID'),
			'reply'				=>	json_encode(I('post.')),
			'createTime'  => time(),
		);

		if(IS_POST){

			$m = D('Reply');
			
			$day = array();
			$day['frist'] =	 strtotime(date('Y-m-01', time()));
			$day['last'] = strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')));
		//查询该问题是否作答过   如果问题保存过，则找出对应的答案 rid
			$where['questionnaire_id'] = $questionnaireId;
			$where['userId'] = session('USERID');
			$where['createTime'] = array('BETWEEN',array($day['frist'],$day['last']));
			$where['status'] = array('EQ',1);
		
			$rid = $m->where($where)->getField('id');
			
			
		//	$where['userId'] = session('USERID');
			
			$m->create();
			$data['status'] = $submitStatus;
		//	if($submit){$mr->isSubmit = 1;}
			if($rid){
			//	print_r($data);exit;
			//如果保存过则更新答案	
				$status = $m->where('id='.$rid)->save($data);

			}else{
			//如果没有保存过则添加答案
				$rid = $m->add($data);
			}
		
		//入库答题说明
			$id = M('Replyremark')->where('replyid='.$rid)->getField('id');
			$rdata['content'] = I('post.shuoming');
			if($id){		
				$rdata['id'] = $id;
				$remarkflag =  M('Replyremark')->save($rdata);
			}else{
				$rdata['replyid'] = $rid;
				$remarkflag = M('Replyremark')->add($rdata);
			}
			
			//入库答题提示警告信息
			$id = M('Replyvalidata')->where('replyid='.$rid)->getField('id');
			$vdata['content'] = I('post.validatas');
			if($id){		
				$vdata['id'] = $id;
				
				$vflag =  M('Replyvalidata')->save($vdata);
			}else{
				$vdata['replyid'] = $rid;
				
				$vflag = M('Replyvalidata')->add($vdata);
			}
			
			if( $status === false ){ //入库失败
				$msg = array(
					'errorMsg'	=>	"问卷入库失败，详情：" + M('Reply')->getDbError()
				);
				$this->error('答题失败');
			//	$this->response($msg, 'json');
			}else{ //入库成功
				$msg = array(
					'errorMsg'	=>	null,
					
				);
				if($submitStatus == 1){
					$this->success('保存成功',U("Home/Index/toQuestion",array('questionnaireId'=>$questionnaireId,'rId'=>$rid)));
				}elseif($submitStatus == 2){
					$this->success('提交成功',U("Home/Index/toQuestion",array('questionnaireId'=>$questionnaireId,'rId'=>$rid)));
				}
			//	$this->response($msg, 'json');
			}
		}else{
//			$this->error('来路不正确');
		}
		
	}
	
	public function cancelQuestion(){
		$data['id'] = I('rId');
		$questionId = I('questionnaireId');
	//	$data['userId'] = session('USERID');
		$data['status'] = 1;
		$m = D('Reply');
		$flag = $m->save($data);
		if($flag){
			$this->success("撤销成功",U('Home/Index/toQuestion',array('questionnaireId'=>$questionId,'rId'=>I('rId'))));
		}else{
			$this->error("撤销失败",U('Home/Index/toQuestion',array('questionnaireId'=>$questionId,'rId'=>I('rId'))));
		}
	}
	
	//修改试卷
	public function editQuestion(){
		
	}
	
	
	
	//公告列表
	public function listAnnouncement(){
		$m = D('Admin/Announcement');
	//	$lists = $m->lists();
		
		$count = count($m->select());

		$pager = new \Think\Page($count,10);
		$pages = $pager->show();

		$page = $m->limit($pager->firstRow,$pager->listRows)->select();

		$this->assign('Page',$page);
		$this->assign('pages',$pages);
		
		
		
		$this->display();
	}
	
	//公告内容
	public function announcement(){
		$m = D('Admin/Announcement');
		$id = I('id');
		$info = $m->getinfoById($id);
		
		$this->assign('info',$info);
		$this->display();
	}
	
	
	
	
	
}




?>