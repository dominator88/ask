<?php
namespace Home\Controller;
use Think\Controller;
/*
*	基础控制器
*/
class BaseController extends Controller{
	
	/**
	 * 空操作处理
	 */
    public function _empty($name){
        $this->assign('msg',"你的思想太飘忽，系统完全跟不上....");
        $this->display('404');
    }
	
	/**
     * ajax程序验证,只要不是会员都返回-999
     */
    public function isUserLogin() {
    	$USER = session('User');
		if (empty($USER) || ($USER['userId']=='')){
			if(IS_AJAX){
				$this->ajaxReturn(array('status'=>-999,'url'=>'Users/login'));
			}else{
				$this->redirect("Users/login");
			}
		}
	}
	
	/**
    * 检查登录状态
    */
   public function checkLoginStatus(){
   	   $USER = session('User');
	   if (empty($USER)){
	   	    die("{status:-999}");
	   }else{
	   		die("{status:1}");
	   }
   }
	
	
}