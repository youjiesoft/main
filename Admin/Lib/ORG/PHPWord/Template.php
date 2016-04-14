<?php
/**
 * PHPWord
 *
 * Copyright (c) 2011 PHPWord
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 010 PHPWord
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    Beta 0.6.3, 08.07.2011
 */


/**
 * PHPWord_DocumentProperties
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 2009 - 2011 PHPWord (http://www.codeplex.com/PHPWord)
 */
class PHPWord_Template {

	/**
	 * ZipArchive
	 *
	 * @var ZipArchive
	 */
	private $_objZip;

	/**
	 * Temporary Filename
	 *
	 * @var string
	 */
	private $_tempFileName;

	/**
	 * Document XML
	 *
	 * @var string
	 */
	private $_documentXML;


	/**
	 * Create a new Template Object
	 *
	 * @param string $strFilename
	 */
	public function __construct($strFilename) {
		$path = dirname($strFilename);
		$this->_tempFileName = $path.DIRECTORY_SEPARATOR.time().'.docx';

		copy($strFilename, $this->_tempFileName); // Copy the source File to the temp File

		$this->_objZip = new ZipArchive();
		$this->_objZip->open($this->_tempFileName);
		$this->_documentXML = $this->_objZip->getFromName('word/document.xml');
	}

	public function setOneValue($search, $replace) {
		$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*'.$search.'(\s*|\n*|\r*)*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
		$this->_documentXML = preg_replace($search, $replace, $this->_documentXML);
	}

