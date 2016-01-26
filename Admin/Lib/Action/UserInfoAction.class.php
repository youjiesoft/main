<?php
/**
 * @Title: UserInfoAction
 * @Package package_name
 * @Description: todo(用户个人档案)
 * @author 杨东
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-29 上午10:42:49
 * @version V1.0
 */
class UserInfoAction extends CommonAction {
	public function index(){
		$model=D("User");
		switch ($_REQUEST['step']) {
			case 'uploadimg':
				$this->update_picture();
				exit;
				break;
			case 'signature':
				$this->imgChange();
				exit;
				break;
			case 'findpassword':
				$this->lookupfindpassword();
				exit;
				break;
			case 'update':
				$this->updateContact();
				exit;
				break;
			case 'contact':
				$this->contact();
				exit;
				break;
			case 'workbench':
				$defaultworkbench  = isset($_POST['id']) ? $_POST['id'] : 0;
				Cookie::set("defaultworkbench",$defaultworkbench,2592000);
				exit;
				break;
				//操作工作台二级
			case 'opwork':
				$opworkbenchoption  = isset($_POST['opid']) ? $_POST['opid'] : 0;
				Cookie::set("opworkbenchoption",$opworkbenchoption,2592000);
				//操作工作台三级
				$subopworkbenchoption  = isset($_POST['subopid']) ? $_POST['subopid'] : 0;
				if($subopworkbenchoption){
					Cookie::set("subopworkbenchoption",$subopworkbenchoption,2592000);
				}else{
					Cookie::set("subopworkbenchoption",0,2592000);
				}
				exit;
				break;
			case 'setisnewmsg':
				//设置是否显示提醒信息
				$model->where('id = ' . $_SESSION [C ( 'USER_AUTH_KEY' )])->setField('isnewmsg', $_POST['isnewmsg']);
				$model->commit();
				exit;
				break;
			case 'solidotpic':
				$this->lookupsolidotpic();
				exit;
				break;
				//保存配图
			case 'solidotpic_save':
				$this->lookupsolidotpicsave();
				exit;
				break;
		}
		$map['id']=$_SESSION [C ( 'USER_AUTH_KEY' )];
		$vo=$model->where($map)->find();
		if($_POST['stype']) {
			$oldImgName = $vo['imgname'];
			$oldFilePath = SIGNATURE_PATH."/".$oldImgName;
			//生成签章
			import('@.ORG.Image');
			$image = new Image();
			$image->setSignature($vo['id'],$vo['name']);
			$model->startTrans();
			$list1 = $model->where('id='.$vo['id'])->setField('imgname',$vo['id'].".gif");
			if ($list1) {
				$model->commit();
				$vo['imgname']=$vo['id'].".gif";
			} else {
				$model->rollback();
			}
			//最后删除原有图片
			if(file_exists($oldFilePath) && $vo['imgname'] != $oldImgName) {
				unlink($oldFilePath);
			}
		}
		//实例化mis_hr_personnel_person_info表
		$misHrPersonnelPersonInfo=D("mis_hr_personnel_person_info");
		$misHrPersonnelPersonInfoVo=$misHrPersonnelPersonInfo->where('id='.$vo['employeid'])->find();
		$this->assign("misHrPersonnelPersonInfoVo",$misHrPersonnelPersonInfoVo);
		$defaultworkbench = Cookie::get("defaultworkbench");
		if(!isset($defaultworkbench)){
			$defaultworkbench = 0;
		}
		//获取工作台权限
		//把操作工作台暂时取消，修改为邮箱。 liminggang
		//$arr = R("Index/lookupisOpWorkbench");
		// 		if(!$arr){
		// 			$defaultworkbench = 0;
		// 		}
		$this->assign("defaultworkbench",$defaultworkbench);
		$this->assign('isopworkbench',$arr);
		$opworkbenchoption = Cookie::get("opworkbenchoption");
		$subopworkbenchoption = Cookie::get("subopworkbenchoption");
		if(!$opworkbenchoption){
			$opworkbenchoption = 0;
			$subopworkbenchoption = -1;
		}
		$this->assign("opworkbenchoption",$opworkbenchoption);
		$this->assign("subopworkbenchoption",$subopworkbenchoption);
		$this->assign("vo",$vo);
		$this->display();
	}
	private function lookupfindpassword(){
		//查询该用户信息
		$model=D('user');
		$map['id']=$_SESSION [C ( 'USER_AUTH_KEY' )];
		if($_POST['id']){
			$date['id']=$_POST['id'];
			$date['mobile']=$_POST['mobile'];
			$date['email']=$_POST['email'];
			//序列化找回密码问题
			if($_POST['questionpwd']){
				$questionList=array();
				foreach ($_POST['questionpwd'] as $qkey=>$qval){
					$questionList[]=array(
							'questionpwd'=>$qval,
							'answerpwd'=>$_POST['answerpwd'][$qkey],
					);
				}
				$date['questionpwd']=serialize($questionList);
			}
			$result=$model->save($date);
			if($result===false){
				$this->error("设置失败！");				
			}else{
				$this->success("设置成功！");
			}
		}else{
			$vo=$model->where($map)->find();
			$vo['questionpwd']=unserialize($vo['questionpwd']);
			$this->assign("vo",$vo);
			$this->display('findpassword');
		}
	}
	/**
	 * @Title: solidotpic 
	 * @Description: todo(修改首页配图)   
	 * @author yuansl 
	 * @date 2014-6-6 下午2:39:16 
	 * @throws
	 */
	private function lookupsolidotpic(){
		//默认图片
		$userModel = D("User");
		$vo = $userModel->where("id = ".$_SESSION [C ( 'USER_AUTH_KEY' )])->find();
		$this->assign("vo",$vo);
		$this->display("lookupsolidotpic");
	}
	/**
	 * @Title: lookuploadimg 
	 * @Description: todo(图片上传)   
	 * @author yuansl 
	 * @date 2014-6-6 下午3:16:43 
	 * @throws
	 */
	public function lookuploadimg(){
		//添加成功后，临时文件夹里面的文件转移到目标文件夹
		$fileinfo=pathinfo($_POST['upload_name']);
		$from = UPLOAD_PATH_TEMP.$_POST['upload_name'];//临时存放文件
		if( file_exists($from) ){
			$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
			if( !file_exists($p) ) {
				$this->createFolders($p); //判断目标文件夹是否存在
			}
			$to= UPLOAD_PATH.$_POST['upload_name'];
			rename($from,$to);
			echo $_POST['upload_name'];
		}
	}
	/**
	 * @Title: lookupsolidotpicsave 
	 * @Description: todo(保存配图)   
	 * @author yuansl 
	 * @date 2014-6-6 下午3:04:41 
	 * @throws
	 */
	private function lookupsolidotpicsave(){
		$userModel = D("User");
		$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$re = $userModel->where("id = ".$userid)->setField('solidpath',$_POST['swf_upload_solidotlist_add_name']);
// 		echo $userModel->getLastSql();
// 		dump($re);
		if($re){
			$this->success("操作成功!");
		}else{
			$this->error("操作失败!");
		}
	}
	/**
	 * @Title: resize (等比缩放)
	 * @Description: todo(上传图片之后如果图片尺寸过将进行等比缩放)
	 * @param resource $srcImage
	 * @param string $toFile
	 * @param string $maxWidth
	 * @param string $maxHeight
	 * @param string $imgQuality
	 * @return void|string|boolean
	 * @author yuansl
	 * @date 2014-1-21 上午11:37:35
	 * @throws
	 */
	private function resize($srcImage,$toFile,$maxWidth = 100,$maxHeight = 100,$imgQuality=100){
		list($width, $height, $type, $attr) = getimagesize($srcImage);
		if($width < $maxWidth  || $height < $maxHeight) return ;
		switch ($type) {
			case 1: $img = imagecreatefromgif($srcImage); break;
			case 2: $img = imagecreatefromjpeg($srcImage); break;
			case 3: $img = imagecreatefrompng($srcImage); break;
		}
		$scale = min($maxWidth/$width, $maxHeight/$height); //求出绽放比例
		if($scale < 1) {
			$newWidth = floor($scale*$width);
			$newHeight = floor($scale*$height);
			$newImg = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
			$newName = "";
			$toFile = preg_replace("/(.gif|.jpg|.jpeg|.png)/i","",$toFile);
			switch($type) {
				case 1: if(imagegif($newImg, "$toFile$newName.gif", $imgQuality))
					return "$newName.gif"; break;
				case 2: if(imagejpeg($newImg, "$toFile$newName.jpg", $imgQuality))
					return "$newName.jpg"; break;
				case 3: if(imagepng($newImg, "$toFile$newName.png", $imgQuality))
					return "$newName.png"; break;
				default: if(imagejpeg($newImg, "$toFile$newName.jpg", $imgQuality))
					return "$newName.jpg"; break;
			}
			imagedestroy($newImg);
		}
		imagedestroy($img);
		return false;
	}
	/**
	 * @Title: lookuppersonpic
	 * @Description: todo(修改用户头像,弹出dialog)
	 * @author yuansl
	 * @date 2014-1-18 上午10:58:55
	 * @throws
	 */
	public function lookuppersonpic(){
		$defpic = "/Images/xyimages/organization/user_male.jpg";
		//在修改的时候,默认下显示头像
		$personid = $_SESSION['user_employeid'];
		$misHrPersonnelPersonInfo=D("mis_hr_personnel_person_info");
		$misHrPersonnelPersonInfoList=$misHrPersonnelPersonInfo->where('id='.$_SESSION['user_employeid'])->find();
		if($misHrPersonnelPersonInfoList != null){
			$picpath = "/".$misHrPersonnelPersonInfoList['picture'];
			$defpic = $picpath;
		}

		if($_POST['lookuppersonpicurl']){
			$defpic = "/Uploadstemp/".$_POST['lookuppersonpicurl'];
			$this->assign("uploadpic",1);
			//	放缩图片
			$filepath = PUBLIC_PATH."/Uploadstemp/".$_POST['lookuppersonpicurl'];
			$image_p = $filepath;
			$picname_arr = explode('/', $filepath);
			$count = count($picname_arr);
			//取得文件名
			$picname = explode('.', $picname_arr[$count-1]);
			$picname = $picname[0];
			//取得原图的类型,后缀
			$type= $picname[1];
			//执行放缩图片
			$this->resize($image_p,$filepath,$maxWidth = 450,$maxHeight = 350,$imgQuality=100);
		}
		$this->assign("defpic",$defpic);
		$this->display();
	}

