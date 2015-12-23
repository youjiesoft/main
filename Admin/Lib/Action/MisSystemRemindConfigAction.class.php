<?php
class MisSystemRemindConfigAction extends CommonAction{
	public function index(){
		$name = $this->getActionName();
		$model=D($name);
		$typetree = $model->getRemindTree("MisSystemRemindConfigBox");
		$this->assign('typetree',json_encode($typetree));
		if($_REQUEST['aname']){
			
			
			
			
			//查找配置文件中getFieldBy user表的字段
			$syscModel = D("SystemConfigDetail");
			$sysclist = $syscModel->getDetail($_REQUEST['aname'] ,'','','','');	
			$userfields = array();		
			foreach($sysclist as $key=>$val){
				if(strtolower($val['table']) == 'user'){
					$userfields[] = $val['name'];
				}					
			}
			//模型字段
			$tablename = D($_REQUEST['aname'])->getTableName();
			$dycModel = D("Dynamicconf");
			$fields = $dycModel->getTableInfo($tablename);
			$newfields = array();
			foreach($fields as $key=>$val){
				$newfields[$key]['title'] = $val['COLUMN_COMMENT'];
				$newfields[$key]['name'] = $key;
				if(in_array($key,$userfields)){
					$newfields[$key]['userfield'] = 1;
					//echo $key."<br/>";
				}				
			}
			
			//选择存表字段
			$map['actionname'] = $_REQUEST['aname'];
			//$map['createid'] = $_SESSION[C("USER_AUTH_KEY")];
			$map['status'] = 1;
			$vo = $model->where($map)->getField("field,fieldlegth");
			$userfield = M('mis_system_remind_config_userfield')->where($map)->getField("id,userfield");
			//echo $model->getlastsql();
			//对字段重新排序
			$temp = array();
			foreach($vo as $k=>$v){
				foreach($newfields as $k1=>$v1){
					if($k==$k1){
						$temp[$k] = $v1;
					}
				}
			}
			$temp2 = array();
			foreach($userfield as $v){
				foreach($newfields as $k1=>$v1){
					if($v==$k1){
						$temp2[$k1] = $v1;
					}
				}
			}
			$newfields = array_merge($temp,$temp2,$newfields);
			$this->assign('newfields',$newfields);
			$this->assign('actionname',$_REQUEST['aname']);
			if($vo){
				$this->assign("isedit","1");
			}
			$this->assign('vo',$vo);
			$this->assign('userfield',$userfield);
			//print_r($userfield);
		}else{
			
		}
		if($_REQUEST['jump']){
			$this->display("indexview");
		}else{
			$this->display();
		}
		
	}
	
	function insert(){
		$model = D($this->getActionName());
		$data=$_POST;
		$sqldata['actionname'] = $data['actionname'];
		//if($data['isedit']){
			$map['actionname'] = $data['actionname'];
			$model->where($map)->delete();
		//}
		$i=1;
		foreach($data['field'] as $key=>$val){
			$sqldata['field'] = $val;
			$sqldata['fieldlegth'] = (int)$data['fieldlegth'][$key]?(int)$data['fieldlegth'][$key]:10;
			$sqldata['fieldsort'] = $i;
			$i++;
			$ret = $model->add($sqldata);
			if(false === $ret){
				$this->error("数据编辑失败");
			}
		}
		$sqldata['actionname'] = $data['actionname'];
		$userfieldModel = M("mis_system_remind_config_userfield");
		//if($data['isedit']){
			$map['actionname'] = $data['actionname'];
			$userfieldModel->where($map)->delete();
		//}
		foreach($data['userfield'] as $key=>$val){
			$sqldata['userfield'] = $val;
			$ret = $userfieldModel->add($sqldata);
			if(false === $ret){
				$this->error("数据通知字段编辑失败");
			}
		}
		$this->success("数据编辑成功");
	}
	
	
	
	
	
	
	
}