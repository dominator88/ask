<?php



function LEFT($str,$len){
	return (int)substr($str,0,$len);
	
}

function LEN($str){
	if($str){
		return strlen($str);
	}else{
		return 0;
	}
	
}

function ISNUM($str){
	if(is_numeric($str)){
		return true;
	}else{
		return false;
	}
}

function INLIST($str,$string){
	if(empty($str)){return false;}
	if(	strpos($string,$str)!==false){
		return true;
	}else{
		return false;
	}
}

function INARRAY($str,$arr){
	
	if(empty($str)){return 0;}
	
	if(	in_array($str,$arr)){
		return true;
	}else{
		return 0;
	}
}

function NOTNULL($str){
	if($str){
		return !empty($str);
	}else{
		return 0;
	}
	
}

function VERIFYCREDITCODE($str){
	if(!$str){
		return true;
	}
	return preg_match('/^(?!I|O|Z|S|V)[\dA-Z][\d]{7}[\w]{10}$/i',$str);
}

//废弃的
function TQSJ($str){
	$name = get_variable_name($str);
	return $last[$name];
}

function CURRENTBGQ(){
	
}

function BGQ(){
	
}

function SQSJ(){
	
}
function validata_if($str1,$str2,$str3){
	
	if($str1 ){ 
		if(isset($str2)){
			if($str2){
				if(isset($str3)){
					if($str3){
						return true;
					}else{
						return 0;
					}
				}
				return true;
			}else{
				return 0;
			}
		}else{
			return true;
		}
	}else{
		if(isset($str2)){
			return true;
		}
		return 0;
	}
}

function validata_and($str1=true,$str2=true,$str3=true){
	if($str1 && $str2 && $str3){
		return 1;
	}else{		
		return 0;
	}
}


function validata_or($str1,$str2,$str3,$str4){
	if($str1 || $str2 || $str3 || $str4){
		return true;
	}else{		
		return false;
	}
}

function in_qhdm($str,$arr){
	return array_key_exists($str,$arr);
}

function validata_qygm($cyrs,$yysr){

	$g1=$g2=0;
	if($cyrs >= 1000 ){
		$g1 = 1;
	}elseif($cyrs >=300 && $cyrs < 1000 ){
		$g1 = 2;
	}elseif($cyrs >= 20 && $cyrs < 300 ){
		$g1 = 3;
	}elseif($cyrs <20 ){
		$g1 = 4;
	}else{
		$g1 = 0;
	}
	
	if($yysr >= 400000){
		$g2 = 1;
	}elseif($yysr >= 20000 && $yysr < 400000){
		$g2 = 2;
	}elseif($yysr >= 3000 && $yysr < 20000){
		$g2 = 3;
	}elseif( $yysr < 3000){
		$g2 = 4;
	}else{
		$g2 = 0;
	}
	return $g1>=$g2?$g1:$g2;
}
/** 
* @param String $var   要查找的变量 
* @param Array  $scope 要搜寻的范围 
* @param String        变量名称 
*/  
function get_variable_name(&$var, $scope=null){  
  
    $scope = $scope==null? $GLOBALS : $scope; // 如果没有范围则在globals中找寻  
  
    // 因有可能有相同值的变量,因此先将当前变量的值保存到一个临时变量中,然后再对原变量赋唯一值,以便查找出变量的名称,找到名字后,将临时变量的值重新赋值到原变量  
    $tmp = $var;  
      
    $var = 'tmp_value_'.mt_rand();  
    $name = array_search($var, $scope, true); // 根据值查找变量名称  
  
    $var = $tmp;  
    return $name;  
} 



/**
 * 根据HTML代码获取word文档内容
 * 创建一个本质为mht的文档，该函数会分析文件内容并从远程下载页面中的图片资源
 * 该函数依赖于类MhtFileMaker
 * 该函数会分析img标签，提取src的属性值。但是，src的属性值必须被引号包围，否则不能提取
 * 
 * @param string $content HTML内容
 * @param string $absolutePath 网页的绝对路径。如果HTML内容里的图片路径为相对路径，那么就需要填写这个参数，来让该函数自动填补成绝对路径。这个参数最后需要以/结束
 * @param bool $isEraseLink 是否去掉HTML内容中的链接
 */
function getWordDocument( $content , $absolutePath = "" , $isEraseLink = true )
{	//@import("Org.Util.MhtFileMaker");
	
    $mht = new \Org\Util\MhtFileMaker;

    if ($isEraseLink)
        $content = preg_replace('/<a\s*.*?\s*>(\s*.*?\s*)<\/a>/i' , '$1' , $content);   //去掉链接
	//$content = "<xml><w:WordDocument><w:View>Print</w:View>< /xml>".$content;
    $images = array();
    $files = array();
    $matches = array();
    //这个算法要求src后的属性值必须使用引号括起来
    if ( preg_match_all('/<img[.\n]*?src\s*?=\s*?[\"\'](.*?)[\"\'](.*?)\/>/i',$content ,$matches ) )
    {
        $arrPath = $matches[1];
        for ( $i=0;$i<count($arrPath);$i++)
        {
            $path = $arrPath[$i];
            $imgPath = trim( $path );
            if ( $imgPath != "" )
            {
                $files[] = $imgPath;
                if( substr($imgPath,0,7) == 'http://')
                {
                    //绝对链接，不加前缀
                }
                else
                {
                    $imgPath = $absolutePath.$imgPath;
                }
                $images[] = $imgPath;
            }
        }
    }
    $mht->AddContents("tmp.html",$mht->GetMimeType("tmp.html"),$content);
    
    for ( $i=0;$i<count($images);$i++)
    {
        $image = $images[$i];
        if ( @fopen($image , 'r') )
        {
            $imgcontent = @file_get_contents( $image );
            if ( $content )
                $mht->AddContents($files[$i],$mht->GetMimeType($image),$imgcontent);
        }
        else
        {
            echo "file:".$image." not exist!<br />";
        }
    }
    
    return $mht->GetFile();
}


?>