	/**
	 * @Title: lookuppersoninfo
	 * @Description: todo(用户头像修改)
	 * @author 袁山林
	 * @date 2014-1-17 上午11:36:03
	 * @throws
	 */
	public function lookuppersoninfo(){
		$x1 = $_POST['personpicx1'];
		$y1 = $_POST['personpicy1'];
		$x2 = $_POST['personpicx2'];
		$y2 = $_POST['personpicy2'];
		// 源文件 -- 处理源文件路径为绝对路径
		$temppath = $_POST['personpicurl'];
		//构造真实的图片存放路劲
		// 		echo $temppath;
		$filepath = explode(__ROOT__."/Public", $temppath);
		$filepath = PUBLIC_PATH.$filepath['1'];//缓存文件的绝对路劲
		$truepath =str_replace("Uploadstemp/", "Uploads/", $filepath);
		// 		echo $truepath;
		$this->createFolders(dirname($truepath));
		// 设置最大宽高
		$width = $x2-$x1;
		$height = $y2-$y1;
		//获取新尺寸
		list($width_orig, $height_orig) = getimagesize($temppath);//取得原来图片的长和宽
		//重新取样
		$image_p = imagecreatetruecolor($width, $height);//创建一个目标图片底片,设置其长和宽
		$image = imagecreatefromjpeg($filepath);//对原来的图片进行取样
		$bool = imagecopyresampled($image_p, $image, 0, 0, $x1, $y1, $width, $height, $width, $height);
		//执行删除(原来缓存图片)操作
		imagejpeg($image_p, $truepath);//保存新的图像,使用原来的路径
		//存入该照片的相对路劲
		$picpath = explode("Uploads/", $truepath);
		$picpath = "Uploads/".$picpath['1'];//要存入数据库的路劲
		$misHrPersonnelPersonInfoModel=D("MisHrPersonnelPersonInfo");
		$personid = $_SESSION['user_employeid'];
		$result = $misHrPersonnelPersonInfoModel->where('id='.$personid)->setField("picture",$picpath);
		if($result ){
			$this->success("头像设置成功！");
		}else{
			$this->error("头像设置失败!");
		}
	}

