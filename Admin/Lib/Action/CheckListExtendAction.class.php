<?php
class CheckListExtendAction extends CommonAction {
	function index() {
		$html = <<<EOF
		<h1>检查 ./Dynamicconf/Models 下的项目是否缺失listExtend.inc.php文件</h1>
EOF;
		
		$this->checkFile ();
	}
	protected function checkFile() {
		import ( '@.ORG.FileUtil' );
		$dir = DConfig_PATH . '/Models';
		$obj = new FileUtil ();
		$chekDir = ROOT . '/nbm';
		$obj->checkFileAndCreateAppend ( $dir, 'listExtend.inc.php', $dir . '/listExtend.inc.php', '' );
		
		var_dump ( $fileArr );
	}
}