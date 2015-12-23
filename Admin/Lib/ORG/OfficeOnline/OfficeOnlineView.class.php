<?php
class OfficeOnlineView {
	public function __construct(){
		set_time_limit(0); //脚本不超时
	}
	public function MakePropertyValue($name,$value,$osm){
		$oStruct = $osm->Bridge_GetStruct("com.sun.star.beans.PropertyValue");
		$oStruct->Name = $name;
		$oStruct->Value = $value;
		return $oStruct;
	}
	/**
	 * @Title: fileCreate
	 * @Description: todo(fileCreate)
	 * @param $outputfile 输出文件夹地址
	 * @param $soc_file 模板文件地址
	 * @param $type 输出类型 默认为 pdf/swf --输出两种格式文件
	 * @author 
	 * @date 2013-8-19 下午5:05:33
	 * @throws
	*/
	public function fileCreate($outputfile, $soc_file, $type = "pdf/swf"){
		$outputfile = str_replace("\\", "/", $outputfile);
		$soc_file = str_replace("\\", "/", $soc_file);
		$type = explode("/", strtolower($type));
		//$pdfsoc_file = "";
		$pdf = in_array('pdf', $type);
		$swf = in_array('swf', $type);
		if ($pdf === true && $swf === true) {
			//生成pdf文件
			$pdf_filename = $this->office2pdf($soc_file, $outputfile);
			//生成swf文件
			$this->pdf2swf($pdf_filename, $outputfile);
		}
		if ($pdf === false && $swf === true) {
			//生成swf文件
			$this->pdf2swf($soc_file, $outputfile);
		}
		return $pdf_filename;
	}
	
	public function office2pdf($soc_file, $output_file){
		$output_file .= '/pdf';
		createFolder($output_file);
		$pathInfo = pathinfo($soc_file);
		$output_file .= '/' . $pathInfo['filename'] . '.pdf';
		
// 		$filesArr = array('pdf','doc','docx','xls','xlsx','ppt','pptx','txt');//允许上传类型
// 		//限制上载类型
// 		if(!in_array($typesIf,$filesArr)){
// 			echo '<script type="text/javascript">alert("show file types in : pdf,doc,docx,xsl,xlsx,ppt,pptx,txt");location.href=location.href;</script>';
// 		}
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$OS_TYPE='WINDOWS';
		} else {
			$OS_TYPE='LINUX';
		}
		
		if($OS_TYPE=='WINDOWS'){
			$soc_file='"'.$soc_file.'"';
			$output_file='"'.$output_file.'"';
			$command = 'java -Doffice.home='."\"".C('OPENOFFICE_PATH_WINDOWS')."\"".' -jar '."\"".C('JODCONVERTER_PATH_WINDOWS')."\" ".$soc_file.' '.$output_file;
			$result = exec($command);
		}		
		if($OS_TYPE=='LINUX'){
			//旧的配制2.2车换java程序
			//$command = 'java -jar /usr/share/jodconverter/lib/jodconverter-cli-2.2.2.jar '.$soc_file.' '.$output_file;
			//3.0.jodconverter 
			//$command = 'java -Doffice.home=/opt/openoffice4/ -jar /usr/share/jodconverter3/lib/jodconverter-core-3.0-beta-4.jar '.$soc_file.' '.$output_file;
			$command = C('JODCONVERTER_PATH_LINUX').' '.$soc_file.' '.$output_file;
			$result = exec($command);
			$path1 = $docpath.$doc;
			$path2 = $pdfpath.$formatName;
		}
		$output_file = str_replace('"',"",$output_file);
		return $output_file;
	}
	
	public function pdf2swf($soc_file, $output_file,$type=0){
		if($type==0){
			$output_file .= '/swf';
		}
		createFolder($output_file);
		$pathInfo = pathinfo($soc_file);
		$output_file .= '/' . $pathInfo['filename'] . '.swf';
		if(C('OS_TYPE')=='WINDOWS'){
			$output_file='"'.$output_file.'"';
			//调用系统软件
			$command =  "\"".C('SWFTOOL_PATH_WINDOWS')."\" ".$soc_file."  -o ".$output_file." -f -T 9 -t -s storeallcharacters";
			exec($command);
		}
		if(C('OS_TYPE')=='LINUX'){
			$command = C('SWFTOOL_PATH_LINUX').' -o '.$output_file.' -T -z -t -f '.$soc_file.' -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -s flashversion=9';
			exec($command);
		}
	}
}
?>
