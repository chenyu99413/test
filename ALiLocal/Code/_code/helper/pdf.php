<?php
require_once dirname(__FILE__) . DS . 'PDFMerger' . DS . 'PDFMerger.php';
@require_once (dirname(__FILE__) . DS . 'PDFMerger' . DS . 'fpdf/fpdf.php');
@require_once (dirname(__FILE__) . DS . 'PDFMerger' . DS . 'fpdi/fpdi.php');

class Helper_PDF
{

	/**
	 * 合并 PDF 文件
	 * 
	 * @param array $filenames
	 * @param string $outputmode
	 * @param string $outputpath
	 */
	static function merge($filenames, $outputpath = 'newfile.pdf' ,$outputmode = 'browser',$watermarkpath=array())
	{
		$filenames = Q::normalize($filenames);
		
		$pdf = new PDFMerger();
		foreach ($filenames as $fname) {
			$pdf->addPDF($fname);
		}
		$pdf->merge($outputmode, $outputpath,$watermarkpath);
	}
	/**
	 *合并invoice和label 文件
	 *
	 * @param array $filenames
	 * @param string $outputmode
	 * @param string $outputpath
	 */
	static function combiprint($filenames, $outputpath = 'newfile.pdf', $outputmode = 'I')
	{
	    $pdf = new FPDI();
	    foreach ($filenames as $filename){
	        $page_count=$pdf->setSourceFile($filename);
            for ($i=1;$i<=$page_count;$i++){
                $tpl=$pdf->importPage($i);
                $size= $pdf->getTemplateSize($tpl);
                if(!strstr($filename, '_invoice')){
                    $pdf->AddPage('P',array($size['w']*3, $size['h']*3));
                    $pdf->useTemplate($tpl,null,null,$size['w']*3, $size['h']*3);
                }else{
                    $pdf->AddPage('P',array($size['w'], $size['h']));
                    $pdf->useTemplate($tpl,null,null,$size['w'], $size['h']);
                }
	        }
	    }
	    $pdf->Output($outputpath,$outputmode);
	}
	/**
	 * 获取pdf页数
	 */
	static function pdfCount($filenames)
	{
		$pdf = new FPDI();
		return $pdf->setSourceFile($filenames);
	}
	/**
	 * 拆分 PDF 文件
	 *
	 * @param array $filenames
	 * @param string $outputmode
	 * @param string $outputpath
	 */
	static function split($filenames, $outputpath = 'newfile.pdf', $outputmode = 'F',$count='H',$watermarkpath=array())
	{
	    $get_count=1;
	    $pdf = new FPDI();
	    $page_count=$pdf->setSourceFile($filenames);
	    if($count=='O'){//只留第一张PDF
	        $get_count=$page_count;
	    }
	    if($count=='H'){//保留一半PDF
	        $get_count=2;
	    }
	    for ($i=1;$i<=$page_count/$get_count;$i++){
	        $tpl=$pdf->importPage($i);
	        $size= $pdf->getTemplateSize($tpl);
	        $pdf->AddPage('P',array($size['w'], $size['h']));
	        $pdf->useTemplate($tpl);
	        if(count($watermarkpath)>0){
	            if(isset($watermarkpath['chapter1'])){
	                $pdf->image($watermarkpath['chapter1'], 5, 122, 39);
	            }
	            if(isset($watermarkpath['chapter2'])){
	                $pdf->image($watermarkpath['chapter2'], 55, 122, 39);
	            }
	            if(isset($watermarkpath['chapter3'])){
	                $pdf->image($watermarkpath['chapter3'], 32, 54, 65);
	            }
	        }
	    }
	    $pdf->Output($outputpath,$outputmode);
	    $pdf->Close();
	}
	
	/**
	 * 旋转 PDF 文件
	 *
	 * @param array $filenames
	 * @param string $outputmode
	 * @param string $outputpath
	 */
	static function rotate($filenames, $outputpath = 'newfile.pdf', $outputmode = 'F')
	{
		$pdf = new FPDI();
		$page_count=$pdf->setSourceFile($filenames);
		for ($i=1;$i<=$page_count;$i++){
			$tpl=$pdf->importPage($i,90);
			$size= $pdf->getTemplateSize($tpl);
			$pdf->AddPage('P',array($size['w'], $size['h']));
			$pdf->useTemplate($tpl);
		}
		$pdf->Output($outputpath,$outputmode);
		$pdf->Close();
	}
	
