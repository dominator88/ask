<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

//企业用户管理控制器类
class ClassController extends CommonController{
	
	
	
	public function index(){
		$arr = M('Class')->select();
		
		$class =  new \Think\Tree($arr);
		$classstr = $class->get_tree_diy(1);
	
	//	$classstr =$class->get_tree(1);
		$this->assign('classstr',$classstr);
		$this->assign('umark','company');
		$this->display();
		
	}
	
	
	
	public function edit_ajax(){
		if(IS_POST){
			$m = D('Admin/Class');
			$id = I('id');
			$catname = I('catname');
			$code = I('code');
			$m = M('Class');
			$m->create();
			
			if($id){
				$flag = $m->save();
				$data['status'] = 1;
				$data['msg'] = '更新成功';
				
			}else{
				$id = $m->add();
				$data['status'] = 2;
				$data['id'] = $id;
				$data['msg'] = '添加成功';
				$flag = 1;
			}
			
			if($flag){
				$this->ajaxReturn($data);
			}else{
				$data['status'] = 0;
				$data['msg'] = '执行操作失败';
				
				$this->ajaxReturn($data);
			}
		}
		
	}
	
	public function del_ajax(){
		if(IS_AJAX){
			$m = D('Admin/Class');
			$flag = $m->delete(I('id'));
			if($flag){
				$data['status'] = 1;
				$data['msg'] = '删除成功';
			}else{
				$data['status'] = 0;
				$data['msg'] = '删除失败';
			}
			$this->ajaxReturn($data);
			
		}
		
	}
	
	
}



?>