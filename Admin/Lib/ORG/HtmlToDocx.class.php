<?php
/**
 *  Example of use of HTML to docx converter
 */
// Load the files we need:
import("@.ORG.htmltodocx.phpword.PHPWord");
import("@.ORG.htmltodocx.simplehtmldom.simple_html_dom");
import("@.ORG.htmltodocx.htmltodocx_converter.h2d_htmlconverter");
import("@.ORG.htmltodocx.example_files.styles");


class HtmlToDocx{
	/*
	 *功能：从一个html文件转换成 docx文件
	*参数 String   $file   文件相对路径+文件名
	*
	*/
	public function save($html, $dir){
		import("@.ORG.htmltodocx.documentation.support_functions");
		$phpword_object = new PHPWord();
		$section = $phpword_object->createSection();

		// HTML Dom object:
		$html_dom = new simple_html_dom();
		$html_dom->load('<html><body>' . $html . '</body></html>');
		// Note, we needed to nest the html in a couple of dummy elements.

		// Create the dom array of elements which we are going to work on:
		$html_dom_array = $html_dom->find('html',0)->children();
		// We need this for setting base_root and base_path in the initial_state array
		// (below). We are using a function here (derived from Drupal) to create these
		// paths automatically - you may want to do something different in your
		// implementation. This function is in the included file
		// documentation/support_functions.inc.
		$paths = htmltodocx_paths();

		// Provide some initial settings:
		$initial_state = array(
				// Required parameters:
				'phpword_object' => &$phpword_object, // Must be passed by reference.
				// 'base_root' => 'http://test.local', // Required for link elements - change it to your domain.
				// 'base_path' => '/htmltodocx/documentation/', // Path from base_root to whatever url your links are relative to.
				'base_root' => $paths['base_root'],
				'base_path' => $paths['base_path'],
				// Optional parameters - showing the defaults if you don't set anything:
				'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
				'parents' => array(0 => 'body'), // Our parent is body.
				'list_depth' => 0, // This is the current depth of any current list.
				'context' => 'section', // Possible values - section, footer or header.
				'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
				'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
				'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
				'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
				'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
				'treat_div_as_paragraph' => TRUE, // If set to TRUE, each new div will trigger a new line in the Word document.

				// Optional - no default:
		'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by htmltodocx_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
		);

		// Convert the HTML and put it into the PHPWord object
		htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);

		// Clear the HTML dom object:
		$html_dom->clear();
		unset($html_dom);

		// Save File
		$str = explode(".",$h2d_file_uri);
		$h2d_file_uri = $dir."wordtemp/".time().".docx";
		if ( !file_exists($dir."wordtemp/") ) {
			$this->createFolders($dir."wordtemp/"); //判断目标文件夹是否存在
		}
		$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
		$objWriter->save($h2d_file_uri);

		return $h2d_file_uri;
	}
	
	/**
	 * 创建目录 2012-12-17
	 * @author wangcheng
	 * @param string $path 创建目录路径
	 */
	function createFolders($path) {
		if (!file_exists($path)) {
			$this->createFolders(dirname($path));
			mkdir($path, 0777);
		}
	}
}
?>