<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

//企业用户管理控制器类
class UsersController extends CommonController{
	
	
	
	/*
	*所有企业用户列表
	*/
	public function all(){
		$m = D('Users');
		$userList = $m->getList();
		
		$this->assign('userList',$userList);
		$this->assign('umark','company');
		$this->display();
	}
	
	public function del(){
		$m = D('Users');
		$mr = M('Reply');
		$userid = I('id');
		if( is_array(I('id')) ){ //批量删除
            $id = implode( ',', I('id', array(), 'intval') );
        }else{ //单个删除
            $id = I('id/d');
        }
		//删除月报
		
		 $state = $m->relation(true)->delete("$id");
	//	 $replysql = "delete r,rr,rv from reply r,replyremark rr,replyvalidata rv  where r.userId={$userid} and r.id=rr.replyid and r.id=rv.replyid";
	//	$replystate = M()->execute($replysql);
		if( $state === false ){
            $this->error('用户删除失败，错误信息: '.M('Users')->getDbError());
        }else{
            $this->success('用户删除成功', U('Admin/Users/all'));
        }
	}
	
	public function edit(){
		$rules = array(
			array('userPassword','/{8,}/','密码至少要8位数','0','length',2),
	 );
		if(IS_GET){
			$this->display();
		}elseif(IS_POST){
			$password = I('password');
			$confrimPassword = I('confrimPassword');
		//$m->field('realName,email,telephone,subphone,mobilephone,tjcyzgzh,userPassword')->create();print_r( $where);exit;
			$data['realName'] = I('realName');
			$data['email'] = I('email');
			$data['telephone'] = I('telephone');
			$data['subphone'] = I('subphone');
			$data['mobilephone'] = I('mobilephone');
			$data['tjcyzgzh'] = I('tjcyzgzh');
			$data['Company']['lxrxm'] = $data['realName'];
			if(!empty($password) && $password === $confrimPassword){
				$data['userPassword'] = md5($userPassword);
			}
			if($m->validate($rules)->where('id='.I('get.id'))->save($data)){
				exit($m->getError());
			}else{
				$this->success('个人资料更新成功');
			}
			
	/*		$flag = $m->where('id='.I('get.id'))->save($data);
			if($flag){
				
			}else{
				$this->error('个人资料更新失败');
			}
	*/	}
		
	}
	public function restPass(){
		$userId = (int)I('id');
		$m = M('Users');
		$userName = $m->where('id='.$userId)->getField('userName');
		$data['userPassword'] = md5($userName);

		$flag = $m->where('id='.$userId)->save($data);
	//	print_r($m->getlastsql());exit;
		if($flag){
			$this->success('重置成功,当前密码为登录用户名');
		}else{
			$this->error('重置失败,当前密码已经是登录用户名');
		}
	}
	
	
	
	/**
	 *
	 * 显示导入页面 ...
	 */

