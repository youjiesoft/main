<?php
/**
 * @Title: MisSystemOrdernoModel 
 * @Package package_name
 * @Description: 编码方案模型
 * @author 黎明刚 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年11月3日 上午11:05:06 
 * @version V1.0
 */
class MisSystemOrdernoModel extends CommonModel{
   	protected $trueTableName = 'mis_system_orderno';
   	
   	/*
   	 * 和数据库missystemorderno表进行数据键值对匹配
   	 */
   	public $arr = array(
   			'1'=>'one',
   			'2'=>'two',
   			'3'=>'three',
   			'4'=>'four',
   			'5'=>'five',
   			'6'=>'six',
   			'7'=>'seven',
   			'8'=>'eight',
   			'9'=>'nine',
   			'10'=>'ten',
   			'11'=>'eleven',
   			'12'=>'twelve',
   			'13'=>'thirteen',
   	);
   	
   	public function validateOrderno($tablename,$orderno,$id,$companyid){
   		$where = array();
   		$where['tablename'] = $tablename;
   		//查询当前tablename是否存在编码方案
   		$vo = $this->where($where)->find();
   		$data = array();
   		if($vo){
   			//实例化模型
   			$model = D($tablename);
   			$where = array();
   			$where['orderno'] = $orderno;
   			if($id){
   				//传入ID表示修改,修改将排除当前编码
   				$where['id'] = array("neq",$id);
				//验证是否存在下级以及编码是否发生变化
   				$list = $model->where("parentid = ".$id ." and status = 1")->find();
   				//查询当前id的orderno值
   				$tableorderno = $model->where("id = ".$id)->getField("orderno");
   				if($list && $orderno!=$tableorderno){
   					//编码已存在
   					$data['result'] = false;
   					$data['altMsg'] = "存在下级编码，请勿修改！";
   					return $data;
   				}
   			}
   			if($tablename=="MisSystemDepartment"){
   				$where['companyid']=$companyid;
   				//排除公司选项
   				//$where['iscompany']=0;
   			}
   			//过滤所有状态为1的单据
   			$where['status']=1;
   			$tableid = $model->where($where)->getField("id");
   			if($tableid){
   				//编码已存在
   				$data['result'] = false;
   				$data['altMsg'] = "当前编码已存在，请更换";
   				return $data;
   			}
   			//存在编码方案，则验证编码方案是否正常
   			$lenght = strlen($orderno);
   			//第一步、验证最大长度
   			if($vo['maxlenght']< $lenght){
   				//超出了最大长度
   				$data['result'] = false;
   				$data['altMsg'] = "编码超出最大长度,请按照编码方案填写。";
   				return $data;
   			}
   			//第二步、验证最大级数 最后得到最大级数的字段总个数
   			$maxlevels = $vo['maxlevels'];
   			$maxleng = 0;
   			$bool = 0; // 存储orderno字符集在几个级别上
   			for($i = 1; $i<=$maxlevels ;$i++){
   				$maxleng += intval($vo[$this->arr[$i]]);
   				if($maxleng == $lenght){
   					$bool = $i;
   					break;
   				}
   			}
   			if($bool){
   				if($vo['levelreadonly']< $bool){
   					$map = array();
   					$map['tablename'] = $tablename;
   					$this->where($map)->setField(levelreadonly,$bool);
   				}
   				if($bool == 1){
   					//表示第一级，及顶级节点
   					$data['parentid'] = 0;
   					$data['result'] = true;
   					return $data;
   				}else{
   					$levelen = 0;
   					for($x = 1; $x<$bool ;$x++){
   						$levelen += intval($vo[$this->arr[$x]]);
   					}
   					//截取字符串长度
   					$result = substr ($orderno, 0,$levelen);
   					//验证上级的类型是否存在
   					$where = array();
   					$where['orderno'] = $result;
   					if($id){
   						//修改，排除本身修改为其下级数据。
   						$where['id'] = array('neq',$id);
   					}
   					$where['status'] = 1;
   					if($tablename=="MisSystemDepartment"){
   						$where['companyid']=$companyid;
   						//$where['iscompany']=0;
   					}
   					$parentvo = $model->where($where)->find();
   					if($parentvo){
   						//表示第一级，及顶级节点
   						$data['parentid'] = $parentvo['id'];
   						$data['result'] = true;
   						return $data;
   					}else{
   						//超出了最大长度
   						$data['result'] = false;
   						$data['altMsg'] = "无上级编码存在,请先填写一级编码。";
   						return $data;
   					}
   				}
   			}else{
   				//不符合编码方案规则。
   				$data['result'] = false;
   				$data['altMsg'] = "编码不符合规则,请按照编码方案填写。";
   				return $data;
   			}
   		}else{
   			$data['result'] = false;
   			$data['altMsg'] = "未添加编码方案";
   			return $data;
   		}
   		
   	}
}
?>