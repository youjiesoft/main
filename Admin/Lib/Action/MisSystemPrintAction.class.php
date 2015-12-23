<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(打印配置控制器)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 上午9:16:42
 * @version V1.0
 */
class MisSystemPrintAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(列表数据展示过滤器)
	 * @param unknown_type $map
	 * @author liminggang
	 * @date 2013-6-1 上午9:17:25
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	/**
	 * @Title: lookupprintedit
	 * @Description: todo(页面自动跳转模板)
	 * @author liminggang
	 * @date 2013-6-1 上午9:22:22
	 * @throws
	 */
	public function lookupprintedit(){
		$model=D("MisSystemPrint");
		$map['id'] = intval($_REQUEST['id']) ? intval($_REQUEST['id']):0;
		$map['status']=1;
		$vo = $model->where($map)->find();

		$this->assign('shipping', $vo);
		$this->assign('shipping_id', $_REQUEST['id']);
		if($vo["modulename"]){
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($vo["modulename"],false);
			if ($detailList) {
				$this->assign ('selectlable',$detailList);
			}
		}
		$this->display();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改前的操作方法)
	 * @author liminggang
	 * @date 2013-6-1 上午9:39:52
	 * @throws
	 */
	public function _before_update(){
		if($_POST["old_print_bg"]!=$_POST["print_bg"] && $_POST["print_bg"]){
			$v = explode("Uploadstemp/",$_POST["print_bg"]);
			$fileinfo=pathinfo($v[1]);
			$from = UPLOAD_PATH_TEMP.$v[1];//临时存放文件
			if( file_exists($from) ){
				$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
				if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
				$to	= UPLOAD_PATH.$v[1];
				rename($from,$to);
				$_POST["print_bg"]= $v[1];
			}
		}
	}
	/**
	 * @Title: lookupprint
	 * @Description: todo(我总算晓得这个方法意思了，配置文件调用的打印模板)
	 * @author liminggang
	 * @date 2013-6-1 上午9:37:10
	 * @throws
	 */
	public function lookupprint(){
		$modulename=$_GET["modulename"];
		$id = $_GET["id"];
		$model = D($modulename);
		$list = $model->find($id);

		if($list){
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($modulename,false);//获取当前配置文件的动态配置

			$model2=M("mis_system_print");
			$shipping = $model2->where("modulename ='".$modulename."'")->find();
			//标签替换
			$temp_config_lable = explode('||,||', $shipping['config_lable']);
			if($temp_config_lable){
				foreach ($temp_config_lable as $temp_key => $temp_lable)
				{
					$temp_info = explode(',', $temp_lable);
					if (is_array($temp_info))
					{
						foreach($detailList as $k=>$v){
							if($v['name']==$temp_info[0]){
								if(count($v['func']) > 0){
									foreach($v['func'] as $k1=>$v1){
										$s="";
										if(!empty($v['extention_html_start'][$k1])){
											$s.=$v['extention_html_start'][$k1];
										}
										$s.=getConfigFunction($list[$v['name']],$v['func'][$k1],$v['funcdata'][$k1],$v);
										if(!empty($v['extention_html_end'][$k1])){
											$s.=$v['extention_html_end'][$k1];
										}
									}
									$temp_info[1] =$s;
								}else{
									$temp_info[1] =$list[$v['name']];
								}
								$temp_config_lable[$temp_key] = implode(',', $temp_info);
								break;
							}
						}
					}
				}
				$shipping['config_lable'] = implode('||,||',  $temp_config_lable);
				/* 取快递单背景宽高 */
				if (!empty($shipping['print_bg']))
				{
					$_size = @getimagesize($shipping['print_bg']);

					if ($_size != false)
					{
						$shipping['print_bg_size'] = array('width' => $_size[0], 'height' => $_size[1]);
					}
				}

				if (empty($shipping['print_bg_size']))
				{
					$shipping['print_bg_size'] = array('width' => '1000', 'height' => '900');
				}
				$this->assign('shipping', $shipping);
				$this->display();
			}
		}else{
			echo "非法操作";
			exit;
		}
	}
	/**
	 * @Title: lookupuploadimg
	 * @Description: todo(图片上传)
	 * @author liminggang
	 * @date 2013-6-1 上午9:26:08
	 * @throws
	 */
	public function lookupuploadimg()
	{
		//设置上传文件类型
		$allow_suffix = array('jpg', 'png', 'jpeg');
		$id = !empty($_POST['id']) ? intval($_POST['id']) : 0;
		//接收上传文件
		if (!empty($_FILES['bg']['name']))
		{
			if(!$this->get_file_suffix($_FILES['bg']['name'], $allow_suffix))
			{
				$this->error("只能上传jpg,png,jpeg格式图片");
			}

			$name = date('Ymd');
			for ($i = 0; $i < 6; $i++)
			{
				$name .= chr(mt_rand(97, 122));
			}
			$name .= '.' . end(explode('.', $_FILES['bg']['name']));
			$target = UPLOAD_PATH_TEMP . 'print/' . $name;

			if ( $this->move_upload_file($_FILES['bg']['tmp_name'], $target))
			{
				$src = WEB_PUBLIC_PATH . '/Uploadstemp/print/' . $name;
				echo '<script language="javascript">';
				echo 'parent.call_flash("bg_add", "' . $src . '");';
				echo '</script>';
				//$this->success("上传成功");
			}
		}
	}
	/**
	 * @Title: move_upload_file
	 * @Description: todo(移除上传方法)
	 * @param unknown_type $file_name
	 * @param unknown_type $target_name
	 * @return boolean
	 * @author liminggang
	 * @date 2013-6-1 上午9:28:23
	 * @throws
	 */
	private function move_upload_file($file_name, $target_name = '')
	{
		if (function_exists("move_uploaded_file"))
		{
			if (move_uploaded_file($file_name, $target_name))
			{
				@chmod($target_name,0777);
				return true;
			}
			else if (copy($file_name, $target_name))
			{
				@chmod($target_name,0777);
				return true;
			}
		}
		elseif (copy($file_name, $target_name))
		{
			@chmod($target_name,0777);
			return true;
		}
		return false;
	}
	/**
	 * @Title: get_file_suffix
	 * @Description: todo(图片上传格式验证)
	 * @param unknown_type $file_name
	 * @param unknown_type $allow_type
	 * @return unknown|boolean
	 * @author liminggang
	 * @date 2013-6-1 上午9:36:12
	 * @throws
	 */
	private function get_file_suffix($file_name, $allow_type = array())
	{
		$file_suffix = strtolower(array_pop(explode('.', $file_name)));
		if (empty($allow_type))
		{
			return $file_suffix;
		}
		else
		{
			if (in_array($file_suffix, $allow_type))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}
?>