<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

//公告管理类
class AnnouncementController extends CommonController{
	
	private $announ_model ;
	
	function _initialize() {
		parent::_initialize();
		$this->announ_model = D("Announcement");
	}
	
	

	public function index(){

		$data = $this->announ_model->lists();
		$this->assign('data',$data);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data = I();
			$data['createtime'] = time();
			$id = $this->announ_model->addData($data);
	
			if($id){
				$this->success("添加成功，成功添加的公告ID为$id ");
			}else{
				$this->error("添加失败");
			}
		}else{
			$this->display();
		}
	}
	
	public function edit(){
		$id = I('id');
		$data = $this->announ_model->getinfoById($id);
		$this->assign($data);
		$this->display();
	}
	
	public function toedit(){
		$data = I();
		
		if($this->announ_model->edit($data)){
			
			$this->success("更新成功",U('Admin/Announcement/edit',array('id'=>$data['id'])));
		}else{
			$this->error("更新失败");
		}
		
	} 
	
	public function del(){
		$id = I('id');
		
		if($this->announ_model->deleteId($id)){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}
	
	
	
}
?>