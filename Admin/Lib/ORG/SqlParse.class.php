<?php
/**
 *  sql语句解析类
 *
 *
 * @example
 *  require 'SqlParse.class.php';
 *  $obj = new SqlParse();
 *  $data = $obj->checkSQLFields($sql);
 *  var_dump($data);
 *
 */	
class SqlParse {

    /**
     * sql源数据格式化，去除多余空格，回车。
     */
    function fmt($str) {
    	
    	$str = preg_replace('/\/\*(.*?)\*\//','',$str);
    	//清除注释
        $str = str_replace("\r\n", ' ', $str);
        //清除换行符
        $str = str_replace("\n", ' ', $str);
        //清除换行符
        $str = str_replace("\t", ' ', $str);
        //清除制表符
        
        //去掉跟随别的挤在一块的空白
        $str = preg_replace('/\s(?=\s)/', '', $str);
        //$str = strtolower($str);
        $pattern = array("/> *([^ ]*) *</", //去掉注释标记
        "/[\s]+/", "/<!--[^!]*-->/", "/\" /", "/ \"/", "'/\*[^*]*\*/'");
        $replace = array(">\\1<", " ", "", "\"", "\"", "");
        return preg_replace($pattern, $replace, $str);
    }

    /**
     *
     * checkSQLCondition
     * @Title: checkSQLCondition
     * @desc: todo(获取sql的条件列表)
     * @param unknown_type $str
     * @return return_type
     * @throws
     */
    function checkSQLCondition($str) {
        $t = preg_split('/(\bwhere\b|\bgroup\b|\border\b|\blimit\b)/i', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        
        if('where' == $t[1]){
	        // 取得where 条件
	        $whereParam = preg_split('/(\band\b|\bor\b)/i', $t[2], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	        unset($i);
	        unset($r);
	        unset($temp);
	        for ($i = 0; $i < count($whereParam); $i++) {
	            $v = trim($whereParam[$i]);
	            $temp .= $v.' ';
	            if (in_array($v, array('and', 'or'))) {
	                $temp .= trim($whereParam[++$i]).' ';
	                if (strstr($v, '(')){
	                    while (!strstr($v, ')')){
	                        $temp .= trim($whereParam[++$i]).' ';
	                    }
	                }
	            }
	        }
	        $r['condition'] = $temp . ' ';
        }
        
        // 取得order 条件
        $orderParam = preg_split('/(\bby\b|\s)/i', $t[4], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        unset($temp);
        for ($i = 0; $i < count($orderParam); $i++) {
        	$v = trim($orderParam[$i]);
        	if (in_array($v, array('desc', 'asc'))) {
        		$temp []= trim($orderParam[$i-2]);
        		$temp []= trim($v);
        	}
        }
        $r['order'] = $temp;
        // 取得group 条件
        $groupIndex=6;
        if(strtolower($t[3])=='group'){
        	$groupIndex = 4;
        }
        // 去除当前字段段中 by 之后的所有空格
        $t[$groupIndex] = preg_replace('/(?<!by)\s/', '', $t[$groupIndex]);
        // 按 by having 及空格分隔字符
        $groupParam = preg_split('/(\bby\b|\bhaving\b|\s)/i' , $t[$groupIndex] , -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        unset($temp);
        unset($v);
        for ($i = 0; $i < count($groupParam); $i++) {
        	$v = trim($groupParam[$i]);
        	if (in_array($v, array('by'))) {
        		$temp []= trim($groupParam[$i+2]);
        	}
        }
        $r['group'] = $temp;
        return $r;
    }

    /**
     *
     * checkSQLFields
     * @Title: checkSQLFields
     * @desc: todo(获取查询字段列表及表信息)
     * @param unknown_type $str
     * @return return_type
     * @throws
     */
    function checkSQLFields($str) {
        $str = self::fmt($str);
        $reg = '/(\bselect\b|\bfrom\b|\bwhere\b)/i';
        $data = preg_split($reg, $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        // 查询表  3
        // 带回字段 1
        // 1 分解出所有字段
        $filedSouce = $data[1];
        // 全查询时字段需要从DB中获取或_fields中去找。
        if ($filedSouce == '*') {

        } else {
            // 先去除mysql特殊的字段写法 ``
            $filedSouce = str_replace('`', '', $filedSouce);
            $fieldReg = '/,/i';
            $fieldData = preg_split($fieldReg, $filedSouce, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            // 按表分离出字段
            $filedListFormTable = '';
            foreach ($fieldData as $k => $v) {
                // 检查是否设置了字段别名 , 有别名字段列表中，key为真实字段名，value为字段别名
                $fieldAsReg = '/\bas\b/i';
                $fieldAsTemp = preg_split($fieldAsReg, $v, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                if (stripos($fieldAsTemp[0], '.')) {
                	// 粗略地检查字段中是否使用了函数。
                	$funcReg = '/\(.*?\)?/';
                	$ret = preg_match($funcReg, $fieldAsTemp[0]);
                	if(!$ret){
	                    $filedTemp = explode('.', $fieldAsTemp[0]);
	                    $filedListFormTable[trim($filedTemp[0])][trim($filedTemp[1])] = $fieldAsTemp[1] ? trim($fieldAsTemp[1]) : trim($filedTemp[1]);
                	}else{
                		// 字段列表中使用函数
                		$filedListFormTable['func'][$fieldAsTemp[0]] =  $fieldAsTemp[1] ? trim($fieldAsTemp[1]) : trim($filedTemp[1]);
                	}
                } else {
                    // 单表查询时或没有指定表别名
                    $filedListFormTable['ontable'][trim($fieldAsTemp[0])] = trim($fieldAsTemp[0]);
                }
            }
            // 处理后的字段，按表分组
            // print_r($filedListFormTable);
        }
        // 2 分解出表名
        // 真实表名 加 on属性
        $tableTrueArr = '';
        // 以别名为key的真实表名数组
        $tableAsArr = '';
        // 高级查询类型
        $tableDescTypeArr = '';

        $tableSouce = $data[3];
        // 先去除mysql特殊的字段写法 ``
        $tableSouce = str_replace('`', '', $tableSouce);
        // 取出高级查询
        // 按高级查询标识分级出每个表的大概信息
        $tableReg = '/\b\sleft\sjoin\s\b|\b\sright\sjoin\s\b|\b\sinner\sjoin\s\b/i';
        //  $tableReg = '/(\b\sleft\s\b|\b\sright\s\b|\b\sinner\s\b)/i';
        $tableDescData = preg_split($tableReg, $tableSouce, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // 取高级查询标识
        $tableTypeReg = '/\b\s(left)\s\b|\b\s(right)\s\b|\b\s(inner\b\s)/i';
        $tableDescTypeData = preg_split($tableTypeReg, $tableSouce, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $tableDescTypeArr = '';
        for ($i = 0; $i < count($tableDescTypeData); $i++) {
            $value = trim($tableDescTypeData[$i]);

            if ($i > 0) {
                if (stripos($value, 'join') !== false) {
                    $value = $value;
                    $value = str_replace('/join/i', '', $value);
                    //$tableAsReg = '/\b\sas\s\b/i';
                    $tableAsReg = '/\b\sas\s\b|\b\son\s\b/i';
                    $tableTemp = preg_split($tableAsReg, $value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    $tableDescTypeArr[trim($tableTemp[0])] = trim($tableDescTypeData[$i - 1]);
                }
            } else {
                $tableAsReg = '/\b\sas\s\b/i';
                $tableTemp = preg_split($tableAsReg, $value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $tableDescTypeArr[trim($tableTemp[0])] = trim($tableDescTypeData[$i + 1]);
            }
        }
        // 取出表 as 与 on 属性
        /**
         [0] =>  t_userinfo AS u
         [1] =>  t_add AS a ON u.id = a.id
         [2] =>  t_add_info AS i ON a.id = i.aid
         */
        foreach ($tableDescData as $key => $value) {
            // 先分离出 on
            $tableOnReg = '/\bon\b/i';
            $tableOnTemp = preg_split($tableOnReg, $value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            // 取出真实表名及表别名
            $tableAsReg = '/\bas\b/i';
            $tableTemp = preg_split($tableAsReg, $tableOnTemp[0], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $tableTrueArr[trim($tableTemp[0])]['tablename'] = trim($tableTemp[0]);
            $tableAsArr[trim($tableTemp[1])] = trim($tableTemp[0]);
            // 将on发属性添加到具体的真实表名数组中
            if (is_array($tableOnTemp) && $tableOnTemp[1]) {
                // 将表别名替换为真实表名
                foreach ($tableAsArr as $k => $v) {
                    $tableOnTemp[1] = str_replace($k . '.', $v . '.', $tableOnTemp[1]);
                }
                $tableTrueArr[trim($tableTemp[0])]['_on'] = $tableOnTemp[1];
            }
            // 追加高级查询类型
            $tableTrueArr[trim($tableTemp[0])]['_type'] = $tableDescTypeArr[trim($tableTemp[0])];
        }

        if (is_array($filedListFormTable) && $filedListFormTable['ontable']) {
            $tableTrueArr[key($tableTrueArr)]['fields'] = $filedListFormTable['ontable'];
        } else {
            // 将字段信息整合到表中
            foreach ($filedListFormTable as $key => $value) {
                $tableTrueArr[$key]['fields'] = $value;
            }
        }
        $conditions = self::checkSQLCondition($str);
        if (is_array($conditions)) {
            $search = array();
            $replace = array();
            // 将表别名替换为真实表名
            foreach ($tableAsArr as $k => $v) {
                $search[] = $k . '.';
                $replace[] = $v . '.';
            }
            $conditions = str_replace($search, $replace, $conditions);
            $tableTrueArr['condition'] = $conditions;
        }
        //print_r($tableTrueArr);
        return $tableTrueArr;

    }

    /**
     * 获取当前查询的所有字段列表
     * @Title: getFileds
     * @Description: todo(获取当前查询的所有字段列表) 
     * @param string $str	SQL 语句  
     * @author quqiang 
     * @date 2015年4月30日 下午6:10:18 
     * @throws
     */
    function getFileds($str){
    	$data = self::checkSQLFields($str);
    	$funcField = $data['func'];
    	unset($data['func']);
    	unset($data['condition']);
    	// 别名为key =>{table , field,alias,title}
    	$tempData=array();
    	foreach ($data as $k=>$v){
    		foreach ($v['fields'] as $key=>$val){
    			unset($temp);
    			// 大数组key 为别名
    			$temp['table']		=	$v['tablename'];	//	表名
    			$temp['field']		=	$v['tablename'].'.'.$key;	// 真实字段名
    			$temp['alias']		=	$val;						//	别名
    			// val 是别名
    			// key 是真实字段名
    			
    			$tempData[$val]	=	$temp;
    		}
    	}
    	foreach ($funcField['fields'] as $key=>$val){
    		$tempData[$val]=array(
    				'table'	=>	'',		//	表名
    				'field'	=>	$key,	// 真实字段名
    				'alias'	=>	$val
    		);
    	}
    	return $tempData;
    }
}
?>