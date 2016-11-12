<?php 
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
* 处理问题相关请求
*/
class CompanyController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();

    	$this->bcItemPush('问卷管理', U('Questionnaire/index'));
    	$this->bcItemPush('企业信息管理', U('Questions/userCenter', array('questionnaire_id'=>I('questionnaire_id/d'))));		
	}
	
	public function setDate(){
		if(IS_GET){
			$date = F('date');
			$this->assign('date',$date);
			$this->display();
		}elseif(IS_POST){
			$date['start_date'] = I('start_date');
			$date['expire_date'] = I('expire_date');
			if($date['expire_date'] < $date['start_date']){$this->error('失效时间不能小于开始时间，请重新输入');}
			
			F('date',$date);
			$this->success("年报时间设置成功");
			//print_r(F('date'));
		}
	}
	
	public function info(){
		$m = D("Company");
		
		$where['id'] = I('get.id');
		
	
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
			
			
			
	
		//	var_dump($qhdmarr);

			$user['Company'] = $m->where($where)->find(); 
			$user['Company']['dwszd'] = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
			$user['Company']['zyjjzb'] = json_decode(htmlspecialchars_decode($user['Company']['zyjjzb']),true);
			$user['Company']['zyywhd'] = json_decode(htmlspecialchars_decode($user['Company']['zyywhd']),true);
			$user['Company']['validata'] = htmlspecialchars_decode($user['Company']['validata']);
			$user['Company']['shuoming'] = htmlspecialchars_decode($user['Company']['shuoming']);
			$this->assign('QHDM', C('QHDM'));
			

			$this->assign('user',$user);
			$this->display();
		}elseif(IS_POST){
		//	$id = $m->where('id='.I('get.id'))->getField('id');
			$com = M('Company');
			$data['dwxxmc'] = I('dwxxmc');
			$data['zzjgdm'] = I('zzjgdm');
			$data['hydm'] = I('hydm');
			$data['shxydm'] = I('shxydm');
			$data['fddbr'] = I('fddbr');
			$data['dianhua'] = I('dianhua');
			$data['qydm'] = I('qydm');
			$data['yzbm'] = I('yzbm');
			$data['qmcyrs'] = I('qmcyrs');
			$data['dwszd'] = I('dwszd');
			$data['zyjjzb'] = I('zyjjzb');
			$data['sfck'] = I('sfck');
			$data['sfss'] = I('sfss');
			$data['sfgykg'] = I('sfgykg');
			$data['dwgm'] = I('dwgm');
			$data['djzclx'] = I('djzclx');
			$data['zyywhd'] = I('zyywhd');
			$data['lxrxm'] = I('lxrxm');
			$data['zw'] = I('zw');
			$data['lxdianhua'] = I('lxdianhua');
			//$data['updateTime'] = date('Y-m-d',time());
			
			$time = date('Y',time());

			$flag = $com->where($where)->save($data);
			
			if($flag){
				$this->success("更新成功");
			}else{
				$this->error("更新失败");
				
			}
			
		}
		
		
	}
	

}
?>