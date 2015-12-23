<?php
/**
 * 导入供应商信息的csv文件服务层
 * @author 王成
 */
class Csvservice extends Think{
	/**
	 * 读取文件名
	 * @access private
	 * @var string
	 */
	var $_filename;
	/**
	 * 打开文件的句柄
	 * @access private
	 * @var resource
	 */
	var $_fp = NULL;
	/**
	 * 从导入的文件中获取数据信息列表
	 * @return array  array(array('字段' => '值')...)
	 */
	function getDataList() {
		//if (!$this->_uploadCsv()) return array();
		if (!$this->_isCsv()) return array();
		$this->_open();
		$filesize = filesize($this->_filename);
		$_result = array();
		$_col = 0;
		while (($data = fgets($this->_fp, $filesize)) !== false) {
			if ($_col >= 100) break;
			$data = explode(',', $this->_filterInputData($data));
			if ($_col > 0 && $data) {
				if (!$data[0] && !$data[1] && !$data[2] && !$data[3]) continue;
				$_formator['sname'] = $this->_convert($data[0]);
				$_formator['name'] = $this->_convert($data[1]);
				$_formator['linkman'] = $this->_convert($data[2]);
				$_formator['mobile'] = $this->_convert($data[3]);
				$_formator['tel'] = $this->_convert($data[4]);
				$_formator['fax'] = $this->_convert($data[5]);
				$_formator['paymentid'] = $this->_convert($data[6]);
				$_formator['transportid'] = $this->_convert($data[7]);
				$_formator['isaddtax'] = $this->_convert($data[8]);
				$_formator['taxrate'] = $this->_convert($data[9]);
				$_formator['coaddr'] = $this->_convert($data[10]);
				$_result[] = $_formator;
			}
			$_col++;
		}
		$this->_close();
		$this->_del();
		return $_result;
	}
	
	function getAllDataList() {
		if (!$this->_isCsv()) return array();
		$this->_open();
		$filesize = filesize($this->_filename);
		$_result =$_result_title =array();
		$_col = 0;
		while (($data = fgets($this->_fp, $filesize)) !== false) {
			$data = explode(',', $this->_filterInputData($data));
			$s=count($data);
			if ($data) {
				for($iss=0;$iss<$s;$iss++){
					$_formator[$iss] = $this->_convert($data[$iss]);
				}
				$_result[] = $_formator;
			}
			$_col++;
		}
		$_resultarr=$_result;
		$this->_close();
		$this->_del();
		return $_resultarr;
	}
	
	function getCommonDataList() {
		if (!$this->_isCsv()) return array();
		$this->_open();
		$filesize = filesize($this->_filename);
		$_result =$_result_title =array();
		$_col = 0;
		while (($data = fgets($this->_fp, $filesize)) !== false) {
			if ($_col >= 500) break;
			$data = explode(',', $this->_filterInputData($data));
			$s=count($data);
			if ($data) {
				for($iss=0;$iss<$s;$iss++){
					$_formator[$iss] = $this->_convert($data[$iss]);
				}
				if($_col==0){
					$_result_title= $_formator;
				}else{
					$_result[] = $_formator;
				}
			}
			$_col++;
		}
		$_resultarr['title']=$_result_title;
		$_resultarr['data']=$_result;
		$this->_close();
		//$this->_del();
		return $_resultarr;
	}
	function _convert($string){
		return iconv('GBK','UTF-8',$string);
	}
	/**
	 * 过滤一行的数据
	 * 过滤空格，制表符\t，并进行字符转换
	 * @access private
	 * @param string $data 需要过滤的数据
	 * @return string 输出过滤后的数据
	 */
	function _filterInputData($data) {
		$data = str_replace(array("\t", ' '), '', $data);
		return $this->_escapeChar($data, 0, true);
	}
	/**
	 * 通用多类型转换
	 * @param $mixed
	 * @param $isint
	 * @param $istrim
	 * @return mixture
	 */
	function _escapeChar($mixed, $isint = false, $istrim = false) {
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = $this->_escapeChar($value, $isint, $istrim);
			}
		} elseif ($isint) {
			$mixed = (int) $mixed;
		} elseif (!is_numeric($mixed) && ($istrim ? $mixed = trim($mixed) : $mixed) && $mixed) {
			$mixed = $this->_escapeStr($mixed);
		}
		return $mixed;
	}
	/**
	 * 字符转换
	 * @param $string
	 * @return string
	 */
	function _escapeStr($string) {
		$string = (MAGIC_QUOTES_GPC && is_string($string))?   stripslashes($string)  :  $string;
		$string = trim($string);
		$string = str_replace(array("\0","%00","\r",'\0','%00','\r','%5C%22'), '', $string);
		$string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
		$string = str_replace(array("%3C",'<'), '&lt;', $string);
		$string = str_replace(array("%3E",'>'), '&gt;', $string);
		$string = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $string);
		return $string;
	}
	/**
	 * 判断导入的文件是否是CSV文件
	 * @access private
	 * @return boolean 判断的结果
	 */
	function _isCsv() {
		if (!$this->_filename || !is_file($this->_filename)) return false;
		$ext = strtolower(substr(strrchr($this->_filename, '.'), 1));
		if (!in_array($ext, array('csv'))) return false;
		return true;
	}
	/**
	 * 打开导入的文件
	 * @access private
	 * @param string $method 打开方式 默认为只读
	 * @return void
	 */
	function _open($method = 'r') {
		if (!is_resource($this->_fp)) {
			$this->_fp = fopen($this->_filename, $method);
		}
	}
	/**
	 * 关闭打开的文件
	 * @access private
	 * @return void
	 */
	function _close() {
		if (is_resource($this->_fp)) {
			fclose($this->_fp);
			$this->_fp = NULL;
		}
	}
	/**
	 * 删除文件
	 * @access private
	 * @return void
	 */
	function _del() {
		if ($this->_filename && is_file($this->_filename)) {
			unlink($this->_filename);
		}
	}
}
?>