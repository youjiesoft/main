<?php
class ToolBarConfigModel extends CommonModel{
	protected $trueTableName = 'mis_system_toolbar_config';
//	protected $autoCheckFields = false;
	public function writeoveronly($file,$list){
		$this->writeover($file,"return ".$this->pw_var_export($list).";\n",true);
	}
	public function writeovertwo($file,$list){
		$str = '$original = ';
		$str .= $this->pw_var_export($list).";\n";
		$str .= "\$extedsTool = require \"toolbar.extensionExtend.inc.php\";\n";
		$str .= 'return array_merge($original , $extedsTool);';
		$this->writeover($file,$str,true);
	}
}