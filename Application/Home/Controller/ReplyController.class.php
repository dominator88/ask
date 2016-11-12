<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class ReplyController extends BaseController{
	
	public function export(){
		A('Index');
		$replyid = I('rId');
		$qid = I('questionnaireId');
		$userid = session('USERID');
		
		
		
		
		@import("Org.Util.PHPWord");
		$PHPWord = new \PHPWord();
		
			//$section = $PHPWord->createSection();
	//	$file_name = "/Public/reply.doc";
		//$objWriter = \PHPWord_IOFactory::createWriter($PHPWord,'Word2007');
		$section = $PHPWord->createSection();
		
		
		$m = D('Admin/Questionnaires');
		$mreply = M('Reply');
		$questionInfo = $m->getQuestionnaires($qid,$replyid);
		$data = $m->getQuestion($qid);
		
		
		$mu = D('Admin/Users');
		$user = $mu->relation(true)->where("id=".$userid)->find();
		$dwszdarr = json_decode(htmlspecialchars_decode($user['Company']['dwszd']),true);
		$user['Company']['dwszd'] = $dwszdarr[0].$dwszdarr[1].$dwszdarr[2].$dwszdarr[3].$dwszdarr[4].$dwszdarr[5];
		
		
		$answer = json_decode($mreply->where('id='.$replyid)->getField('reply'),true);
		if($answer){
	//如果指定问题已经设置了答题，就将答案列在相应的选项内	
			foreach($data as $k=>$v){
				if($v['qtype'] == 'checkbox' || $v['qtype'] == 'radio'){
				//获取到被选中答案的键值	
				//	print_r( $v);
					$indexarr = explode(',',$answer[$v['qid']]); 
					$data[$k]['answer'] = $answer[$v['qid']];
				//给指定的键值添加一个属性selected	
					foreach($indexarr as $index){
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
				
			}
		}
		
	//用户基本信息
	foreach($user['Company'] as $key=>$value){
		$$key = $value;
	}

	
		
	//问卷基本信息	
/*	foreach($questionInfo as $key=>$value){
		if($key=='reply'){
			$$key = $value;
		}
	}
	print_r($questionInfo);exit;
*/	//问卷答案	

	foreach($data as $key=>$value){
		$str = "";
		foreach($value['options'] as $k=>$v){
			if($v['selected']==1){
				$str .= '●'.$v['text'].'   ';
			}else{
				$str .= '○'.$v['text'].'   ';
			}
			
		}
		$k = 'Q'.$value['qid'];
	
		$$k = $str;
	}

	//	$Q25 = iconv('GB2312','UTF-8',$Q25);
	$filename = "reply_{$userid}_{$qid}_{$replyid}.docx";
	if(!file_exists("Public/".$filename)){
		$template = $PHPWord->loadTemplate("./Public/reply_$qid.docx");

		//print_r($data);exit;
		foreach($data as $key=>$value){
		$str = "";
		if($value['qtype'] == 'radio' || $value['qtype'] == 'checkbox'){
			foreach($value['options'] as $k=>$v){
				if($v['type']=="checkbox" || $v['type']=="radio" ){
					if($v['selected']==1){
						
						$str .= '●'.$v['text'].'   ';
						
					}else{
						$str .= '○'.$v['text'].'   ';
					}
				}elseif($v['type']=="checkbox_othertext"){
					if($v['selected']==1){
						
						$str .= '●其它 '.$v['text'].'  ';
						
					}else{
						$str .= '○其它 '.$v['text'].'  ';
					}
				}
				
			}
			$k = 'Q'.$value['qid'];
			$$k = $str;
		}elseif($value['qtype'] == 'text' ){
			
			foreach($value['options'] as $k=>$v){
				$ke = 'Q'.$value['qid'].$k;
				$$ke = $v['value']?$v['value']:'a11';
				$template->setValue($ke,$$ke);
			}	
			continue;
		}
		
		
		
		
		$template->setValue($k,$$k);
		}
		
		foreach($user as $key=>$value){
		if(!is_array($value)){
			$$key = $value;
			$template->setValue($key,$value);
		}
	}	
		
		foreach($user['Company'] as $k=>$v){
			
			$template->setValue($k,$v);
		}

		//设置报出日期	
		$template->setValue("createtime",date('Y-m-d',$questionInfo['reply']['createtime']));
//	print_r($questionInfo['reply']['createtime']);exit;
		$createdate = date('Y年m月',$questionInfo['reply']['createtime']);
		$template->setValue("createdate",$createdate );
		$template->save("Public/reply_{$userid}_{$qid}_{$replyid}.docx");
		
	}
		
		
		//print_r($template);
		//$template->setValue('Name', 'Somebody someone');
		//$template->setValue('Street', 'Coming-Undone-Street 32');
	//	$objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
	//	$fileName = "word报表".date("YmdHis");
		$file=fopen('./Public/'.$filename,"r");
		$size = filesize('./Public/'.$filename);
		
		header("Content-type: application/doc");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".$size);
		header("Content-Disposition:attachment;filename=".$filename); 
		echo fread($file, $size);
		fclose($file);

	}

}