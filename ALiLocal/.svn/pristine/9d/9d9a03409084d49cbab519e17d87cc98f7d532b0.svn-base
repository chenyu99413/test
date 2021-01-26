<?php
class Controller_Channelcost extends Controller_Abstract {
	/**
	 */
	function actionIndex() {
	}
	/**
	 * 验算
	 */
	function actionCalResult(){
		$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
		if($order->isNewRecord()){
			echo '<h4 style="color:red">验算失败，单号不存在</h4>';
		}else{
			$channel=Channel::find('channel_id=?',request ( "channel_id" ))->getOne();
			$network=Network::find('network_code=?',$channel->network_code)->getOne();
			$channelcost = ChannelCost::find ( "product_id = ? and [channel_id] = ?", request ( "product_id" ), request ( "channel_id" ) )->getOne ();
			$channelcostppr= Channelcostppr::find('channel_cost_id =? and effective_time <=? and invalid_time>?',$channelcost->channel_cost_id,time(),time())->getOne();
			$quote=new Helper_Quote();
			$fee_quantity=Fee::find("order_id=? and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne()->quantity;
			if ($order->customer->customs_code=='ALCN'){
				$cainiaofee = new Helper_CainiaoFee();
				$r=$cainiaofee->payment($order,$channelcostppr,$network->network_id);
			}else{
				$r=$quote->payment($order,$channelcostppr,$network->network_id,$fee_quantity);
			}
			if(is_array($r)){
				foreach ($r['price_info']['fee_item'] as $key=>$value){
					if($value['fee']!=0){
						$currency = @$value['currency_code'] ? $value['currency_code'] :'CNY';
						if($currency != 'CNY'){
							//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$currency,time(),time())->getOne();
							$rate = CodeCurrencyItem::getCurrencyRate($currency,time(), $channel->supplier_id);
							if(!$rate){
								echo '<h4 style="color:red">验算失败，'.$currency.'币种不存在或已过期</h4>';
								exit;
							}
						}
					}
				}
				$str='<table class="FarTable" style="width:40%"><thead><tr><th>费用项名称</th><th>金额</th><th>币种</th></tr></thead><tbody>';
				$fee_total=0;
				bcscale(2);
				foreach ($r['price_info']['fee_item'] as $key=>$value){
					if($value['fee']!=0){
						$fee_item=FeeItem::find('sub_code=?',$key)->getOne();
						$str.='<tr><td>'.(($fee_item->item_name=='基础运费' || $fee_item->item_name=='偏远地区附加费')?$fee_item->item_name.'（含燃油）':$fee_item->item_name).'</td><td>'.$value['fee'].'</td><td>'.$value['currency_code'].'</td></tr>';
						if(@$value['currency_code'] && @$value['currency_code'] != 'CNY'){
							$value['fee'] = Helper_Quote::exchangeRate(time(),$value['fee'], $value['currency_code'],0,$channel->supplier_id);
						}
						$fee_total=bcadd($value['fee'],$fee_total);
					}
				}
				if($fee_total>0){
					$str.='<tr><th>总金额</th><th>'.sprintf("%.2f",$fee_total).'</th><th></th></tr>';
				}
				$str.='</tbody></table>';
				echo $str;
			}else{
				echo '<h4 style="color:red">验算失败，请检查公式正确性</h4>';
			}
		}
		// 		dump($r,1,11);
		exit;
	}
	/**
	 * 计算某一产品的多个渠道成本
	 */
	function actionCalChannelcost(){
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		if (request('cal_type')=='1'){
			$order=Order::find('ali_order_no=?',request('order_no'))->getOne();
			if($order->isNewRecord()){
				return $this->_redirectAjax ( false, '<span style="color:red">验算失败，单号不存在</span>' );
			}else{
				$channel=Channel::find('channel_id=?',request ( "channel_id" ))->getOne();
				$network=Network::find('network_code=?',$channel->network_code)->getOne();
				$channelcost = ChannelCost::find ( "product_id = ? and [channel_id] = ?", request ( "product_id" ), request ( "channel_id" ) )->getOne ();
				$channelcostppr= Channelcostppr::find('channel_cost_id =? and effective_time <=? and invalid_time>?',$channelcost->channel_cost_id,time(),time())->getOne();
				$quote=new Helper_Quote();
				$fee_quantity=Fee::find("order_id=? and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne()->quantity;
				if ($order->customer->customs_code=='ALCN'){
					$cainiaofee = new Helper_Cainiaofee();
					$r=$cainiaofee->payment($order,$channelcostppr,$network->network_id);
				}else{
					$r=$quote->payment($order,$channelcostppr,$network->network_id,$fee_quantity);
				}
				if(is_array($r)){
					$fee_total=0;
					if (@$r['price_info']){
						bcscale(2);
						foreach ($r['price_info']['fee_item'] as $key=>$value){
							if($value['fee']!=0){
								if(@$value['currency_code'] && @$value['currency_code'] != 'CNY'){
									//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$value['currency_code'],time(),time())->getOne();
									$rate = CodeCurrencyItem::getCurrencyRate($value['currency_code'],time(), $channel->supplier_id);
									if(!$rate){
										return $this->_redirectAjax ( false, '<span style="color:red">'.$value['currency_code'].'币种不存在或已过期</span>' );
									}
									$value['fee'] = Helper_Quote::exchangeRate(time(),$value['fee'], $value['currency_code'],0,$channel->supplier_id);
								}
								$fee_total=bcadd($value['fee'],$fee_total);
							}
						}
					}
					return $this->_redirectAjax ( true, '<span>'.sprintf("%.2f",$fee_total).'</span>' );
				}else{
					return $this->_redirectAjax ( false, '<span style="color:red">验算失败，请检查公式正确性</span>' );
				}
			}
		}else{
			$channel=Channel::find('channel_id=?',request ( "channel_id" ))->getOne();
			$network=Network::find('network_code=?',$channel->network_code)->getOne();
			$channelcost = ChannelCost::find ( "product_id = ? and [channel_id] = ?", request ( "product_id" ), request ( "channel_id" ) )->getOne ();
			$channelcostppr= Channelcostppr::find('channel_cost_id =? and effective_time <=? and invalid_time>?',$channelcost->channel_cost_id,time(),time())->getOne();
			$quote=new Helper_Quote();
			$caldata = array(
				'country_code' => request('country_code'),
				'city' => request('city'),
				'zip_code' => request('zip_code'),
				'weight' => request('weight'),
				'length_out' => request('length_out'),
				'width_out' => request('width_out'),
				'height_out' => request('height_out'),
				'packing_type' => request('packing_type'),
				'product_name' => request('product_name')
			);
			$r=$quote->calchannelpayment($caldata,$channelcostppr,$network->network_id);
			if(is_array($r)){
				$fee_total=0;
				if (@$r['price_info']){
					bcscale(2);
					foreach ($r['price_info']['fee_item'] as $key=>$value){
						if($value['fee']!=0){
							if(@$value['currency_code'] && @$value['currency_code'] != 'CNY'){
								//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$value['currency_code'],time(),time())->getOne();
								$rate = CodeCurrencyItem::getCurrencyRate($value['currency_code'],time(), $channel->supplier_id);
								if(!$rate){
									return $this->_redirectAjax ( false, '<span style="color:red">'.$value['currency_code'].'币种不存在或已过期</span>' );
								}
								$value['fee'] = Helper_Quote::exchangeRate(time(),$value['fee'], $value['currency_code'],0,$channel->supplier_id);
							}
							$fee_total=bcadd($value['fee'],$fee_total);
						}
					}
				}
				return $this->_redirectAjax ( true, '<span>'.sprintf("%.2f",$fee_total).'</span>' );
			}else{
				return $this->_redirectAjax ( false, '<span style="color:red">验算失败，请检查公式正确性</span>' );
			}
			
		}
	}
	/**
	 * 检索
	 */
	function actionSearch() {
		$product = Product::find ( "product_id = ?", request ( "id" ) )->getOne ();
		if ($product->isNewRecord ()) {
			return $this->_redirectMessage ( "产品信息", "产品ID错误,请重新选择产品", url ( "product/search" ) );
		}
		
		$this->_view ["tabs"] = Controller_Product::createTabs ( $product );
		$this->_view ["product"] = $product;
	}
	
	/**
	 * 编辑
	 */
	function actionEdit() {
		//产品
		$product = Product::find ( "product_id = ?", request ( "id" ) )->getOne ();
		if ($product->isNewRecord ()) {
			return $this->_redirectMessage ( "产品信息", "产品ID错误,请重新选择产品", url ( "product/search" ) );
		}
		
		//产品渠道
		$channelcost = ChannelCost::find ( "product_id = ? and [channel.channel_id] = ?", request ( "id" ), request ( "channel_id" ) )->getOne ();
		//渠道成本类型
		$channelcosttypebox = ChannelCosttype::find ( "channel_cost_id = ? and package_type = 'BOX' and customs_code=?", $channelcost->channel_cost_id, request ( "customs_code" ,'FARA00001'))->getOne ();
		$channelcosttypepak = ChannelCosttype::find ( "channel_cost_id = ? and package_type = 'PAK' and customs_code=?", $channelcost->channel_cost_id, request ( "customs_code" ,'FARA00001'))->getOne ();
		$channelcosttypedoc = ChannelCosttype::find ( "channel_cost_id = ? and package_type = 'DOC' and customs_code=?", $channelcost->channel_cost_id, request ( "customs_code" ,'FARA00001'))->getOne ();
		//保存
		if (request_is_post ()) {
			$conn = QDB::getConn ();
			$conn->startTrans ();
			
			//渠道成本
			if (request ( "channelcost" ) != null || strlen ( request ( "channelcost" ) ) > 0) {
				$channelcost->product_id = $product->product_id;
				$channelcost->changeProps ( request ( "channelcost" ) );
				$channelcost->save ();
			}
			//渠道成本类型
			if (strlen ( $channelcost->channel_cost_id ) > 0) {
				$channelcosttypebox->channel_cost_id = $channelcost->channel_cost_id;
				$channelcosttypebox->package_type = 'BOX';
				$channelcosttypebox->customs_code = request ( "customs_code" ,'FARA00001');
				$channelcosttypebox->save ();
				$channelcosttypepak->channel_cost_id = $channelcost->channel_cost_id;
				$channelcosttypepak->package_type = 'PAK';
				$channelcosttypepak->customs_code = request ( "customs_code" ,'FARA00001');
				$channelcosttypepak->save ();
				$channelcosttypedoc->channel_cost_id = $channelcost->channel_cost_id;
				$channelcosttypedoc->package_type = 'DOC';
				$channelcosttypedoc->customs_code = request ( "customs_code" ,'FARA00001');
				$channelcosttypedoc->save ();
			}
			//渠道折扣-价格-分区-偏派列表
			if (request ( "Channelcostppr" ) != null || strlen ( request ( "Channelcostppr" ) ) > 0) {
				Channelcostppr::meta ()->destroyWhere ( "channel_cost_id = ?", $channelcost->channel_cost_id);
				foreach ( json_decode ( request ( "Channelcostppr" ) ) as $value ) {
					$value->effective_time=strtotime($value->effective_time."00:00:00");
					$value->invalid_time=strtotime($value->invalid_time."23:59:59");
					$Channelcostppr = new Channelcostppr ();
					$Channelcostppr->changeProps ( $value );
					$Channelcostppr->channel_cost_id=$channelcost->channel_cost_id;
					$Channelcostppr->save ();
				}
			}
			
			$conn->completeTrans ();
			return $this->_redirectMessage ( "渠道成本", "保存成功", url ( "channelcost/edit", array (
				"id" => $channelcost->product_id,
				"channel_id" => $channelcost->channel_id,
				"type" => request ( "type", "BOX" )
			) ) );
		}
		
		$this->_view ["tabs"] = Controller_Product::createTabs ( $product );
		$this->_view ["tabs_type"] = $this->createTabsType ( $product, $channelcost );
		$this->_view ["tabs_customer"] = $this->createTabsCustomerId ( $product, $channelcost );
		//产品
		$this->_view ["product"] = $product;
		//渠道成本
		$this->_view ["channelcost"] = $channelcost;
		//渠道列表
		$select = Channel::find ();
		$channel_ids = Helper_Array::getCols ( ChannelCost::find ( "product_id = ? and channel_id != ?", request ( "id" ), request ( "channel_id", "" ) )->getAll (), "channel_id" );
		if (! empty ( $channel_ids )) {
			$select->where ( "channel_id not in (?)", $channel_ids );
		}
		$this->_view ["channelCostType"] = request('type','BOX')=='BOX'?$channelcosttypebox:(request('type','BOX')=='PAK'?$channelcosttypepak:$channelcosttypedoc);
		$this->_view ["channel"] = $select->getAll ();
	}
	/**
	 * @todo   创建标签 客户
	 * @author stt
	 * @since  August 18th 2020
	 * @return 标签列
	 */
	function createTabsCustomerId( $product, $channelcost ) {
		$result = array ();
		$customers = Customer::find()->getAll();
		foreach ($customers as $cus){
			$result [] = array (
				"id" => $cus->customs_code,
				"title" => $cus->customer,
				"href" => url ( "channelcost/edit", array (
					"id" => $product->product_id,
					"channel_id" => $channelcost->channel_id,
					"customs_code" => $cus->customs_code
				) )
			);
		}
		return $result;
	}
	
	/**
	 * 删除
	 */
	function actionDelete() {
		//产品
		$product = Product::find ( "product_id = ?", request ( "id" ) )->getOne ();
		if ($product->isNewRecord ()) {
			return $this->_redirectMessage ( "产品信息", "产品ID错误,请重新选择产品", url ( "product/search" ) );
		}
		
		//产品渠道
		$channelcost = ChannelCost::find ( "product_id = ? and [channel.channel_id] = ?", request ( "id" ), request ( "channel_id" ) )->getOne ();
		if ($channelcost->isNewRecord ()) {
			return $this->_redirectMessage ( "产品渠道", "产品渠道错误,请重新选择渠道", url ( "channelcost/search", array (
				"id" => $product->product_id
			) ) );
		}
		
		$channelcost->destroy ();
		
		return $this->_redirectMessage ( "产品渠道", "删除成功", url ( "channelcost/search", array (
			"id" => $product->product_id
		) ) );
	}
	/**
	 * 创建标签 包装类型
	 *
	 * @param 产品 $product
	 * @return 标签列
	 */
	function createTabsType($product, $channelcost) {
		$customs_code = request ( "customs_code", "FARA00001" );
		return array (
			array (
				"id" => "BOX",
				"title" => "BOX",
				"href" => url ( "channelcost/edit", array (
					"id" => $product->product_id,
					"channel_id" => $channelcost->channel_id,
					"type" => "BOX",
					"customs_code" => $customs_code
				) )
			),
			array (
				"id" => "PAK",
				"title" => "PAK",
				"href" => url ( "channelcost/edit", array (
					"id" => $product->product_id,
					"channel_id" => $channelcost->channel_id,
					"type" => "PAK",
					"customs_code" => $customs_code
				) )
			),
			array (
				"id" => "DOC",
				"title" => "DOC",
				"href" => url ( "channelcost/edit", array (
					"id" => $product->product_id,
					"channel_id" => $channelcost->channel_id,
					"type" => "DOC",
					"customs_code" => $customs_code
				) )
			)
		);
	}
	/**
	 * 费用公式保存
	 */
	function actionsaveoperate(){
		//保存
		if (request_is_post ()) {
			//操作费
			if (request ( "formula" ) != null || strlen ( request ( "formula" ) ) > 0) {
				$value=json_decode ( request ( "formula" ),true);
				$formula =  ChannelCostformula::find("channel_cost_formula_id=?",$value[0]["channel_cost_formula_id"])->getOne();
				if (request ( "delete_flag" ) == "true") {
					if (! $formula->isNewRecord ()) {
						$formula->destroy ();
					}
				}else{
					$channelcosttype = ChannelCosttype::find('type_id=?',$value[0]['type_id'])->getOne();
					if ($channelcosttype->isNewRecord()){
						$channelcosttype = new ChannelCosttype();
						$channelcosttype->channel_cost_id = $value[0]['channel_cost_id'];
						$channelcosttype->package_type = $value[0]['package_type'];
						$channelcosttype->customs_code = $value[0]['customs_code'];
						$channelcosttype->save();
					}
					//编辑或新增的生效日期
					$start_time = strtotime($value[0]['effective_time']."00:00:00");
					//编辑或新增的失效日期
					$end_time = strtotime($value[0]['fail_time']."23:59:59");
					//生效日期大于失效日期报错
					if ($start_time>$end_time){
						echo 'timeerror';
						exit;
					}
					//除编辑数据本身之外的公式
					$old_formula = ChannelCostformula::find('fee_name=? and type_id=? and channel_cost_formula_id!=?',$value[0]['fee_name'],$channelcosttype->type_id,$value[0]['channel_cost_formula_id'])
					//查找是否有时间段重叠的数据
					->where('(effective_time>=? and effective_time<=?) or (effective_time<=? and fail_time>=?) or (fail_time>=? and fail_time<=?)',$start_time,$end_time,$start_time,$end_time,$start_time,$end_time)
					->getOne();
					//有时间段重叠,报错
					if (!$old_formula->isNewRecord()){
						echo 'formularepeat';
						exit;
					}
					$value[0]['effective_time']=strtotime($value[0]['effective_time']."00:00:00");
					$value[0]['fail_time']=strtotime($value[0]['fail_time']."23:59:59");
					$supplier = Supplier::find('supplier = ?',$value[0]['supplier'])->getOne();
					$formula->changeProps ( $value[0] );
					$formula->supplier_id = $supplier->supplier_id;
					$formula->type_id = $channelcosttype->type_id;
					$formula->save ();
				}
				echo ($formula->channel_cost_formula_id);
			}
			exit;
		}
	}
	/**
	 * @todo   公式操作费管理 导出
	 * @author stt
	 * @since  2020-11-16
	 * @param
	 * @return json
	 * @link   #83417
	 */
	function actionExport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		//产品渠道
		$channelcost = ChannelCost::find ( "product_id = ? and [channel.channel_id] = ?", request ( "id" ), request ( "channel_id" ) )->getOne ();
		//渠道成本类型
		$channelcosttypes = ChannelCosttype::find ( "channel_cost_id = ? and customs_code=?", $channelcost->channel_cost_id, request ( "customs_code" ,'FARA00001'))->getAll();
		$customer = Customer::find('customs_code=?',request ( "customs_code" ,'FARA00001'))->getOne();
		//导出字段
		$header = array (
			'费用名称',
			'公式',
			'备注',
			'自动',
			'生效日期',
			'失效日期',
			'币种',
			'供应商',
			'包裹类型',
			'客户'
		);
		$sheet = array (
			$header
		);
		foreach ($channelcosttypes as $channelcosttype){
			foreach ($channelcosttype->channelcostformula as $value){
				if ($value->fail_time>time()){
					//供应商名称
					$supplier = Supplier::find('supplier_id=?',$value->supplier_id)->getOne();
					$row =array(
						//费用名称
						$value->fee_name,
						//公式
						$value->formula,
						//备注
						$value->remark,
						//自动
						$value->calculation_flag==1?'是':'否',
						//生效日期
						date('Y-m-d',$value->effective_time),
						//失效日期
						date('Y-m-d',$value->fail_time),
						//币种
						$value->currency_code,
						//供应商
						$supplier->supplier,
						//包裹类型
						$channelcosttype->package_type,
						//客户
						$customer->customer
					);
					$sheet [] = $row;
				}
			}
		}
		//导出
		Helper_Excel::array2xls ( $sheet, '杂费项导出.xlsx' );
		exit ();
	}
	/**
	 * @todo   杂费项管理 导入
	 * @author stt
	 * @since  2020-11-18
	 * @param
	 * @return json
	 * @link   #83417
	 */
	function actionImport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		//上传文件开始
		$uploader = new Helper_Uploader();
		//检查指定名字的上传对象是否存在
		if (! $uploader->existsFile ( 'file' )) {
			return $this->_redirectMessage('未上传文件','',url( 'channelcost/edit' ,array('id'=>request('id_import'),'channel_id'=>request('channel_id_import'))), 3 );
		}
		//获得文件对象
		$file = $uploader->file ( 'file' );
		//文件格式
		if (! $file->isValid ( 'xls,xlsx' )) {
			return $this->_redirectMessage('文件格式不正确：xls,xlsx','',url( 'channelcost/edit' ,array('id'=>request('id_import'),'channel_id'=>request('channel_id_import'))), 3 );
		}
		//缓存路径
		$des_dir = Q::ini ( 'upload_tmp_dir' );
		$filename = $des_dir.DS.date ( 'YmdHis' ).'channelcostformulaimport.'.$file->extname ();
		$file->move ( $filename );
		$xls = Helper_Excel::readFile ( $filename,true);
		$sheets =$xls->toHeaderMap ();
		//必填字段
		$required_fields = array (
			'费用名称',
			'公式',
			'备注',
			'自动',
			'生效日期',
			'失效日期',
			'币种',
			'供应商',
			'包裹类型'
		);
		//缺少字段:费用名称、公式、备注、自动、生效日期、失效日期、币种、供应商、包裹类型
		if(!isset($sheets[0]['费用名称']) || !isset($sheets[0]['公式'])|| !isset($sheets[0]['备注'])|| !isset($sheets[0]['自动'])|| !isset($sheets[0]['生效日期'])|| !isset($sheets[0]['失效日期'])|| !isset($sheets[0]['币种'])|| !isset($sheets[0]['供应商']) || !isset($sheets[0]['包裹类型'])){
			return $this->_redirectMessage('缺少字段','缺少字段:费用名称、公式、备注、自动、生效日期、失效日期、币种、供应商、包裹类型',url( 'channelcost/edit' ,array('id'=>request('id_import'),'channel_id'=>request('channel_id_import'))), 10 );
		}
		//不能为空
		foreach($sheets as $k=>$row){
			foreach ( $required_fields as $col ) {
				$row [$col] = trim ( $row [$col] );
				if (strlen ( $row [$col] ) == 0) {
					//跳转报错
					return $this->_redirectMessage('不能为空','第'.($k+2).'行'.$col.'不能为空',url( 'channelcost/edit' ,array('id'=>request('id_import'),'channel_id'=>request('channel_id_import'))), 10 );
				}
			}
			if(!in_array($row['包裹类型'],array('BOX','PAK','DOC'))){
				//跳转报错
				return $this->_redirectMessage('包裹类型不存在','第'.($k+2).'行，包裹类型不存在，包裹类型应填BOX、PAK、DOC',url( 'channelcost/edit' ,array('id'=>request('id_import'),'channel_id'=>request('channel_id_import'))), 10 );
			}
		}
		//产品渠道
		$channelcost = ChannelCost::find ( "product_id = ? and channel_id = ?", request ( "id_import" ), request ( "channel_id_import" ) )->getOne ();
		//渠道成本类型
		$customers = Customer::find()->getAll();
		foreach ($customers as $customer){
			foreach ($sheets as $sheet){
				//导入的生效日期
				$start_time = strtotime($sheet['生效日期']."00:00:00");
				//导入的失效日期
				$end_time = strtotime($sheet['失效日期']."23:59:59");
				//生效日期大于失效日期跳过
				if ($start_time>$end_time){
					continue;
				}
				//渠道成本->客户->包裹类型
				$channelcosttype = ChannelCosttype::find('package_type=? and channel_cost_id=? and customs_code=?',$sheet['包裹类型'],$channelcost->channel_cost_id,$customer->customs_code)->getOne();
				//渠道成本->客户->包裹类型是否存在
				if ($channelcosttype->isNewRecord()){
					$channelcosttype = new ChannelCosttype();
					$channelcosttype->package_type = $sheet['包裹类型'];
					//渠道成本ID
					$channelcosttype->channel_cost_id = $channelcost->channel_cost_id;
					//客户代码
					$channelcosttype->customs_code = $customer->customs_code;
					$channelcosttype->save();
				}
				//应付公式，费用项是否已存在
				$old_formula = ChannelCostformula::find('fee_name=? and type_id=?',$sheet['费用名称'],$channelcosttype->type_id)
				//查找是否有时间段重叠的数据
				->where('(effective_time>=? and effective_time<=?) or (effective_time<=? and fail_time>=?) or (fail_time>=? and fail_time<=?)',$start_time,$end_time,$start_time,$end_time,$start_time,$end_time)
				->getOne();
				//有时间段重叠的数据
				if (!$old_formula->isNewRecord()){
					continue;
				}
				$supplier = Supplier::find('supplier=?',trim($sheet['供应商']))->getOne();
				$formula = new ChannelCostformula();
				$formula->type_id = $channelcosttype->type_id;
				$formula->supplier_id = $supplier->supplier_id;
				$formula->fee_name = $sheet['费用名称'];
				$formula->formula = $sheet['公式'];
				$formula->remark = $sheet['备注'];
				$formula->calculation_flag = $sheet['自动']=='是'?'1':'';
				$formula->effective_time = strtotime($sheet['生效日期']."00:00:00");
				$formula->fail_time = strtotime($sheet['失效日期']."23:59:59");
				$formula->currency_code = $sheet['币种'];
				$formula->save();
			}
		}
		//导入成功
		return $this->_redirectMessage ( '导入成功', '成功', url ( 'channelcost/edit' ,array('id'=>request('id_import'),'channel_id'=>request('channel_id_import'))),3);
		exit ();
	}
}