	/**
	 * Set a Template value
	 *
	 * @param mixed $search
	 * @param mixed $replace
	 */
	public function setValue($search, $replace) {
		$findArr = $this->find($search);
		if(count($findArr)==0){
			return;
		}
		$yuan_xml = $this->_documentXML;
		if(!is_array($replace["value"])){
			if(($replace["istestarea"]==1 && ! empty($replace["original"]) && mb_strpos($replace["original"],"&lt;")!==FALSE) || ($search=="newTab" && ! empty($replace["original"]))){
				$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				if($xml){
					$xmlTable = $xml->getElementsByTagName('p');
					foreach($xmlTable as $table) {
						$text = $table->textContent;
						if(mb_strpos($text, '${'.$search."}") !== false){
							$element = $xml->createElement('replace', '###'.$search.'###');
							$table->parentNode->insertBefore($element, $table);
							$table->parentNode->removeChild($table);
						}
					}

					$this->_documentXML = $xml->saveXML();

					if(mb_strpos($this->_documentXML, '<replace>###'.$search.'###</replace>') !== false){
						if(mb_strpos($replace["original"],"&lt;")!==FALSE){
							$replace["value"] = htmlToWordXml(html_entity_decode($replace["original"]));
						}
						$this->_documentXML = str_replace('<replace>###'.$search.'###</replace>', $replace["value"], $this->_documentXML);
					}
					if( ! DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING)){ // 转富文本格式有错时
						$this->_documentXML = $yuan_xml;
					}
				}
			}else{
				if($replace["ischecked"]==1){
					$checkArr = explode(",",$replace["original"]);
					$checkYuanArr = explode(",",$replace["checkList"]);
					foreach($checkYuanArr as $k => $v){
						$checksearch = $replace["name"].$v;
						if(in_array($v,$checkArr)){
							$checkReplaceValue = '<w:sym w:font="Wingdings 2" w:char="F052" />';
						}else{
							$checkReplaceValue = '<w:sym w:font="Wingdings 2" w:char="F0A3" />';
						}
						$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
						if($xml){
							$xmlTable = $xml->getElementsByTagName('t');
							foreach($xmlTable as $table) {
								$text = $table->textContent;
								if(mb_strpos($text, '${'.$checksearch."}") !== false){
									$element = $xml->createElement('replace', '###'.$checksearch.'###');
									$table->parentNode->appendChild($element);
									$table->parentNode->removeChild($table);
								}
							}
							$this->_documentXML = $xml->saveXML();
								
							if(mb_strpos($this->_documentXML, '<replace>###'.$checksearch.'###</replace>') !== false){
								$this->_documentXML = str_replace('<replace>###'.$checksearch.'###</replace>', $checkReplaceValue, $this->_documentXML);
							}
							if( ! DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING)){ // 转富文本格式有错时
								$this->_documentXML = $yuan_xml;
							}
						}
					}
				}
				//         		$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*'.$search.'(\s*|\n*|\r*)*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
				$search = '${'.$search."}";
				$this->_documentXML = str_replace($search, $replace["value"], $this->_documentXML);
			}
		}else{
			if(!isset($replace["colORrow"])){//整个表格输出
				$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				if($xml){
					$xmlTable = $xml->getElementsByTagName('p');
					foreach($xmlTable as $table) {
						$text = $table->textContent;
						if(mb_strpos($text, '${'.$search."}") !== false){
							$element = $xml->createElement('replace', '###'.$search.'###');
							$table->parentNode->insertBefore($element, $table);
						}
					}
					$this->_documentXML = $xml->saveXML();
				}
				//     			$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*'.$search.'(\s*|\n*|\r*)*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
				if(!$replace["showtitle"]){
					$replace["showname"] = "";
				}
				$is_empty = true;
				foreach($replace["value"] as $k => $v){
					if(!empty($v["value"])){
						$is_empty = false;
						break;
					}
				}
				if( ! $is_empty){
					$textStyle = array(
						"name"=>$replace["ziti"]?$replace["ziti"]:"仿宋_GB2312",
						"size"=>$replace["zihao"]?$replace["zihao"]:9,
						"spacing"=>$replace["hangjianju"]?$replace["hangjianju"]:240,
					);
					$tableXmlStr = $this->getPHPWordTableXmlStr($replace["showname"],$replace["titleArr"],$replace["value"],NULL,$replace["showtype"],$textStyle,$replace["fieldwidth"]);
				}else{
					$tableXmlStr = "";
				}
				$this->_documentXML = str_replace('<replace>###'.$search.'###</replace>', $tableXmlStr, $this->_documentXML);
			}else{
				$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				if($xml){
					$xmlTable = $xml->getElementsByTagName('tbl');
					//横向填充
					if(!empty($replace["showtype"])) {
						foreach ($xmlTable as $table) {
							$rows = $table->getElementsByTagName('tr');
							foreach ($rows as $k => $row) {
								$text = $row->textContent;
								if(mb_strpos($text, '${'.$search."}") !== false){
									$cols = $row->getElementsByTagName('tc');
									$isFind = false;
									$index = 0; //value编号
									foreach($cols as $kk => $col){
										$text = $col->textContent;
										if(mb_strpos($text, '${'.$search."}") !== false){
											$isFind = true;
										}
										if(isset($replace["value"][$index]) && $isFind){
											$p = $col->getElementsByTagName('t');
											if($p->item(0)->nodeName=="w:t"){
												$p->item(0)->nodeValue = $replace["value"][$index];
											}else{
												$element_r = $xml->createElement("w:r");
												$element_rPr = $xml->createElement("w:rPr");
												$element_rFonts = $xml->createElement("w:rFonts");
												$element_rFonts->setAttribute("w:ascii","仿宋_GB2312");
												$element_rFonts->setAttribute("w:eastAsia","仿宋_GB2312");
												$element_rFonts->setAttribute("w:hAnsi","仿宋_GB2312");
												$element_rFonts->setAttribute("w:cs","仿宋_GB2312");
												$element_rPr->appendChild($element_rFonts);
												$element_t = $xml->createElement("w:t");
												$element_sz = $xml->createElement("w:sz");
												$element_sz->setAttribute("w:val","24");
												$element_t->nodeValue = $replace["value"][$index];
												$element_r->appendChild($element_rPr);
												$element_rPr->appendChild($element_sz);
												$element_r->appendChild($element_t);
												$col->getElementsByTagName('p')->item(0)->appendChild($element_r);
											}
											$index++;
										}
									}
								}
							}
						}
					}else{ //竖向填充
						foreach ($xmlTable as $table) {
							$rows = $table->getElementsByTagName('tr');
							$isFind = false;
							$td_index = -1; //行填充td编号
							$index = 0; //value编号
							foreach ($rows as $k => $row) {
								$text = $row->textContent;
								if(mb_strpos($text, '${'.$search."}") !== false || $td_index!=-1){ //填充td编号不为空时即已经找到标签并准备向下填充
									$cols = $row->getElementsByTagName('tc');
									foreach($cols as $kk => $col){
										$text = $col->textContent;
										if(mb_strpos($text, '${'.$search."}") !== false && $td_index==-1){
											$isFind = true;
											$td_index = $kk;
										}elseif($td_index!=-1){
											$isFind = true;
										}
										$p = $col->getElementsByTagName('t');
										if(isset($replace["value"][$index]) && $isFind && $td_index == $kk){
											if($p->item(0)->nodeName=="w:t"){
												$p->item(0)->nodeValue = $replace["value"][$index];
											}else{
												$element_r = $xml->createElement("w:r");
												$element_rPr = $xml->createElement("w:rPr");
												$element_rFonts = $xml->createElement("w:rFonts");
												$element_rFonts->setAttribute("w:ascii","仿宋_GB2312");
												$element_rFonts->setAttribute("w:eastAsia","仿宋_GB2312");
												$element_rFonts->setAttribute("w:hAnsi","仿宋_GB2312");
												$element_rFonts->setAttribute("w:cs","仿宋_GB2312");
												$element_rPr->appendChild($element_rFonts);
												$element_t = $xml->createElement("w:t");
												$element_sz = $xml->createElement("w:sz");
												$element_sz->setAttribute("w:val","24");
												$element_t->nodeValue = $replace["value"][$index];
												$element_r->appendChild($element_rPr);
												$element_rPr->appendChild($element_sz);
												$element_r->appendChild($element_t);
												$col->getElementsByTagName('p')->item(0)->appendChild($element_r);
											}
											$index++;
										}
									}
								}
							}
						}
					}
					$res_string = $xml->saveXML();
					$this->_documentXML = $res_string;
				}
			}
		}
	}

	/**
	 * @Title: getWordPHPTableXmlStr
	 * @Description: todo(用phpword对象生成word表格xml字符串)
	 * @param $title         表格表名
	 * @param $titleArr      表头
	 * @param $titleGroupArr 分组表头  例：array(array("colspan"=>2, "title"=>"第一大组"),array("colspan"=>3, "title"=>"第二大组"))
	 * @param $data          表数据
	 * @author 王昭侠
	 * @date 2015-01-30 上午90:30:00
	 * @throws
	 */
	public function getPHPWordTableXmlStr($title,$titleArr,$data=NULL,$titleGroupArr=NULL,$showtype=0,$textStyle=array(),$widthArray=array()){
		$th_count = count($titleArr);
		 
		$obPHPWord = new PHPWord();
		$section = $obPHPWord->createSection();
		$styleTable = array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80, 'align'=>'center');
		$obPHPWord->addTableStyle('myOwnTableStyle', $styleTable);
		$table = $section->addTable('myOwnTableStyle');
		$cellStyle = array('borderSize'=>6, 'name'=>$textStyle["name"],'size'=>$textStyle["size"],'spacing'=>$textStyle["spacing"], 'borderColor'=>'000000', 'cellMargin'=>80);
		$td_width = 2000;
		$newStats = array();
		foreach($data as $k => $v){
			if($v["is_stats"]==="1"){
				$sum = 0;
				if( ! empty($v["original"][0]) && is_numeric($v["original"][0])){
					foreach($v["original"] as $kk => $vv){
						$sum+=floatval($vv);
					}
					$sum = unitExchange($sum,$v["funcdata"][0][0][1],$v["funcdata"][0][0][2],3);
					$newStats[] = "小计:".$sum;
				}else{
					$newStats[] = "";
				}
			}else{
				$newStats[] = "";
			}
		}
		$stats = false;
		foreach($newStats as $k => $v){
			if(!empty($v)){
				$stats = true;
				break;
			}
		}
		if(!$stats){
			$newStats = array();
		}
		 
		if(!empty($title))
		{
			if($showtype!=0){
				$th_count = count($data[0]["value"])+1;
			}
			$table->addRow();
			$table->addCell($td_width*$th_count,array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80, 'align'=>'center','gridSpan' => $th_count))->addText($title,array('name'=>$textStyle["name"],'size'=>$textStyle["size"],'spacing'=>$textStyle["spacing"],'align'=>'center'));
		}
		if(!empty($titleGroupArr))
		{
			$table->addRow();
			foreach($titleGroupArr as $k => $v){
				$table->addCell($td_width*$v["colspan"],array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80,'gridSpan' => $v["colspan"]))->addText($v["title"],array('name'=>$textStyle["name"],'size'=>$textStyle["size"],'spacing'=>$textStyle["spacing"],'align'=>'center'));
			}
		}
		if(empty($showtype)){ //默认为横向输出
			if(!empty($titleArr))
			{
				$table->addRow();
				foreach($titleArr as $k => $v){
					$table->addCell($widthArray[$k]?$widthArray[$k]:$td_width,array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80, 'valign'=>'center', 'align'=>'center'))->addText($v,$textStyle);
				}
			}
			foreach($data[0]["value"] as $k => $v){
				$table->addRow();
				foreach($data as $kk => $vv){
					$table->addCell($widthArray[$kk]?$widthArray[$kk]:$td_width,$cellStyle)->addText($vv["value"][$k],$textStyle);
				}
			}
			if(count($newStats)>0){
				$table->addRow();
				foreach($newStats as $k => $v){
					$table->addCell($widthArray[$k]?$widthArray[$k]:$td_width,$cellStyle)->addText($v,$textStyle);
				}
			}
		}else{
			if(!empty($titleArr))
			{
				foreach($titleArr as $k => $v){
					$table->addRow();
					$table->addCell($widthArray[$k]?$widthArray[$k]:$td_width,array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80, 'align'=>'center'))->addText($v,$textStyle);
					if(!empty($data)){
						foreach($data[$k]["value"] as $kk => $vv){
							$table->addCell($widthArray[$kk]?$widthArray[$kk]:$td_width,$cellStyle)->addText($vv,$textStyle);
						}
					}
				}
			}else{
				foreach($titleArr as $k => $v){
					$table->addRow();
					if(!empty($data)){
						foreach($data[$k]["value"] as $kk => $vv){
							$table->addCell($widthArray[$kk]?$widthArray[$kk]:$td_width,$cellStyle)->addText($vv,$textStyle);
						}
						if(count($newStats)>0){
							$table->addCell($widthArray[$kk]?$widthArray[$kk]:$td_width,$cellStyle)->addText($newStats[$k],$textStyle);
						}
					}
				}
			}
		}
		$objWriter = PHPWord_IOFactory::createWriter($obPHPWord, 'Word2007');
		$tableXml = $objWriter->getWriterPart('document')->getObjectAsText($table);
		return $tableXml;
	}

	/**
	 * @Title: getWordTableXmlStr
	 * @Description: todo(生成word表格xml字符串)
	 * @param $title         表格表名
	 * @param $titleArr      表头
	 * @param $titleGroupArr 分组表头  例：array(array("colspan"=>2, "title"=>"第一大组"),array("colspan"=>3, "title"=>"第二大组"))
	 * @param $data          表数据
	 * @author 王昭侠
	 * @date 2014-11-12 上午10:30:00
	 * @throws
	 */
	public function getWordTableXmlStr($title,$titleArr,$data=NULL,$titleGroupArr=NULL){
		$th_count = count($titleArr);
		$dataRows = count($data);
		$str = "<w:tbl>";
		$str .= <<<EOT
		  <w:tblPr>
		    <w:tblStyle w:val="a6"/>
		    <w:tblW w:w="0" w:type="auto"/>
		    <w:tblLook w:val="04A0"/>
		  </w:tblPr>
		  <w:tblGrid>
		    <w:gridCol w:w="4644"/>
		    <w:gridCol w:w="4644"/>
		  </w:tblGrid>
EOT;
		if(!empty($title))
		{
			$str .=<<<EOT
			<w:tr w:rsidR="0061466D" w:rsidTr="00E275B3">
		        <w:tc>
		          <w:tcPr>
		            <w:tcW w:w="9288" w:type="dxa"/>
		            <w:gridSpan w:val="{$th_count}"/>
		          </w:tcPr>
		          <w:p w:rsidR="0061466D" w:rsidRDefault="0061466D" w:rsidP="0061466D">
		            <w:pPr>
		              <w:jc w:val="center"/>
		              <w:rPr>
		                <w:rFonts w:hint="eastAsia"/>
		              </w:rPr>
		            </w:pPr>
		            <w:r>
		              <w:rPr>
		                <w:rFonts w:hint="eastAsia"/>
		              </w:rPr>
		              <w:t>$title</w:t>
		            </w:r>
		          </w:p>
		        </w:tc>
		      </w:tr>
EOT;
		}
		if(!empty($titleGroupArr))
		{

			$str .='<w:tr w:rsidR="0061466D" w:rsidTr="00E275B3">';
			foreach($titleGroupArr as $k => $v)
			{
				$str .= <<<EOT
		        <w:tc>
		          <w:tcPr>
		            <w:tcW w:w="9288" w:type="dxa"/>
		            <w:gridSpan w:val="{$v['colspan']}"/>
		          </w:tcPr>
		          <w:p w:rsidR="0061466D" w:rsidRDefault="0061466D" w:rsidP="0061466D">
		            <w:pPr>
		              <w:jc w:val="center"/>
		              <w:rPr>
		                <w:rFonts w:hint="eastAsia"/>
		              </w:rPr>
		            </w:pPr>
		            <w:r>
		              <w:rPr>
		                <w:rFonts w:hint="eastAsia"/>
		              </w:rPr>
		              <w:t>{$v['title']}</w:t>
		            </w:r>
		          </w:p>
		        </w:tc>
EOT;
			}
			$str .='</w:tr>';
		}
		if(!empty($titleArr))
		{
			$str .= '<w:tr w:rsidR="00856BC2" w:rsidTr="00856BC2">';
			foreach($titleArr as $k => $v)
			{
				$str .= <<<EOT
				<w:tc>
			      <w:tcPr>
			        <w:tcW w:w="4644" w:type="dxa"/>
			      </w:tcPr>
			      <w:p w:rsidR="00856BC2" w:rsidRDefault="00856BC2" w:rsidP="00856BC2">
			        <w:pPr>
			          <w:jc w:val="left"/>
			        </w:pPr>
			        <w:r>
			          <w:rPr>
			            <w:rFonts w:hint="eastAsia"/>
			          </w:rPr>
			          <w:t>{$v}</w:t>
			        </w:r>
			      </w:p>
			    </w:tc>
EOT;
			}
			$str .= '</w:tr>';
		}
		if(!empty($data))
		{
			foreach($data[0]["value"] as $k => $v)
			{
				$str .= '<w:tr w:rsidR="00856BC2" w:rsidTr="00856BC2">';
				foreach($data as $kk => $vv)
				{
					$str .= <<<EOT
					<w:tc>
				      <w:tcPr>
				        <w:tcW w:w="4644" w:type="dxa"/>
				      </w:tcPr>
				      <w:p w:rsidR="00856BC2" w:rsidRDefault="00856BC2" w:rsidP="00856BC2">
				        <w:pPr>
				          <w:jc w:val="left"/>
				        </w:pPr>
				        <w:r>
				          <w:rPr>
				            <w:rFonts w:hint="eastAsia"/>
				          </w:rPr>
				          <w:t>{$vv["value"][$k]}</w:t>
				        </w:r>
				      </w:p>
				    </w:tc>
EOT;
				}
				$str .= '</w:tr>';
			}
		}
		$str .= "";
		$str .= "</w:tbl>";
		return $str;
	}

	public function getStr() {
		return $this->_documentXML;
	}

	public function setStr($str) {
		$this->_documentXML = $str;
	}

	public function find($search) {
		$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*'.$search.'(\s*|\n*|\r*)*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
		preg_match($search,$this->_documentXML,$arr);
		return $arr;
	}

	//清除模板标签的多余代码
	public function clearBiaoji($search) {
		$replace = '${'.$search.'}';
		$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*'.$search.'(\s*|\n*|\r*)*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
		$this->_documentXML = preg_replace($search, $replace, $this->_documentXML);
	}

	//清除模板标签的多余代码
	public function clearAllBiaoji() {
		preg_match_all('/\$(\<.[^<>]*\>|\s*|\n*|\r*)*\{[^}]+\}/', $this->_documentXML, $matches);
		foreach ($matches[0] as $k => $match) {
			$no_tag = strip_tags($match);
			$this->_documentXML = str_replace($match, $no_tag, $this->_documentXML);
		}
	}

	//清除未替换的模板标签
	public function clearTemplateTag() {
		$replace = '';
		$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*(\s*|\n*|\r*)*\w*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
		$this->_documentXML = preg_replace($search, $replace, $this->_documentXML);
	}

	/**
	 * Save Template
	 *
	 * @param string $strFilename
	 */
	public function save($strFilename) {
		$this->delEmptyRow();
		$this->_documentXML = trim(preg_replace("/>\s*</", "><", $this->_documentXML));
		$this->_objZip->addFromString('word/document.xml', $this->_documentXML);

		// Close zip file
		if($this->_objZip->close() === false) {
			throw new Exception('Could not close zip file.');
		}

		rename($this->_tempFileName, $strFilename);
	}

	/**
	 * Clone a table row
	 *
	 * @param mixed $search
	 * @param mixed $numberOfClones
	 */
	public function cloneRow($search, $numberOfClones) {
		if(substr($search, 0, 2) !== '${' && substr($search, -1) !== '}') {
			$search = '${'.$search.'}';
		}

		$tagPos      = strpos($this->_documentXML, $search);
		$rowStartPos = strrpos($this->_documentXML, "<w:tr ", ((strlen($this->_documentXML) - $tagPos) * -1));
		$rowEndPos   = strpos($this->_documentXML, "</w:tr>", $tagPos) + 7;

		$result = substr($this->_documentXML, 0, $rowStartPos);
		$resultEnd = substr($this->_documentXML, $rowEndPos);
		$xmlRow = substr($this->_documentXML, $rowStartPos, ($rowEndPos - $rowStartPos));

		$delIndex = strrpos($result,"</w:tr>")+7; //获取
		$del = strlen($result)-$delIndex;
		if($del>0)
		{
			$result = substr($result,0,-$del); // 删除尾部多余的tr
		}

		if(substr($xmlRow,0,6)!="<w:tr " && substr($xmlRow,0,6)!="<w:tr>"){
			$xmlRow='<w:tr>'.$xmlRow;
		}
		if(substr($xmlRow,-7)!="</w:tr>"){
			$xmlRow.='</w:tr>';
		}
		for ($i = 1; $i <= $numberOfClones; $i++) {
			 
			$result .= preg_replace('/\$\{(.*?)\}/','\${\\1#'.$i.'}', $xmlRow);

		}
		$result .= $resultEnd;

		$this->_documentXML = $result;
	}

	/**
	 * Clone a table row 最原始的克隆方法
	 *
	 * @param mixed $search
	 * @param mixed $numberOfClones
	 */
	public function cloneRowOld($search, $numberOfClones) {
		if(substr($search, 0, 2) !== '${' && substr($search, -1) !== '}') {
			$search = '${'.$search.'}';
		}

		$tagPos = strpos($this->_documentXML, $search);
		$rowStartPos = strrpos($this->_documentXML, "<w:tr", ((strlen($this-="">_documentXML) - $tagPos) * -1));
		$rowEndPos   = strpos($this->_documentXML, "", $tagPos) + 7;

		$result = substr($this->_documentXML, 0, $rowStartPos);
		$xmlRow = substr($this->_documentXML, $rowStartPos, ($rowEndPos - $rowStartPos));
		for ($i = 1; $i <= $numberOfClones; $i++) {
			$result .= preg_replace('/${(.*?)}/','${\1#'.$i.'}', $xmlRow);
		}
		$result .= substr($this->_documentXML, $rowEndPos);

		$this->_documentXML = $result;
	}

	/**
	 * Set a Template value 替换新方法
	 *
	 * @param mixed $search
	 * @param mixed $replace
	 */
	public function setValueNew($search, $replace, $limit=-1) {
		$search = '{'.$search.'}';
		preg_match_all('/\{[^}]+\}/', $this->_documentXML, $matches);
		foreach ($matches[0] as $k => $match) {
			$no_tag = strip_tags($match);
			if ($no_tag == $search) {
				$match = '{'.$match.'}';
				$this->_documentXML = preg_replace($match, $replace, $this->_documentXML, $limit);
				if ($limit == 1) {
					break;
				}
			}
		}
	}

	/**
	 * Clone Rows in tables 新的克隆行方法
	 *
	 * @param string $search
	 * @param array $data
	 */
	public function cloneRowNew($search, $data=array()) {
		// remove ooxml-tags inside pattern
		foreach ($data as $nn => $fieldset) {
			foreach ($fieldset as $field => $val) {
				$key = '{'.$search.'.'.$field.'}';
				$this->setValueNew($key, $key, 1);
			}
		}
		// how many clons we need
		$numberOfClones = 0;
		if (is_array($data)) {
			foreach ($data as $colName => $dataArr) {
				if (is_array($dataArr)) {
					$c = count($dataArr);
					if ($c > $numberOfClones)
						$numberOfClones = $c;
				}
			}
		}
		 
		if ($numberOfClones > 0) {
			// read document as XML
			$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);

			// search for tables
			$tables = $xml->getElementsByTagName('tbl');
			foreach ($tables as $table) {
				$text = $table->textContent;
				// search for pattern. Like {TBL1.
				if (mb_strpos($text, '{'.$search.'.') !== false) {
					// search row for clone
					$patterns = array();
					$rows = $table->getElementsByTagName('tr');
					$isUpdate = false;
					$isFind = false;
					foreach ($rows as $row) {
						$text = $row->textContent;
						$TextWithTags = $xml->saveXML($row);
						if (
								mb_strpos($text, '{'.$search.'.') !== false // Pattern found in this row
								OR
								(mb_strpos($TextWithTags, '<w:vMerge/>') !== false AND $isFind) // This row is merged with upper row (Upper row have pattern)
						)
						{
							// This row need to clone
							$patterns[] = $row->cloneNode(true);
							$isFind = true;
						} else {
							// This row don't have any patterns. It's table header or footer
							if (!$isUpdate and $isFind) {
								// This is table footer
								// Insert new rows before footer
								$this->InsertNewRows($table, $patterns, $row, $numberOfClones);
								$isUpdate = true;
							}
						}
					}
					// if table without footer
					if (!$isUpdate and $isFind) {
						$this->InsertNewRows($table, $patterns, $row, $numberOfClones);
					}
				}
			}
			// save document
			$res_string = $xml->saveXML();
			$this->_documentXML = $res_string;

			// parsing data
			foreach ($data as $colName => $dataArr) {
				$pattern = '{' . $search . '.' . $colName . '}';
				foreach ($dataArr as $value) {
					$this->setValue($pattern, $value, 1);
				}
			}
		}
	}

	//克隆列
	public function cloneColumn($search, $numberOfClones) {
		if ($numberOfClones > 0) {
			// read document as XML
			$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);

			// search for tables
			$tables = $xml->getElementsByTagName('tbl');
			foreach ($tables as $k=> $table) {
				$text = $table->textContent;
				if (mb_strpos($text, '${'.$search.'}') !== false) {
					$patterns = "";
					$column = $table->getElementsByTagName('tc');
					$isUpdate = false;
					$isFind = false;
					foreach ($column as $col) {
						$text = $col->textContent;
						$TextWithTags = $xml->saveXML($col);
						if (mb_strpos($text, '${'.$search.'}') !== false)
						{
							$cloneCol = $col->cloneNode(true);
							for($i=0;$i<$numberOfClones;$i++){
								$table->insertBefore($cloneCol);
							}
						}
					}
				}
			}
			// save document
			$res_string = $xml->saveXML();
			$this->_documentXML = $res_string;
		}
	}

	/**
	 * Insert new rows in table
	 *
	 * @param object &$table
	 * @param object $patterns
	 * @param object $row
	 * @param int $numberOfClones
	 */
	protected function InsertNewRows(&$table, $patterns, $row, $numberOfClones)	{
		for ($i = 1; $i < $numberOfClones; $i++) {
			foreach ($patterns as $pattern) {
				$new_row = $pattern->cloneNode(true);
				$table->insertBefore($new_row, $row);
			}
		}
	}

	public function unlinkFile(){
		$this->_objZip->addFromString('word/document.xml', $this->_documentXML);
		 
		// Close zip file
		if($this->_objZip->close() === false) {
			throw new Exception('Could not close zip file.');
		}
		 
		unlink($this->_tempFileName);
	}

	public function delEmptyRow(){
		$a = $this->_documentXML;
		$xml = DOMDocument::loadXML($this->_documentXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
		try{
			if($xml){
				$element = $xml->documentElement;
				$xmlTable = $xml->getElementsByTagName('tbl');
				foreach ($xmlTable as $table) {
					$rows = $table->getElementsByTagName('tr');
					for($k=(int)($rows->length)-1;$k>=0;$k--) {
						if(empty($rows->item($k)->nodeValue)){
							$table->removeChild($rows->item($k));
						}
					}
				}
				$res_string = $xml->saveXML();
				$this->_documentXML = $res_string;
			}
		}catch(Exception $e){
			$this->_documentXML = $a;
		}
	}
}
?>