	/**
	 * 根据输入的 ups gif label 图片文件转换为 pdf 文件
	 * @param url $gifFilePath
	 * @param url $saveFilePath
	 * @example
	 * 	# direct output to the browser
	 * 	Helper_PDF::upslabel(INDEX_DIR.'/_tmp/upload/1Z3X22910415814856.gif')
	 * @example 
	 * 	# save to file save to file
	 * 	Helper_PDF::upslabel(INDEX_DIR.'/_tmp/upload/1Z3X22910415814856.gif','./a.pdf')
	 */
	static function upslabel($gifFilePath,$saveFilePath=''){
		$pdf = new PDF('P','in',array(4,6));
		$pdf->SetMargins(0,0,0);
		$pdf->AddPage();
		$pdf->Rotate(270);
		$pdf->Image($gifFilePath,0.01,-4,7,4);
		$pdf->Rotate(0);
		$pdf->Output($saveFilePath);
	}
	/**
	 * ems pdf面单增加A0
	 * @param unknown $filename
	 */
	static function ems($filename){
	    $url=_INDEX_DIR_.'/public/img/emsflag.gif';
        $pdf = new FPDI();
	    $page_count=$pdf->setSourceFile($filename);
	    for ($i=1;$i<=$page_count;$i++){
            $tpl=$pdf->importPage($i);
	        $size= $pdf->getTemplateSize($tpl);
	        $pdf->AddPage('P',array($size['w']*3, $size['h']*3));
	        $pdf->useTemplate($tpl,null,null,$size['w']*3, $size['h']*3);
            $pdf->Image($url, '557','345','42','53');
	        $pdf->Image($url, '557','790','42','53');
	    }
        $pdf->Output($filename,'F');
	}
	
	/**
	 * fsp pdf面单增加01t
	 * @param unknown $filename
	 */
	static function fsp($filename){
	    $url=_INDEX_DIR_.'/public/img/usfylogo.gif';
	    $pdf = new FPDI();
	    $page_count=$pdf->setSourceFile($filename);
	    for ($i=1;$i<=$page_count;$i++){
	        $tpl=$pdf->importPage($i);
	        $size= $pdf->getTemplateSize($tpl);
	        $pdf->AddPage('P',array($size['w'], $size['h']));
	        $pdf->useTemplate($tpl);
	        $pdf->Image($url, '35','147','10','5');
	    }
	    $pdf->Output($filename,'F');
	}
	
	static function fedex($filename,$product){
	    $pdf = new FPDI();
	    $pdf->AddGBFont('simhei','黑体');
	    $page_count=$pdf->setSourceFile($filename);
	    for ($i=1;$i<=$page_count;$i++){
	        $tpl=$pdf->importPage($i);
	        $size= $pdf->getTemplateSize($tpl);
	        $pdf->AddPage('P',array($size['w'], $size['h']));
	        $pdf->useTemplate($tpl);
	        if($i>1 && $i<5){
	            $pdf->Cell(20,72);
	            $pdf->Ln();
                $pdf->SetFont('simhei','B',7);
                //FAR中文品名FAR英文品名FAR HS编码
                foreach ($product as $p){
                    
                }
                $pdf->Cell(92,10,iconv("utf-8","gbk",$p->product_name_far.' '.$p->product_quantity),'','','R');
	        }
	    }
        $pdf->Output($filename,'F');
	}
	
