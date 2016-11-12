<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class CompanyController extends BaseController{
	
	public function export(){
		A('Index');
		$companyid = I('id');
		
		$userid = session('USERID');
		
		$m = D('Admin/Company');
		$comInfo = $m->getInfoById($companyid);
		
		$comInfo['zyywhd'] = json_decode(htmlspecialchars_decode($comInfo['zyywhd']),true);
		$comInfo['dwszd'] = json_decode(htmlspecialchars_decode($comInfo['dwszd']),true);
		$comInfo['zyjjzb'] = json_decode(htmlspecialchars_decode($comInfo['zyjjzb']),true);
		
		
		foreach($comInfo as $key=>$value){
			if(is_array($value)){
				foreach($value as $k=>$v){
					$newkey = $key.$k;
					$$newkey = $v;
					$comInfo[$newkey] = $v;	
				}				
				unset($comInfo[$key]);
			}else{
				$$key = $value;
			}
		}


		@import("Org.Util.PHPWord");
		$PHPWord = new \PHPWord();
		
			//$section = $PHPWord->createSection();
	//	$file_name = "/Public/reply.doc";
		//$objWriter = \PHPWord_IOFactory::createWriter($PHPWord,'Word2007');
		$section = $PHPWord->createSection();
		
		
		
		
		
	
	
	$sfarr = array('sfck','sfss','sfgykg');
	$dwgm = array('大型','中型','小型','微型');
	$djzclx = array('110'=>'110国有','159'=>'159其他有限责任公司','210'=>'210与港澳台商合资经营','310'=>'310中外合资经营','120'=>'120集体','160'=>'160股份有限公司','220'=>'220与港澳台商合作经营','320'=>'320中外合作经营','130'=>'130股份合作','171'=>'171私营独资','230'=>'230港澳台独资','330'=>'330外资企业','141'=>'141国有联营','172'=>'172私营合伙','240'=>'240港澳台商投资股份有限公司','340'=>'340外商投资股份有限公司','142'=>'142集体联营','173'=>'173私营有限责任公司','290'=>'290其他港澳台投资','390'=>'390其他外商投资','143'=>'143国有与集体联营','174'=>'174私营股份有限公司','149'=>'149其他联营','190'=>'190其他','151'=>'151国有独资公司');

	//问卷答案	
/*	foreach($comInfo as $key=>$value){
		$str = "";
		if(in_array($key,$sfarr)){
			if($value==1){$str = "●是   ○否";}elseif($value==2){$str = "○是   ●否";}
		}elseif(in_array($key,array('dwgm'))){
			for($i=1;$i<=4;$i++){
				if($i==$value){
					$str .= '●'.$dwgm[$i-1];
				}else{
					$str .= '○'.$dwgm[$i-1];
				}
			}
		}elseif(in_array($key,array('djzclx'))){
			foreach($djzclx as $k=>$v){
				if($value==$v){
					$str .= "●".$v."  ";
				}else{
					$str .= "○".$v."  ";
				}
			}
		}else{		
			$str = $value;
		}
		$$key = $str;
	}
*/
	//	$Q25 = iconv('GB2312','UTF-8',$Q25);
	
	$filename = "company_{$companyid}.docx";
	if(!file_exists("Public/".$filename)){
		$template = $PHPWord->loadTemplate("./Public/company.docx");
		

		foreach($comInfo as $key=>$value){
			$str = "";
			if(in_array($key,$sfarr)){
				if($value==1){$str = "●是   ○否";}elseif($value==2){$str = "○是   ●否";}
			}elseif(in_array($key,array('dwgm'))){
				for( $i=1;$i<=4;$i++){
					if($i==$value){
						$str .= '● '.$dwgm[$i-1];
					}else{
						$str .= '○ '.$dwgm[$i-1];
					}
				}
				
			}elseif(in_array($key,array('djzclx'))){
				$i=1;
				foreach($djzclx as $k=>$v){
				
					if($value==$k){
						$str .= "●".$v."   ";
					}else{
						$str .= "○".$v."   ";
					}
					if($i%4==0){$str .="<br/>";}
				//	echo $i%4;echo '<br/>';
					$i++;
				}
		
			}else{		
				$str = $value;
			}

			$template->setValue($key,$str);
		}
		//设置换行
		$template->setValue('<br/>','<w:br />');
		$year = $updatetime;
		//设置表格前面的年份
		$template->setValue('year',substr($year,0,4));

	//	echo $updatetime;exit;


		$template->save("Public/company_{$companyid}.docx");
	}
		
		
		//print_r($template);
		//$template->setValue('Name', 'Somebody someone');
		//$template->setValue('Street', 'Coming-Undone-Street 32');
	//	$objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
	//	$fileName = "word报表".date("YmdHis");
		$file=fopen('./Public/'.$filename,"r");
		$size = filesize('./Public/'.$filename);
		
		header("Content-type: application/vnd.ms-word");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".$size);
		header("Content-Disposition:attachment;filename=".$filename); 
		echo fread($file, $size);
		fclose($file);

	}

}