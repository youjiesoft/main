			
			<?php
			/**
			 * @Title: MisSystemPanelChmAction
			 * @Package package_name
			 * @Description: todo(自定义小块面板（个人首页，公司首页）)";
			 * @author 管理员
			 * @company 重庆特米洛科技有限公司";
			 * @copyright 本文件归属于重庆特米洛科技有限公司";
			 * @date 2015-05-29 11:25:19
			 * @version V1.08
			*/
			class MisSystemPanelChmAction extends AutoPanelAction{
				public function setting(){
				}
				public function getConfig(){
				}
				/**
				 * 显示当前面板内容
				 * @Title: showPanel
				 * @Description: todo(页面展示)
				 * @author 管理员
				 * @date 2015-05-29 11:25:19
				 * @throws
				 */
				public function showPanel(){
					$submodel=M("mis_system_panel_desing_sub");
					$sublist = $submodel->where("masid=5")->select();
					$scdmodel = D("SystemConfigDetail");
					$map["status"]=1;
					foreach($sublist as $key=>$val){
					$curl="http://news.baidu.com/ns?cl=2&rn=20&tn={$val['keywordtype']}&word=";
					$title=$val['keyword'];
					$endcurl=$curl.urlencode($title); 
					$html = file_get_contents($endcurl);
					$star = strpos($html,'<div id="content_left">');
					$end = strpos($html,'<div id="gotoPage">');
					$html = substr($html,$star,$end-$star);
					preg_match_all('#<h3(.*?)>(.*?)</h3>#is' , $html , $str );
					if($val['keywordtype']=="news"){
						preg_match_all('#<p class=\"c-author\">(.*?)</p>#is' , $html , $str1 );
					}else{
						preg_match_all('#<div class=\"c-title-author\">(.*?)<a(.*?)>(.*?)</a></div>#is' , $html , $str1 );
					}
					if(empty($val["num"])){
						$num = 5;
					}else{
						$num = $val["num"] ;
					}
					$i=1;
					foreach($str1[1] as $tk => $tv){
						if($i>$num) break;
						$time = explode("&nbsp;&nbsp;",$tv);
						$sublist[$key]["time"][]=substr(strip_tags($time[1]),0,18);
									$i++;
					}
					$j=1;
					foreach($str[0] as $k => $v){
						if($j>$num) break;
						preg_match_all('#href=\"(.*?)\"#' , $v , $url);
						preg_match_all('#<a(.*?)>(.*?)</a>#is' , $v , $text);
						$listArr=array();
						$listArr=array(
							'title'=>reset($text[2]),
							'url'=>reset($url[0]),
						);
						$sublist[$key]["list"][]=$listArr;
						$j++;
					}
					}
					$this->assign("sublist",$sublist);
					$this->display("MisSystemPanelDesingMas:newscontent");
				}
			}