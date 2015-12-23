<?php 
/**
 * PDF生成类
 *
 * @author      JIVERO.COM
 * @copyright   Copyright © 2012 - 2018 www.jivero.com All rights reserved.
 * @created     2012-08-21
 * @updated     2012-08-21
 * @version     1.0
 */

// 加载TCPDF文件
if(!defined('TCPDF_BASE_PATH')) {
    define('TCPDF_BASE_PATH', dirname(__FILE__) . '/');
    require TCPDF_BASE_PATH . 'tcpdf/tcpdf.php';
}
class Pdf extends TCPDF
{
    private $pdf_author      = ''; // 作者
    private $pdf_keywords    = ''; // 关键字
    private $pdf_subject     = ''; // 主题
    private $pdf_title       = ''; // 标题

    /**
     * 初始化
     *
     * @access public
     * @param  array  $params 初始化参数
     * @return void
     */

    public function __construct($params)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($this->pdf_author);
        $pdf->SetTitle($this->pdf_title);
        $pdf->SetSubject($this->pdf_subject);
        $pdf->SetKeywords($this->pdf_keywords);

        // 不显示头部和底部
        $pdf->setPrintHeader(FALSE);
        $pdf->setPrintFooter(FALSE);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        require TCPDF_BASE_PATH.('tcpdf/config/lang/eng.php');
        $pdf->setLanguageArray($l);

        // set font
        $pdf->SetFont('stsongstdlight', '', 10);
        $pdf->AddPage();

        $pdf->writeHTML($params['content'], true, false, true, false, '');
        $pdf->lastPage();

        // 输出方式 I:浏览器直接输出 D：文件下载 如果需要浏览器输出或者下载的同时生成文件请在前面加上F
        $pdf->Output($params['filename'], $params['flag']);
    }
}