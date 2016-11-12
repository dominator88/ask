<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Org\Util\String;

/**
 * 处理问卷相关请求
 */
class QuestionnaireController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();
        $this->bcItemPush('问卷管理', U('Questionnaire/index'));
		$this->assign('umark','questionnaire');
	}

    /* 问卷列表 */
    public function index()
    {
        $this->assign('questionnaires', M('Questionnaires')->field(true)->select());
		
        $this->display();
    }

	
	
	
    /* 问卷添加 */
    public function add()
    {
        $this->bcItemPush('添加');
	
        if( IS_GET ){ //访问页面
			$this->assign('catelist',$catelist);
            $this->display();
        }else{ //表单提交
            $questionnaires = M('Questionnaires');
         
            if( $questionnaires->create() ){
                $state = $questionnaires->add();
			
                if( $state===false ){
                    $this->error('问卷添加失败，错误信息：'.$questionnaires->getDbError());
                }else{
                    $this->success('问卷添加成功', '/questionnaire/index', 0);
                }
            }else{ //表单提交不完整
                $this->assign(I('post.')); //反馈提交的数据
                $this->assign('errorNote', $questionnaires->getError());
                $this->display();
            }
        }
    }

    /* 问卷编辑 */
    public function edit()
    {
        $this->bcItemPush('编辑');
		
        if( IS_GET ){ //访问页面
            $questionnaires = M('Questionnaires')->field(true)->find( I('get.id/d') );
			$this->assign('catelist',$catelist);
            $this->assign($questionnaires);
            $this->display();
        }else{ //表单提交
            $questionnaires = M('Questionnaires');

            if( $questionnaires->create() ){ //表单提交
			//问卷验证
				if($questionnaires->start_date > $questionnaires->expire_date){$this->error('问卷失效时间不能小于开始时间，请重新输入');}
			
                $state = $questionnaires->save();
				
                if( $state===false ){
                    $this->error('问卷更新失败，错误信息：'.$questionnaires->getDbError());
                }else{
                    $this->success('问卷编辑成功', U('/Admin/questionnaire/index'), 0);
                }
            }else{ //表单提交不完整
                $this->assign(I('post.')); //反馈提交的数据
                $this->assign('errorNote', $questionnaires->getError());
                $this->display();
            }
        }
    }

    /* 问卷删除 */
    public function delete()
    {
        if( is_array(I('id')) ){ //批量删除
            $id = implode( ',', I('id', array(), 'intval') );
        }else{ //单个删除
            $id = I('id/d');
        }

        $questionnaires = M('Questionnaires');
		$questions = M('Questions');
        $state = $questionnaires->delete($id);
		$state2 = $questions->where('questionnaire_id='.$id)->delete();

        if( $state===false ){
            $this->error('问卷删除失败，错误信息: '.$questionnaires->getDbError()); //删除问卷，并关联删除旗下问题
        }else{
            $this->redirect('/Admin/questionnaire/index');
        }
    }
	
	//查询当前问卷已填和未填企业的列表
	public function usersDetail(){
		$id = (int)I('get.id');
		$m = M('Users');
		$status = I('get.status');
		$time = I('post.time')?strtotime(I('post.time')):time();

		$firstday =	 strtotime(date('Y-m-01', $time));
		$lastday = strtotime(date('Y-m-d', strtotime(date('Y-m-01', $time) . ' +1 month -1 day')));
		$dwmc = I('post.dwxxmc');
		
		$users = D('Users')->order('id')->getField('id',true);

		$map['r.status'] = 2;
		$map['r.questionnaire_id'] = 10;
		$map['r.createTime'] = array('between',$firstday.','.$lastday);
		$submits = M('Reply')->alias('r')->join(' __USERS__ u on r.userid=u.id')->where($map)->order('userid desc')->getField('userid',true);
        $submits =  $submits ? $submits : [];
		$nosubmits = array_diff($users,$submits);

        if(!empty($submits)){
            $map_r['r.userid'] = array('in',$submits);
            $map_r['dwmc'] = array('like',"%{$dwmc}%");
            $map_r['createTime'] = array('between',$firstday.','.$lastday);
            $reply_submit = D('Users')->alias('u')->field('r.id as rid,questionnaire_id,username,u.id,realname,email,telephone,subphone,mobilephone,tjcyzgzh,dwmc,userid,sfcy,status,rv.content as rcontent')->join('left join __REPLY__ r on u.id=r.userid')->join('__REPLYVALIDATA__ rv on r.id=rv.replyid')->where($map_r)->select();
        }

        if(!empty($nosubmits)) {
            $map_nr['u.id'] = array('in', $nosubmits);
            $map_nr['dwmc'] = array('like', "%{$dwmc}%");

            $map_hnr['questionnaire_id'] = 10;
            $map_hnr['createTime'] = array('between', $firstday . ',' . $lastday);
            $reply_nosubmit = D('Users')->alias('u')->field('r.id as rid,questionnaire_id,username,u.id,realname,email,telephone,subphone,mobilephone,tjcyzgzh,dwmc,userid,sfcy,status,rv.content as rcontent,createtime')->join("left join __REPLY__ r on u.id=r.userid and questionnaire_id=10 and createtime between '$firstday' and '$lastday'")->join('left join __REPLYVALIDATA__ rv on r.id=rv.replyid')->where($map_nr)->select();
        }
		$countArr['submitcount'] = count($submits);
		$countArr['nosubmitcount'] = count($nosubmits);
		if($status == 1){
			$userList = $reply_submit;
		}else{
			$userList = $reply_nosubmit;
		}
	
		
//		print_r($replys);exit;
		
	//	$subQuery = $m->field('users.id,realname,username,email,telephone,subphone,mobilephone,tjcyzgzh,dwmc,questionnaire_id,status,reply.createTime,reply.id rid,replyremark.content as rcontent,reply.sfcy')->join('left join reply on users.id=reply.userId')->join('left join replyremark on reply.id=replyremark.replyid')->order('reply.createTime desc')->select(false);
		
		
	//	$submitCount =  count($m->field('users.id,realname,username,email,telephone,subphone,mobilephone,tjcyzgzh,questionnaire_id,status,reply.createTime')->join('left join reply on users.id=reply.userId')->where("status=2 and  createTime between '$firstday' and '$lastday' and questionnaire_id=$id")->group('id')->select());
		
	//	$nosubmitCount =  count($m->field('users.id,realname,username,email,telephone,subphone,mobilephone,tjcyzgzh,questionnaire_id,status,reply.createTime')->join('left join reply on users.id=reply.userId')->where("(status=2 and  createTime not between '$firstday' and '$lastday') or (status<>2 and questionnaire_id=$id) or (status<>2 and questionnaire_id<>$id)  or questionnaire_id IS NULL ")->group('id')->select());


		//提交为 status=2 createTime 在区间内
		//未提交为 status!=2 createTime 不在区间内
		//			status!=2 createTime 在区间内
		//			status==2 createTime 不在区间内
		//			questionnaire_id IS NULL		未答过题
		
/*		$username = I('post.zzjgdm');
		$dwmc = I('post.dwxxmc');
		if($username){$where['username'] = array('like',"%{$username}%");}
		if($dwmc){$where['dwmc'] = array('like',"%{$dwmc}%");}
		
		
		if($status){
			$where['status'] = array('EQ',2);
			$where['questionnaire_id'] = array('EQ',$id);
			$where['createTime'] = array('BETWEEN',array($firstday,$lastday));
		}else{
			$where['_string'] = "(status=2 and createTime not Between $firstday and $lastday) or   (status<>2 and questionnaire_id=$id ) or (status<>2 and questionnaire_id=$id) or questionnaire_id IS NULL  ";
		//	$where['_string'] = ' status<>2 or status IS NULL';
		}
		
		
		
		
		//获取所有用户的列表
		$userList = M()->table($subQuery.' questionlist')->where($where)->group('id')->select();

	
		$countArr['submitcount'] = $submitCount;
		$countArr['nosubmitcount'] = $nosubmitCount;
		$count =  count($userList);
		$pager = new \Think\Page($count,100);
		$pages = $pager->show(); 
*/
		//查询出本月已经填报的企业，
	/*	$r = D('Reply');
		$where= array();
		
		
		$where['questionnaire_id'] = $id;
		
		$qr = $r->where($where)->getField('userId,status');	
		foreach($userList as $key=>$value){
			$userList[$key]['status'] = $qr[$key];
		}
	*/	
		
	//	$userList = $m->where($where)->group('userId')->select();   print_r($userList);
		$season = $this->countSeason('2016-06',date('Y-m',time()));
		$this->assign('status',$status);
		$this->assign('id',$id);
		$this->assign('time',I('time'));
		$this->assign('season',$season);

		$this->assign('count',$countArr);
		$this->assign('userList',$userList);
		$this->display();
		
	}
	
	public function replyYanshou(){
		$replyid = I('id');
		$m = M('Reply');
		$data['id'] = $replyid;
		$data['sfcy'] = 1;
		$m->save($data);

		$this->success("验收成功");
	}
	
	public function exportList(){
	//	$time = time();
		$time = I('post.time')?strtotime(I('post.time')):time();
		
		$firstday =	 strtotime(date('Y-m-01', $time));
		$lastday = strtotime(date('Y-m-d', strtotime(date('Y-m-01', $time) . ' +1 month -1 day')));
		$where['createTime'] = array('between',"$firstday,$lastday");
		$ytime = date('Y',$time);
		$where['updateTime'] = array('BETWEEN',$ytime.'-01-01'.','.date('Y-m-d',strtotime(' +1year',strtotime($ytime.'-01-01'))));
		$where['reply.status'] = array('eq',2);
		//$where['company.status'] = array('eq',2);
		import("Org.Util.PHPExcel");
		$list = M('Reply')->field('username,zzjgdm,dwxxmc,lxrxm,lxdianhua,subphone,createTime,shxydm,reply')->join(array('LEFT JOIN __USERS__ ON __REPLY__.userId=__USERS__.id','LEFT JOIN __COMPANY__ ON __REPLY__.userId=__COMPANY__.userId'))->where($where)->group('reply.id')->select();

		foreach($list as $key=>$value){
			$list[$key]['bgq'] = date('Y-m',$list[$key]['createtime']);
			$list[$key]['createtime'] = date('Y-m-d',$list[$key]['createtime']);
			$notarr = array("91","103","101");
			$list[$key]['reply'] = json_decode($list[$key]['reply'],true);	
			foreach($list[$key]['reply'] as $k=>$v){
				if(in_array($k,$notarr)){
					switch($k){
						case "91":
							$tmparr = json_decode(htmlspecialchars_decode($v),true);
							$list[$key][$k."1"] = $tmparr["1"];
							$list[$key][$k."2"] = $tmparr["2"];
						break;
						case "101":
							$tmparr = json_decode(htmlspecialchars_decode($v),true);
							$list[$key][$k."1"] = $tmparr["1"];
						break;
						case "103":
							$tmparr = explode(',',$v);
							for($i=1;$i<=9;$i++){
								$list[$key][$k.$i] = in_array($i,$tmparr)?1:0;								
							}
						break;
					}
				}else{
					
					$list[$key][$k] = $v=="null"? "无":$v;
				}
				
			}
			unset($list[$key]['reply']);
		}
		
		$headArr = array("统计机构","汇总标识一","报告期","移动终端标识","调查对象系统码","组织机构代码","统计机构级别","组织机构代码","单位详细名称","生产量","订货量","出口订货量","剩余订货量","产成品库存","采购量","进口","购进价格","价格上升常用品","价格下降常用品","出厂价格","主要原材料库存","生产经营人员","供应商配送时间","国内采购原材料提前订货时间","进口生产用原材料提前订货时间","生产或维修用零部件提前订货时间","生产用固定资产提前订货时间","短缺原材料常用品","生产经营活动预期","资金紧张","市场需求减少、订单不足","生产用主要原材料价格上涨","运输成本上涨","劳动力成本上涨","能源等原材料供应紧张","劳动力供应不足","人民币汇率波动","其他","其他(说明信息)","评价或建议","采购经理","电话","分机号","报出日期","统一社会信用代码");
		$list = self::getExcelData($list);

		self::getExcel(date('Y-m',$time),$headArr,$list);
		
	}
	
	private function getExcel($fileName,$headArr,$data){
		
		if(empty($data) || !is_array($data)){
			die("data must be a array");
		}
		if(empty($fileName)){
			exit;
		}
		$date = date("Y_m_d",time());
		$fileName = rawurlencode($fileName." 制造业.csv");
	 
		//创建新的PHPExcel对象
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		
			
		//设置表头
		$key = ord("A");
		 $key2 = ord("@");//@--64
		foreach($headArr as $v){
			//$colum = chr($key);
			if($key>ord("Z")){
				$key2 += 1;
				$key = ord("A");
				$colum = chr($key2).chr($key);//超过26个字母时才会启用  
			}else{
				if($key2>=ord("A")){
					$colum = chr($key2).chr($key);//超过26个字母时才会启用  
					
				}else{
					$colum = chr($key);
				}
			}
			
			
			
			$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
			$key += 1;
		}
		
		$column = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();
		foreach($data as $key => $rows){ //行写入
			$span = ord("A");
			$span2 = ord("@");//@--64
			
			foreach($rows as $keyName=>$value){// 列写入
			//	$j = chr($span);
				
				
		 if($span>ord("Z")){
            $span2 += 1;
            $span = ord("A");
            $j = chr($span2).chr($span);//超过26个字母时才会启用  
        }else{
            if($span2>=ord("A")){
                $j = chr($span2).chr($span);//超过26个字母时才会启用  
			
            }else{
                $j = chr($span);
				
            }
        }
				
				
				$objActSheet->setCellValueExplicit($j.$column, $value.'','str');
				$span++;
			}
			$column++;
		}

		$fileName = iconv("utf-8", "gb2312", $fileName);
		//重命名表
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		//将输出重定向到一个客户端web浏览器(Excel2007)
			  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			  header("Content-Disposition: attachment; filename=\"$fileName\"");
			  header('Cache-Control: max-age=0');
			  $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	  
			 
				$objWriter->save('php://output'); //文件通过浏览器下载
			
	  exit;
 
	}
	
	private function getExcelData($data){
		if(empty($data)){return false;}
		$newdata = array();
		foreach($data as $key=>$value){
			$newvalue = array();
			array_push($newvalue,"0","",$value['bgq'],"",$value['username'],$value["zzjgdm"],"",$value["zzjgdm"],$value["dwxxmc"],$value["83"],$value["84"],$value["85"],$value["86"],$value["87"],$value["88"],$value["89"],$value["90"],$value["911"],$value["912"],$value["92"],$value["93"],$value["94"],$value["95"],$value["97"],$value["98"],$value["99"],$value["100"],$value["1011"],$value["102"],
			$value["1031"],$value["1032"],$value["1033"],$value["1034"],$value["1035"],$value["1036"],$value["1037"],$value["1038"],$value["1039"],$value["othertext103"],
			$value["1041"],$value['lxrxm'],$value['lxdianhua'],$value['subphone'],$value['createtime'],$value['shxydm']);
			array_push($newdata,$newvalue);
		}
		
		return $newdata;
	}
	
	private function countSeason($start,$end){
		$temp = date("Y-m",strtotime("$start +1month"));
		while ($temp <= $end){
			$time[] = $temp;
			$temp = date("Y-m",strtotime("$temp +1month"));
		}
		rsort($time);
		return $time;
	}
	
	/*问卷分类*/
/*	public function type(){
		if(!F('type')){			
			F('type',C('QuestionnaireType'));
		}
		$types = F('type');
		$this->assign('types',$types);
		$this->display();
	}
*/

}
?>