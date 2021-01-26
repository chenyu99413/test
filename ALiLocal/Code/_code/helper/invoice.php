<?php
require_once _INDEX_DIR_ . '/_code/helper/PDFMerger/fpdf/fpdf.php';
require_once _INDEX_DIR_ . '/_code/helper/PDFMerger/fpdf/chinese.php';
/**
 * @todo   发票PDF版
 * @author stt
 * @since  2020-09-28
 * @link   #81897
 */
class Helper_Invoice{
	/**
	 * @todo   UPS发票PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function upsinvoice($jsonarr){
		//_tmp\upload
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tks'].'_invoice.pdf';
		//取生成的条码图片
		$barcode=$dir.DS.$jsonarr['tks'].'.barcode.png';
		//创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		//--
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		//字体Arial大小14加粗
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell('0','8','Invoice','0','0','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		//From 
		$pdf->Cell('99','6','From','1');
		$pdf->Cell('2','6');
		//--
		$pdf->Cell('99','6','','1');
		$pdf->Ln();
		//税号 
		$pdf->Cell('99','6','Tax ID/EIN/VAT No.:','LR');
		$pdf->Cell('2','6');
		//--
		$pdf->Cell('99','6','Waybill number: '.(isset($jsonarr['tks']) ? $jsonarr['tks'] : ''),'LR');
		$pdf->Ln();
		//字体Arial大小10 
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('99','6','Contact Name: '.(isset($jsonarr['shipper']['aname']) ? $jsonarr['shipper']['aname'] : ''),'LR');
		$pdf->Cell('2','6');
		//导出高价数据时生成的shipid(UPS) 
		$pdf->Cell('99','6','Shipment ID: '.(isset($jsonarr['shipmentid']) ? $jsonarr['shipmentid'] : ''),'LR');
		$pdf->Ln();
		//末端单号条码 
		$pdf->Image($barcode,'118','31','60','13');
		//发件人姓名 
		//发件人姓名超出长度处理 
		$namearr = Helper_Common::getdatasplit($jsonarr['shipper']['name'], 35);
		for ($i=0;$i<count($namearr);$i++){
			$pdf->Cell('99','6',$namearr[$i],'LR');
			$pdf->Cell('2','6');
			$pdf->Cell('99','6','','LR');
			$pdf->Ln();
		}
		//发件人地址超长处理 
		$addressarr = Helper_Common::getdatasplit($jsonarr['shipper']['address'], 35);
		//地址 
		$pdf->Cell('99','6',isset($addressarr['0']) ? $addressarr['0'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		//地址 
		$pdf->Cell('99','6',isset($addressarr['1']) ? $addressarr['1'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','Date:','LR');
		$pdf->Ln();
		//地址 
		$pdf->Cell('99','6',isset($addressarr['2']) ? $addressarr['2'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','invoice NO.: ','LR');
		$pdf->Ln();
		//发件人城市 
		$pdf->Cell('99','6',(isset($jsonarr['shipper']['city']) ? $jsonarr['shipper']['city'] : '').' '.(isset($jsonarr['shipper']['postcode']) ? $jsonarr['shipper']['postcode'] : ''),'LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','Purchase No.: '.(isset($jsonarr['ali_order_no']) ? $jsonarr['ali_order_no'] : ''),'LR');
		$pdf->Ln();
		//Terms of Sale(Incoterm) 
		$pdf->Cell('99','6','','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','Terms of Sale(Incoterm): '.(isset($jsonarr['sales_term']) ? $jsonarr['sales_term'] : ''),'LR');
		$pdf->Ln();
		//China, People's Republic of 
		$pdf->Cell('99','6',"China, People's Republic of",'LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','Reason for Export: '.(isset($jsonarr['reason_for_export']) ? $jsonarr['reason_for_export'] : 'Sample'),'LR');
		$pdf->Ln();
		//发件人电话 
		$pdf->Cell('99','6','Phone: '.(isset($jsonarr['shipper']['phone']) ? $jsonarr['shipper']['phone'] : ''),'LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		//字体Arial大小10加粗 
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('99','6','SHIP TO','1');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','SOLD TO INFORMATION','1');
		//换行 
		$pdf->Ln();
		$pdf->Cell('99','6','Tax ID/EIN/VAT No.:','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','Tax ID/VAT NO.:','LR');
		//换行 
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('99','6','Contact Name: '.(isset($jsonarr['name']) ? $jsonarr['name'] : ''),'LR');
		$pdf->Cell('2','6');
		//Contact Name 
		$pdf->Cell('99','6','Contact Name:','LR');
		$pdf->Ln();
		$pdf->Cell('99','6',isset($jsonarr['aname']) ? $jsonarr['aname'] : '','LR');
		$pdf->Cell('2','6');
		//Same as Ship to 
		$pdf->Cell('99','6','Same as Ship to','LR');
		$pdf->Ln();
		// 收件人地址超长处理
		$c_addressarr = Helper_Common::getdatasplit($jsonarr['address'], 35);
		// 收件人地址
		$pdf->Cell('99','6',isset($c_addressarr['0']) ? $c_addressarr['0'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		// 收件人地址
		$pdf->Cell('99','6',isset($c_addressarr['1']) ? $c_addressarr['1'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		// 收件人地址
		$pdf->Cell('99','6',isset($c_addressarr['2']) ? $c_addressarr['2'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		// 收件人城市
		$pdf->Cell('99','6',(isset($jsonarr['city']) ? $jsonarr['city'] : '').' '.(isset($jsonarr['postcode']) ? $jsonarr['postcode'] : ''),'LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		// 收件人国家
		$pdf->Cell('99','6',isset($jsonarr['countryname']) ? $jsonarr['countryname'] : '','LR');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','','LR');
		$pdf->Ln();
		// 收件人电话
		$pdf->Cell('99','6','Phone: '.$jsonarr['phone'],'LRB');
		$pdf->Cell('2','6');
		$pdf->Cell('99','6','Phone:','LRB');
		$pdf->Ln();
		// 换行，行高度为3
		$pdf->Ln('3');
		$pdf->SetFont('Arial','',10);
		// 数量
		$pdf->Cell('13','6','Units','LTB');
		// 单位
		$pdf->Cell('13','6','U/M','TB');
		// 描述
		$pdf->Cell('59','6','Description of Goods/Part No.','TB');
		// HS Code
		$pdf->Cell('21','6','Harm.Code','TB');
		$pdf->Cell('13','6','C/O','TB');
		// 申报单价
		$pdf->Cell('21','6','Unit Value','TB');
		// 申报总价
		$pdf->Cell('21','6','Total Value','TB');
		// 材质用途
		$pdf->Cell('39','6','Material&Use for','RTB','','R');
		$pdf->ln();
		foreach ($jsonarr['invoice']['items'] as $item){
			// 材质用途超长处理
			$namelinearr = Helper_Common::getdatasplit($item['material'],25);
			// 材质用途
			foreach ($namelinearr as $key=>$val){
				if($key == '0'){
					$pdf->SetFont('Arial','',10);
					// 数量
					$pdf->Cell('13','5',isset($item['quantity']) ? round($item['quantity'],2) : '','LTB');
					$pdf->Cell('13','5',isset($item['unit']) ? $item['unit'] : '','TB');
					$pdf->Cell('59','5',isset($item['name']) ? $item['name'] : '','TB');
					// hscode
					$pdf->Cell('21','5',isset($item['hscode']) ? $item['hscode'] : '','TB');
					$pdf->Cell('13','5',isset($item['country']) ? $item['country'] : '','TB');
					$pdf->Cell('21','5',isset($item['price']) ? round($item['price'],3) : '','TB');
					$pdf->Cell('21','5',$item['itotal'],'TB');
					// 材质用途
					$pdf->Cell('39','5',$val,'RTB','','R');
					$pdf->ln();
				}else{
					$pdf->SetFont('Arial','',10);
					// 材质用途
					$pdf->Cell('95','5','','L');
					$pdf->Cell('105','5',$val,'R','','R');
					// 换行
					$pdf->ln();
				}
			}
		}
		// 换行，行高度为10
		$pdf->Ln('10');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('200','6','Additional Comments:','T');
		$pdf->Ln();
		// Declaration
		$pdf->Cell('99','6','Declaration Statement:','TLR');
		$pdf->Cell('2','6');
		$pdf->Cell('50','6','Invoice Line Total:','TL','','R');
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6',isset($jsonarr['invoice']['total']) ? round($jsonarr['invoice']['total'],2) : '','TR','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','I hereby certify that the information on this invoice is true','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Discount/Rebate:','L','','R');
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6','0.00','R','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','and correct and the contents and value of this shipment','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Invoice Sub-Total:','L','','R');
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6',isset($jsonarr['invoice']['total']) ? round($jsonarr['invoice']['total'],2) : '','R','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','is as stated above.','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Freight:','L','','R');
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6',$jsonarr['freight'],'R','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Insurance:','L','','R');
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6','0.00','R','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Other:','L','','R');
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6','0.00','R','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Total Invoice Amount:','BL','','R');
		// 总申报价值
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('49','6',isset($jsonarr['invoice']['total']) ? round($jsonarr['invoice']['total'],2) : '','BR','','R');
		$pdf->Ln();
		$pdf->Cell('99','6','','LR');
		// 宽度2高度6
		$pdf->Cell('2','6');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('50','6','Total Number of Packages:','L','','R');
		$pdf->SetFont('Arial','',10);
		// 宽度2高度6
		$pdf->Cell('12','6',isset($jsonarr['itemcount']) ? $jsonarr['itemcount'] : '');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('12','6','Currency:');
		$pdf->SetFont('Arial','',10);
		// Currency
		$pdf->Cell('25','6',isset($jsonarr['invoice']['items']['0']['currency']) ? $jsonarr['invoice']['items']['0']['currency'] : '','R','','R');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('30','6','Shipper','BL');
		// Currency
		$pdf->Cell('69','6','Date','BR');
		$pdf->Cell('2','6');
		$pdf->Cell('50','6','Total Weight:','BL','','R');
		// 总重量
		// Arial
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('12','6',isset($jsonarr['total_weight']) ? round($jsonarr['total_weight'],2) : '','B');
		$pdf->Cell('37','6','KGS','BR');
		$pdf->Ln();
		// 输出pdf
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   UPS国家是PE和VG发票PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function upsinvoicepe($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tracking_no'].'_invoice.pdf';
		//取生成的条码图片
		$barcode=$dir.DS.$jsonarr['tracking_no'].'.barcode.png';
		//创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		//--
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		//字体Arial大小14加粗
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell('0','12','Commercial Invoice','0','0','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		//From
		$pdf->Cell('30','8','From','1','','C');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('170','8',$jsonarr['sender']['name'],'TBR');
		$pdf->Ln();
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','12',$jsonarr['sender']['address'],'LR');
		$pdf->Ln();
		//发件人信息
		$pdf->Cell('200','6',$jsonarr['sender']['city'].$jsonarr['sender']['state'].'CHINA'.$jsonarr['sender']['postcode'],'LR');
		$pdf->Ln();
		$pdf->Cell('200','6',$jsonarr['sender']['phone'],'LRB');
		$pdf->Ln();
		//末端单号条形码空间
		$pdf->Cell('200','30','','LR');
		$pdf->Image($barcode,'74','56','60','23');
		$pdf->Ln();
		$pdf->Cell('200','6',$jsonarr['tracking_no'],'LR','','C');
		//换行
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('30','8','To','1','','C');
		$pdf->SetFont('Arial','',10);
		//收件人公司
		$pdf->Cell('170','8',$jsonarr['consignee_company'],'TBR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		//收件人地址
		$pdf->Cell('200','12',$jsonarr['consignee_address'],'LR');
		$pdf->Ln();
		$pdf->Cell('200','6',$jsonarr['consignee_city'].$jsonarr['consignee_state'].$jsonarr['consignee_country_code'].$jsonarr['consignee_postal_code'],'LR');
		$pdf->Ln();
		//收件人电话
		$pdf->Cell('200','6',$jsonarr['consignee_phone'],'LRB');
		$pdf->Ln();
		//中文PDF显示方法字体simhei
		//--
		$pdf->SetFont('simhei','B',10);
		$pdf->Cell('110','6',iconv("utf-8","gbk","货品描述"),'LR','','C');
		$pdf->Cell('30','6',iconv("utf-8","gbk",'数量(PCS)'),'R','','C');	
		$pdf->Cell('30','6',iconv("utf-8","gbk",'单价(USD)'),'R','','C');
		//总价(USD)
		$pdf->Cell('30','6',iconv("utf-8","gbk",'总价(USD)'),'R','','C');
		$pdf->Ln();
		//--
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('110','6','Description of Goods','LR','','C');
		// 数量
		$pdf->Cell('30','6','Quantities','R','','C');
		$pdf->Cell('30','6','Unit Price','R','','C');
		$pdf->Cell('30','6','Total Amount','R','','C');
		$pdf->Ln();
		// 中文PDF显示方法字体simhei
		// --
		$pdf->SetFont('simhei','B',10);
		$pdf->Cell('200','12',iconv("utf-8","gbk",'收件方税号').'Import Tax  No.:'.$jsonarr['tax'],'1','','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 循环申报产品
		foreach ($jsonarr['invoice']['items'] as $item){
			$pdf->Cell('110','8',isset($item['desc']) ? $item['desc'] : '','LR','','C');
			$pdf->Cell('30','8',isset($item['quantity']) ? $item['quantity'] : '','R','','C');
			$pdf->Cell('30','8',isset($item['price']) ? $item['price'] : '','R','','C');
			// 申报价值
			$pdf->Cell('30','8',isset($item['itotal']) ? $item['itotal'] : '','R','','C');
			$pdf->ln();
		}
		// --
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('110','8','N.C.V           MADE IN CHINA','LRB','','C');
		// 空单元格
		$pdf->Cell('30','8','','RB','','C');
		$pdf->Cell('30','8','','RB','','C');
		$pdf->Cell('30','8','','RB','','C');
		$pdf->ln();
		// 0
		$pdf->Cell('110','8','','0','','C');
		$pdf->Cell('30','8','','0','','C');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('30','8','Total:','0','','C');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('30','8',$jsonarr['total_value'],'0','','C');
		// 换行
		$pdf->ln();
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   fda发票PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function invoicefda($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tracking_no'].'_invoice.pdf';
		// 取生成的条码图片
		$barcode=$dir.DS.$jsonarr['tracking_no'].'.barcode.png';
		// 创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		// 字体Arial大小14加粗
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell('0','12','COMMERCIAL INVOICE','0','0','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// Date
		$pdf->Cell('50','8','Date','1','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',date('Y-m-d H:i:s',time()),'TBR');
		$pdf->Ln();
		// 字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','AIR WAYBILL NO.','LR','');
		$pdf->SetFont('Arial','',10);
		// 末端单号
		$pdf->Cell('150','8',$jsonarr['tracking_no'],'R');
		$pdf->Ln();
		$pdf->Cell('200','30','','1');
		// 末端单号条码
		$pdf->Image($barcode,'74','36','60','23');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('200','8','Manufacturer','LR');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','Company Name','1','');
		// --
		$pdf->SetFont('simhei','',10);
		// FDA制造商信息 生产商公司名
		$pdf->Cell('150','8',iconv("utf-8","gbk",$jsonarr['fda_company']),'TBR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// FDA制造商 地址
		$pdf->Cell('50','8','Address','LBR','');
		// --
		$pdf->SetFont('simhei','',10);
		$pdf->Cell('150','8',iconv("utf-8","gbk",$jsonarr['fda_address']),'BR');
		$pdf->Ln();
		// FDA制造商 邮编
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','City/Zip code','LBR','');
		// simhei
		$pdf->SetFont('simhei','',10);
		$pdf->Cell('150','8',iconv("utf-8","gbk",$jsonarr['fda_city'].'/'.$jsonarr['fda_post_code']),'BR');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('200','8','Shipper','LR');
		$pdf->Ln();
		// 字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','Company Name','1','');
		// simhei 
		$pdf->SetFont('Arial','',10);
		// 发件人姓名
		$pdf->Cell('150','8',$jsonarr['shipper']['name'],'TBR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','Address','LBR','');
		// 发件人地址
		// -- 
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['shipper']['address'],'BR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 发件人邮编
		$pdf->Cell('50','8','City/Zip code','LBR','');
		// -- 
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['shipper']['city'].'/'.$jsonarr['shipper']['postcode'],'BR');
		$pdf->Ln();
		// 字体Arial大小10加粗
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('200','8','Consignee','LR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 收件人公司
		$pdf->Cell('50','8','Company Name','1','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['consignee_company'],'TBR');
		$pdf->Ln();
		// 收件人地址
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','Address','LBR','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['consignee_address'],'BR');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','City/Zip code','LBR','');
		$pdf->SetFont('Arial','',10);
		// 收件人城市
		$pdf->Cell('150','8',$jsonarr['consignee_city'].'/'.$jsonarr['consignee_postal_code'],'BR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','Country/State','LBR','');
		// 收件人国家二字码
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['consignee_country_code'].'/'.$jsonarr['consignee_state'],'BR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 收件人姓名
		$pdf->Cell('50','8','Contact Name','LBR','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['consignee_name'],'BR');
		$pdf->Ln();
		// 收件人电话
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('50','8','Phone','LBR','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['consignee_phone'],'BR');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('200','8','Shipment  Details','LR');
		$pdf->Ln();
		// 字体Arial大小10加粗
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('60','6','Description','TLR','');
		$pdf->Cell('10','6','PCS','TR','');
		$pdf->Cell('50','6','Material&Use','TR','');
		// hscode
		$pdf->Cell('20','6','HS CODE','TR','');
		$pdf->Cell('20','6','Origin','TR','');
		$pdf->Cell('20','6','Unit','TR','');
		$pdf->Cell('20','6','Total','TR','');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('60','6','','BLR','');
		$pdf->Cell('10','6','','BR','');
		// Material
		$pdf->Cell('50','6','','BR','');
		$pdf->Cell('20','6','','BR','');
		$pdf->Cell('20','6','','BR','');
		$pdf->Cell('20','6','Value(USD)','BR','');
		// Total
		$pdf->Cell('20','6','Value(USD)','BR','');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		foreach ($jsonarr['invoice']['items'] as $item){
			// 描述
			$pdf->Cell('60','6',$item['description'],'LRB','');
			$pdf->Cell('10','6',$item['quantity'],'RB','');
			$pdf->Cell('50','6',$item['material'],'RB','');
			// hscode
			$pdf->Cell('20','6',$item['hscode'],'RB','');
			$pdf->Cell('20','6',$item['origin'],'RB','');
			$pdf->Cell('20','6',$item['price'],'RB','');
			// 申报价值
			$pdf->Cell('20','6',$item['itotal'],'RB','');
		}
		$pdf->Ln();
		$pdf->Cell('160','8','','0','');
		// Total
		$pdf->Cell('20','8','Total (USD)','0','');
		$pdf->Cell('20','8',$jsonarr['invoice']['total'],'0','');
		$pdf->ln();
		// 输出PDF文件
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   dhl发票PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function dhlinvoice($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tracking_no'].'_invoice.pdf';
		// 取生成的条码图片
		$barcode=$dir.DS.$jsonarr['tracking_no'].'.barcode.png';
		// 创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		// 字体Arial大小14加粗
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell('0','12','Commercial Invoice','0','0','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		// FROM 发件人公司
		$pdf->Cell('50','8','FROM','1','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['sender']['company'],'TBR');
		$pdf->Ln();
		// 发件人姓名
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['sender']['name'],'LR');
		$pdf->Ln();
		// 发件人地址过长处理
		$addressarr = Helper_Common::getdatasplit($jsonarr['sender']['address'],80);
		// 循环地址数组
		for($i=0;$i<count($addressarr);$i++){
			$pdf->SetFont('Arial','',10);
			// --
			$pdf->Cell('200','8',$addressarr[$i],'LR');
			$pdf->Ln();
		}
		// 发件人信息
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['sender']['city'].' '.$jsonarr['sender']['state'].' '.$jsonarr['sender']['postcode'],'LR');
		$pdf->Ln();
		// 发件人电话
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['sender']['phone'],'LR');
		$pdf->Ln();
		
		if ($jsonarr['invoice_type']==2){
			//贸易商
			$pdf->SetFont('Arial','B',12);
			// FROM 发件人公司
			$pdf->Cell('50','8','Trader','1','');
			$pdf->SetFont('Arial','',10);
			$pdf->Cell('150','8',Helper_Chinese::toPinYinucfirst($jsonarr['actual_sender']['sender_name']),'TBR');
			$pdf->Ln();
			// 发件人地址过长处理
			$actual_sender_addressarr = Helper_Common::getdatasplit(Helper_Chinese::toPinYinucfirst($jsonarr['actual_sender']['sender_address']),180);
			// 循环地址数组
			for($i=0;$i<count($actual_sender_addressarr);$i++){
				$pdf->SetFont('Arial','',10);
				// --
				$pdf->Cell('200','8',$actual_sender_addressarr[$i],'LR');
				$pdf->Ln();
			}
		}
		
		
		$pdf->Cell('200','30','','LRT');
		// 末端单号条码
		// 条码位置
		$width = 60;
		if(count($addressarr)){
			// 循环地址数组
			for($i=0;$i<count($addressarr);$i++){
				$width += $i*10;
			}
		}
		
		// 循环地址数组
		if ($jsonarr['invoice_type']==2){
			$width = $width+15;
			if(count($actual_sender_addressarr)){
				for($j=0;$j<count($actual_sender_addressarr);$j++){
					$width += $j*10;
				}
			}
		}
		$pdf->Image($barcode,'80',$width,'46','25');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['tracking_no'],'LRB','','C');
		$pdf->Ln();
		
		// 收件人公司
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell('50','8','TO','LRB','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['consignee_company'],'BR');
		// 换行
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['consignee_company'],'LR','');
		$pdf->Ln();
		// 收件人地址
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['consignee_address'],'LR');
		$pdf->Ln();
		// 收件人地址
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['consignee_city'].' '.$jsonarr['consignee_state'].' '.$jsonarr['consignee_country_code'].' '.$jsonarr['consignee_postal_code'],'LR');
		$pdf->Ln();
		// 收件人电话
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['consignee_phone'],'LBR');
		$pdf->Ln();
		// simhei
		$pdf->SetFont('simhei','B',10);
		// 申报产品
		$pdf->Cell('103','6',iconv("utf-8","gbk",'货品描述'),'LR','','C');
		$pdf->Cell('32','6',iconv("utf-8","gbk",'数量(PCS)'),'R','','C');
		$pdf->Cell('32','6',iconv("utf-8","gbk",'单价(USD)'),'R','','C');
		$pdf->Cell('33','6',iconv("utf-8","gbk",'总价(USD)'),'R','','C');
		// 换行
		$pdf->Ln();
		// --
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('103','6','Description of Goods','LBR','','C');
		$pdf->Cell('32','6','Quantities','BR','','C');
		// 申报单价
		$pdf->Cell('32','6','Unit Price','BR','','C');
		$pdf->Cell('33','6','Total Amount','BR','','C');
		$pdf->Ln();
		// simhei
		$pdf->SetFont('simhei','B',10);
		// 收件方税号
		$pdf->Cell('200','15',iconv("utf-8","gbk",'收件方税号').'Import Tax  No.:'.$jsonarr['tax'],'LRB','','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		foreach ($jsonarr['invoice']['items'] as $key=>$item){
			// 循环申报产品
			// 描述超长处理
			$descarr = Helper_Common::getdatasplit($item['desc'],45);
			// 循环地址数组
			for($i=0;$i<count($descarr);$i++){
				// 地址第一行
				if($i==0){
					// simhei
					$pdf->SetFont('simhei','',10);
					$pdf->Cell('103','8',iconv("utf-8","gbk",$descarr[$i]),'LR','','C');
					$pdf->SetFont('Arial','',10);
					$pdf->Cell('32','8',$item['quantity'],'R','','C');
					// 申报单价
					$pdf->Cell('32','8',$item['price'],'R','','C');
					$pdf->Cell('33','8',$item['itotal'],'R','','C');
					// --
					$pdf->Ln();
				}else{
					// simhei
					$pdf->SetFont('simhei','',10);
					$pdf->Cell('103','8',iconv("utf-8","gbk",$descarr[$i]),'LR','','C');
					// --
					$pdf->SetFont('Arial','',10);
					$pdf->Cell('32','8','','R','','C');
					// 申报单价
					$pdf->Cell('32','8','','R','','C');
					$pdf->Cell('33','8','','R','','C');
					$pdf->Ln();
				}
			// --
			}
		}
		// 字体Arial大小10
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('103','6','N.C.V           MADE IN CHINA','LBR','','C');
		$pdf->Cell('32','6','','RB','');
		$pdf->Cell('32','6','','RB','');
		// 空单元格
		$pdf->Cell('33','6','','RB','');
		$pdf->ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('135','6','','0','');
		// Total
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('32','6','Total:','0','','C');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('33','6',$jsonarr['total_value'],'0','','C');
		$pdf->ln();
		// 输出PDF
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   FEDEX发票PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function fedexinvoice($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tks'].'_invoice.pdf';
		// 创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		// 字体Arial大小14加粗
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell('0','12','INVOICE','0','0','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		// 发件人公司
		$pdf->Cell('50','8','FROM','1','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['shipper']['CompanyName'],'TBR');
		$pdf->Ln();
		// 发件人地址
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['shipper']['StreetLines'],'LR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 发件人城市
		$pdf->Cell('200','8',$jsonarr['shipper']['City'],'LR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 发件人姓名
		$pdf->Cell('50','8',$jsonarr['shipper']['PersonName'],'LB','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['shipper']['PhoneNumber'],'BR');
		$pdf->Ln();
		// 末端单号
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','15',$jsonarr['tks'],'LBR','','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		// TO
		$pdf->Cell('50','8','TO','LRB','');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('150','8',$jsonarr['name'],'BR');
		$pdf->Ln();
		// 收件人地址
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('200','8',$jsonarr['address'],'LR','');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 收件人城市
		$pdf->Cell('200','8',$jsonarr['city'],'LR');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		// 收件人电话
		$pdf->Cell('200','8',$jsonarr['phone'],'LBR');
		$pdf->Ln();
		// simhei 
		$pdf->SetFont('simhei','B',10);
		// 件数
		$pdf->Cell('15','6',iconv("utf-8","gbk",'件数'),'LR','','C');
		$pdf->Cell('83','6',iconv("utf-8","gbk",'货品描述'),'R','','C');
		$pdf->Cell('27','6',iconv("utf-8","gbk",'数量(PCS)'),'R','','C');
		$pdf->Cell('25','6',iconv("utf-8","gbk",'单价(USD)'),'R','','C');
		// 总价(USD)
		$pdf->Cell('25','6',iconv("utf-8","gbk",'总价(USD)'),'R','','C');
		$pdf->Cell('25','6',iconv("utf-8","gbk",'总重量(KG)'),'R','','C');
		$pdf->Ln();
		// 字体 
		$pdf->SetFont('Arial','B',10);
		// Amount
		$pdf->Cell('15','6','Amount','BLR','','C');
		$pdf->Cell('83','6','Description of Goods','BR','','C');
		$pdf->Cell('27','6','Quantities','BR','','C');
		$pdf->Cell('25','6','Unit Price','BR','','C');
		// Total Amount
		$pdf->Cell('25','6','Total Amount','BR','','C');
		$pdf->Cell('25','6','Total Weight','BR','','C');
		$pdf->Ln();
		// simhei 
		$pdf->SetFont('simhei','B',10);
		// 收件方税号
		$pdf->Cell('200','15',iconv("utf-8","gbk",'收件方税号').'Import Tax  No.:'.$jsonarr['tax_payer_id'],'LBR','','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		foreach ($jsonarr['invoice']['items'] as $key=>$item){
			// -- 
			$descarr = Helper_Common::getdatasplit($item['description'],29);
			// 循环地址数组
			for($i=0;$i<count($descarr);$i++){
				// 地址第一行
				if($i==0){
					$pdf->Cell('15','8',$jsonarr['itemcount'],'LR','','C');
					// 描述
					// simhei
					$pdf->SetFont('simhei','',10);
					$pdf->Cell('83','8',iconv("utf-8","gbk",$descarr[$i]),'R','','C');
					$pdf->SetFont('Arial','',10);
					// --
					$pdf->Cell('27','8',$item['quantity'],'R','','C');
					// 申报单价
					// --
					$pdf->Cell('25','8',$item['price'],'R','','C');
					$pdf->Cell('25','8',$item['itotal'],'R','','C');
					$pdf->Cell('25','8',$jsonarr['weight'],'R','','C');
					// --
					$pdf->Ln();
				}else{
					// simhei
					$pdf->SetFont('simhei','',10);
					$pdf->Cell('15','8','','LR','','C');
					$pdf->Cell('83','8',iconv("utf-8","gbk",$descarr[$i]),'R','','C');
					// --
					$pdf->SetFont('Arial','',10);
					$pdf->Cell('27','8','','R','','C');
					$pdf->Cell('25','8','','R','','C');
					// --
					$pdf->Cell('25','8','','R','','C');
					$pdf->Cell('25','8','','R','','C');
					$pdf->Ln();
				}
			}
		// --
		}
		$pdf->Cell('15','6','','LT','');
		// --
		$pdf->Cell('83','6','','TR','');
		$pdf->Cell('27','6','','TR','');
		// 申报单价
		$pdf->Cell('25','6','','TR','');
		// --
		$pdf->Cell('25','6','','TR','');
		$pdf->Cell('25','6','','TR','');
		$pdf->ln();
		// 字体Arial大小10 
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell('98','6','TERMS OF SALE:DDU     MADE IN CHINA','LBR','');
		$pdf->Cell('27','6','','RB','');
		$pdf->Cell('25','6','','RB','');
		// 申报总价
		$pdf->Cell('25','6','','RB','');
		$pdf->Cell('25','6','','RB','');
		$pdf->ln();
		// --
		$pdf->SetFont('Arial','B',10);
		// 0
		$pdf->Cell('125','6','','0','');
		$pdf->Cell('25','6','Total:','0','','C');
		// --
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('25','6',$jsonarr['invoice']['total'],'0','','C');
		$pdf->Cell('25','6','','0','');
		// 换行
		$pdf->ln();
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   copy1 PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function upscopy1($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tks'].'_copy_1.pdf';
		// 获取已生成的条码图片
		$barcode=$dir.DS.$jsonarr['tks'].'.barcode.png';
		// 创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		// --
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		// 字体Arial大小18
		$pdf->SetFont('Arial','',18);
		$pdf->Cell('0','8','UPS COPY','0','0','C');
		$pdf->Ln();
		// 字体Arial大小10
		$pdf->SetFont('Arial','',10);
		// 设置文本颜色
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell('66','6','SHIPPER','1','','','1');
		// --
		$pdf->Cell('60','6','','1');
		// UPS WAYBILL/TRACKING NUMBER
		// -- 
		$pdf->Cell('74','6','UPS WAYBILL/TRACKING NUMBER','1','','','1');
		$pdf->Ln();
		$pdf->SetTextColor('black');
		// UPS Account Number
		$pdf->Cell('66','5','UPS Account Number: '.$jsonarr['shipper']['account'],'L');
		// 字体Arial大小12
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell('50','5',$jsonarr['service_name'],'L');
		$pdf->Cell('10','5',$jsonarr['service'],'R');
		// 末端单号
		// --
		$pdf->Cell('74','5',$jsonarr['tks'],'R');
		$pdf->Ln();
		// 字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('66','5','Tax ID/VAT No.:','L');
		// 设置文本颜色
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell('60','5','SHIPMENT INFORMATION','1','','','1');
		// --
		$pdf->Cell('74','5','UPS SHIPMENT ID','1','','','1');
		$pdf->Ln();
		// 设置文本颜色
		$pdf->SetTextColor('black');
		$pdf->Cell('66','5','Contact: '.$jsonarr['shipper']['aname'],'L');
		$pdf->Cell('60','5','Pkgs: '.$jsonarr['itemcount'],'L');
		// 导出高价数据时生成的shipid(UPS)
		// --
		$pdf->Cell('74','5',$jsonarr['shipmentid'],'LR');
		$pdf->Ln();
		// --
		// 发件人过长
		$namearr = Helper_Common::getdatasplit($jsonarr['shipper']['name'],28);
		// 循环发件人数组
		for($i=0;$i<count($namearr);$i++){
			if($i==0){
				// 字体Arial大小10
				$pdf->SetFont('Arial','',10);
				$pdf->Cell('66','5',$namearr[$i],'L');
				$pdf->Cell('60','5','Lg. Pkgs. 0','L');
				// 设置文本颜色
				$pdf->SetTextColor(255,255,255);
				// --
				$pdf->Cell('74','5','SPECIAL INTRUCTIONS','1','','','1');
				$pdf->SetTextColor('black');
				// 换行
				$pdf->Ln();
			}else{
				// 字体Arial大小10
				$pdf->SetFont('Arial','',10);
				$pdf->Cell('66','5',$namearr[$i],'L');
				$pdf->Cell('60','5','','L');
				// 空格
				// --
				$pdf->Cell('74','5','','LR');
				$pdf->Ln();
			}
		}
		// 发件人电话
		$pdf->Cell('66','5','Phone: '.$jsonarr['shipper']['phone'],'L');
		$pdf->Cell('60','5','Actual Wt '.round($jsonarr['weight'],2).' Kg','L');
		// --
		$pdf->Cell('74','5',$jsonarr['specialInstruction'],'LR');
		$pdf->Ln();
		// 地址字符串过长时 分为多行
		// 发件人地址过长
		$addressarr = Helper_Common::getdatasplit($jsonarr['shipper']['address'],29);
		// 循环地址数组
		for($i=0;$i<count($addressarr);$i++){
			// 地址第一行
			if($i==0){
				$pdf->Cell('66','5',$addressarr[$i],'L');
				$pdf->Cell('60','5','Billable Wt '.round($jsonarr['weight'],2).' Kg','L');
				// --
				$pdf->Cell('74','5','','LR');
				//换行
				$pdf->Ln();
			}else{
				//地址其他行
				$pdf->Cell('66','5',$addressarr[$i],'L');
				$pdf->Cell('60','5','','L');
				//--
				$pdf->Cell('74','5','','LR');
				//换行
				$pdf->Ln();
			}
		}
		//发件人邮编
		$pdf->Cell('66','5',$jsonarr['shipper']['postcode'],'L');
		$pdf->Cell('60','5',$jsonarr['documentOnly'],'L');
		//包裹数量大于5字体simhei,显示中文
		if($jsonarr['itemcount']>5){
			//simhei
			$pdf->SetFont('simhei','',10);
		}else{
			//字体Arial大小12
			$pdf->SetFont('Arial','B',12);
		}
		//---
		$pdf->Cell('74','5',$jsonarr['weight_table'],'LR','','C');
		//--
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Ln();
		$pdf->Cell('66','5','','L');
		$pdf->Cell('60','5','','L');
		//空格
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		$pdf->Cell('66','5',$jsonarr['shipper']['city'] ? iconv("utf-8","gbk",$jsonarr['shipper']['city']) : '','L');
		$pdf->Cell('60','5','Description of Goods:','L');
		//空格
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		$pdf->Cell('66','5','','L');
		$pdf->Cell('60','5','','L');
		//空格
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		$description_line1 = '';
		$description_line2 = '';
		//描述过长处理
		$description_words = explode(' ', $jsonarr['description']);
		foreach ($description_words as $word){
			if(strlen($description_line1.' '.$word) > 30){
				//字符长度超过30
				$description_line2 = strlen($description_line2)==0 ? $word : $description_line2.' '.$word;
			}else{
				//字符长度不超过30
				$description_line1 = strlen($description_line1)==0 ? $word : $description_line1.' '.$word;
			}
		}
		$pdf->Cell('66','5',"CHINA, PEOPLE'S REPUBLIC OF",'L');
		//字符长度不超过30
		$pdf->Cell('60','5',$description_line1,'L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		$pdf->Cell('66','5','','L');
		//字符长度超过30
		$pdf->Cell('60','5',$description_line2,'L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		//cn
		$pdf->Cell('66','5',"CN",'L');
		$pdf->Cell('60','5','','L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		//设置文本颜色
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell('66','5','SHIP TO','1','','','1');
		$pdf->Cell('60','5','','L');
		//--
		$pdf->Cell('74','5','','LR');
		//换行
		$pdf->Ln();
		$pdf->SetTextColor('black');
		$pdf->Cell('66','5','UPS Account Number: ','L');
		$pdf->Cell('60','5','Declared Value for Carriage:','L');
		//末端单号
		$pdf->Cell('60','5',$jsonarr['tks'],'L','','C');
		//--
		$pdf->Cell('14','5',$jsonarr['hv'],'R');
		$pdf->Ln();
		//税号
		$pdf->Cell('66','5','Tax ID/VAT No.:','L');
		$pdf->Cell('60','5','','L');
		$pdf->SetTextColor(255,255,255);
		//--
		$pdf->Cell('74','5','PAYMENT OF CHARGES','1','','','1');
		//换行
		$pdf->Ln();
		$pdf->SetTextColor('black');
		$pdf->Cell('66','5','Contact: '.$jsonarr['name'],'L');
		$pdf->Cell('60','5','Additional Handling:','L');
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		//--
		$pdf->Cell('74','5',$jsonarr['poc_line1'],'LR');
		$pdf->Ln();
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		//发件人姓名
		$pdf->Cell('66','5',$jsonarr['aname'],'L');
		$pdf->Cell('60','5','Residential: No','L');
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$poc_cn = explode("\n", $jsonarr['poc_line2']);
		//处理poc_line2
		for($i=0;$i<count($poc_cn);$i++){
			if($i==0){
				//Bill Transportation to Shipper
				//--
				$pdf->Cell('74','5',trim($poc_cn[$i]),'LR');
				$pdf->Ln();
			}else{
				//发件人地址
				$poc_cnarr = Helper_Common::getdatasplit($poc_cn[$i],40);
				for($j=0;$j<count($poc_cnarr);$j++){
					//空格
					$pdf->Cell('66','5','','L');
					$pdf->Cell('60','5','','L');
					//发件人支付运输费用
					//--
					$pdf->Cell('74','5',$poc_cnarr[$j],'LR');
					$pdf->Ln();
				}
			}
		}
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('66','5','Phone: '.$jsonarr['phone'],'L');
		$pdf->Cell('60','5','Reference 1:','L');
		//--
		$pdf->Cell('74','5','','LR');
		//换行
		$pdf->Ln();
		//发件人地址 
		$c_addressarr = Helper_Common::getdatasplit($jsonarr['address'],28);
		for($i=0;$i<count($c_addressarr);$i++){
			if ($i==0){
				$pdf->Cell('66','5',$c_addressarr[$i],'L');
				$pdf->Cell('60','5',$jsonarr['ref1'],'L');
				//--
				$pdf->Cell('74','5','','LR');
				$pdf->Ln();
			}else{
				$pdf->Cell('66','5',$c_addressarr[$i],'L');
				$pdf->Cell('60','5','','L');
				//--
				$pdf->Cell('74','5','','LR');
				$pdf->Ln();
			}
		}
		
		//Reference
		$pdf->Cell('66','5','','L');
		$pdf->Cell('60','5','Reference 2:','L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		//发件人邮编
		$pdf->Cell('66','5',$jsonarr['postcode'],'L');
		$pdf->Cell('60','5',$jsonarr['ref2'],'L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		//发件人州省
		$pdf->Cell('66','5',$jsonarr['state'].','.$jsonarr['city'],'L');
		$pdf->Cell('60','5','','L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		//发件人国家名
		$pdf->Cell('66','5',$jsonarr['countryname'],'L');
		$pdf->Cell('60','5','','L');
		//--
		$pdf->Cell('74','5','','LR');
		$pdf->Ln();
		//发件人国家二字码
		$pdf->Cell('66','5',$jsonarr['countrycode'],'L');
		$pdf->SetTextColor(255,255,255);
		//--
		$pdf->Cell('134','5','CARRIER USE','1','','','1');
		$pdf->Ln();
		//设置文本颜色
		$pdf->SetTextColor('black');
		$pdf->Cell('66','5','','L');
		$pdf->Cell('60','5','Received for UPS by:','1');
		$pdf->Cell('30','5','Date','1');
		//Time
		//--
		$pdf->Cell('44','5','Time','1');
		$pdf->Ln();
		$pdf->Cell('66','5','','L');
		$pdf->Cell('60','5','','1');
		//空格
		$pdf->Cell('30','5','','1');
		//--
		$pdf->Cell('44','5','','1');
		$pdf->Ln();
		$pdf->Cell('66','5','','L');
		//Amount Received
		$pdf->Cell('60','5','Amount Received:','L');
		$pdf->Cell('30','5','[ ] Cheque','');
		//--
		$pdf->Cell('44','5','[ ] Cash','R');
		$pdf->Ln();
		//No. of packages for which the Additional Handling charge applies
		$pdf->Cell('66','5','','L');
		//--
		$pdf->Cell('134','5','No. of packages for which the Additional Handling charge applies:','1');
		$pdf->Ln();
		$pdf->Cell('66','5','','LB');
		//Other Information:
		//--
		$pdf->Cell('134','5','Other Information:','1');
		$pdf->Ln();
		$pdf->Cell('66','5','....................................................................','');
		//Fold Here and Place in Pouch
		$pdf->Cell('60','5','Fold Here and Place in Pouch','','','C');
		//--
		$pdf->Cell('74','5','....................................................................','');
		$pdf->Ln();
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		//Tracking Numbers for additional packages in the shipment:
		$pdf->Cell('0','5','Tracking Numbers for additional packages in the shipment:','');
		$pdf->Ln();
		if(isset($jsonarr['subcode1']['info']) && is_array($jsonarr['subcode1']['info'])){
			foreach ($jsonarr['subcode1']['info'] as $subcode){
				//子单号
				$pdf->Cell('90','5',$subcode['subcode'],'');
				$pdf->Ln();
			}
		}
		//插入条形码
		$pdf->Image($barcode,'141','75','50','13');
		//电子章1
		if ($jsonarr['chapter1']){
			$pdf->Image($jsonarr['chapter1'],'141','200','50','40','gif');
		}
		//电子章2
		if ($jsonarr['chapter2']){
			$pdf->Image($jsonarr['chapter2'],'141','240','50','40','gif');
		}
		//生成pdf文件
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   copy2 PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function upscopy2($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tks'].'_copy_2.pdf';
		//创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		$pdf->Cell('100','6','Child Package Tracking Numbers (Continued):','');
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell('100','6','UPS Waybill No.','1','','','1');
		//换行
		$pdf->Ln();
		$pdf->SetTextColor('black');
		$pdf->Cell('100','6','','');
		//末端单号
		$pdf->Cell('100','6',$jsonarr['tks'],'1');
		$pdf->Ln();
		if(isset($jsonarr['subcode1']['info']) && is_array($jsonarr['subcode1']['info'])){
			foreach ($jsonarr['subcode1']['info'] as $subcode){
				//子单号
				$pdf->Cell('100','6',$subcode['subcode'],'');
				$pdf->Ln();
			}
		}
		//生成pdf文件
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
	/**
	 * @todo   runfeng copy联PDF版
	 * @author stt
	 * @since  2020-09-28
	 * @link   #81897
	 */
	static function runfengcopy($jsonarr){
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$filepath = $dir.DS.$jsonarr['tks'].'_copy_1.pdf';
		//创建pdf文件
		$pdf=new PDF_Chinese('P','mm','A4');
		$pdf->SetMargins(5, 5);
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		//字体Arial大小10
		$pdf->SetFont('Arial','',10);
		//插入条形码
		$pdf->Image($jsonarr['copy_label'],'50','15','117.9','176.9');
		//电子章1
		if ($jsonarr['chapter1']){
			$pdf->Image($jsonarr['chapter1'],'50','200','50','40','gif');
		}
		//电子章2
		if ($jsonarr['chapter2']){
			$pdf->Image($jsonarr['chapter2'],'130','200','50','40','gif');
		}
		//生成pdf文件
		$pdf->Output($filepath,'F');
		$pdf->Close();
	}
}