	/**
	 * @Title: imgChange
	 * @Description: todo(上传签章)
	 * @author 杨东
	 * @date 2013-3-29 上午10:46:42
	 * @throws
	 */
	private function imgChange(){
		$id = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$path = SIGNATURE_PATH."/";
		$this->upload($id,$path);
	}

	/**
	 * @Title: upload
	 * @Description: todo(上传函数)
	 * @param $id 当前用户ID
	 * @param $path 上传路径
	 * @author 杨东
	 * @date 2013-3-29 上午10:26:45
	 * @throws
	 */
	public function upload($id,$path=''){
		import ( '@.ORG.UploadFile' );
		$upload = new UploadFile(); // 实例化上传类
		$upload->maxSize = C('maxSize') ; // 设置附件上传大小 3MB
		$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 上传类型设置
		$upload->savePath = $path? $path:C('savePath'); // 上传目录
		$upload->saveRule2=true;
		$upload->autoSub = false; // 是否开启子目录
		if(!$upload->upload()) { // 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		} else {
			$info =  $upload->getUploadFileInfo();
			$model = M("User");
			$u = $model->field("imgname")->where("id=$id")->find();
			$oldFilePath = $path.$u['imgname'];
			if(file_exists($oldFilePath)) {
				unlink($oldFilePath);
			}
			$model->imgname =$info[0]['savename'];
			$model->where("id=$id")->save();
			$this->success("上传签章成功！");
		}
	}
	public function lookupmishrpm(){
		$model = D ('MisHrBasicEmployee');
		$map['id'] = $_REQUEST['id'];
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		if($vo['chinaid']){
			$vo['age'] = $this->getAgeByToCarId($vo['chinaid'],false,false)+1;
		}
		$this->assign("vo",$vo);
		//获取公司
		$UserDeptDutyModel=D("UserDeptDuty");
		$UserDeptDutyList=$UserDeptDutyModel->where(" typeid=1 and  status=1 and employeid=".$_REQUEST['id'])->select();
		$this->assign("list",$UserDeptDutyList);
		$this->display();
	}
	/**
	 * @Title: update_picture
	 * @Description: todo(上传用户头像)
	 * @param $id 用户ID
	 * @author 杨东
	 * @date 2013-3-29 上午10:29:01
	 * @throws
	 */
	private function update_picture($id){
		$id = $_POST['id'];
		if ($id) {
			$modelname = M("mis_hr_personnel_person_info");
			$list = $modelname->where('id='.$id)->setfield('picture',$_POST['upload_name']);
			if ($list) {
				//添加成功后，临时文件夹里面的文件转移到目标文件夹
				$fileinfo=pathinfo($_POST['upload_name']);
				$from = UPLOAD_PATH_TEMP.$_POST['upload_name'];//临时存放文件
				if( file_exists($from) ){
					$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
					if( !file_exists($p) ) {
						$this->createFolders($p); //判断目标文件夹是否存在
					}
					$to= UPLOAD_PATH.$_POST['upload_name'];
					rename($from,$to);
					$topathinfo = pathinfo($to);
					$graypath = $topathinfo['dirname'].'/'. $topathinfo['filename'] .'gray.'. $topathinfo['extension'];
					getGrayImg($to, $graypath);
					$this->success ( L('_SUCCESS_'));
				}
			}
		} else {
			$this->error( L('_ERROR_') );
		}
	}

