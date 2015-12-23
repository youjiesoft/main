<?php
class ClearBomAction extends Action{

	function checkBOM ($filename , $auto = 1) {
		
		$contents = file_get_contents($filename);
		$charset[1] = substr($contents, 0, 1);
		$charset[2] = substr($contents, 1, 1);
		$charset[3] = substr($contents, 2, 1);
		if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
			if ($auto == 1) {
				$rest = substr($contents, 3);
				file_put_contents($filename, $rest.'<hr/>');
				return ("<font color=red>DIR:{$filename} BOM found, automatically removed.</font>");
			} else {
				return ("<font color=red>DIR:{$filename} BOM found.</font>");
			}
		}
		else return ("BOM Not Found.");
	}

	function index(){
		$edit = intval($_GET['edit']);
		import('@.ORG.FileUtil');
		$fileObj = new FileUtil();
		$dir= ROOT.'/Tpl/default';

		$data = $fileObj->getDir($dir);
		$fileDir = array();

		foreach ($data['dir'] as $k=>$v){
			$fileDir[] = $fileObj->getFile($v);
		}
		foreach ($fileDir as $k=>$v){
			foreach ($v as $key=>$val){
				// 目录
				foreach ($val as $k1=>$v1){
					// 文件名
					echo $key.'/'.$v1.':'.$this->checkBOM($key.'/'.$v1 , $edit).'<hr />';
				}
				//echo $key.'/'.$val.':'.$this->checkBOM($key.'/'.$val).'<hr />';
			}
		}

	}
	function css() {
		$edit = intval($_GET['edit']);
		$dir= ROOT;
		$dirRoot =  substr($dir , 0 , strlen($dir)-5);
		import('@.ORG.FileUtil');
		$fileObj = new FileUtil();
		$data = $fileObj->getDir($dirRoot.'Public');
		foreach ($data['dir'] as $k=>$v){
			$fileDir[] = $fileObj->getFile($v);
		}
		foreach ($fileDir as $k=>$v){
			foreach ($v as $key=>$val){
				// 目录
				foreach ($val as $k1=>$v1){
					$cssFile = $key.'/'.$v1;
					$cssPathArr = pathinfo($cssFile);
					if('css' == $cssPathArr['extension']){
						// 文件名
						echo $key.'/'.$v1.':'.$this->checkBOM($key.'/'.$v1 , $edit).'<hr />';
					}
				}
				//echo $key.'/'.$val.':'.$this->checkBOM($key.'/'.$val , $edit).'<hr />';
			}
		}

	}

	function config() {
		$edit = intval($_GET['edit']);
		$dir= ROOT.'';

		import('@.ORG.FileUtil');
		$fileObj = new FileUtil();
		$data = $fileObj->getDir($dir);
		$fileDir = array();
		foreach ($data['dir'] as $k=>$v){
			if($dir.'\Conf' == $v){
				$fileDir[] = $fileObj->getFile($v);
			}
		}

		foreach ($fileDir as $k=>$v){
			foreach ($v as $key=>$val){
				// 目录
				foreach ($val as $k1=>$v1){
					// 文件名
					echo $key.'/'.$v1.':'.$this->checkBOM($key.'/'.$v1 , $edit).'<hr />';

				}
				//echo $key.'/'.$val.':'.$this->checkBOM($key.'/'.$val , $edit).'<hr />';
			}
		}

	}
}