	/**实现导入excel
	 **/
   function userImport(){
	  if(IS_GET){
			$this->assign('umark','company');
			$this->display();
			
		}elseif(IS_POST){ 
			if (!empty($_FILES)) {
				//import("@.ORG.UploadFile");
				$config=array(
					'exts'=>array('xlsx','xls'),
					'rootPath'=>"./Public/",
					'savePath'=>'Uploads/',
					//'autoSub'    =>    true,
					'subName'    =>    array('date','Ymd'),
				);
				$upload = new \Think\Upload($config);
				//var_dump($upload);exit;
				if (!$info=$upload->upload()) {
					$this->error($upload->getError());
				} /*else {
					//$info = $upload->getUploadFileInfo();
					  
				}
				*/
				
				//var_dump($_FILES);exit;
				import("Org.Util.PHPExcel");
				$file_name=$upload->rootPath.$info['file']['savepath'].$info['file']['savename'];
				
				$objReader = \PHPExcel_IOFactory::createReader('Excel5');
				$objPHPExcel = $objReader->load($file_name,$encode='utf-8');
				$sheet = $objPHPExcel->getSheet(0);
				$highestRow = $sheet->getHighestRow(); // 取得总行数
				$highestColumn = $sheet->getHighestColumn(); // 取得总列数
				$m = D('Users');
				for($i=2;$i<=$highestRow;$i++)
				{
					$data['userName'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
					$data['realName'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
					$data['mobilePhone'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
					$data['email'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
					$data['telePhone'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
					$data['tjcyzgzh'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
					$data['subPhone'] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
					$data['dwmc'] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
					$data['Company']['dwxxmc'] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
					$data['userPassword'] = md5($data['userName']);
					$data['Company']['lxrxm'] = $data['realName'];
					$data['Company']['zzjgdm'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
					$data['Company']['lxdianhua'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
					$data['Company']['shxydm'] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
					$data['Company']['qydm'] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
					$data['Company']['hydm'] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
					$data['Company']['updateTime'] = date('Y-m-d',time());
					
					$data['Company']['status'] = 3;
					$m->relation(true)->add($data);
					
			
				}
				$this->success('导入成功！');
			}else
			{
				$this->error("请选择上传的文件");
			}
		}	

	}
	
	public function companyList(){
		$m = M('Users');
		$status = I('get.status');
		$date = F('date');
		$time = I('time')?strtotime(I('time')."-01"):time();
	//未提交状态分为   status=2 但updateTime  不在区间内
	//			status!=2 但updateTime 在区间内
	//			status!=2 update 也不在区间内
	//			status is null 		从未有过提交
	
		$zzjgdm = I('post.zzjgdm');
		$dwxxmc = I('post.dwxxmc');
		if($zzjgdm){$where['zzjgdm'] = array('like',"%{$zzjgdm}%");}
		if($dwxxmc){$where['dwxxmc'] = array('like',"%{$dwxxmc}%");}
		
		if($status){
			$where['status'] = array('EQ',2);
			$where['updateTime'] = array('BETWEEN',$date[start_date].','.$date[expire_date]);
			
		}else{
		//	$where['status'] = array('NEQ',2);
		//已经设置状态为空，不用再作时间判断
			$where['_string'] = "(status=2 and updateTime not between '$date[start_date]' and '$date[expire_date]') or (status<>2 ) or updateTime IS NULL";
		}
	
		
		//子查询 对企业信息列表进行排序，方便后面取值
		$subQuery = $m->field('users.id,userId,company.id as companyId,dwxxmc,zzjgdm,lxrxm,lxdianhua,status,updateTime,shuoming,sfcy,validata')->join('LEFT join company on users.id=company.userid')->order('updateTime desc')->select(false);
		

		//已提交和未提交的条数
		$submitCount =  count($m->field('users.id,userId,company.id as companyId,dwxxmc,zzjgdm,lxrxm,lxdianhua,status,updateTime')->join('LEFT join company on users.id=company.userid')->where("status=2 and  updateTime between '$date[start_date]' and '$date[expire_date]'")->group('id')->select());

		$nosubmitCount =  count($m->field('users.id,userId,company.id as companyId,dwxxmc,zzjgdm,lxrxm,lxdianhua,status,updateTime')->join('LEFT join company on users.id=company.userid')->where("(status=2 and updateTime not between '$date[start_date]' and '$date[expire_date]') or  (status<>2 ) or status IS NULL ")->group('id')->select());
		
		$count =  count(M()->table($subQuery.' companylist')->where($where)->group('id')->select());
		$pager = new \Think\Page($count,100);
		//所有的
		$list = M()->table($subQuery.' companylist')->where($where)->group('id')->order('companyId desc')->limit($pager->firstRow,$pager->listRows)->select();

		

		
		$pages = $pager->show(); 

		$countarr['submitCount'] = $submitCount;
		$countarr['nosubmitCount'] = $nosubmitCount;
		$season = self::countYear('2015',date('Y',time()));
		$this->assign('season',$season);
		$this->assign('userList',$list);
		$this->assign('pages',$pages);
		$this->assign('count',$countarr);
		$this->display();
		
	}
	
	
	public function companyYanshou(){
		$companyid = I('id');
		$m = D('Company');
		$data['id'] = $companyid;
		$data['sfcy'] = 1;
		$m->save($data);
	
		$this->success("验收成功");
	}
	
	public function exportList(){
		$m = M('Users');
		$time = I('time')?strtotime(I('time').'-01'):time();
		$time = date('Y',$time);
		$where['updateTime'] = array('BETWEEN',$time.'-01-01'.','.date('Y-m-d',strtotime(' +1year',strtotime($time.'-01-01'))));
		$where['company.status'] = array('eq',2);
		import("Org.Util.PHPExcel");
	//	import("Org.Util.PHPExcel.Writer.Excel2007");
		$list = $m->join("LEFT JOIN __COMPANY__ ON __USERS__.id=__COMPANY__.userid")->where($where)->group('users.id')->select();

		foreach($list as $key=>$value){
			$list[$key]['dwszd'] = json_decode(htmlspecialchars_decode($list[$key]['dwszd']),true);			
			$list[$key]['zyjjzb'] = json_decode(htmlspecialchars_decode($list[$key]['zyjjzb']),true);
			$list[$key]['zyywhd'] = json_decode(htmlspecialchars_decode($list[$key]['zyywhd']),true);
			$list[$key]['bgq'] = date('Y',strtotime($list[$key]['updatetime']))-1;
			foreach($value as $k=>$v){
				$allowArr = array("dwszd","zyjjzb","zyywhd");
				if(in_array($k,$allowArr)){
					foreach($list[$key][$k] as $i=>$j){
						$list[$key][$k.$i] = $j;
					}
					unset($list[$key][$k]);
				}
			}
			
		}

		$headArr = array("报告期",	"调查对象系统码"
		,"组织机构代码","统计机构","统计机构级别","单位详细名称","组织机构代码","联系电话","详细地址省","详细地址市","详细地址区县","详细地址乡","详细地址街","详细地址门牌号","区划代码","邮政编码","是否有出口业务","是否为上市公司","登记注册类型","行业代码","从业人员期末人数","营业收入","业务活动一","行业代码一","业务活动二","行业代码二","业务活动三","行业代码三","占营业收入比重一","占营业收入比重二","占营业收入比重三","职务","联系人姓名","是否为国有控股企业","单位规模","电话","分机号","报出日期","制造业、非制造业标识 ","主营业务收入","新旧样本标识 ","法定代表人","资产本月","统一社会信用代码");
		
		//$data = array(array("1",'abc'),array("2","333"));
		$list = self::getExcelData($list);
		
		
		//$time = time();
		self::getExcel($time,$headArr,$list);
	}
	
	private function getExcel($fileName,$headArr,$data){
		
		if(empty($data) || !is_array($data)){
			die("data must be a array");
		}
		if(empty($fileName)){
			exit;
		}
		//$date = date("Y_m_d",time());
		$fileName = rawurlencode($fileName."年报.csv");
	 
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
			array_push($newvalue,$value["bgq"],"",$value['zzjgdm'],$value["tjjgdm"],"",$value['dwxxmc'],$value['zzjgdm'],$value['dianhua'],$value['dwszd0'],$value['dwszd1'],$value['dwszd2'],$value['dwszd3'],$value['dwszd4'],$value['dwszd5'],$value['qydm'],$value['yzbm'],$value['sfck'],$value['sfss'],$value['djzclx'],$value['hydm'],$value['qmcyrs'],$value['zyjjzb0'],$value['zyywhd0'],$value['zyywhd1'],$value['zyywhd3'],$value['zyywhd4'],$value['zyywhd6'],$value['zyywhd7'],$value['zyywhd2'],$value['zyywhd5'],$value['zyywhd8'],$value['zw'],$value['lxrxm'],$value['sfgykg'],$value['dwgm'],$value['lxdianhua'],$value['subphone'],$value['updatetime'],"",$value['zyjjzb1'],"",$value['fddbr'],$value['zyjjzb2'],$value['shxydm']);
			array_push($newdata,$newvalue);
		}
		return $newdata;
	}
	
	private function countYear($start,$end){
		$temp = date("Y",strtotime("$start-01 +1year"));

		while ($temp <= $end){
			$time[] = $temp;
			$temp = date("Y",strtotime("$temp-01 +1year"));
		}
		
		return $time;
	}
	
	
	
	
}



?>