	//修改联系方式
	private function updateContact() {
		$MHPPIModel=M("mis_hr_personnel_person_info");
		if(false === $MHPPIModel->create()){
			$this->error(L('构建数据失败，输入数据不完整'));
		}else{
			if($MHPPIModel->save()){
				$UserModel=M("user");
				if(false === $UserModel->create()){
					$this->error(L('构建数据失败，输入数据不完整'));
				}else{
					if($UserModel->save()){
						$this->success("基本资料修改成功");
					}else{
						$this->error("基本资料修改失败");
					}
				}
			}else{
				$this->error("基本资料修改失败");
			}
		}
	}

	/**
	 * @Title: contact
	 * @Description: todo(加载联系方式页面)
	 * @author 杨东
	 * @date 2013-3-29 下午5:16:56
	 * @throws
	 */
	private function contact() {
		$UserModel=D("User");
		$map['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userList=$UserModel->where($map)->find();
		//实例化mis_hr_personnel_person_info表
		$misHrPersonnelPersonInfo=D("mis_hr_personnel_person_info");
		$misHrPersonnelPersonInfoList=$misHrPersonnelPersonInfo->where('id='.$userList['employeid'])->find();
		$this->assign("ul",$misHrPersonnelPersonInfoList);
		$this->display('contact');
	}

	/**
	 * @Title: 密码修改
	 * @Description: todo(密码修改)
	 * @author qchlian
	 * @date 2013-3-29
	 */
	public function changePwd()
	{
		$step = intval($_POST['step']);
		if($step){
			//对表单提交处理进行处理或者增加非表单数据
			if(md5($_POST['verify'])	!= $_SESSION['verify']) {
				$this->error('验证码错误！');
			}
			$map=array();
			$map['password']= pwdHash($_POST['oldpassword']);
			if(isset($_POST['account'])) {
				$map['account']=$_POST['account'];
			}elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
				$map['id']=$_SESSION[C('USER_AUTH_KEY')];
			}
			//检查用户
			$User =  M("User");
			if(!$User->where($map)->field('id')->find()) {
				$this->error('旧密码不符或者用户名错误！');
			}else {
				$User->password =pwdHash($_POST['repassword']);
				$result=$User->save();
				if(!$result){
					$this->error(L('_ERROR_'));
				}
				else{
					$this->success('密码修改成功！');
				}
			}
		}
		$this->display();
	}
}
?>