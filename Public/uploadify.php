<?php
function createFolders_upload($path) {
        if (!file_exists($path)) {
            createFolders_upload(dirname($path));
            @mkdir($path, 0777);
        }
}
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['uploadpath'] . '/'.date("Y/m/d/",time());
	$targetPath =  str_replace('//','/',$targetPath);

	if(!file_exists($targetPath)){
		createFolders_upload($targetPath);
	}
	$arr=explode(".",$_FILES['Filedata']['name']);
	$newname= rand("100","999").time().".".end($arr);
	$targetFile =  str_replace('//','/',$targetPath) . $newname;
	if(file_exists($targetFile)){
		unlink($targetFile);
	}
	$fileTypes = array('jpg', 'gif', 'png', 'jpeg','doc','xls','csv','zip','pdf','xlsx','ppt','docx','rar','html','htm','apk');
	if (in_array(end($arr),$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		$s=explode($_REQUEST['uploadpath'],$targetFile);
		echo $_REQUEST['uploadpath'].$s[1];
		//echo $targetFile;
	} else {
		echo 'Invalid file type.';
	}
}
?>