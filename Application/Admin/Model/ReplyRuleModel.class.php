<?php 
namespace Admin\Model;
use Think\Model;

class ReplyRuleModel extends Model
{
	Protected $autoCheckFields = false;
	private $objReader ;
	private $objPHPExcel ;
	
	public function __construct(){
		@import("Org.Util.PHPExcel");
		$file_name="./Public/questionnairesRule.xlsx";
		
		if (!file_exists($file_name)) {
			die('no file!');
		}
		$extension = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );

		if ($extension =='xlsx') {
			$this->objReader = new \PHPExcel_Reader_Excel2007();
			$this->objPHPExcel = $this->objReader ->load($file_name);
		} else if ($extension =='xls') {
			$this->objReader = new \PHPExcel_Reader_Excel5();
			$this->objPHPExcel =$this->objReader ->load($file_name);
		} else if ($extension=='csv') {
			$this->objReader = new \PHPExcel_Reader_CSV();

			//默认输入字符集
			$this->objReader->setInputEncoding('GBK');

			//默认的分隔符
			$this->objReader->setDelimiter(',');

			//载入文件
			$this->objPHPExcel = $this->objReader->load($file_name);
		}
		
		
		
				
		//$objReader = \PHPExcel_IOFactory::createReader('Excel5');
		//$objPHPExcel = $objReader->load($file_name,$encode='utf-8');
		
		
	}
	
	public function ruleList(){
		$sheet = $this->objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$rules = array();
		for($i=2;$i<=40;$i++){
			if($this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue() && $this->objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue()=='启用'){
				$rules[$this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()]['code'] =  $this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
				$rules[$this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()]['exp'] = $this->objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
				$rules[$this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()]['msg'] = $this->objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
				$rules[$this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()]['from'] = $this->objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
				$rules[$this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()]['status'] = $this->objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
				$rules[$this->objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()]['num'] = $this->objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
			}
		}	
		
		return  $rules;
	}
	
	
	
}