	static function kingspeed($filename,$sign){
		$pdf = new PDF('P','in',array(4,6));
		$pdf->SetMargins(0,0,0);
		$pdf->AddPage();
		$pdf->Rotate(0);
		$pdf->Image($filename.'.jpg',0,0,4,6);
		$pdf->Rotate(0);
// 		$pdf->Output();
		$pdf->Output($filename);
		
	    $pdf = new FPDI();
	    $pdf->AddGBFont('simhei','黑体');
	    $page_count=$pdf->setSourceFile($filename);
	    for ($i=1;$i<=$page_count;$i++){
	       $tpl=$pdf->importPage($i);
	        $size= $pdf->getTemplateSize($tpl);
	        $pdf->AddPage('P',array($size['w'], $size['h']));
	        $pdf->useTemplate($tpl);
	        $pdf->Cell(20,122);
            $pdf->Ln();
            $pdf->SetFont('simhei','B',10);
            $pdf->Cell(70,0,iconv("utf-8","gbk",$sign),'','','R');
	    }
        $pdf->Output($filename,'F');
// 	    $pdf->Output();
	}
	/**
	 * @todo   ib加标签标记
	 * @author stt
	 * @since  2021年1月15日09:20:58
	 * @param
	 * @return
	 * @link   #85275
	 */
	static function ib($filename,$sign){
		//PDF
		$pdf = new PDF('P','in',array(4,6));
		
		$pdf->SetMargins(0,0,0);
		$pdf->AddPage();
		$pdf->Rotate(0);
		//面单图片
		$pdf->Image($filename.'.jpg',0,0,4,6);
		$pdf->Rotate(0);
		$pdf->Output($filename);
		$pdf = new FPDI();
		$pdf->AddGBFont('simhei','黑体');
		
		//PDF页数
		$page_count=$pdf->setSourceFile($filename);
		//循环加标签标记
		for ($i=1;$i<=$page_count;$i++){
			$tpl=$pdf->importPage($i);
			$size= $pdf->getTemplateSize($tpl);
			$pdf->SetAutoPageBreak(false);
			$pdf->AddPage('P',array($size['w'], $size['h']));
			$pdf->useTemplate($tpl);
			$pdf->Cell(90,139);
			$pdf->Ln();
			$pdf->SetFont('simhei','B',10);
			//标签标记位置
			$pdf->Cell(90,0,iconv("utf-8","gbk",$sign),'','','R');
		}
		$pdf->Output($filename,'F');
	}
	/**
	 * @todo   shuncheng加标签标记
	 * @author stt
	 * @since  2021年1月15日09:20:58
	 * @param
	 * @return
	 * @link   #85275
	 */
	static function shuncheng($filename,$sign){
		//PDF
		$pdf = new PDF('P','in',array(4,6));
		$pdf->SetMargins(0,0,0);
		$pdf->AddPage();
		$pdf->Rotate(0);
		//面单图片
		$pdf->Image($filename.'.jpg',0,0,4,6);
		$pdf->Rotate(0);
		$pdf->Output($filename);
		$pdf = new FPDI();
		$pdf->AddGBFont('simhei','黑体');
		//PDF页数
		$page_count=$pdf->setSourceFile($filename);
		//循环加标签标记
		for ($i=1;$i<=$page_count;$i++){
			$tpl=$pdf->importPage($i);
			$size= $pdf->getTemplateSize($tpl);
			//不自动分页
			$pdf->SetAutoPageBreak(false);
			$pdf->AddPage('P',array($size['w'], $size['h']));
			$pdf->useTemplate($tpl);
			$pdf->Cell(90,105);
			$pdf->Ln();
			$pdf->SetFont('simhei','B',10);
			//标签标记位置
			$pdf->Cell(80,0,iconv("utf-8","gbk",$sign),'','','R');
		}
		//输出
		$pdf->Output($filename,'F');
	}
	/**
	 * @todo   abcsp加标签标记
	 * @author stt
	 * @since  2021年1月15日09:20:58
	 * @param
	 * @return
	 * @link   #85275
	 */
	static function abcsp($filename,$sign){
		//PDF
		$pdf = new PDF('P','in',array(4,6));
		$pdf->SetMargins(0,0,0);
		$pdf->AddPage();
		$pdf->Rotate(0);
		//面单图片
		$pdf->Image($filename.'.jpg',0,0,4,6);
		$pdf->Rotate(0);
		$pdf->Output($filename);
		$pdf = new FPDI();
		$pdf->AddGBFont('simhei','黑体');
		//PDF页数
		$page_count=$pdf->setSourceFile($filename);
		//循环加标签标记
		for ($i=1;$i<=$page_count;$i++){
			$tpl=$pdf->importPage($i);
			$size= $pdf->getTemplateSize($tpl);
			//不自动分页
			$pdf->SetAutoPageBreak(false);
			$pdf->AddPage('P',array($size['w'], $size['h']));
			$pdf->useTemplate($tpl);
			$pdf->Cell(90,125);
			$pdf->Ln();
			$pdf->SetFont('simhei','B',10);
			//标签标记位置
			$pdf->Cell(80,0,iconv("utf-8","gbk",$sign),'','','R');
		}
		//输出
		$pdf->Output($filename,'F');
	}
	/**
	 * @todo   PDF文件是否存在
	 * @author stt
	 * @since  2020-10-19
	 * @param
	 * @return
	 * @link   #81897
	 */
	static function pdfisexist($filename,$type='yes'){
		// 
		$data['message']='noexist';
		$data['url']='';
		if ($type=='no'){
			// 
			return $data;
		}
		$ali_oss = new Helper_AlipicsOss();
		$dir=Q::ini('upload_tmp_dir');
		//正式路径
		$ossurl = 'http://ia1.oss-cn-hangzhou.aliyuncs.com/alipics_tmp/'.$filename;
		$farurl = $dir.DS.$filename;
		if($ali_oss->doesExist($filename)){
			$data['message']='oss';
			$data['url']=$ossurl;
		}elseif (file_exists($farurl)){
			$data['message']='far';
			$data['url']=$farurl;
		}else{
			$data['message']='noexist';
			$data['url']='';
		}
		return $data;
	}
}

class PDF_Rotate extends FPDF
{

	var $angle = 0;

	function Rotate($angle, $x = -1, $y = -1)
	{
		if ($x == - 1)
			$x = $this->x;
		if ($y == - 1)
			$y = $this->y;
		if ($this->angle != 0)
			$this->_out('Q');
		$this->angle = $angle;
		if ($angle != 0) {
			$angle *= M_PI / 180;
			$c = cos($angle);
			$s = sin($angle);
			$cx = $x * $this->k;
			$cy = ($this->h - $y) * $this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, - $s, $c, $cx, $cy, - $cx, - $cy));
		}
	}

	function _endpage()
	{
		if ($this->angle != 0) {
			$this->angle = 0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}

class PDF extends PDF_Rotate
{

	function RotatedText($x, $y, $txt, $angle)
	{
		// Text rotated around its origin
		$this->Rotate($angle, $x, $y);
		$this->Text($x, $y, $txt);
		$this->Rotate(0);
	}

	function RotatedImage($file, $x, $y, $w, $h, $angle)
	{
		// Image rotated around its upper-left corner
		$this->Rotate($angle, $x, $y);
		$this->Image($file, $x, $y, $w, $h);
		$this->Rotate(0);
	}
}
