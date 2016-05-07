<?php
/**
 * @Title: ShowAddUserWidget
 * @Package package_name
 * @Description: todo(建立后台用户)
 * @author renling
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-9-9 下午5:19:53
 * @version V1.0
 */
class ShowAddUserWidget extends Widget{
	public function render($data){
		$modulename = MODULE_NAME;// 当前类名
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		//判断是修改页面，如果存在编码，只获取相关信息
		if($data[2]&& $data[2]!= ''){
			$ordernoInfo=$scnmodel->GetRules($data[1],$modulename);
			$ordernoInfo['orderno']=$data[2];
		}else{
		   $ordernoInfo = $scnmodel->getOrderno($data[1],$modulename);
		}
		$orderno =$ordernoInfo['orderno'];
		$MisSystemCompanyModel=D('MisSystemCompany');
		$MisSystemCompanyVo=$MisSystemCompanyModel->where("status=1")->find();
		//随机生成id选择器
		$num=rand(1000,999999);
		$str="isadduser".$num;
		$loginurl=$MisSystemCompanyVo['loginurl'];
		$html = "<input type='hidden' name='loginurl' value='".$loginurl."'>";
		$html= '<script src="__PUBLIC__/Js/addUser.js" type="text/javascript"></script>';
		$html .='<div class="tml-form-row"><label> 是否建立账号：</label><div class="left tml-checkbox">';
		$html .='<input type="checkbox" name="isaddUser" checked="checked" onclick="changeUser(this,\''.$str.'\')" value="1" /></div></div>';
		$html .='<div id="'.$str.'">';
		$html .='<div class="tml-row">';
		$html .='				<div class="tml-form-col">';
		$html .='					<label>英文账号：</label>';
		$html .='					<input type="text" value="'.$orderno.'"  placeholder="输入字母，数字组成的账号" onblur="checkUser(this,\'accountenglish\')"  onfocus="hidediv(\'accountenglish\')" class="required  alphanumeric" name="accountenglish" />';
		$html .='					<span for="accountenglish" style="display:none"  id="accountenglishspan" generated="true" class="error" title="必填字段">必填字段</span>	';
		$html .='				</div>';
		$html .='			<span id="accountenglish" style="display:none;" class="tml-form-text "></span>';
		$html .='		</div>';
		$html .='		<div class="tml-row">';
		$html .='			<div class="tml-form-col">';
		$html .='				<label>中文账号：</label>';
		$html .='				<input type="text"   onblur="checkUser(this,\'zhname\')"  onfocus="hidediv(\'zhname\')"  name="zhname" />';
		$html .='			<span for="zhname" style="display:none" id="zhnamespan" generated="true" class="error" title="必填字段">必填字段</span>';
		$html .='		</div>				';
		$html .='				<span id="zhname"  style="display:none"  class="tml-form-text">用户名可用</span>';
		$html .='			</div>';
// 		$html .='			<div class="tml-row">';
// 		$html .='	            <div class="tml-form-col">';
// 		$html .='					<label>邮件通知：</label>';
// 		$html .='					<input type="text" class="  email" onclick="changeemail(this)"  minlength="3" maxlength="20" name="useremail" />';
// 		$html .='				</div>';
// 		$html .='				<div class="tml-mt5">';
// 		$html .='				<input class="switch-check" checked="checked" value="1" onclick="changeemail(this)"  name="issendemail"  type="checkbox">';
// 		$html .='</div>';
// 		$html .='</div>';
		$html .='<div class="tml-form-col">';
		$html .='<label>登陆地址：</label>';
		if($loginurl){
			$html.='<input type="text"    name="userurl" value="'.$loginurl.'" />';
		}else{
			$html.='<input type="text" class=""  placeholder="请输入系统登陆地址" name="userurl"  />';
		}
		$html.='</div></div>';
		return $html;
	}
}