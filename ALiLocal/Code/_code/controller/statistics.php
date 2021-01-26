<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';

class Controller_Statistics extends Controller_Abstract {
    /**
     * 应收统计
     */
    function actionReceivable(){
        $fee_select = Fee::find ('fee_type = "1"');
        $fee_select->joinLeft ( 'tb_order', '*', 'tb_fee.order_id = tb_order.order_id' );
        $fee_select = $this->paymentOrderSelect ( $fee_select );
        $customer_ids = $fee_select->distinct ()
        ->setColumns ( 'btype_id' )
        ->order ( 'btype_id' )
        ->getAll ()
        ->getCols ( 'btype_id' );
        $filters = array ();
        foreach ( $customer_ids as $customer_id ) {
            $customer=Customer::find('customer_id = ?',$customer_id)->getOne();
            $filters [] = array (
                'filter_id' => $customer_id ?: '无',
                'filter_name' => $customer->customer ?: '无'
            );
        }
        $this->_view ['filters'] = $filters;
    }
    function actionReceivableDetail(){
        $page = intval ( request ( 'page', 1 ) );
        $page_size = intval ( request ( 'page_size', 30 ) );
        $pagination = null;
        $fee_select = Fee::find ('fee_type = "1"');
        $fee_select->joinLeft('tb_order','tb_order.far_no,tb_order.service_code,tb_order.ali_order_no,tb_order.tracking_no,tb_order.customer_id,tb_order.warehouse_out_time,tb_order.create_time as order_create_time','tb_fee.order_id = tb_order.order_id');
        $fee_select = $this->paymentOrderSelect ( $fee_select );
        if (request ( 'filter_id' )) {
            if(request ( 'filter_id' ) == '全部'){
            }elseif (request ( 'filter_id' ) == '无') {
                $fee_select->where ( 'ifnull(btype_id,0)=0' );
            } else {
                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
            }
        }
        $sum_total_str = '';
        $sum_order = clone $fee_select;
        $sum_total = $sum_order->group('tb_fee.currency')->sum('tb_fee.amount','amount')->columns('tb_fee.currency')->getAll();
        if(count($sum_total)>0){
        	foreach ($sum_total as $sum){
        		$sum_total_str .= $sum['currency'].':'.sprintf('%.2f',$sum['amount']).';';
        	}
        }
        $this->_view ['sum_total']=$sum_total_str;
        $fees = $fee_select->limitPage ( $page, $page_size )
        ->fetchPagination ( $pagination )
        ->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
        ->asArray ()
        ->getAll ();
        
        $this->_view ['fees'] = $fees;
        $this->_view ['pagination'] = $pagination;
        
        $fee_item = Helper_Array::toHashmap ( FeeItem::find ()->setColumns ( 'sub_code,item_name,item_code' )
            ->asArray ()
            ->getAll (), 'sub_code' );
        $this->_view ['fee_item'] = $fee_item;
        $customer=Customer::find()->setColumns ( 'customer_id,customer' )
        ->getAll ()
        ->toHashMap ( 'customer_id', 'customer' );
        $this->_view ['customer'] = $customer;
        $product = Product::find()->setColumns( 'product_name,product_chinese_name' )
        ->getAll()->toHashMap( 'product_name','product_chinese_name' );
        $this->_view ['product'] = $product;
    }
    function actionReceivableTotal(){
        set_time_limit(0);//不限制超时时间
        ini_set('memory_limit', '-1');//不限制内存
        $page = intval ( request ( 'page', 1 ) );
        $page_size = intval ( request ( 'page_size', 30 ) );
        $pagination = null;
        $fee_select = Fee::find('fee_type = "1"');
        $fee_select->joinLeft('tb_order','','tb_fee.order_id = tb_order.order_id');
        $fee_select = $this->paymentOrderSelect ( $fee_select );
        if (request ( 'filter_id' )) {
            if(request ( 'filter_id' ) == '全部'){
            }elseif (request ( 'filter_id' ) == '无') {
                $fee_select->where ( 'ifnull(btype_id,0)=0' );
            } else {
                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
            }
        }
        $sum_order = clone $fee_select;
        $sum_order_id = $sum_order->setColumns('order_id')->getAll()->getCols('order_id');
        $sum_total=0;
        $sum_total_str = '';
        array_filter($sum_order_id);
        if(count($sum_order_id)>0){
        	if(request('timetype')=='3'){
        		$sum_total = Fee::find('order_id in (?) and account_date >= ? and account_date <=? and fee_type = "1"',$sum_order_id,strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( 'end_date', date ( 'Y-m-d' ) ) . ' 23:59:59' ))->group('currency')->sum('amount','amount')->columns('currency')->getAll();
        	}else{
        		$sum_total = Fee::find('order_id in (?) and fee_type = "1"',$sum_order_id)->group('currency')->sum('amount','amount')->columns('currency')->getAll();
        	}
        	$order = Order::find('order_id in (?)',$sum_order_id)->limitPage ( $page, $page_size )
        	->fetchPagination ( $pagination )->asArray()->getAll();
        }
        if (count ( $sum_total ) > 0) {
			foreach ( $sum_total as $sum ) {
				$sum_total_str .= $sum ['currency'] . ':' . sprintf('%.2f',$sum ['amount']) . ';';
			}
		}
        $customer = Customer::find()->setColumns('customer_id,customer')
        ->getAll()->toHashMap('customer_id','customer');
        $this->_view ['customer'] = $customer;
        $department = Department::find()->setColumns('department_id,department_name')
        ->getAll()->toHashMap('department_id','department_name');
        $this->_view ['department'] = $department;
        $product = Product::find()->setColumns('product_name,product_chinese_name')
        ->getAll()->toHashMap('product_name','product_chinese_name');
        $this->_view ['product'] = $product;
        $this->_view ['sum_total'] = $sum_total_str;
        $this->_view ['order'] = @$order;
        $this->_view ['pagination'] = $pagination;
    }
    
	/**
	 * 应付统计
	 */
	function actionPayment() {
			$fee_select = Fee::find ("fee_type = '2'");
			$fee_select->joinLeft ( 'tb_order', '*', 'tb_fee.order_id = tb_order.order_id' );
			$fee_select->joinLeft('tb_channel','tb_channel.supplier_id','tb_channel.channel_id=tb_order.channel_id');
			$fee_select = $this->paymentOrderSelect ( $fee_select );

			$supplier_ids = $fee_select->distinct ()
				->setColumns ( 'btype_id' )
				->order ( 'btype_id' )->asArray()
				->getAll ();
			$filters = array ();
			foreach ( $supplier_ids as $supplier_id ) {
			    $supplier=Supplier::find('supplier_id = ?',$supplier_id['btype_id'])->getOne();
				$filters [] = array (
					'filter_id' => $supplier_id['btype_id'] ?: '无',
					'filter_name' => $supplier->supplier ?: '无'
				);
			}
		$this->_view ['filters'] = $filters;
	}
	function paymentOrderSelect(QDB_Select $order_select) {
		if (request ( 'timetype', '1' ) == '1') {
			$order_select->where ( "tb_order.warehouse_out_time >= ?", strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01' ) . ' 00:00:00' ) );
			$order_select->where ( "tb_order.warehouse_out_time <= ?", strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ) );
		} elseif (request ( 'timetype', '1' ) == '2') {
			$order_select->where ( "tb_order.create_time >= ?", strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01' ) . ' 00:00:00' ) );
			$order_select->where ( "tb_order.create_time <= ?", strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ) );
		} elseif (request ( 'timetype', '1' ) == '3') {
			$order_select->where ( "tb_fee.account_date >= ?", strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01' ) . ' 00:00:00' ) );
			$order_select->where ( "tb_fee.account_date <= ?", strtotime ( request ( 'end_date', date ( 'Y-m-d' ) ) . ' 23:59:59' ) );
		} elseif (request ( 'timetype', '1' ) == '4') {
			$order_select->where ( "tb_order.warehouse_confirm_time >= ?", strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01' ) . ' 00:00:00' ) );
			$order_select->where ( "tb_order.warehouse_confirm_time <= ?", strtotime ( request ( 'end_date', date ( 'Y-m-d' ) ) . ' 23:59:59' ) );
		}
		if (request ( 'ali_order_no' )) {
			$ali_order_no = explode ( "\r\n", request ( 'ali_order_no' ) );
			$ali_order_no = array_filter ( $ali_order_no ); //去空
			$ali_order_no = array_unique ( $ali_order_no ); //去重
			$order_select->where ( 'tb_order.ali_order_no in (?)', $ali_order_no );
		}
		if (request ( 'tracking_no' )) {
			$tracking_no = explode ( "\r\n", request ( 'tracking_no' ) );
			$tracking_no = array_filter ( $tracking_no ); //去空
			$tracking_no = array_unique ( $tracking_no ); //去重
			$order_select->where ( 'tb_order.tracking_no in (?)', $tracking_no );
		}
		if (request ( 'bill_no' )) {
			$bill_no = explode ( "\r\n", request ( 'bill_no' ) );
			$bill_no = array_filter ( $bill_no ); //去空
			$bill_no = array_unique ( $bill_no ); //去重
			$order_select->where ( 'tb_fee.bill_no in (?)', $bill_no );
		}
		if (request ( 'customer_id' )) {
			$order_select->where ( 'tb_fee.btype_id = ?', request ( 'customer_id' ) );
		}
		
		if (request ( 'order_status' )) {
			$order_select->where ( 'tb_order.order_status = ?', request ( 'order_status' ) );
		}
		
		if (request ( 'service_code' )) {
			$order_select->where ( 'tb_order.service_code = ?', request ( 'service_code' ) );
		}
		if (request ( 'supplier_id' )) {
			$order_select->where ( 'tb_fee.btype_id = ?', request ( 'supplier_id' ) );
		}
		if (request ( 'channel_id' )) {
			$order_select->where ( 'tb_order.channel_id = ?', request ( 'channel_id' ) );
		}
		if (request ( 'status', '0' ) == 0) {
			$order_select->where ( "(tb_fee.voucher_no = '' OR tb_fee.voucher_no IS NULL)" );
		} else if (request ( 'status', '0' ) == 1) {
			$order_select->where ( "(tb_fee.voucher_no != '' AND tb_fee.voucher_no IS NOT NULL)" );
		}
		if (request ( "currency" )) {
			$order_select->where ( 'tb_fee.currency = ?', request ( 'currency' ) );
		}
		return $order_select;
	}
	/**
	 * Payment Detail
	 */
	function actionPaymentDetail() {
	    $page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;

		$fee_select = Fee::find ("fee_type = '2'");
		$fee_select->joinLeft('tb_order','tb_order.department_id,tb_order.far_no,tb_order.service_code,tb_order.ali_order_no,tb_order.tracking_no,tb_order.customer_id,tb_order.warehouse_out_time,tb_order.create_time as order_create_time','tb_order.order_id = tb_fee.order_id');
		$fee_select->joinLeft ( 'tb_channel', 'tb_channel.supplier_id', 'tb_channel.channel_id = tb_order.channel_id' );
		$fee_select = $this->paymentOrderSelect ( $fee_select );
		if (request ( 'filter_id' )) {
			if(request ( 'filter_id' ) == '全部'){
				
			}elseif (request ( 'filter_id' ) == '无') {
				$fee_select->where ( 'ifnull(btype_id,0)=0' );
			} else {
				$fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
			}
		}
        $sum_order = clone $fee_select;
        $sum_total_str = '';
        $sum_total = $sum_order->group('tb_fee.currency')->sum('tb_fee.amount','amount')->columns('currency')->getAll();
        if(count($sum_total)>0){
        	foreach ($sum_total as $sum){
        		$sum_total_str .= $sum['currency'].':'.sprintf('%.2f',$sum['amount']).';';
        	}
        }
		$fees = $fee_select->limitPage ( $page, $page_size )
			->fetchPagination ( $pagination )
			->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
			->asArray ()
			->getAll ();

		$this->_view ['fees'] = $fees;
		$this->_view ['pagination'] = $pagination;
		$this->_view ['sum_total'] = $sum_total_str;

		$fee_item = Helper_Array::toHashmap ( FeeItem::find ()->setColumns ( 'sub_code,item_name,item_code' )
			->asArray ()
			->getAll (), 'sub_code' );
		$this->_view ['fee_item'] = $fee_item;
		$channel = Channel::find ()->setColumns ( 'channel_id,channel_name' )
			->getAll ()
			->toHashMap ( 'channel_id', 'channel_name' );
		$this->_view ['channel'] = $channel;
		$supplier = Supplier::find()->setColumns('supplier_id,supplier')
		      ->getAll()->toHashMap('supplier_id','supplier');
		$this->_view ['supplier'] = $supplier;
	}
	/**
	 * Payment Total
	 */
	function actionPaymentTotal() {
	    set_time_limit(0);//不限制超时时间
	    ini_set('memory_limit', '-1');//不限制内存
	    $page = intval ( request ( 'page', 1 ) );
	    $page_size = intval ( request ( 'page_size', 30 ) );
	    $pagination = null;
        $fee_select = Fee::find("fee_type = '2'")->joinLeft('tb_order','','tb_fee.order_id = tb_order.order_id');
        $fee_select->joinLeft('tb_channel','','tb_channel.channel_id = tb_order.channel_id');
        $fee_select = $this->paymentOrderSelect ( $fee_select );
        if (request ( 'filter_id' )) {
            if(request ( 'filter_id' ) == '全部'){
            }elseif (request ( 'filter_id' ) == '无') {
                $fee_select->where ( 'ifnull(btype_id,0)=0' );
            } else {
                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
            }
        }
        $sum_order = clone $fee_select;
        $sum_order_id = $sum_order->setColumns('order_id')->getAll()->getCols('order_id');
        $sum_total=0;
        $sum_total_str = '';
        array_filter($sum_order_id);
        if(count($sum_order_id)>0){
        	if(request('timetype')=='3'){
        		$sum_total = Fee::find('order_id in (?) and account_date >= ? and account_date <=? and fee_type = "2"',$sum_order_id,strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( 'end_date', date ( 'Y-m-d' ) ) . ' 23:59:59' ))->group('currency')->sum('amount','amount')->columns('currency')->getAll();
        	}else{
        		$sum_total = Fee::find('order_id in (?) and fee_type = "2"',$sum_order_id)->group('currency')->sum('amount','amount')->columns('currency')->getAll();
        	}
        	$total = Order::find('order_id in (?)',$sum_order_id)->limitPage ( $page, $page_size )
        	->fetchPagination ( $pagination )->asArray()->getAll();
        }
        if(count($sum_total)>0){
        	foreach ($sum_total as $sum){
        		$sum_total_str .= $sum['currency'].':'.sprintf('%.2f',$sum['amount']).';';
        	}
        }
        $this->_view ['sum_total'] = $sum_total_str;
        $this->_view ['total'] = @$total;
        $this->_view ['pagination'] = $pagination;
		$channel = Channel::find ()->setColumns ( 'channel_id,channel_name' )
			->getAll ()
			->toHashMap ( 'channel_id', 'channel_name' );
		$this->_view ['channel'] = $channel;
		$supplier = Supplier::find()->setColumns('supplier_id,supplier')
		      ->getAll()->toHashMap('supplier_id','supplier');
		$this->_view ['supplier'] = $supplier;
    }
	/**
	* @todo 应付统计导出
	* @author 吴开龙
	* @since May 10th 2020
	* @return file
	* @link #79779
	 */
	function actionPaymentexport(){
	    set_time_limit(0);
	    ini_set('memory_limit', '-1');
	    //链表查询Fee
	    $fee_select = Fee::find ("fee_type = '2'")
	    ->joinLeft( 'tb_order', 'tb_order.total_volumn_weight,tb_order.total_out_volumn_weight,tb_order.package_total_in,tb_order.weight_cost_out,tb_order.weight_actual_out,tb_order.weight_income_in,tb_order.weight_actual_in,tb_order.consignee_country_code,tb_order.packing_type,tb_order.channel_id,tb_order.department_id,tb_order.far_no,tb_order.service_code,tb_order.ali_order_no,tb_order.tracking_no,tb_order.customer_id,tb_order.warehouse_out_time,tb_order.delivery_time,tb_order.create_time as order_create_time', 'tb_fee.order_id = tb_order.order_id' );
	    //根据列表搜索条件来查询，paymentOrderSelect列表页面的条件封装
	    $fee_select = $this->paymentOrderSelect ( $fee_select );
	    //链表tb_channel
	    //$fee_select->joinLeft('tb_channel','tb_channel.supplier_id','tb_channel.channel_id = tb_order.channel_id');
	    //filter_id条件判断
	    if (request ( 'filter_id' )) {
            if (request ( 'filter_id' ) == '无') {
                $fee_select->where ( 'ifnull(btype_id,0)=0' );
            }elseif (request ( 'filter_id' ) == '全部'){
            	
            } else {
                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
            }
	    }
	    //排序，以数组形式取出
	    $fee_select=$fee_select->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
	    ->asArray()->getAll ();
	    //创建一个excel空文件，文件名 应付统计
	    Helper_ExcelX::startWriter2 ( 'paymentexport'  );
	    $header = array (
	        'ID','订单时间','阿里订单号','泛远单号','跟踪单号','出库时间','仓库','供应商','费用类型','分类','产品','费用代码','费用名称','数量','单价','金额','币种','汇率','本位币金额','凭证号','发票号','账单抬头','销账时间','开票时间','登账日期','包裹类型','包裹件数','目的国','收货实重','收货体积重','收货计费重量','出货实重','出货体积重','出货计费重量','合同天数','签收时间'
	    );
	    $channel=Channel::find()->setColumns('channel_id,channel_name')->asArray()->getAll();
	    $channel = Helper_Array::toHashmap ( $channel, 'channel_id' );
	    $supplier = Supplier::find()->setColumns('supplier_id,supplier,contract_expiration_date')->asArray()->getAll();
	    $supplier = Helper_Array::toHashmap ( $supplier, 'supplier_id' );
	    $product = Product::find()->setColumns('product_name,product_chinese_name,product_id,ratio')->asArray()->getAll();
	    $product = Helper_Array::toHashmap ( $product, 'product_name' );
	    $department = Department::find()->setColumns('department_id,department_name')->asArray()->getAll();
	    $department = Helper_Array::toHashmap ( $department, 'department_id' );
	    //$ch=ChannelCost::find()->setColumns('ratio,channel_id,product_id')->asArray()->getAll();
	    
// 	    echo "<pre>";
// 	    print_r($supplier);
// 	    exit;
	    
	    //写入表头 内容为$header,addRow为写入内容
	    Helper_ExcelX::addRow2 ($header);
	    //循环写入数据，以每1000条为节点
	    $tmp_order = array ();
	    $count = 0;
	    //控制sheet的变量
	    $page = 1;
	    foreach($fee_select as $k => $v){
	    	$tmp_order[] = $v;
	    	$order_ids[] = (int)$v['order_id'];
	    	$count = $count+1;
	    	if (count ( $tmp_order ) == '1000') {
	    		//写入数据的函数封装 $page是分页的页数，$header是表头
	    		$this->paymentExportAddRow($tmp_order,$order_ids,$channel,$supplier,$product,$department,$page,$header);
	    		//重置数组以便循环插入时不重复 
	    		$tmp_order = array ();
	    	}
	    	//一百万条数据的时候就换到第二个sheet
	    	if($count % 1000000 == 0){
	    		$page = $page+1;
	    	}
	    }
	    if (count ( $tmp_order )) {
	    	$this->paymentExportAddRow ( $tmp_order,$order_ids,$channel,$supplier,$product,$department,$page,$header);
	    }
	    //写入结束
	    Helper_ExcelX::closeWriter2 ();
	    exit ();
	}
	/**
	 * @todo 应付统计内容具体写入函数
	 * @author 吴开龙
	 * @since May 10th 2020
	 * @return array
	 * @link #79779
	 * @ps 代码是系统原来的代码，所以代码内部没有注释，整体功能是为excel写入数据
	 */
	function paymentExportAddRow($fee_select,$order_ids,$channel,$supplier,$product,$department,$page,$header){
		

		foreach ($fee_select as $num => $value){
			//查询渠道

			bcscale(2);
// 			$curr = CodeCurrency::find('code=? and start_date<=? and end_date>=?',$value['currency'],$value['account_date'],$value['account_date'])->getOne();
			$sheet =array(
				$value['fee_id'],
				Helper_Util::strDate('Y-m-d', $value['order_create_time']),
				$value['ali_order_no'],
				$value['far_no'],
				"'".$value['tracking_no'],
				Helper_Util::strDate('Y-m-d', $value['warehouse_out_time']),
				$department[$value['department_id']]['department_name'],
				$supplier[$value['btype_id']]['supplier'],
				'成本',
				$product[$value['service_code']]['product_chinese_name'],
				$channel[$value['channel_id']]['channel_name'],
				$value['fee_item_code'],
				$value['fee_item_name'],
				$value['quantity']?$value['quantity']:1,
				$value['amount']/($value['quantity']?$value['quantity']:1),
				$value['amount'],
				$value['currency'],
				$value['rate']? $value['rate'] : '未设置',
				bcmul($value['amount'],$value['rate']),
				"'".$value['voucher_no'],
				"'".$value['invoice_no'],
				"'".$value['waybill_title'],
				Helper_Util::strDate('Y-m-d', $value['voucher_time']),
				Helper_Util::strDate('Y-m-d', $value['invoice_time']),
				Helper_Util::strDate('Y-m-d', $value['account_date']),
				$value['packing_type'],
				$value['package_total_in'],
				$value['consignee_country_code'],
				$value['weight_actual_in'],
				$value['total_volumn_weight'],
				$value['weight_income_in'],
				$value['weight_actual_out'],
				$value['total_out_volumn_weight'],
				$value['weight_cost_out'],
				$supplier[$value['btype_id']]['contract_expiration_date']?round((strtotime($supplier[$value['btype_id']]['contract_expiration_date'])-time())/86400,0).'天':'',
				Helper_Util::strDate('Y-m-d', $value['delivery_time']),
			);
// 				    echo "<pre>";
// 				    print_r($supplier);
// 				    exit; 
			Helper_ExcelX::addRow2 ( $sheet,$page,$header );
		}
	}
	
	/**
	* @todo 应收统计导出
	* @author 吴开龙
	* @since May 10th 2020
	* @return file
	* @link #79779
	 */
	function actionReceiveexport(){
	    set_time_limit(0);
	    ini_set('memory_limit', '-1');
	    //链表查询Fee
	    $fee_select = Fee::find ("fee_type = '1'");
	    $fee_select->joinLeft( 'tb_order', 'tb_order.total_volumn_weight,tb_order.total_out_volumn_weight,tb_order.package_total_in,tb_order.weight_cost_out,tb_order.weight_actual_out,tb_order.weight_income_in,tb_order.weight_actual_in,tb_order.consignee_country_code,tb_order.packing_type,tb_order.department_id,tb_order.channel_id,tb_order.far_no,tb_order.service_code,tb_order.ali_order_no,tb_order.tracking_no,tb_order.customer_id,tb_order.warehouse_out_time,tb_order.delivery_time,tb_order.create_time as order_create_time', 'tb_fee.order_id = tb_order.order_id' );
	    //根据列表搜索条件来查询，paymentOrderSelect列表页面的条件封装
	    $fee_select = $this->paymentOrderSelect ( $fee_select );
	    //filter_id条件判断
	    if (request ( 'filter_id' )) {
// 	        if (request ( 'fee_type' ) == '2') {
	            if (request ( 'filter_id' ) == '无') {
	                $fee_select->where ( 'ifnull(btype_id,0)=0' );
	            }elseif (request ( 'filter_id' ) == '全部'){
	            	
	            } else {
	                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
	            }
// 	        }
	    }
	    //排序，以数组形式取出
	    $order_select=$fee_select->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
	    ->asarray()->getAll ();
	    //创建一个excel空文件，文件名 应收统计
	    Helper_ExcelX::startWriter ( 'receivable'  );
	    $header = array (
	        'ID','订单时间','阿里订单号','泛远单号','跟踪单号','出库时间','仓库','供应商','费用类型','产品','费用代码','费用名称','数量','单价','金额','币种','汇率','本位币金额','凭证号','发票号','账单抬头','销账时间','开票时间','登账日期','包裹类型','包裹件数','目的国','收货实重','收货体积重','收货计费重量','出货实重','出货体积重','出货计费重量','合同天数','签收时间'
	    );
	    //print_r($arr);exit;
	    $channel=Channel::find()->setColumns('channel_id,channel_name')->asArray()->getAll();
	    $channel = Helper_Array::toHashmap ( $channel, 'channel_id' );
	    $customer = Customer::find()->setColumns('customer_id,customer,contract_expiration_date')->asArray()->getAll();
	    $customer = Helper_Array::toHashmap ( $customer, 'customer_id' );
	    $product = Product::find()->setColumns('product_name,product_chinese_name,product_id,ratio')->asArray()->getAll();
	    $product = Helper_Array::toHashmap ( $product, 'product_name' );
	    $department = Department::find()->setColumns('department_id,department_name')->asArray()->getAll();
	    $department = Helper_Array::toHashmap ( $department, 'department_id' );
	    //$ch=ChannelCost::find()->setColumns('ratio,channel_id,product_id')->asArray()->getAll();
	    
	    
	    
	    //写入表头 内容为$header,addRow为写入内容
	    Helper_ExcelX::addRow ($header);
	    //循环写入数据，以每200条为节点
	    $tmp_order = array ();
	    foreach($order_select as $k => $v){
	    	$tmp_order[] = $v;
	    	$order_ids[] = (int)$v['order_id'];
	    	if (count ( $tmp_order ) == '1000') {
		    	//写入数据的函数封装
	    		$this->receiveExportAddRow($tmp_order,$order_ids,$channel,$customer,$product,$department);
		    	//重置数组以便循环插入时不重复
		    	$tmp_order = array ();
	    	}
	    }
	    if (count ( $tmp_order )) {
	    	$this->receiveExportAddRow($tmp_order,$order_ids,$channel,$customer,$product,$department);
	    }
	    //写入结束
	    Helper_ExcelX::closeWriter ();
	    exit();
	}
	/**
	 * @todo 应收统计内容具体写入函数
	 * @author 吴开龙
	 * @since May 10th 2020
	 * @return array
	 * @link #79779
	 * @ps 代码是系统原来的代码，所以代码内部没有注释，整体功能是为excel写入数据
	 */
	function receiveExportAddRow($fee_select,$order_ids,$channel,$customer,$product,$department){

		foreach ($fee_select as $num => $value){
			//查询渠道
			bcscale(2);
// 			$curr = CodeCurrency::find('code=? and start_date<=? and end_date>=?',$value['currency'],$value['account_date'],$value['account_date'])->getOne();
			$sheet =array(
				$value['fee_id'],
				Helper_Util::strDate('Y-m-d', $value['order_create_time']),
				$value['ali_order_no'],
				$value['far_no'],
				"'".$value['tracking_no'],
				Helper_Util::strDate('Y-m-d', $value['warehouse_out_time']),
				$department[$value['department_id']]['department_name'],
				$customer[$value['btype_id']]['customer'],
				'收入',
				$product[$value['service_code']]['product_chinese_name'],
				$value['fee_item_code'],
				$value['fee_item_name'],
				$value['quantity']?$value['quantity']:1,
				$value['amount']/($value['quantity']?$value['quantity']:1),
				$value['amount'],
				$value['currency'],
				$value['rate']? $value['rate'] : '未设置',
				bcmul($value['amount'],$value['rate']),
				"'".$value['voucher_no'],
				"'".$value['invoice_no'],
				"'".$value['waybill_title'],
				Helper_Util::strDate('Y-m-d', $value['voucher_time']),
				Helper_Util::strDate('Y-m-d', $value['invoice_time']),
				Helper_Util::strDate('Y-m-d', $value['account_date']),
				$value['packing_type'],
				$value['package_total_in'],
				$value['consignee_country_code'],
				$value['weight_actual_in'],
				$value['total_volumn_weight'],
				$value['weight_income_in'],
				$value['weight_actual_out'],
				$value['total_out_volumn_weight'],
				$value['weight_cost_out'],
				$customer[$value['btype_id']]['contract_expiration_date']?round((strtotime($customer[$value['btype_id']]['contract_expiration_date'])-time())/86400,0).'天':'',
				Helper_Util::strDate('Y-m-d', $value['delivery_time']),				
			);
			Helper_ExcelX::addRow ( $sheet );
		}
	}
	
	/*
	 * 应收账单导出
	 */
	function actionreceivableexport(){
	    set_time_limit(0);//不限制超时时间
	    ini_set('memory_limit', '-1');//不限制内存
        $order_select = Order::find ();
	    $order_select = $this->paymentOrderSelect ( $order_select );
	    $order_select->joinLeft( 'tb_fee', 'tb_fee.fee_item_code,tb_fee.fee_item_name,tb_fee.quantity,tb_fee.amount,tb_fee.create_time as fee_create_time', 'tb_fee.order_id = tb_order.order_id' );
	    $order_select->where ( "tb_fee.fee_type = '1'");
	    $order_select=$order_select->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
	    ->asarray()->getAll ();
	    $order_select=Helper_Array::groupBy($order_select, 'ali_order_no');
        $objExcel = new PHPExcel();
	    $sheet = $objExcel->getActiveSheet();
	    $sheet->mergeCells('A1:AG1');
	    $sheet->getStyle('A1:AG1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $sheet->setCellValue('A1', '账单明细');
	    $sheet->getStyle('A1:AG1')->getFont()->setSize(20);
	    $sheet->getStyle('A1:AG1')->getFont()->setBold(true);
	    $sheet->mergeCells('A2:AG2');
	    $sheet->getStyle('A2:AG2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $sheet->setCellValue('A2', '***绿色区域单元格必填 没有的费用项可以不填或填0');
	    $sheet->getStyle('A2:AG2')->getFont()->setBold(true);
	    $sheet->setCellValue('A3', '供应商名称：');
	    $sheet->setCellValue('B3', '越航');
	    $sheet->setCellValue('C3', '出库起始日期：');
	    $sheet->setCellValue('D3',date('Y/m/d',strtotime(request ( 'start_date'))));
	    $sheet->setCellValue('E3','出库结束日期：');
	    $sheet->setCellValue('F3',date('Y/m/d',strtotime(request ( "end_date"))));
	    $sheet->getStyle( 'A3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0');
	    $sheet->getStyle( 'B3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('02b925');
	    $sheet->getStyle( 'C3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0');
	    $sheet->getStyle( 'D3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('02b925');
	    $sheet->getStyle( 'E3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0');
	    $sheet->getStyle( 'F3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('02b925');
	    $sheet->getStyle( 'A5:E5')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0');
	    $sheet->getStyle( 'F5:K5')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F00');
	    $sheet->getStyle( 'L5')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('60009b');
	    $sheet->getStyle( 'M5:AT5')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F00');
// 	    $sheet->setCellValue('G3','实际费用');
// 	    $sheet->setCellValue('H3','显示数量');
// 	    $sheet->setCellValue('I3','显示数量');
// 	    $sheet->setCellValue('P3','实际费用');
// 	    $sheet->setCellValue('U3','实际费用');
// 	    $sheet->setCellValue('V3','实际费用');
// 	    $sheet->setCellValue('AE3','显示该项费用总计');
// 	    $sheet->setCellValue('AF3','显示该项费用总计');
		// 	    $sheet->setCellValue('G3','实际费用');
	    // 	    $sheet->setCellValue('H3','显示数量');
	    // 	    $sheet->setCellValue('I3','显示数量');
	    // 	    $sheet->setCellValue('P3','实际费用');
	    // 	    $sheet->setCellValue('U3','实际费用');
	    // 	    $sheet->setCellValue('V3','实际费用');
	    // 	    $sheet->setCellValue('AE3','显示该项费用总计');
	    // 	    $sheet->setCellValue('AF3','显示该项费用总计');
	    // 	    $sheet->setCellValue('AF3','显示该项费用总计');
	    $sheet->mergeCells('A4:E4');
	    $sheet->setCellValue('F4','logisticsExpressASP_Total');
	    $sheet->setCellValue('G4','logisticsExpressASP_EX0001');
	    $sheet->setCellValue('H4','logisticsExpressASP_EX0002');
	    $sheet->setCellValue('I4','logisticsExpressASP_EX0003');
	    $sheet->setCellValue('J4','logisticsExpressASP_EX0004');
	    $sheet->setCellValue('K4','logisticsExpressASP_EX0005');
	    $sheet->setCellValue('L4','logisticsExpressASP_EX0007');
	    $sheet->setCellValue('M4','logisticsExpressASP_EX0008');
	    $sheet->setCellValue('N4','logisticsExpressASP_EX0009');
	    $sheet->setCellValue('O4','logisticsExpressASP_EX0010');
	    $sheet->setCellValue('P4','logisticsExpressASP_EX0012');
	    $sheet->setCellValue('Q4','logisticsExpressASP_EX0013');
	    $sheet->setCellValue('R4','logisticsExpressASP_EX0015');
	    $sheet->setCellValue('S4','logisticsExpressASP_EX0016');
	    $sheet->setCellValue('T4','logisticsExpressASP_EX0017');
	    $sheet->setCellValue('U4','logisticsExpressASP_EX0019');
	    $sheet->setCellValue('V4','logisticsExpressASP_EX0020');
	    $sheet->setCellValue('W4','logisticsExpressASP_EX0021');
	    $sheet->setCellValue('X4','logisticsExpressASP_EX0022');
	    $sheet->setCellValue('Y4','logisticsExpressASP_EX0023');
	    $sheet->setCellValue('Z4','logisticsExpressASP_EX0025');
	    $sheet->setCellValue('AA4','logisticsExpressASP_EX0026');
	    $sheet->setCellValue('AB4','logisticsExpressASP_EX0027');
	    $sheet->setCellValue('AC4','logisticsExpressASP_EX0028');
	    $sheet->setCellValue('AD4','logisticsExpressASP_EX0033');
	    $sheet->setCellValue('AE4','logisticsExpressASP_EX0034');
	    $sheet->setCellValue('AF4','logisticsExpressASP_EX0035');
	    $sheet->setCellValue('AG4','logisticsExpressASP_EX0036');
	    $sheet->setCellValue('AH4','logisticsExpressASP_EX0037');
	    $sheet->setCellValue('AI4','logisticsExpressASP_EX0038');
	    $sheet->setCellValue('AJ4','logisticsExpressASP_EX0042');
	    $sheet->setCellValue('AK4','logisticsExpressASP_EX0043');
	    $sheet->setCellValue('AL4','logisticsExpressASP_EX0044');
	    $sheet->setCellValue('AM4','logisticsExpressASP_EX0045');
	    $sheet->setCellValue('AN4','logisticsExpressASP_EX0046');
	    $sheet->setCellValue('AO4','logisticsExpressASP_EX0047');
	    $sheet->setCellValue('AP4','logisticsExpressASP_EX0048');
	    $sheet->setCellValue('AQ4','logisticsExpressASP_EX0049');
	    $sheet->setCellValue('AR4','logisticsExpressASP_EX0050');
	    $sheet->setCellValue('AS4','logisticsExpressASP_EX0051');
	    $sheet->setCellValue('AT4','logisticsExpressASP_EX0052');
	    $sheet->setCellValue('A5','序号');
	    $sheet->setCellValue('B5','ALS单号');
	    $sheet->setCellValue('C5','转运单号');
	    $sheet->setCellValue('D5','目的地');
	    $sheet->setCellValue('E5','计费重量');
	    $sheet->setCellValue('F5','该票小计');
	    $sheet->setCellValue('G5','基础运费');
	    $sheet->setCellValue('H5','包装-包裹袋');
	    $sheet->setCellValue('I5','包装-纸箱');
	    $sheet->setCellValue('J5','包装-托盘/木箱/木架');
	    $sheet->setCellValue('K5','带电附加费');
	    $sheet->setCellValue('L5','仓库服务费--修改包装含物料');
	    $sheet->setCellValue('M5','仓库服务费--扣件与退货');
	    $sheet->setCellValue('N5','仓库服务费--更换地址或申报资料');
	    $sheet->setCellValue('O5','代办服务费--代办产地证');
	    $sheet->setCellValue('P5','代办服务费--一般贸易报关');
	    $sheet->setCellValue('Q5','特殊处理服务费--香港扣货');
	    $sheet->setCellValue('R5','特殊处理服务费--退货重出');
	    $sheet->setCellValue('S5','特殊处理服务费--国外退货');
	    $sheet->setCellValue('T5','特殊处理服务费--目的地关税');
	    $sheet->setCellValue('U5','燃油附加费');
	    $sheet->setCellValue('V5','偏远地区附加费');
	    $sheet->setCellValue('W5','超尺寸/超重附加费');
	    $sheet->setCellValue('X5','目的地关税');
	    $sheet->setCellValue('Y5','关税预付手续费');
	    $sheet->setCellValue('Z5','更改账单附加费');
	    $sheet->setCellValue('AA5','高风险地区附加费');
	    $sheet->setCellValue('AB5','特别处理货件附加费');
	    $sheet->setCellValue('AC5','更改地址附加费');
	    $sheet->setCellValue('AD5','代办服务费--超USD125香港出口申报费');
	    $sheet->setCellValue('AE5','异形包装费');
	    $sheet->setCellValue('AF5','超尺寸/超重附加费-包裹');
	    $sheet->setCellValue('AG5','上门揽收费');
	    $sheet->setCellValue('AH5','特殊品类附加费1');
	    $sheet->setCellValue('AI5','小包操作费');
	    $sheet->setCellValue('AJ5','防疫附加费');
	    $sheet->setCellValue('AK5','非正式报关费');
	    $sheet->setCellValue('AL5','旺季附加费*');
	    $sheet->setCellValue('AM5','旺季附加费');
	    $sheet->setCellValue('AN5','进口国VAT增值税');
	    $sheet->setCellValue('AO5','转运费&退运费');
	    $sheet->setCellValue('AP5','技术服务费');
	    $sheet->setCellValue('AQ5','超远取件费');
	    $sheet->setCellValue('AR5','签名确认费');
	    $sheet->setCellValue('AS5','住宅交付附加费');
	    $sheet->setCellValue('AT5','其他附加费');
	    $i=1;
	    $j=6;
	    foreach ($order_select as $v => $row){
	        $total='';
	        $sheet->setCellValue("A$j",$i);
	        $sheet->setCellValue("B$j",$v);
	        $fee_item_code=array();
	        $fee_item=array('logisticsExpressASP_EX0001'=>"G",'logisticsExpressASP_EX0002'=>"H",'logisticsExpressASP_EX0003'=>"I"
	            ,'logisticsExpressASP_EX0012'=>"P",'logisticsExpressASP_EX0019'=>"U",'logisticsExpressASP_EX0020'=>"V",'logisticsExpressASP_EX0035'=>"AF",'logisticsExpressASP_EX0034'=>"AE",'logisticsExpressASP_EX0036'=>"AG"
	        	,'logisticsExpressASP_EX0037'=>"AH",'logisticsExpressASP_EX0038'=>"AI",'logisticsExpressASP_EX0042'=>"AJ",'logisticsExpressASP_EX0043'=>"AK",'logisticsExpressASP_EX0044'=>"AL",'logisticsExpressASP_EX0045'=>"AM",'logisticsExpressASP_EX0046'=>"AN"
	        	,'logisticsExpressASP_EX0047'=>"AO",'logisticsExpressASP_EX0048'=>"AP",'logisticsExpressASP_EX0049'=>"AQ",'logisticsExpressASP_EX0050'=>"AR",'logisticsExpressASP_EX0051'=>"AS",'logisticsExpressASP_EX0052'=>"AT"
	        );
	        foreach ($row as $r){
	            $fee_item_code[]=$r['fee_item_code'];
	            if($r['fee_item_code']=='logisticsExpressASP_EX0001'){
	                $sheet->setCellValue("G$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0002'){
	                $sheet->setCellValue("H$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0003'){
	                $sheet->setCellValue("I$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0012'){
	                $sheet->setCellValue("P$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0019'){
	                $sheet->setCellValue("U$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0020'){
	                $sheet->setCellValue("V$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0035'){
	                $sheet->setCellValue("AF$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0034'){
	                $sheet->setCellValue("AE$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0036'){
	                $sheet->setCellValue("AG$j",$r['amount']);
	                $total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0037'){
	            	$sheet->setCellValue("AH$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0038'){
	            	$sheet->setCellValue("AI$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0042'){
	            	$sheet->setCellValue("AJ$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0043'){
	            	$sheet->setCellValue("AK$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0044'){
	            	$sheet->setCellValue("AL$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0045'){
	            	$sheet->setCellValue("AM$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0046'){
	            	$sheet->setCellValue("AN$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0047'){
	            	$sheet->setCellValue("AO$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0048'){
	            	$sheet->setCellValue("AP$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0049'){
	            	$sheet->setCellValue("AQ$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0050'){
	            	$sheet->setCellValue("AR$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0051'){
	            	$sheet->setCellValue("AS$j",$r['amount']);
	            	$total += $r['amount'];
	            }elseif ($r['fee_item_code']=='logisticsExpressASP_EX0052'){
	            	$sheet->setCellValue("AT$j",$r['amount']);
	            	$total += $r['amount'];
	            }
	        }
	        foreach ($fee_item as $key => $f){
	            if(!in_array($key, $fee_item_code)){
	                $sheet->setCellValue("$f$j",'0');
	            }
	        }
	        
	        $sheet->setCellValue("C$j",$r['far_no']);
	        $sheet->setCellValue("D$j",$r['consignee_country_code']);
	        $sheet->setCellValue("E$j",$r['weight_income_in']);
	        $sheet->setCellValue("F$j",$total);
	        $sheet->setCellValue("J$j",'0');
	        $sheet->setCellValue("K$j",'0');
	        $sheet->setCellValue("L$j",'0');
	        $sheet->setCellValue("M$j",'0');
	        $sheet->setCellValue("N$j",'0');
	        $sheet->setCellValue("O$j",'0');
	        $sheet->setCellValue("Q$j",'0');
	        $sheet->setCellValue("R$j",'0');
	        $sheet->setCellValue("S$j",'0');
	        $sheet->setCellValue("T$j",'0');
	        $sheet->setCellValue("W$j",'0');
	        $sheet->setCellValue("X$j",'0');
	        $sheet->setCellValue("Y$j",'0');
	        $sheet->setCellValue("Z$j",'0');
	        $sheet->setCellValue("AA$j",'0');
	        $sheet->setCellValue("AB$j",'0');
	        $sheet->setCellValue("AC$j",'0');
	        $sheet->setCellValue("AD$j",'0');
	        $i++;$j++;
	    }
	    header('Content-Type: application/vnd.ms-excel');
	    @$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
        header('Content-Disposition: attachment;filename=应收账单.xls');
	    header('Cache-Control: max-age=0');
	    try {
	        @$objWriter->save('php://output');
	    } catch (PHPExcel_Writer_Exception $ex) {
	        $tmpF = INDEX_DIR . DS . '_tmp' . DS . 'upload' . DS . 'tmp' . time() . '.xlsx';
	        @$objWriter->save($tmpF);
	        readfile($tmpF);
	        unlink($tmpF);
	    }
	    exit();
	}
	/**
	 * 轨迹监控
	 */
	function actionRoute(){
    		$select=VRouteLatest::find();
    		if(request('ali_order_no')){
    		    $order_nos = explode("\r\n", request('ali_order_no'));
    		    $order_no = array_filter($order_nos);
    		    $order_no = array_unique($order_no);
    		    $select->where('ali_order_no in (?) or tracking_no in (?)',$order_no,$order_no);
    		}
    		if (request('confirm_flag','0') !='all'){
    			if (request('confirm_flag') =='isnull'){
    				$select->where('confirm_flag is null');
    			}else {
    				$select->where('confirm_flag=?',request('confirm_flag','0'));
    			}
    		}
    		if(request('tracking_name')){
    		    $select->where('description like ?','%'.request('tracking_name').'%');
    		}
    		if(request('network_code') && request('network_code') != "all"){
    		    $select->joinLeft('tb_channel', '' ,'tb_channel.channel_id=v_route_latest.channel_id')->where('tb_channel.network_code=?',request('network_code'));
    		}
    		//问题件状态
    		if(request ( 'parcel_flag' )){
    		    if(request ( 'parcel_flag' )=='2'){
    		        $ali_order_no = array();
    		        $ali_order_no = Abnormalparcel::find('parcel_flag = "1" or parcel_flag = "3"')->setColumns('ali_order_no')->asArray()->getAll();
    		        $ali_order_no = Helper_Array::getCols($ali_order_no, 'ali_order_no');
    		        $select->joinLeft('tb_abnormal_parcel', '' ,'tb_abnormal_parcel.ali_order_no=v_route_latest.ali_order_no')
    		        ->where('tb_abnormal_parcel.parcel_flag=? and tb_abnormal_parcel.ali_order_no not in (?)',request ( 'parcel_flag' ),$ali_order_no);
    		    }else{
    		        $select->joinLeft('tb_abnormal_parcel', '' ,'tb_abnormal_parcel.ali_order_no=v_route_latest.ali_order_no')
    		        ->where('tb_abnormal_parcel.parcel_flag=?',request ( 'parcel_flag' ));
    		    }
    		}
    		//未更新时长
    		if(request ( 'nochange_time' )){
    		    $newtime = time()-request ( 'nochange_time' )*24*3600;
    		    $select->where('v_route_latest.create_time < ? ',$newtime);
    		}
    		if(request('ali_code')){
    		    $select->where('v_route_latest.tracking_code = ?',request('ali_code'));
    		}
    		if(request('channel_id')){
    		   $select->where('v_route_latest.channel_id = ?',request('channel_id'));
    		}
    		if(request('consignee_country_code')){
    		   $select->where('v_route_latest.consignee_country_code = ?',request('consignee_country_code'));
    		}
    		if(request('exp')=='exp'){
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '2G');
            set_time_limit(0);
			$list=clone $select;
        	$lists=$list->getAll();
        	$header = array (
        	    '阿里单号',
        	    'DST',
        	    '网络',
        	    '运单号',
        	    '预派时间',
        	    '最近轨迹时间',
        	    '最近轨迹地点',
        	    '最近轨迹',
        	    '抓取时间',
        	    '状态',
        	    '问题最新跟进',
        	);
        	$sheet = array (
        		$header
        	);
        	foreach ($lists as $value){
        	    $state = '';
        	    if($value->confirm_flag ==2){
        	        $state = '已忽略';
        	    }elseif (strlen($value->confirm_flag)==0){
        	        $state = '无匹配';
        	    }elseif($value->confirm_flag =='0'){
        	        $state = '未确认';
        	    }
        	    $ab=Abnormalparcel::find('ali_order_no = ?',$value->ali_order_no)->columns('abnormal_parcel_id')->asArray()->getAll();
        	    $ab=Helper_Array::getCols($ab, 'abnormal_parcel_id');
        	    $hi=new Abnormalparcelhistory();
        	    if(count($ab)>0){
        	        $hi=Abnormalparcelhistory::find('abnormal_parcel_id in (?)',$ab)->order('abnormal_parcel_history_id DESC')->getOne();
        	    }
				$row =array(
				    $value->ali_order_no,
				    $value->consignee_country_code,
				    $value->order->channel->network_code,
				    "'".$value->tracking_no,
				    $value->order->present_time?date('m-d H:i',$value->order->present_time):'',
				    $value->time?date('m-d H:i',$value->time):'',
				    $value->location,
				    $value->description,
				    date('m-d H:i',$value->create_time),
				    $state,
				    $hi?$hi->follow_up_content:'',
        		);
				$sheet [] = $row;
        	}
        	Helper_ExcelX::array2xlsx ( $sheet, '轨迹监控导出' );
        	exit ();
		}
    		$select=$select->order('v_route_latest.create_time asc')->group('v_route_latest.ali_order_no');
    		$pagination = null;
    		$list=$select->limitPage(request_is_post()?1:request('page'),request( 'page_size', 25 ))
    			->fetchPagination($pagination)
    			->getAll();
    		$this->_view['list']=$list;
    		$this->_view['pagination']=$pagination;
	}
	/**
	 * 服务指标监控
	 */
	function actionServiceindicators(){
	    if(request_is_post()){
	        set_time_limit(0);//不限制超时时间
	        ini_set('memory_limit', '-1');//不限制内存
	        $orders=Order::find("ali_testing_order!= '1' and order_status != '2' and order_status != '3'");
	        if(request('channel_id')){
	            $orders->where('channel_id = ?',request('channel_id'));
	        }
	        //入库及时率
	        $warehouse_in=array();
	        $warehouse_in_orders=clone $orders;
	        //订单日期
	        if(request("start_date")){
	            $warehouse_in_orders->where("create_time >=?",strtotime(request("start_date").' 00:00:00'));
	        }
	        if (request("end_date")){
	            $warehouse_in_orders->where("create_time <=?",strtotime(request("end_date").' 23:59:59'));
	        }
	        $warehouse_in_orders=$warehouse_in_orders->getAll();
	        $warehouse_in_order_ids=array();
	        foreach ($warehouse_in_orders as $warehouse_in_order){
	            if(!$warehouse_in_order->warehouse_in_time || (self::getdaycount($warehouse_in_order->create_time, $warehouse_in_order->warehouse_in_time)>5)){
	                $warehouse_in_order_ids[]=$warehouse_in_order->order_id;
	            }
	        }
	        $warehouse_in['count']=count($warehouse_in_orders);
	        $warehouse_in['substandard']=$warehouse_in_order_ids;
	        //支付及时率
	        $pay=array();
	        $pay_orders=clone $orders;
	        //核查日期
	        if(request("start_date")){
	            $pay_orders->where("warehouse_confirm_time >=?",strtotime(request("start_date").' 00:00:00'));
	        }
	        if (request("end_date")){
	            $pay_orders->where("warehouse_confirm_time <=?",strtotime(request("end_date").' 23:59:59'));
	        }
	        $pay_orders=$pay_orders->getAll();
	        $pay_order_ids=array();
	        foreach ($pay_orders as $pay_order){
	            if(!$pay_order->payment_time || (self::getdaycount($pay_order->warehouse_confirm_time, $pay_order->payment_time)>2)){
	                $pay_order_ids[]=$pay_order->order_id;
	            }
	        }
	        $pay['count']=count($pay_orders);
	        $pay['substandard']=$pay_order_ids;
	        //出库及时率
	        $warehouse_out=array();
	        $warehouse_out_orders=clone $orders;
	        if(request("start_date")){
	            $warehouse_out_orders->where("payment_time >=?",strtotime(request("start_date").' 00:00:00'));
	        }
	        if (request("end_date")){
	            $warehouse_out_orders->where("payment_time <=?",strtotime(request("end_date").' 23:59:59'));
	        }
	        $warehouse_out_orders=$warehouse_out_orders->getAll();
	        $warehouse_out_order_ids=array();
	        foreach ($warehouse_out_orders as $warehouse_out_order){
	            if(!$warehouse_out_order->warehouse_out_time){
	                $warehouse_out_order_ids[]=$warehouse_out_order->order_id;
	            }else{
	                //周一-周六的14:00前支付，当天24:00前出库
	                if(date('w',$warehouse_out_order->payment_time)!=0 && $warehouse_out_order->payment_time<=strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 14:00:00')){
	                    if($warehouse_out_order->warehouse_out_time>strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 23:59:59')){
	                        $warehouse_out_order_ids[]=$warehouse_out_order->order_id;
	                    }
	                }
	                //周一-周五的14:00后支付，次日10:00前出库
	                if(date('w',$warehouse_out_order->payment_time)!=0 && date('w',$warehouse_out_order->payment_time)!=6 && $warehouse_out_order->payment_time>strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 14:00:00')){
	                    if($warehouse_out_order->warehouse_out_time>strtotime('+1 day',strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 10:00:00'))){
	                        $warehouse_out_order_ids[]=$warehouse_out_order->order_id;
	                    }
	                }
	                //周六14:00后支付，下周一10:00前出库
	                if(date('w',$warehouse_out_order->payment_time)==6 && $warehouse_out_order->payment_time>strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 14:00:00')){
	                    if($warehouse_out_order->warehouse_out_time>strtotime('+2 day',strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 10:00:00'))){
	                        $warehouse_out_order_ids[]=$warehouse_out_order->order_id;
	                    }
	                }
	                //周日支付的订单，下周一10:00前出库
	                if(date('w',$warehouse_out_order->payment_time)==0){
	                    if($warehouse_out_order->warehouse_out_time>strtotime('+1 day',strtotime(date('Y-m-d',$warehouse_out_order->payment_time).' 10:00:00'))){
	                        $warehouse_out_order_ids[]=$warehouse_out_order->order_id;
	                    }
	                }
	            }
	        }
	        $warehouse_out['count']=count($warehouse_out_orders);
	        $warehouse_out['substandard']=$warehouse_out_order_ids;
	        //派送准时率
	        $delivery_on_time=array();
	        $delivery_completed=array();
	        $delivery_orders=clone $orders;
	        if(request("start_date")){
	            $delivery_orders->where("warehouse_out_time >=?",strtotime(request("start_date").' 00:00:00'));
	        }
	        if (request("end_date")){
	            $delivery_orders->where("warehouse_out_time <=?",strtotime(request("end_date").' 23:59:59'));
	        }
	        $delivery_orders=$delivery_orders->getAll();
	        $delivery_on_time_order_ids=array();
	        $delivery_completed_order_ids=array();
	        foreach ($delivery_orders as $delivery_order){
	            if(!$delivery_order->delivery_time || (self::getdaycount($delivery_order->warehouse_out_time, $delivery_order->delivery_time)>5)){
	                $delivery_on_time_order_ids[]=$delivery_order->order_id;
	            }
	            if(!$delivery_order->delivery_time || ((($delivery_order->delivery_time-$delivery_order->warehouse_out_time)/86400)>15)){
	                $delivery_completed_order_ids[]=$delivery_order->order_id;
	            }
	        }
	        $delivery_on_time['count']=count($delivery_orders);
	        $delivery_on_time['substandard']=$delivery_on_time_order_ids;
	        //派送妥投率
	        $delivery_completed['count']=count($delivery_orders);
	        $delivery_completed['substandard']=$delivery_completed_order_ids;
	        //国际物流商时效达成率
	        $carriers_pick_up=array();
	        $carriers_pick_up_orders=clone $orders;
	        if(request("start_date")){
	            $carriers_pick_up_orders->where("carrier_pick_time >=?",strtotime(request("start_date").' 00:00:00'));
	        }
	        if (request("end_date")){
	            $carriers_pick_up_orders->where("carrier_pick_time <=?",strtotime(request("end_date").' 23:59:59'));
	        }
	        $carriers_pick_up_orders=$carriers_pick_up_orders->getAll();
	        $carriers_pick_up_ids=array();
	        foreach ($carriers_pick_up_orders as $carriers_pick_up_order){
	            if(!$carriers_pick_up_order->delivery_time || (self::getdaycount($carriers_pick_up_order->carrier_pick_time, $carriers_pick_up_order->delivery_time)>4)){
	                $carriers_pick_up_ids[]=$carriers_pick_up_order->order_id;
	            }
	        }
	        $carriers_pick_up['count']=count($carriers_pick_up_orders);
	        $carriers_pick_up['substandard']=$carriers_pick_up_ids;
	        $this->_view['warehouse_in']=$warehouse_in;
	        $this->_view['pay']=$pay;
	        $this->_view['warehouse_out']=$warehouse_out;
	        $this->_view['delivery_on_time']=$delivery_on_time;
	        $this->_view['delivery_completed']=$delivery_completed;
	        $this->_view['carriers_pick_up']=$carriers_pick_up;
	    }
	}
	/**
	 * 计算天数（除去周末）
	 */
	static function getdaycount($start_date,$end_date){
	    //获取总天数
	    if(date("w",$end_date)=='0' || date("w",$end_date)=='6'){
	        $end_date=strtotime(date('Y-m-d',$end_date).' 23:59:59');
	    }
	    $all_days=ceil(($end_date-$start_date)/3600)/24;
	    //周末天数
	    $week_counts=0;
	    for ($end_date;$end_date>$start_date;$end_date=($end_date-3600*24)){
	        $week=date("w",$end_date);
	        if($week=='0' || $week=='6'){
	            $week_counts+=1;
	        }
	    }
	    //除去周末总天数
	    $days=$all_days-$week_counts;
	    return $days;
	}
	/**
	 * 导出不达标订单
	 */
	function actionExport(){
	    $lists=Order::find('order_id in (?)',explode(',', request('order_ids')))->getAll();
	    $header = array (
	        '阿里订单号','件数','泛远单号','末端运单号','渠道','目的国','报关','申报总价','计费重','上门取件','状态','中心仓','订单时间','入库时间','核查时间','支付时间','出库时间','承运商取件时间','签收时间','发件人邮箱','快递单号'
	    );
	    $sheet = array (
	        $header
	    );
	    $status=array('1'=>'未入库','2'=>'已取消','3'=>'已退货','4'=>'已支付','5'=>'已入库','6'=>'已打印','7'=>'已出库','8'=>'已提取','9'=>'已签收','10'=>'已查验','11'=>'待退货','12'=>'已扣件');
	    foreach ($lists as $value){
	        $department_name = '';
	        $department = Department::find ( 'department_id=?', $value->department_id )->getOne ();
	        if($department->isNewRecord()){
	            $department_name = '';
	        }elseif ($department->department_name == '杭州仓') {
	            $department_name = '杭州仓';
	        } elseif ($department->department_name == '义乌仓') {
	            $department_name = '义乌仓';
	        } elseif ($department->department_name == '上海仓') {
	            $department_name = '上海仓';
	        }elseif ($department->department_name == '广州仓') {
	            $department_name = '广州仓';
	        }elseif ($department->department_name == '青岛仓') {
	            $department_name = '青岛仓';
	        }elseif ($department->department_name == '深圳仓') {
	            $department_name = '深圳仓';
	        }elseif ($department->department_name == '南京仓') {
	            $department_name = '南京仓';
	        }
	        $item_count=0;
	        foreach ($value->packages as $package){
	            $item_count+=$package->quantity;
	        }
	        $sheet [] =array(
	            $value->ali_order_no,$item_count,$value->far_no,"'".$value->tracking_no,$value->channel->channel_name,$value->consignee_country_code,$value->declaration_type=='DL'?'是':'',$value->total_amount,
	            $value->weight_income_in?$value->weight_income_in:'',$value->need_pick_up?"是":"",$status[$value->order_status],$department_name,
	            Helper_Util::strDate('Y-m-d H:i', $value->create_time),Helper_Util::strDate('Y-m-d H:i', $value->warehouse_in_time),Helper_Util::strDate('Y-m-d H:i', $value->warehouse_confirm_time)
	            ,Helper_Util::strDate('Y-m-d H:i', $value->payment_time),Helper_Util::strDate('Y-m-d H:i', $value->warehouse_out_time),Helper_Util::strDate('Y-m-d H:i', $value->carrier_pick_time)
	            ,Helper_Util::strDate('Y-m-d H:i', $value->delivery_time),$value->sender_email,$value->reference_no
	        );
	    }
	    Helper_ExcelX::array2xlsx ( $sheet, '不达标订单列表' );
	    exit ();
	}
	/**
	 * 轨迹已签收，未推送阿里
	 */
	function actionSign(){
	    set_time_limit(0);//不限制超时时间
// 	    //连表查询，tb_tracking和tb_order
// 	    //优化sql
// 	    $select = Route::find('tb_routes.rouets_type <> 2');
// 	    //链接tb_tracking表
// 	    $select->joinLeft('tb_tracking', '*','tb_routes.id=tb_tracking.route_id');
// 	    //链接tb_order表
// 	    $select->joinLeft('tb_order', 'tb_order.ali_order_no,tb_order.channel_id,tb_order.consignee_country_code','tb_routes.tracking_no=tb_order.tracking_no');
// 	    //条件
// 	    //优化sql
// 	    $select->where('((tb_tracking.tracking_code!="S_DELIVERY_SIGNED" and (tb_routes.description like "%妥投%" or tb_routes.description like "%delivered%")) or tb_tracking.tracking_code="S_DELIVERY_SIGNED") and tb_tracking.confirm_flag=0 and tb_tracking.send_flag=0');
		
		
	    $select = Order::find('order_status IN (6, 7, 8)')->setColumns('ali_order_no,channel_id,consignee_country_code');
	    $select->joinLeft('tb_routes', '*','tb_routes.tracking_no=tb_order.tracking_no');
	    $select->joinLeft('tb_tracking', '*','tb_routes.id=tb_tracking.route_id');
	    //条件
	    $select->where('((tb_tracking.tracking_code!="S_DELIVERY_SIGNED" and ((tb_routes.description like "%妥投%" and tb_routes.description not regexp "未妥投|退回妥投|退回 妥投|妥投失败") or (tb_routes.description like "%delivered%" and tb_routes.description not regexp "not delivered|data delivered|undelivered|not be delivered"))) or tracking_code="S_DELIVERY_SIGNED") and confirm_flag in (0,2) and send_flag=0 and rouets_type != 2');
		
		//导出
		if(request('export')=='export'){
			//设置时间和内存
			ini_set('max_execution_time', '0');
			ini_set('memory_limit', '2G');
			$select = $select->asArray()->getAll();
			$header = array (
				'阿里单号',
				'DST',
				'网络',
				'运单号',
				
				'最近轨迹时间',
				'最近轨迹地点',
				'最近轨迹',
				'抓取时间',
				'状态'
			);
			$sheet = array (
				$header
			);
			foreach ($select as $row){
				$channel = Channel::find('channel_id=?',$row['channel_id'])->getOne();
				$yichu = $row['rouets_type'] == 0 ? '待确认' : '已确认';
				$data =array(
					$row['ali_order_no'],
					$row['consignee_country_code'],
					$channel->network_code,
					$row['tracking_no'],
					
					date('Y-m-d H:i',$row['time']),
					$row['location'],
					$row['description'],
					date('Y-m-d H:i',$row['create_time']),
					$yichu
				);
				$sheet[] = $data;
			}
			//导出
			Helper_ExcelX::array2xlsx ( $sheet, '签收轨迹导出' );
			exit ();
		}
		//分页
		$this->_view['list']=$select->limitPage(request('page',1),request( 'page_size', 25 ))
			->fetchPagination($this->_view['pagination'])
			->asArray()
			->getAll();
	}
	function actionSign2(){
		set_time_limit(0);//不限制超时时间
		$select = Order::find('order_status IN (6, 7, 8)')->setColumns('order_id,ali_order_no,channel_id,consignee_country_code');
		$select->joinLeft('tb_routes', '*','tb_routes.tracking_no=tb_order.tracking_no');
		$select->joinLeft('tb_tracking', 'tracking_id','tb_routes.id=tb_tracking.route_id');
		//条件 
		$select->where('((tb_routes.description like "%妥投%" and tb_routes.description not like "%未妥投%" and tb_routes.description not regexp "未妥投|退回妥投|退回 妥投|妥投失败") or (tb_routes.description like "%delivered%" and tb_routes.description not regexp "not delivered|data delivered|undelivered|not be delivered")) and rouets_type != 2 and tb_tracking.route_id is null');
		//导出
		if(request('export')=='export'){
			//设置时间和内存
			ini_set('max_execution_time', '0');
			ini_set('memory_limit', '2G');
			$select = $select->asArray()->getAll();
			$header = array (
				'阿里单号',
				'DST',
				'网络',
				'运单号',
				
				'最近轨迹时间',
				'最近轨迹地点',
				'最近轨迹',
				'抓取时间',
				'状态'
			);
			$sheet = array (
				$header
			);
			foreach ($select as $row){
				$channel = Channel::find('channel_id=?',$row['channel_id'])->getOne();
				$yichu = $row['rouets_type'] == 0 ? '待确认' : '已确认';
				$data =array(
					$row['ali_order_no'],
					$row['consignee_country_code'],
					$channel->network_code,
					$row['tracking_no'],
					
					date('Y-m-d H:i',$row['time']),
					$row['location'],
					$row['description'],
					date('Y-m-d H:i',$row['create_time']),
					$yichu
				);
				$sheet[] = $data;
			}
			//导出
			Helper_ExcelX::array2xlsx ( $sheet, '签收轨迹导出' );
			exit ();
		}
		//分页
		$this->_view['list']=$select->limitPage(request('page',1),request( 'page_size', 25 ))
		->fetchPagination($this->_view['pagination'])
		->asArray()
		->getAll();
	}
	/**
	 * @todo   签收监控修改状态
	 * @author 吴开龙
	 * @since  2020-10-28
	 * @param  $type:1：已确认 2：移除;$id 表id
	 * @return json
	 * @link   #83413
	 */
	function actionSignType(){
		if(request('id')){
			//修改tb_routes表数据
			$rouets = Route::find('id=?',request('id'))->getOne();
			//修改状态
			$rouets->rouets_type = request('type');
			$rouets->save();
		}
		return $this->_redirectMessage('成功', '修改成功', url('/sign'));
	}
	/**
	 * 监控时效
	 */
	function actionRoute1(){
		$select=VRouteLatest::find(VRouteLatest::meta()->table->name.'.order_status <> ?',Order::STATUS_SIGN)
	               ->joinLeft(Channel::meta()->table->name,'', Channel::meta()->table->name.'.channel_id = '.VRouteLatest::meta()->table->name.'.channel_id');
	    if(request('network')){
	        if(request('network') == 'EMS'){
	            $select->where(Channel::meta()->table->name.'.network_code = ?',request('network'));
	            //时间
	            $select->where(
	                '('.VRouteLatest::meta()->table->name.'.consignee_country_code in (?) AND '.VRouteLatest::meta()->table->name.'.warehouse_out_time < ? )'
	                .' OR('.VRouteLatest::meta()->table->name.'.consignee_country_code in (?) AND '.VRouteLatest::meta()->table->name.'.warehouse_out_time < ? )'
	                ,VRouteLatest::$country_1,strtotime('-8 weekdays')
	                ,VRouteLatest::$country_2,strtotime('-20 weekdays')
	                );
	        }else{
	            $select->where(Channel::meta()->table->name.'.network_code = ?',request('network'))
	                   ->where(VRouteLatest::meta()->table->name.'.warehouse_out_time <?',strtotime('-4 weekdays'));
	        }
	    }else{
	        $select->where(
	            '('.VRouteLatest::meta()->table->name.'.consignee_country_code in (?) AND '.VRouteLatest::meta()->table->name.'.warehouse_out_time < ? AND '.Channel::meta()->table->name.'.network_code = ? )'
	            .' OR('.VRouteLatest::meta()->table->name.'.consignee_country_code in (?) AND '.VRouteLatest::meta()->table->name.'.warehouse_out_time < ? AND '.Channel::meta()->table->name.'.network_code = ? )'
	            .' OR('.VRouteLatest::meta()->table->name.'.warehouse_out_time < ? AND '.Channel::meta()->table->name.'.network_code in (?))'
	            ,VRouteLatest::$country_1,strtotime('-8 weekdays'),'EMS'
	            ,VRouteLatest::$country_2,strtotime('-20 weekdays'),'EMS'
	            ,strtotime('-4 weekdays'),array('UPS','FedEx','DHL')
	            );
	    }
	    $this->_view['list'] = $select->limitPage(request('page',1),request( 'page_size', 25 ))
	    ->order('time')
	    ->fetchPagination($this->_view['pagination'])
	    ->getAll();
	}
	/**
	 * 监控时效,出库5天以上
	 */
    function actionRoute2(){
	    $select=VRouteLatest::find('order_status !=?',Order::STATUS_SIGN)
	            ->joinLeft('tb_channel','','v_route_latest.channel_id=tb_channel.channel_id');
	    if(request('network')){
	        if(request('network')=='EMS'){//EMS
	           $select->where('tb_channel.network_code= ?',request('network'))
	                  ->where('(v_route_latest.consignee_country_code in (?) and v_route_latest.warehouse_out_time <?) or 
	                           (v_route_latest.consignee_country_code in (?) and v_route_latest.warehouse_out_time <?)',
	                            VRouteLatest::$country_1,strtotime('-12 weekdays'),
	                            VRouteLatest::$country_2,strtotime('-25 weekdays'));
	        }else{//UPS,FedEx,DHL
	           $select->where('tb_channel.network_code= ? and v_route_latest.warehouse_out_time <?',request('network'),strtotime('-5 weekdays'));
	        }
	    }else{
	        //全部
	        $select->where("(tb_channel.network_code= 'EMS' and (
	                        (v_route_latest.consignee_country_code in (?) and v_route_latest.warehouse_out_time <?) or 
	                        (v_route_latest.consignee_country_code in (?) and v_route_latest.warehouse_out_time <?))) or 
	                        (tb_channel.network_code!= 'EMS' and v_route_latest.warehouse_out_time <?)",
	                         VRouteLatest::$country_1,strtotime('-12 weekdays'),
	                         VRouteLatest::$country_2,strtotime('-25 weekdays'),
	                         strtotime('-5 weekdays'));
	    }
		$this->_view['list']=$select->limitPage(request('page',1),request( 'page_size', 25 ))
		->order('time')
		->fetchPagination($this->_view['pagination'])
		->getAll();
	}
	/*
	 * 轨迹自动忽略报表
	 */
	function actionRoute3(){
	    $select=Tracking::find('confirm_flag="2" and route_id<>"0" and tb_tracking.create_time>"1573001812"');
	    $select->joinLeft('tb_order', '','tb_order.order_id=tb_tracking.order_id');
	    $select->joinLeft('tb_channel', '','tb_order.channel_id=tb_channel.channel_id');
	    if(request('tracking_no')){
	        $select->where('tb_order.tracking_no =?',request('tracking_no'));
	    }
	    if(request('network_code','UPS')){
	        $select->where('tb_channel.network_code =?',request('network_code','UPS'));
	    }
	    if(request('channel_id')){
	        $select->where('tb_order.channel_id =?',request('channel_id'));
	    }
	    if(request('start_date')){
	    	$select->where('tb_tracking.trace_time >?',strtotime(request('start_date')));
	    }
	    if(request('end_date')){
	    	$select->where('tb_tracking.trace_time <?',strtotime(request('end_date')));
	    }
	    if(request("export")=='exportlist'){
	    	ini_set('max_execution_time', '0');
	    	ini_set('memory_limit', '2G');
	    	set_time_limit(0);
	    	$orders=clone $select;
	    	$lists=$orders->getAll();
	    	$header = array (
	    		'网络','渠道','运单号','轨迹内容','轨迹时间','类型'
	    	);
	    	$sheet = array (
	    		$header
	    	);
	    	foreach ($lists as $l){
	    		$order=Order::find('order_id = ?',$l->order_id)->getOne();
	    		$route=Route::find('id = ?',$l->route_id)->getOne();
	    		if($l->flag==2){
	    			$flag='无匹配';
	    		}elseif($l->flag==1){
	    			$flag='无时区';
	    		}else{
	    			$flag='人工';
	    		}
	    		$row =array(
	    			$order->channel->network_code,
	    			$order->channel->channel_name,
	    			"'".$route->tracking_no,
	    			$route->description,
	    			date('m-d H:i',$route->time),
	    			$flag	    			
	    		);
	    		$sheet [] = $row;
	    	}
	    	Helper_ExcelX::array2xlsx ( $sheet, '自动忽略报表' );
	    	exit ();
	    }
	    $list=$select->limitPage(request('page',1),request( 'page_size', 25 ))
	    ->order('tracking_id')
	    ->fetchPagination($this->_view['pagination'])
	    ->getAll();
	    $this->_view['list']=$list;
	}
	/**
	 * 重算收付列表
	 */
	function actionRecalculationcost(){
	    if(request_is_post()){
    	    $orders=Order::find()
    	    ->joinLeft('(select order_id,sum(amount) as receivable_fee from tb_fee where fee_type=1 group by order_id) as tb_receivable_fee', null,'tb_order.order_id=tb_receivable_fee.order_id')
    	    ->joinLeft("(select order_id,sum(amount) as pay_fee from tb_fee where fee_type=2 group by order_id) as tb_payment_fee", null,'tb_order.order_id=tb_payment_fee.order_id');
    	    if(request('type','warehouse_confirm_time')=='warehouse_confirm_time'){
    	        if(request("start_date")){
    	            $orders->where("warehouse_confirm_time >=?",strtotime(request("start_date").':00'));
    	        }
    	        if (request("end_date")){
    	            $orders->where("warehouse_confirm_time <=?",strtotime(request("end_date").':59'));
    	        }
    	    }else{
    	        if(request("start_date")){
    	            $orders->where("record_order_date >=?",strtotime(request("start_date").':00'));
    	        }
    	        if (request("end_date")){
    	            $orders->where("record_order_date <=?",strtotime(request("end_date").':59'));
    	        }	    
    	    }
    	    if(request('ali_order_no')){
    	       $order = explode("\r\n",request('ali_order_no'));
    	       $orders->where('ali_order_no in (?)',$order);
    	    }
    	    $orders->sum('tb_receivable_fee.receivable_fee','receivable_amount');
    	    $orders->sum('tb_payment_fee.pay_fee','payment_amount');
    	    $orders=$orders->group('ali_order_no')
    	    ->setColumns('tb_order.warehouse_confirm_time,tb_order.record_order_date,tb_order.ali_order_no,tb_order.order_id')
    	    ->order('warehouse_confirm_time desc')->order(' record_order_date desc')->getAll();
    	    foreach ($orders as $k => $o){
    	    	//收入
    	    	$fee = Fee::find('order_id=? and fee_type=1',$o['order_id'])->getAll();
    	    	$sum_fee = 0;
    	    	bcscale(2);
    	    	foreach($fee as $f){
    	    		if($f->currency != 'CNY'){
    	    			$sum_fee = bcadd(Helper_Quote::exchangeRate($o['warehouse_confirm_time'],$f->amount, $f->currency,0,'',$f->rate),$sum_fee);
    	    		}else{
    	    			$sum_fee=bcadd($f->amount,$sum_fee);
    	    		}
    	    	}
				$orders[$k]['receivable_amount'] = $sum_fee;
				//成本
				$fee = Fee::find('order_id=? and fee_type=2',$o['order_id'])->getAll();
				$sum_fee1 = 0;
				foreach($fee as $f){
					if($f->currency != 'CNY'){
						$sum_fee1 = bcadd(Helper_Quote::exchangeRate($o['record_order_date'],$f->amount, $f->currency,0,'',$f->rate),$sum_fee1);
					}else{
						$sum_fee1=bcadd($f->amount,$sum_fee1);
					}
				}
				$orders[$k]['payment_amount'] = $sum_fee1;
    	    }
    	    $this->_view['orders']=$orders;
	   }
	}
	/**
	 * 重新计算应收和应付
	 */
	function actionNewfee(){
	    if(request('order_ids')){
	    	set_time_limit(0);
	        $order_ids=explode(',', request('order_ids'));
	        $orders=Order::find('order_id in (?)',$order_ids)->getAll();
	        foreach ($orders as $order){            
	            
	            $fee_item_code = Helper_Array::toHashmap ( FeeItem::find ()->setColumns ( 'item_code,sub_code,item_name' )
	                ->asArray ()
	                ->getAll (), 'item_code' );
	            if(request('type') == '1' || request('type') == '3' ){
		            //重新计算应收
		            if(!$order->warehouse_confirm_time){
		                continue;
		            }
		            //包装-纸箱数量
		            $packing_box_quantity=0;
		            $packing_box_quantity_f=Fee::find("order_id=? and fee_item_code = 'logisticsExpressASP_EX0003' and fee_type= '1'",$order->order_id)->getOne();
		            if(!$packing_box_quantity_f->isNewRecord()){
		                $packing_box_quantity=$packing_box_quantity_f->quantity;
		            }
		            //包装-包裹袋费用
		            $packing_pak_quantity=0;
		            $packing_pak_quantity_f=Fee::find("order_id=? and fee_item_code = 'logisticsExpressASP_EX0002' and fee_type= '1'",$order->order_id)->getOne();
		            if(!$packing_pak_quantity_f->isNewRecord()){
		                $packing_pak_quantity=$packing_pak_quantity_f->quantity;
		            }
		            //异形包装费费用
		            $special_packing_quantity=0;
		            $special_packing_quantity_f=Fee::find("order_id=? and fee_item_code = 'logisticsExpressASP_EX00034' and fee_type= '1'",$order->order_id)->getOne();
		            if(!$special_packing_quantity_f->isNewRecord()){
		                $special_packing_quantity=$special_packing_quantity_f->quantity;
		            }
		            $quote = new Helper_Quote ();
		            //原始功能
		            	//$receivable = $quote->receivable ( $order, $order->weight_income_in, $packing_box_quantity, $packing_pak_quantity, $special_packing_quantity,$order->warehouse_confirm_time);
		            //菜鸟功能
		             if ($order->customer->customs_code=='ALCN'){
		            	$cainiaofee = new Helper_CainiaoFee();
		            	$receivable = $cainiaofee->receivable ( $order, $order->weight_income_in, $packing_box_quantity, $packing_pak_quantity, $special_packing_quantity,$order->warehouse_confirm_time);
		             }else{
		            	$receivable = $quote->receivable ( $order, $order->weight_income_in, $packing_box_quantity, $packing_pak_quantity, $special_packing_quantity,$order->warehouse_confirm_time);
		             } 
		             //#83414
		            //存在生效费用项无法计算
		            if(@$receivable['success']=='formulaerror'){
		            	$data['message']=$receivable['success'];
		            	echo json_encode($data);
		            	exit();
		            }
		            if(!count($receivable)){
		                continue;
		            }
		            //echo "<pre>";print_r($receivable);exit;
		            //Fee::meta ()->deleteWhere ( 'fee_type=1 and order_id=?', $order->order_id );
		            $orderfees=Fee::find('order_id=? and fee_type=1',$order->order_id)->asArray()->getAll();
		            $ofees=array();
		            foreach ($orderfees as $orderfee){	                    
		                if(!$orderfee['account_date']){
	                        Fee::meta()->deleteWhere('fee_id=?',$orderfee['fee_id']);
	                    }else if($orderfee['account_date'] > strtotime(Config::cbDate())){
	                        if($orderfee['invoice_no'] || $orderfee['voucher_no']){
	                            if(array_key_exists($orderfee['fee_item_code'],$ofees)){                              
	                                $ofees[$orderfee['fee_item_code']]['amount']=$ofees[$orderfee['fee_item_code']]['amount']+$orderfee['amount'];
	                            }else{
	                                $ofees[$orderfee['fee_item_code']]=$orderfee;
	                            }
	                        }else{
	                            Fee::meta()->deleteWhere('fee_id=?',$orderfee['fee_id']);
	                        }
	                            
	                    }else if($orderfee['account_date'] < strtotime(Config::cbDate())){
	                        if(array_key_exists($orderfee['fee_item_code'],$ofees)){
	                            $ofees[$orderfee['fee_item_code']]['amount']=$ofees[$orderfee['fee_item_code']]['amount']+$orderfee['amount'];
	                        }else{
	                            $ofees[$orderfee['fee_item_code']]=$orderfee;
	                        }
	                    }	                    	                
		            }
		            
		            QLog::log ( print_r ( $receivable, true ) );
		            foreach ( $receivable as $key => $value ) {
		                if ($value ['fee']) {
		                	//币种
		                	if(@$value['currency_code']){
		                		$currency_code = $value['currency_code'];
		                		$rate = $value['rate'];
		                	}else{
		                		$currency_code = 'CNY';
		                		$rate = 1;
		                	}
		                    $code=false;
	    	                foreach ($ofees as $of){
	    	                    //计算差额
	    	                    if($of['fee_item_code']== $fee_item_code [$key] ['sub_code']){
	    	                    	$exquote = new Helper_Quote ();
	    	                    	//重算的应付费用不是人民币，转换成人民币
	    	                    	if ($value['currency_code']!='CNY'){
	    	                    		$newamount = $exquote::exchangeRate($of['account_date'],$value['fee'],$value['currency_code'],0,'',$value['rate']);
	    	                    	}else{
	    	                    		$newamount = $value['fee'];
	    	                    	}
	    	                    	//原始应付费用不是人民币，转换成人民币
	    	                    	if ($of['currency']!='CNY'){
	    	                    		$pfamount = $exquote::exchangeRate($of['account_date'],$of['amount'],$of['currency'],0,'',$of['rate']);
	    	                    	}else{
	    	                    		$pfamount = $of['amount'];
	    	                    	}
	    	                    	//统一成人民币计算差额
	    	                    	$feeval=ceil(($newamount-$pfamount)*100)/100;
	    	                    	//$feeval=ceil(($value ['fee']-$of['amount'])*100)/100;
	    	                        //echo $fee_item_code [$key] ['sub_code'].'---'.$feeval;
	    	                        if($feeval != 0){ 	                            
	        	                        $fee = new Fee ( array (
	        	                            'order_id' => $order->order_id,
	        	                            'fee_type' => 1,
	        	                            'fee_item_code' => $fee_item_code [$key] ['sub_code'],
	        	                            'fee_item_name' => $fee_item_code [$key] ['item_name'],
	        	                            'quantity' => $value ['quantity'],
	        	                        	'amount' => $feeval,
	        	                        	'currency'=>'CNY',
	        	                        	'rate'=>'1',
	        	                            'account_date'=>time(),
	        	                        	'btype_id' => $order->customer_id
	        	                        ) );  
	        	                        $fee->save ();
	    	                        }
	    	                        $code=true;
	    	                    }
	    	                   
		                    }
		                   
		                    //保存新的费用
		                    if(!$code){
	    	                    $fee = new Fee ( array (
	    	                        'order_id' => $order->order_id,
	    	                        'fee_type' => 1,
	    	                        'fee_item_code' => $fee_item_code [$key] ['sub_code'],
	    	                        'fee_item_name' => $fee_item_code [$key] ['item_name'],
	    	                        'quantity' => $value ['quantity'],
	    	                    	'amount' => $value ['fee'],
	    	                    	'currency'=>$currency_code,
	    	                    	'rate'=>$rate,
	    	                        'account_date'=>$order->warehouse_out_time,
	    	                    	'btype_id' => $order->customer_id
	    	                    ) );
	    	                    $fee->save ();
		                    }
		                    
		                    
		                }
		            }
	            }
	            if(request('type') == '2' || request('type') == '3' ){
		            //重新计算应付
		            if(!$order->record_order_date){
		                continue;
		            }
		            $product=Product::find('product_name=?',$order->service_code)->getOne();
		            
		            //获取异形包装费
		            $special_fee_c_t=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
		            $special_count_c_t=0;
		            if(!$special_fee_c_t->isNewRecord()){
		                $special_count_c_t=$special_fee_c_t->quantity;
		            }
		            //查找渠道成本
		            $channelcost_c_t=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
		            if(!$channelcost_c_t->isNewRecord()){
		                $channelcostppr_c_t=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c_t->channel_cost_id,$order->record_order_date,$order->record_order_date)->getOne();
		                if(!$channelcostppr_c_t->isNewRecord()){
		                    $network_c_t=Network::find("network_code=? ",$order->channel->network_code)->getOne();
		                    $quote= new Helper_Quote();
		                    //原始功能
		                    	//$price_c_t=$quote->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t,$order->record_order_date);
		                    //菜鸟功能
		                     if ($order->customer->customs_code=='ALCN'){
		                    	$cainiaofee = new Helper_CainiaoFee();
		                    	$price_c_t=$cainiaofee->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t,$order->record_order_date);
		                    }else{
		                    	$price_c_t=$quote->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t,$order->record_order_date);
		                    } 
		                    //#83414
		                    //存在生效费用项无法计算
		                    if(@$price_c_t['success']=='formulaerror'){
		                    	$data['message']=$price_c_t['success'];
		                    	echo json_encode($data);
		                    	exit();
		                    }
		                    //print_r($price_c_t);exit;
		                    if (count($price_c_t)&&$price_c_t['total_single_weight']){
		                    	//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
		                    	$order->total_single_weight = $price_c_t['total_single_weight'];
		                    	$order->save();
		                    }
		                    if(count($price_c_t) && count($price_c_t['price_info'])){
		                        //删除应付费用
		                        $pfees=array();
		                        $orderfees=Fee::find('order_id=? and fee_type=2',$order->order_id)->asArray()->getAll();
		                        foreach ($orderfees as $orderfee){
		                            if(!$orderfee['account_date']){
		                                Fee::meta()->deleteWhere('fee_id=?',$orderfee['fee_id']);
		                            }else if($orderfee['account_date'] > strtotime(Config::cbDate())){
		                                if($orderfee['invoice_no'] || $orderfee['voucher_no']){
		                                    if(array_key_exists($orderfee['fee_item_code'],$pfees)){
		                                        $pfees[$orderfee['fee_item_code']]['amount']=$pfees[$orderfee['fee_item_code']]['amount']+$orderfee['amount'];
		                                    }else{
		                                        $pfees[$orderfee['fee_item_code']]=$orderfee;
		                                    }
		                                }else{
		                                    Fee::meta()->deleteWhere('fee_id=?',$orderfee['fee_id']);
		                                }
		                                
		                            }else if($orderfee['account_date'] < strtotime(Config::cbDate())){
		                                if(array_key_exists($orderfee['fee_item_code'],$pfees)){
		                                    $pfees[$orderfee['fee_item_code']]['amount']=$pfees[$orderfee['fee_item_code']]['amount']+$orderfee['amount'];
		                                }else{
		                                    $pfees[$orderfee['fee_item_code']]=$orderfee;
		                                }
		                            }
		                        }
		                        
		                        
		                        //Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll()->destroy();
		                        foreach ($price_c_t['price_info']['fee_item'] as $key=>$fee_item){
		                            //判断费用项中是否存在费用
		                            if($fee_item['fee']!='0'){
		                            	//币种
		                            	if(@$fee_item['currency_code']){
		                            		$currency_code = $fee_item['currency_code'];
		                            		$rate = $fee_item['rate'];
		                            	}else{
		                            		$currency_code = 'CNY';
		                            		$rate = 1;
		                            	}
		                                //获取fee_sub_code
		                                $code=false;
		                                $fee_sub_code=FeeItem::find('sub_code=?',$key)->getOne();
		                                foreach ($pfees as $pf){
		                                    //计算差额
		                                    if($pf['fee_item_code']== $fee_sub_code->sub_code){
		                                    	$exquote = new Helper_Quote ();
		                                    	//重算的应付费用不是人民币，转换成人民币
		                                    	if ($fee_item['currency_code']!='CNY'){
		                                    		$newamount = $exquote::exchangeRate($pf['account_date'],$fee_item['fee'],$fee_item['currency_code']);
		                                    	}else{
		                                    		$newamount = $fee_item['fee'];
		                                    	}
		                                    	//原始应付费用不是人民币，转换成人民币
		                                    	if ($pf['currency']!='CNY'){
		                                    		$pfamount = $exquote::exchangeRate($pf['account_date'],$pf['amount'],$pf['currency']);
		                                    	}else{
		                                    		$pfamount = $pf['amount'];
		                                    	}
		                                    	//统一成人民币计算差额
		                                    	$pfeeval=floor(($newamount-$pfamount)*100)/100;
		                                        //$pfeeval=floor(($fee_item['fee']-$pf['amount'])*100)/100;
		                                        if($pfeeval!=0){
	    	                                        $fee= new Fee();
	    	                                        $fee->changeProps(array(
	    	                                            'order_id'=>$order->order_id,
	    	                                            'fee_type'=>'2',
	    	                                            'fee_item_code'=>$fee_sub_code->sub_code,
	    	                                            'fee_item_name'=>$fee_sub_code->item_name,
	    	                                            'quantity'=>$fee_item['quantity'],
	    	                                            'account_date'=>time(),  	                                           
	    	                                        	'amount'=>$pfeeval,
	    	                                        	'currency'=>'CNY',
	    	                                        	'rate'=>'1',
	    	                                            'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $order->channel->supplier_id
	    	                                        ));
	    	                                        $fee->save ();
		                                        }
		                                        $code=true;
		                                    }
		                                    
		                                }
		                                //保存新的费用
		                                if(!$code){
		                                    $fee= new Fee();
		                                    $fee->changeProps(array(
		                                        'order_id'=>$order->order_id,
		                                        'fee_type'=>'2',
		                                        'fee_item_code'=>$fee_sub_code->sub_code,
		                                        'fee_item_name'=>$fee_sub_code->item_name,
		                                        'quantity'=>$fee_item['quantity'],
		                                    	'amount'=>$fee_item['fee'],
		                                    	'currency'=>$currency_code,
		                                    	'rate'=>$rate,
		                                        'account_date'=>$order->warehouse_out_time,
		                                        'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $order->channel->supplier_id
		                                    ));
		                                    $fee->save ();
		                                }
		                                
		                                
		                            }
		                        }
		                    }
		                }
		            }
	            }
	        }
	    }
	    die();
	}
	/*
	 * 导入账单，显示差异值
	 */
	function actioncompare(){
        ini_set ( 'max_execution_time', '0' );
	    if (request_is_post ()) {
	    	$currency = request('hiddencurrency');
	    	if(strlen($currency) == 0){
	    		return $this->_redirectMessage ( '数据错误', '请选择币种', url ( '/payment' ), 3 );
	    	}
	        $uploader = new Helper_Uploader ();
	        if ($uploader->existsFile ( 'file' )) {
	            $des_dir = Q::ini ( 'upload_tmp_dir' );
	            $file = $uploader->file ( 'file' );
	            $filename = 'ups_invoice_' . date ( 'YmdHis' ) . '.' . $file->extname ();
	            $file->move ( $des_dir . DS . $filename );
	            return $this->_redirect ( url ( '/compare', array (
	                'f' => $filename,'currency' => $currency
	            ) ) );
	        }
	    }
	    if (request ( 'f' )) {
	        ini_set("memory_limit", "3072M");
	        $filename = Q::ini ( 'upload_tmp_dir' ) . DS . request ( 'f' );
	        try {
	            $xls = Helper_Excel::readFile ( $filename );
	            $ppData = $xls->toHeaderMap ( );
	        } catch ( Exception $ex ) {
	            return $this->_redirectMessage ( '文件错误', '请确认您的excel文件是否正确', url ( '/compare' ), 3 );
	        }
	        //数据处理
	        $ppData = Helper_Array::sortByCol ( $ppData, '运单号码' );
	        $ppData = Helper_Array::groupBy ( $ppData, '运单号码' );
	        $newData=array();
	        $currency = request("currency");
	        foreach ( $ppData as $track_no => $rows) {
	            if(!$track_no){
	                continue;
	            }
	            $bill_amount='';
                foreach ($rows as $row){
                    $bill_amount += $row['费用(￥)'];
                    $weight_bill = $row['计费重量(KG)'];
                }
                $order=Order::find('tracking_no = ?',$track_no)->getOne();
                if($order->isNewRecord()){
                    $newData[]=array(
                        'ali_order_no'=>'',
                        'tracking_no'=>$track_no,
                        'weight_label'=>'',
                    	'currency'=>'',
                        'fee_amount'=>'',
                        'weight_bill'=>$weight_bill,
                        'bill_amount'=>$bill_amount,
                        'balance'=>'无订单'
                    );
                }else {
                    $order->weight_bill=$weight_bill;
                    $order->save();
                    $fee_amount=Fee::find('order_id = ? and fee_type="2" and currency = ?',$order->order_id,$currency)->getSum('amount');
                    $balance=$fee_amount-$bill_amount;
                    $newData[]=array(
                        'ali_order_no'=>$order->ali_order_no,
                        'tracking_no'=>$order->tracking_no,
                        'weight_label'=>$order->weight_label,
                    	'currency'=>$currency,
                        'fee_amount'=>$fee_amount,
                        'weight_bill'=>$order->weight_bill,
                        'bill_amount'=>$bill_amount,
                        'balance'=>$balance?$balance:''
                    );
                }
	        }
	        if (request ( 'export' )) {
                $tableHead = array (
	                '阿里订单号','运单号','预报重','币种','应付总金额','账单重','账单总金额','差异'
	            );
	            $newSheet = array (
	                $tableHead
	            );
	            foreach ($newData as $r){
	                $newSheet [] = $r;
	            }
	            Helper_Excel::array2xls ( $newSheet, '对账单核对结果' . date ( 'Ymd' ) . '.xls' );
	        }
	        $this->_view ['newData'] = $newData;
	        $this->_view ['currency'] = $currency;
       }
   }
   /*
    * EMS付款成本对账
    */
   function actionemscompare(){
       ini_set ( 'max_execution_time', '0' );
       if (request_is_post ()) {
            set_time_limit ( 0 );
            $uploader = new Helper_Uploader();
            //检查指定名字的上传对象是否存在
            if (! $uploader->existsFile ( 'file' )) {
                return $this->_redirectMessage('未上传文件','',url('statistics/emscompare'));
            }
            $file = $uploader->file ( 'file' );//获得文件对象
            if (! $file->isValid ( 'xls,xlsx' )) {
                return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('statistics/emscompare'));
            }
            $des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
            $filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
            $file->move ( $filename );
            ini_set ( "memory_limit", "3072M" );
            $xls = Helper_Excel::readFile ( $filename,true);
            $sheet =$xls->toHeaderMap ();
           //数据处理
           $newData=array();
           $diffData=array();
           $orderData=array();
           $tracking_no=array();
           $hiddenchannel_id=array();
                      
           if(request('hiddensupplier_idt')){
               $channels=Channel::find('supplier_id=?',request('hiddensupplier_idt'))->setColumns('channel_id')->asArray()->getAll();
               $supplier_id=request('hiddensupplier_idt');
               $hiddenchannel_id=Helper_Array::getCols($channels, 'channel_id');
               
           }
           
           if(request('hiddenchannel_id')){
               $supplier_id = Channel::find('channel_id=?',request('hiddenchannel_id'))->getOne()->supplier_id;
               $hiddenchannel_id[]=request('hiddenchannel_id');
           }
           
           if(!count($hiddenchannel_id)){
               $hiddenchannel_id='';
           }
           $currency = request("hiddencurrency");
           $track_no=Order::find('channel_id in (?)',$hiddenchannel_id)
           ->joinLeft('tb_fee', '','tb_fee.order_id=tb_order.order_id')
           ->where('fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$supplier_id,$currency)->setColumns('tracking_no')->asArray()->getAll();
           $track_no=Helper_Array::getCols($track_no, 'tracking_no');
           foreach ( $sheet as $rows) {
	           if(!$rows['邮件号']){
	           	   continue;
	           }
	           foreach ($track_no as $tr){
		           	if($tr==$rows['邮件号']){
		           		$tracking_no[]=$tr;
		           	}
	           }
           	   if(isset($rows['邮件号']) && isset($rows['计费重量(克)']) && isset($rows['邮资合计'])){
           	       $order=Order::find('tracking_no = ? and channel_id in (?)',$rows['邮件号'],$hiddenchannel_id)->getOne();
	               if($order->isNewRecord()){
	                   $newData[]=array(
	                       'tracking_no'=>$rows['邮件号'],
	                       'weight_bill'=>$rows['计费重量(克)']/1000,
	                       'bill_amount'=>$rows['邮资合计'],
	                   );
	               }else {
	                   $fee_count=Fee::find('tb_fee.order_id = ? and fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$order->order_id,$supplier_id,$currency)->getCount();
	           	   		if($fee_count > 0){
	           	   		   $fee_amount=Fee::find('tb_fee.order_id = ? and fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$order->order_id,$supplier_id,$currency)->getSum('amount');
	                       $balance=$fee_amount-$rows['邮资合计'];
	                       if($balance<>0){
	                           $diffData[]=array(
	                               'order_id'=>$order->order_id,
	                               'ali_order_no'=>$order->ali_order_no,
	                               'tracking_no'=>$order->tracking_no,
	                               'weight_label'=>$order->weight_cost_out,
	                           	   'currency'=>$currency,
	                               'weight_bill'=>$rows['计费重量(克)']/1000,
	                               'fee_amount'=>$fee_amount,
	                               'bill_amount'=>$rows['邮资合计'],
	                               'balance'=>$balance?$balance:''
	                           );
	                       }
	                       $order->weight_bill=$rows['计费重量(克)']/1000;
	                       $order->bill_amount=$rows['邮资合计'];
	                       $order->save();
	                   }
	               }
           	   }elseif(isset($rows['邮件号']) && isset($rows['重量(克)']) && isset($rows['总邮资'])) {
           	       $order=Order::find('tracking_no = ? and channel_id in (?)',$rows['邮件号'],$hiddenchannel_id)->getOne();
	           	   	if($order->isNewRecord()){
	           	   		$newData[]=array(
	           	   			'tracking_no'=>$rows['邮件号'],
	           	   			'weight_bill'=>$rows['重量(克)']/1000,
	           	   			'bill_amount'=>$rows['总邮资'],
	           	   		);
	           	   	}else {
	           	   		$fee_count=Fee::find('tb_fee.order_id = ? and fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$order->order_id,$supplier_id,$currency)->getCount();
	           	   		if($fee_count > 0){
	           	   			$fee_amount=Fee::find('tb_fee.order_id = ? and fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$order->order_id,$supplier_id,$currency)->getSum('amount');
	           	   			$balance=$fee_amount-$rows['总邮资'];
	           	   			if($balance<>0){
	           	   				$diffData[]=array(
	           	   					'order_id'=>$order->order_id,
	           	   					'ali_order_no'=>$order->ali_order_no,
	           	   					'tracking_no'=>$order->tracking_no,
	           	   					'weight_label'=>$order->weight_cost_out,
	           	   					'currency'=>$currency,
	           	   					'weight_bill'=>$rows['重量(克)']/1000,
	           	   					'fee_amount'=>$fee_amount,
	           	   					'bill_amount'=>$rows['总邮资'],
	           	   					'balance'=>$balance?$balance:''
	           	   				);
	           	   			}
	           	   			$order->weight_bill=$rows['重量(克)']/1000;
	           	   			$order->bill_amount=$rows['总邮资'];
	           	   			$order->save();
	           	   		}
	           	   	}
           	   }
           }
           $orderData=array_diff($track_no,$tracking_no);
           $this->_view ['orderData'] = $orderData;
           $this->_view ['newData'] = $newData;
           $this->_view ['diffData'] = $diffData;
           $this->_view ['currency'] = $currency;
       }
   }
   /*
    * 保存ems账单号
    */
   function actionsavebill(){
       $order_ids=request('order_ids');
       $bill_no=request('bill_no');
       $currency = request("currency");
       foreach ($order_ids as $order_id){
           $order=Order::find('order_id = ?',$order_id)->getOne();
           $order->weight_cost_out=$order->weight_bill;
           $order->weight_label=$order->weight_bill;
           $order->save();
           $fee_amount=Fee::find('order_id = ? and fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$order->order_id,$order->channel->supplier_id,$currency)->getSum('amount');
           $fees=Fee::find('order_id = ? and fee_type="2" and IFNULL(bill_no,"")="" and btype_id=? and currency=?',$order->order_id,$order->channel->supplier_id,$currency)->getAll();
           $i=0;
           $num=1;
           foreach ($fees as $fe){
               $fe->bill_no=$bill_no;
               $fe->save();
               $order_log= new OrderLog();
               $order_log->changeProps(array(
                   'order_id'=>$order->order_id,
                   'staff_id'=>MyApp::currentUser('staff_id'),
                   'staff_name'=>MyApp::currentUser('staff_name'),
                   'comment'=>'第'.$num.'应付费用,账单号 >'.$bill_no
               ));
               $order_log->save();
               $num++;
           }
           $num=1;
           foreach ($fees as $fee){
               if (!$fee->account_date || $fee->account_date>strtotime(Config::cbDate())){
                   if (trim($fee->invoice_no)=='' && trim($fee->voucher_no)==''){
                       $amount=$fee->amount;
                       $fee->amount +=($order->bill_amount-$fee_amount);
                       $fee->save();
                       $order_log= new OrderLog();
                       $order_log->changeProps(array(
                           'order_id'=>$order->order_id,
                           'staff_id'=>MyApp::currentUser('staff_id'),
                           'staff_name'=>MyApp::currentUser('staff_name'),
                           'comment'=>'第'.$num.'应付费用,金额 '.$amount.'>'.$fee->amount
                       ));
                       $order_log->save();
                       $i++;
                       break;
                   }
               }
               $num++;
           }
           if($i==0){
               $f=new Fee(array(
                   'order_id'=>$order->order_id,
                   'fee_type'=>'2',
                   'fee_item_code'=>'logisticsExpressASP_EX0001',
                   'fee_item_name'=>'基础运费',
               	   'currency'=>$currency,
                   'quantity'=>'1',
                   'amount'=>$order->bill_amount-$fee_amount,
                   'bill_no'=>$bill_no,
               	   'btype_id'=>$order->channel->supplier_id
               ));
               if(!Config::closeBalance() && $order->warehouse_out_time){
               		$f->account_date=$order->warehouse_out_time;
               }else {
               		$f->account_date=time();
               }
               $f->save();
               $order_log= new OrderLog();
               $order_log->changeProps(array(
                   'order_id'=>$order->order_id,
                   'staff_id'=>MyApp::currentUser('staff_id'),
                   'staff_name'=>MyApp::currentUser('staff_name'),
                   'comment'=>'新增一条应付,金额: '.$order->bill_amount-$fee_amount
               ));
               $order_log->save();
           }
       }
       return 'success';
   }
   /*
    * 更改地址报表
    */
   function actionEditaddressform(){
       $orders=Order::find("ali_testing_order!= '1'and address_change='1'");
       if(request('timetype')=='1'){
           if(request("start_date")){
               $orders->where("tb_order.create_time >=?",strtotime(request("start_date").' 00:00:00'));
           }
           if (request("end_date")){
               $orders->where("tb_order.create_time <=?",strtotime(request("end_date").' 23:59:59'));
           }
       }elseif (request('timetype')=='2'){
           if(request("start_date")){
               $orders->where("warehouse_out_time >=?",strtotime(request("start_date").' 00:00:00'));
           }
           if (request("end_date")){
               $orders->where("warehouse_out_time <=?",strtotime(request("end_date").' 23:59:59'));
           }
       }
     
       if(request("export")=='exportlist'){
           ini_set('memory_limit', '2G');
           set_time_limit(0);
           $list=clone $orders;
           $lists=$list->getAll();
           $header = array (
               '阿里订单号',
               '订单时间',
               '出库时间',
               '件数',
               '泛远单号',
               '网络',
               '末端运单号',
               '应收地址更改费用',
               '轨迹'
           );
           $sheet = array (
               $header
           );
           foreach ($lists as $value){
               $pakgesum = Orderpackage::find("order_id = ?",$value->order_id)->getSum('quantity');
               if($pakgesum<4){
                  $otherfee = $pakgesum *73 ;
               }else{$otherfee=280;}
               $row = array(
                   $value->ali_order_no,
                   Helper_Util::strDate('Y-m-d H:i', $value->create_time),
                   Helper_Util::strDate('Y-m-d H:i', $value->warehouse_out_time),
                   $pakgesum,
                   $value->far_no,
                   $value->channel->network_code,
                   $value->tracking_no,
                   $otherfee,
                   $value->address_change_info
               );
               $sheet [] = $row;
            }
           Helper_ExcelX::array2xlsx ( $sheet, '订单地址更改列表' );
           exit ();
       }
       $pagination = null;
       $list=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
       ->fetchPagination ( $pagination )
       ->order('order_id')->getAll();
       $this->_view['orders']=$list;
       $this->_view['pagination']=$pagination;
   }
   
   /*
    * 未上传照片预警
    */
   function actionPicture() {
   		// file有记录的order_id
       $select = Order::find('((far_warehouse_in_time>0) OR (warehouse_in_time >0))')
       ->where('is_picture=0 AND department_id in (?)',RelevantDepartment::relateddepartmentids()); 
       //部门搜索
       if(request('department_id')){
       	$select->where('department_id = ?',request('department_id'));
       }
       //排序条件
       $select ->order('create_time desc');
       //导出
       if(request('export') == 'exportlist'){
       	ini_set('max_execution_time', '0');
       	ini_set('memory_limit', '-1');
       	set_time_limit(0);
       	$list=clone $select;
       	$lists=$list->getAll();
       	//创建一个excel空文件 
       	Helper_ExcelX::startWriter ( 'no_picture_list'  );
       	$header = array (
       		'仓库',
       		'阿里订单号',
       		'订单时间',
       		'入库时间',
       		'网络',
       		'渠道'
       	);
       	
       	//写入表头 内容为$header,addRow为写入内容 
       	Helper_ExcelX::addRow ($header);
       	foreach ($lists as $order){
       		$time = $order->far_warehouse_in_time ? $order->far_warehouse_in_time : $order->warehouse_in_time;
       		$row =array(
       			$order->warehouse_name,
       			$order->ali_order_no,
       			Helper_Util::strDate('Y-m-d H:i:s', $order->create_time),
       			Helper_Util::strDate('Y-m-d H:i:s', $time),
       			$order->channel->network_code,
       			$order->channel->channel_name
       		);
       		Helper_ExcelX::addRow ( $row );
       	}
       	//写入结束
       	Helper_ExcelX::closeWriter ();
       	exit ();
       }
       //页面数据
       $this->_view['orders']= $select->limitPage(request('page',1),request ( 'page_size', 25 ))
       ->fetchPagination($this->_view['pagination'])
       ->getAll();
   }
   
   /*
    * 收款
    */
   function actionreceivablecost(){
       $fee_select = Fee::find ('fee_type = "1"');
       $fee_select->joinLeft ( 'tb_order', '*', 'tb_fee.order_id = tb_order.order_id' );
       $fee_select = $this->payOrderSelect ( $fee_select );
       $customer_ids = $fee_select->distinct ()
       ->setColumns ( 'btype_id' )
       ->order ( 'btype_id' )
       ->getAll ()
       ->getCols ( 'btype_id' );
       $filters = array ();
       foreach ( $customer_ids as $customer_id ) {
           $customer=Customer::find('customer_id = ?',$customer_id)->getOne();
           $filters [] = array (
               'filter_id' => $customer_id ?: '无',
               'filter_name' => $customer->customer ?: '无'
           );
       }
       $this->_view ['filters'] = $filters;
   }
   function actionreceivabletable(){
       $page = intval ( request ( 'page', 1 ) );
       $page_size = intval ( request ( 'page_size', 30 ) );
       $pagination = null;
       $order_select = Fee::find ('fee_type = "1"');
       $order_select->joinLeft('tb_order','', 'tb_fee.order_id = tb_order.order_id');
       $order_select = $this->payOrderSelect ( $order_select );
       $customer_id = '';
       if (request ( 'filter_id' )) {
           $customer_id = request ( 'filter_id' );
           if(request ( 'filter_id' ) == '全部'){
               $customer_id = 'all';
           }elseif (request ( 'filter_id' ) == '无') {
               $order_select->where ( 'ifnull(btype_id,0)=0' );
           } else {
               $order_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
           }
       }
       $temp_select = clone $order_select;
       $total_str = '';
       $total = $temp_select->group('currency')->sum ( "amount", "sum_should_balance" )->columns('currency')
       ->getAll ();
       if(count($total)>0){
       	  foreach ($total as $sum){
       	  	$total_str .= $sum['currency'].':'.sprintf('%.2f',$sum['sum_should_balance']).';';
       	  }
       }
       $fees = $order_select->limitPage ( $page, $page_size )
       ->fetchPagination ( $pagination )
       ->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
       ->getAll ();
       
       $currency = CodeCurrency::find()->asArray()->getAll();
       $fee_sum = 0;
       foreach($fees as $fee){
       	foreach ($currency as $curr){
       		if($fee['currency'] == $curr['code']){
       			if($fee['account_date'] >= $curr['start_date'] && $fee['account_date'] <= $curr['end_date']){
       				$fee_sum += $fee['amount'] * $curr['rate'];
       				continue 2;
       			}
       		}
       	}
       }
       $this->_view ['fee_sum'] = $fee_sum;
       $this->_view ['fees'] = $fees;
       $this->_view ['pagination'] = $pagination;
       $this->_view ["total"] = $total_str;
       $this->_view ["customer_id"] = @$customer_id;
   }
   function actionedit(){
       if (request_is_post ()) {
           $conn = QDB::getConn ();
           $conn->startTrans ();
           $fee_id = post ( 'fee_id' );
           //单选时 发票号 发票日期 开票日期都可以改
           //多选时 发票号为空时   不修改发票号跟发票日期
           $fees = Fee::find ( 'fee_id in (?)', $fee_id )->getAll ();
           //判断有几条数据
           if (count ( $fees ) > 0) {
               //一条
               if (count ( $fees ) == 1) {
                   foreach ( $fees as $fee ) {
                       //发票号改
                       $fee->invoice_no = post ( 'invoice_code' );
                       //账单抬头
                       $fee->waybill_title = post ( 'waybill_title' );
                       //如果凭证号不为空，凭证号赋值 销账日期也赋值
                       if (post ( 'voucher_code' )) {
                           $fee->voucher_no = post ( 'voucher_code' );
                           $fee->voucher_time = strtotime(date ( 'Y-m-d' ));
                       }else{
                           $fee->voucher_no = null;
                           $fee->voucher_time = null;
                       }
                       //开票日期赋值    备注赋值
                       $fee->invoice_time = strtotime( post ( 'billing_date' ));
                       $fee->remark = post ( 'remark' );
                       $fee->bill_no = post ( 'bill_no' );
                       //保存
                       $fee->save ();
                   }
               } else {
                   //多条数据
                   foreach ( $fees as $fee ) {
                       //账单抬头
                       if(post ( 'waybill_title' ) != null){
                           $fee->waybill_title = post ( 'waybill_title' );
                       }
                       //先判断发票号是否为空
                       if (post ( 'invoice_code' ) != null) {
                           //发票号改
                           $fee->invoice_no = post ( 'invoice_code' );
                       }
                       //判断开票日期是否为空
                       if (post ( 'billing_date' ) != null) {
                           $fee->invoice_time = strtotime( post ( 'billing_date' ));
                       }
                       //如果凭证号不为空，凭证号赋值 销账日期也赋值
                       if (post ( 'voucher_code' ) != null) {
                           $fee->voucher_no = post ( 'voucher_code' );
                           $fee->voucher_time = strtotime( date ( 'Y-m-d' ));
                       }
                       if (strlen(post('remark'))){
                           $fee->remark = post ( 'remark' );
                       }
                       if (strlen(post('bill_no'))){
                       	   $fee->bill_no = post ( 'bill_no' );
                       }
                       //保存
                       $fee->save ();
                   }
               }
           }
           $conn->completeTrans ();
           echo json_encode ( Fee::find ( 'fee_id in (?)', $fee_id )->asArray ()->getAll () );
           exit ();
       }
   }
   /**
    * 费用类型树结构
    */
   function actionfeetypetree(){
       $feetypes=FeeItem::find()->getAll();
       $arr=array();
       $checkeds = array ();
       if (request ( "checked" ) != null) {
           $checkeds = explode ( ",", request ( "checked" ) );
       }
       $array = array ();
       foreach($feetypes as $feetype){
           $array[]=array(
               "id" => $feetype->sub_code,
               "text" => $feetype->item_name,
               "checked" => in_array ( $feetype->sub_code, $checkeds ) ? "checked" : "",
               "attributes" => "",
           );
       }
       $arr [] = array (
           "id" => "all",
           "text" => "",
           "state" => request ( "state" ) != null && request ( "state" ) ? "" : "open",
           "checked" => in_array ( "all", $checkeds ) ? "checked" : "",
           "attributes" => "",
           "children" => $array
       );
       echo json_encode($arr);
       exit();
   }
   /**
    * 付款
    */
   function actionPay(){
       $fee_select = Fee::find ("tb_fee.fee_type = '2'");
       $fee_select->joinLeft ( 'tb_order', '', 'tb_fee.order_id = tb_order.order_id' );
       $fee_select->joinLeft('tb_channel','tb_channel.supplier_id','tb_channel.channel_id=tb_order.channel_id');
       $fee_select = $this->payOrderSelect($fee_select);
       	
       $supplier_ids = $fee_select->distinct ()
       ->setColumns ( 'btype_id' )
       ->order ( 'btype_id' )->asArray()
       ->getAll ();
       $filters = array ();
       foreach ( $supplier_ids as $supplier_id ) {
           $supplier=Supplier::find('supplier_id = ?',$supplier_id['btype_id'])->getOne();
           $filters [] = array (
               'filter_id' => $supplier_id['btype_id'] ?: '无',
               'filter_name' => $supplier->supplier ?: '无'
           );
       }
       $this->_view ['filters'] = $filters;
   }
   function actionPaytable(){
       $page =  request ( 'page', 1 ) ;
       $page_size = intval ( request ( 'page_size', 30 ) );
       $pagination = null;
       //精确计算结果
       bcscale(2);
       $fee_select = Fee::find ("fee_type = '2'");
       $fee_select->joinLeft ( 'tb_order', 'tb_order.ali_order_no,tb_order.tracking_no,tb_order.channel_id,tb_order.record_order_date', 'tb_fee.order_id = tb_order.order_id' );
       $fee_select->joinLeft ( 'tb_channel', 'tb_channel.supplier_id', 'tb_channel.channel_id = tb_order.channel_id' );
       $fee_select = $this->payOrderSelect ( $fee_select );
       $supplier_id = '';
       if (request ( 'filter_id' )) {
           $supplier_id = request ( 'filter_id' );
           if(request ( 'filter_id' ) == '全部'){
               $supplier_id = 'all';
           }elseif (request ( 'filter_id' ) == '无') {
               $fee_select->where ( 'ifnull(btype_id,0)=0' );
           } else {
               $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
           }
       }
       $temp_select = clone $fee_select;
       $total_str = '';
       $total = $temp_select->group('currency')->sum ( "amount", "sum_should_balance" )->columns('currency')
       ->getAll();
       if(count($total)>0){
       	  foreach ($total as $amount){
       	  	$total_str .= $amount['currency'].':'.sprintf('%.2f',$amount['sum_should_balance']).';';
       	  }
       }
       $this->_view ['total'] = $total_str;
   
       $fees = $fee_select->limitPage ( $page, $page_size )
       ->fetchPagination ( $pagination )
       ->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
       ->asArray ()
       ->getAll ();
   
       $currency = CodeCurrency::find()->asArray()->getAll();
       $fee_sum = 0;
       foreach($fees as $fee){	 
       	$fee_sum += bcmul( $fee['amount'],$fee['rate'] );	              	
       }
       $this->_view ['fee_sum'] = $fee_sum;
       $this->_view ['fees'] = $fees;
       $this->_view ['pagination'] = $pagination;
       $this->_view ['supplier_id'] = @$supplier_id;
   }
   function payOrderSelect(QDB_Select $order_select) {
       if(request('timetype','1')=='1'){
           $order_select->where ( "tb_order.warehouse_out_time >= ?", strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ) );
           $order_select->where ( "tb_order.warehouse_out_time <= ?", strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ) );
       }elseif(request('timetype','1')=='2') {
           $order_select->where ( "tb_order.create_time >= ?", strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ) );
           $order_select->where ( "tb_order.create_time <= ?", strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ) );
       }
       if (request ( 'far_no' )) {
           $order_select->where ( 'tb_order.far_no = ?', request ( 'far_no' ) );
       }
       if(request('supplier_id')){
           $order_select->where('tb_fee.btype_id = ?',request('supplier_id'));
       }
       if(request('channel_id')){
           $order_select->where('tb_order.channel_id = ?',request('channel_id'));
       }
       if(request('customer_id')){
           $order_select->where('tb_fee.btype_id = ?',request('customer_id'));
       }
       if(request('product_name')){
           $order_select->where('tb_order.service_code = ?',request('product_name'));
       }
       if(request('currency')){
       	$order_select->where('tb_fee.currency = ?',request('currency'));
       }
       if(request ( "fee_type" )){
    	  $order_select->where ( "tb_fee.fee_item_code in (?)", request ( "fee_type" ));
       }else {
    	  if (request ( "fee_type_" )) {
    	      $order_select->where ( "tb_fee.fee_item_code in (?)", Q::normalize (request ( "fee_type_" )));
    	  }
       }
       if(request('ali_order_no')){
          $ali_order_no = explode("\r\n", request('ali_order_no'));
          $ali_order_no=array_filter($ali_order_no);//去空
          $ali_order_no=array_unique($ali_order_no);//去重
          $order_select->where('tb_order.ali_order_no in (?)',$ali_order_no);
       }
       if(request('tracking_no')){
          $tracking_no = explode("\r\n", request('tracking_no'));
          $tracking_no=array_filter($tracking_no);//去空
          $tracking_no=array_unique($tracking_no);//去重
          $order_select->where('tb_order.tracking_no in (?)',$tracking_no);
       }
       if(request('invoice_no')){
           $invoice_no = explode("\r\n", request('invoice_no'));
           $invoice_no=array_filter($invoice_no);//去空
           $invoice_no=array_unique($invoice_no);//去重
           $order_select->where('tb_fee.invoice_no in (?)',$invoice_no);
       }
       if(request('voucher_no')){
           $voucher_no = explode("\r\n", request('voucher_no'));
           $voucher_no=array_filter($voucher_no);//去空
           $voucher_no=array_unique($voucher_no);//去重
           $order_select->where('tb_fee.voucher_no in (?)',$voucher_no);
       }
       if(request ( 'bill_no' )) {
		  $bill_no = explode ( "\r\n", request ( 'bill_no' ) );
		  $bill_no = array_filter ( $bill_no ); //去空
		  $bill_no = array_unique ( $bill_no ); //去重
		  $order_select->where ( 'tb_fee.bill_no in (?)', $bill_no );
	   }
       if (request('status','0') == 0) {
		   $order_select->where ( "(tb_fee.voucher_no = '' OR tb_fee.voucher_no IS NULL)" );
	   } else if (request('status','0') == 1) {
		   $order_select->where ( "(tb_fee.voucher_no != '' AND tb_fee.voucher_no IS NOT NULL)" );
	   }
       return $order_select;
   }
   function actionOffsedit(){
       if (request_is_post ()) {
           $conn = QDB::getConn ();
           $conn->startTrans ();
           try {
	           $fee_id = post ( 'fee_id' );
	           //单选时 发票号 发票日期 开票日期都可以改
	           //多选时 发票号为空时   不修改发票号跟发票日期
	           $fees = Fee::find ( 'fee_id in (?)', $fee_id )->getAll ();
	           //QLog::log('num:'.count($fees));
	           //判断有几条数据
	           if (count ( $fees ) > 0) {
	               //一条
	               if (count ( $fees ) == 1) {
	                   foreach ( $fees as $fee ) {
	                       //发票号改
	                       $fee->invoice_no = post ( 'invoice_code' );
	                       //账单抬头
	                       $fee->waybill_title = post ( 'waybill_title' );
	                       //如果凭证号不为空，凭证号赋值 销账日期也赋值
	                       if (post ( 'voucher_code' )) {
	                           $fee->voucher_no = post ( 'voucher_code' );
	                           $fee->voucher_time = strtotime(date ( 'Y-m-d' ));
	                       }else{
	                           $fee->voucher_no = null;
	                           $fee->voucher_time = null;
	                       }
	                       //开票日期赋值    备注赋值
	                       $fee->invoice_time = strtotime( post ( 'billing_date' ));
	                       $fee->remark = post ( 'remark' );
	                       $fee->bill_no = post ( 'bill_no' );
	                       //保存
	                       $fee->save ();
	                   }
	               } else {
	                   //多条数据
	               	
	                   foreach ( $fees as $fee ) {
	                       //账单抬头
	                       if(post ( 'waybill_title' ) != null){
	                       	
	                           $fee->waybill_title = post ( 'waybill_title' );
	                       }
	                       //先判断发票号是否为空
	                       if (post ( 'invoice_code' ) != null) {
	                           //发票号改
	                           $fee->invoice_no = post ( 'invoice_code' );
	                       }
	                       //判断开票日期是否为空
	                       if (post ( 'billing_date' ) != null) {
	                           $fee->invoice_time = strtotime( post ( 'billing_date' ));
	                       }
	                       //如果凭证号不为空，凭证号赋值 销账日期也赋值
	                       if (post ( 'voucher_code' ) != null) {
	                           $fee->voucher_no = post ( 'voucher_code' );
	                           $fee->voucher_time = strtotime( date ( 'Y-m-d' ));
	                       }
	                       if (strlen(post('remark'))){
	                           $fee->remark = post ( 'remark' );
	                       }
	                       if (strlen(post('bill_no'))){
	                       	   $fee->bill_no = post ( 'bill_no' );
	                       }
	                       
	                       //保存
	                       $fee->save ();
	                   }
	               }
	           }
           } catch ( Exception $ex ) {
           	QLog::log ('wkl:'. print_r ( $ex, true ) );
           	QDB::getConn ()->setTransFailed ();
           	QDB::getConn ()->completeTrans ();
           	echo $ex->getMessage;
           	exit ();
           }
           $conn->completeTrans ();
           echo json_encode ( count($fees) );
           exit ();
       }
   }
   function actionPayexport(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
	    $fee_select = Fee::find ("fee_type = '2'");
	    $fee_select = $this->payOrderSelect ( $fee_select );
	    $fee_select->joinLeft( 'tb_order', 'tb_order.ali_order_no,tb_order.tracking_no,tb_order.record_order_date,tb_order.create_time as order_create_time,tb_order.warehouse_out_time', 'tb_fee.order_id = tb_order.order_id' );
	    $fee_select->joinLeft('tb_channel','tb_channel.supplier_id','tb_channel.channel_id = tb_order.channel_id');
	    if (request ( 'filter_id' )) {
            if (request ( 'filter_id' ) == '无') {
                $fee_select->where ( 'ifnull(btype_id,0)=0' );
            }elseif (request ( 'filter_id' ) == '全部'){
            	
            } else {
                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
            }
	    }
	    $fee_select=$fee_select->setColumns('fee_id')->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
	    ->asArray()->all ()->getQueryHandle ();
	    
	    Helper_ExcelX::startWriter ( 'export_orders' );
	    $header = array (
	    	'ID','发件日','订单号','运单号','类型','币种','应付金额','汇率','本位币金额','发票号','开票日期','凭证号','销账日期','账单抬头','备注','对账状态'
	    );
	    Helper_ExcelX::addRow ($header);
	    $tmp_order_ids = array ();
	    while ( ($row = $fee_select->fetchRow ()) != false ) {
// 	    	echo "<pre>";
// 	    	print_r($fee_select->fetchRow ());
// 	    	exit;
	    	$tmp_order_ids [] = $row ['fee_id'];
	    	if (count ( $tmp_order_ids ) == '1000') {
	    		$this->orderFeeListAddRow ( $tmp_order_ids );
	    		$tmp_order_ids = array ();
	    	}
	    }
	    if (count ( $tmp_order_ids )) {
	    	$this->orderFeeListAddRow ( $tmp_order_ids );
	    }
	    Helper_ExcelX::closeWriter ();
	    exit ();
	}
	function orderFeeListAddRow($order_ids = array()) {
		if (! count ( $order_ids )) {
			return;
		}
		$fees = Fee::find('fee_id in (?)',$order_ids)->getAll();
		foreach ($fees as $fee){
			$order_id[] = $fee->order_id;
		}
		$orders = Order::find('order_id in (?)',$order_id)->setColumns('order_id,tb_order.ali_order_no,tb_order.tracking_no,tb_order.record_order_date')->asArray ()->getAll();
		$order_id_to_orders = Helper_Array::toHashmap ( $orders, 'order_id' );
		
		bcscale(2);
		foreach ($fees as $value){
// 			$curr = CodeCurrency::find('code=? and start_date<=? and end_date>=?',$value['currency'],$value['account_date'],$value['account_date'])->getOne();
			$sheet =array(
				$value['fee_id'],
				Helper_Util::strDate('Y-m-d', $order_id_to_orders[$value['order_id']]['record_order_date']),
				$order_id_to_orders[$value['order_id']]['ali_order_no'],
				"'".$order_id_to_orders[$value['order_id']]['tracking_no'],
				$value['fee_item_name'],
				$value['currency'],
				$value['amount'],
				$value['rate']? $value['rate'] : '未设置',
				bcmul($value['amount'],$value['rate']),
				$value['invoice_no'],
				Helper_Util::strDate('Y-m-d', $value['invoice_time']),
				$value['voucher_no'],
				Helper_Util::strDate('Y-m-d', $value['voucher_time']),
				$value['waybill_title'],
				$value['remark'],
				Fee::$s_recon[$value['recon_state']]
			);
			Helper_ExcelX::addRow ( $sheet );
		}
	}
	function actionReceivabletableExport(){
	    set_time_limit(0);
	    ini_set('memory_limit', '-1');
	    $fee_select = Fee::find ("fee_type = '1'");
	    $fee_select = $this->payOrderSelect ( $fee_select );
	    $fee_select->joinLeft( 'tb_order', 'tb_order.ali_order_no,tb_order.tracking_no,tb_order.record_order_date,tb_order.create_time as order_create_time,tb_order.warehouse_out_time', 'tb_fee.order_id = tb_order.order_id' );
	    if (request ( 'filter_id' )) {
            if (request ( 'filter_id' ) == '无') {
                $fee_select->where ( 'ifnull(btype_id,0)=0' );
            }elseif (request ( 'filter_id' ) == '全部'){
            	
            } else {
                $fee_select->where ( 'btype_id = ?', request ( 'filter_id' ) );
            }
	    }
	    $fee_select=$fee_select->order ( 'tb_order.order_id desc,tb_fee.create_time desc' )
	    ->asArray()->getAll ();
	    $header = array (
	    	'ID','发件日','订单号','运单号','类型','币种','应付金额','汇率','本位币金额','发票号','开票日期','凭证号','销账日期','账单抬头','备注'
	    );
	    $sheet = array (
	    	
	        $header
	    );
	    foreach ($fee_select as $value){
	        //查询渠道
// 	        $account_date = $value['account_date'];
// 	        $rate = CodeCurrency::find('code=? and start_date<? and end_date>?',$value['currency'],$account_date,$account_date)->getOne()->rate;
// 	        if($value['currency']=='CNY'){
// 	            $rate = 1;
// 	        }
	    	bcscale(2);
// 	    	$curr = CodeCurrency::find('code=? and start_date<=? and end_date>=?',$value['currency'],$value['account_date'],$value['account_date'])->getOne();
	        $sheet [] =array(
	        	$value['fee_id'],
	            Helper_Util::strDate('Y-m-d', $value['record_order_date']),
	            $value['ali_order_no'],
	            "'".$value['tracking_no'],
	            $value['fee_item_name'],
	        	$value['currency'],
	        	$value['amount'],
	        	$value['rate']? $value['rate'] : '未设置',
	        	bcmul($value['amount'],$value['rate']),
	            $value['invoice_no'],
	            Helper_Util::strDate('Y-m-d', $value['invoice_time']),
	            $value['voucher_no'],
	            Helper_Util::strDate('Y-m-d', $value['voucher_time']),
	            $value['waybill_title'],
	            $value['remark'],
	        );
	    }
	    Helper_ExcelX::array2xlsx ( $sheet, '收款' );
	    exit();
	}
	//拆分金额
	function actionSplit(){
	    if(request("fee_id") && request("originamount") && request("nowamount")){
	       $fee = Fee::find('fee_id = ?',request("fee_id"))->getOne();
	       if(!$fee->isNewRecord()){
              //拆分后金额
              $amount = request("originamount")-request("nowamount");
              $fee->amount = $amount;
              $fee->remark = '拆分后金额'.$amount;
              $fee->save();
              //成本、收入
              $type = '';
              if($fee->fee_type == '1'){
                 $type = '收入';
              }elseif($fee->fee_type == '2'){
                 $type = '成本';
              }
              $newfee = new Fee();
              $newfee->order_id = $fee->order_id;
              $newfee->fee_type = $fee->fee_type;
              $newfee->fee_item_code = $fee->fee_item_code;
              $newfee->fee_item_name = $fee->fee_item_name;
              $newfee->quantity = $fee->quantity;
              $newfee->amount = request('nowamount');
              $newfee->currency = $fee->currency;
              $newfee->remark = '新建'.$type.'金额'.request('nowamount');
              $newfee->account_date = $fee->account_date;
              $newfee->btype_id = $fee->btype_id;
              $newfee->save();
	       }
	       echo 'true';
	    }else{
	        echo 'false';
	    }
	    exit();
	}
	//条件查询
	function actionSearch(){
	    set_time_limit(0);
	    if(request('voucher_no') || request('invoice_no') || request('waybill_title') || request('invoice_nos')){
	       $pagination=null;
	       $customer = Helper_Array::toHashmap(Customer::find()->asArray()->getAll(),'customer_id','customer');
	       $supplier = Helper_Array::toHashmap(Supplier::find()->asArray()->getAll(),'supplier_id','supplier');
	       $dpt = Helper_Array::toHashmap(Department::find()->asArray()->getAll(),'department_id','department_name');
	       $fee = Fee::find();
    	   if(request('voucher_no')){
    	      $fee->where('voucher_no like ?','%'.request('voucher_no').'%');
    	   }
    	   if(request('invoice_no')){
    	      $fee->where('invoice_no like ?','%'.request('invoice_no').'%');
    	   }
    	   if(request('waybill_title')){
    	      $fee->where('waybill_title like ?','%'.request('waybill_title').'%');
    	   }
    	   if(request('fee_type')){
    	      $fee->where('fee_type = ?',request('fee_type'));
    	   }
    	   if(request('invoice_no') == ''){
        	   if(request('invoice_nos')){
        	      $invoicecodesArray = explode ( "\r\n", request('invoice_nos') );
        	      $invoicecodesArray = array_filter(array_unique ( $invoicecodesArray ));
        	      $fee->where("invoice_no in (?)",$invoicecodesArray);
        	   }
    	   }
           $exportfee = clone $fee;
    	   if(request('export')=='export'){
        	  $exportfeelist = $exportfee->getAll();
        	  //添加签收日
    	      $header = array (
    			'类型','运单号','发件日','登帐日期','签收日','客户/供应商','部门','金额','费用项目','发票号','发票日期','凭证号','凭证日期','账单抬头' 
        	  );
        	  $sheet = array (
        		 $header 
        	  );
        	  $total = 0;
        	  foreach ( $exportfeelist as $value ) {
        		$sheet [] = array (
        		    $value->fee_type == '1'?'应收':'应付',
        		    $value->order->tracking_no,
        		    Helper_Util::strDate('Y-m-d',$value->order->record_order_date),
        			Helper_Util::strDate('Y-m-d',$value->account_date),
        			Helper_Util::strDate('Y-m-d',$value->order->delivery_time),
        		    $value->fee_type == '1'?@$customer[$value->order->customer_id]:@$supplier[$value->order->channel->supplier_id],
        		    @$dpt[$value->order->department_id],
        		    $value->amount,
        		    $value->fee_item_name,
        		    $value->invoice_no,
        		    Helper_Util::strDate('Y-m-d',$value->invoice_time),
        		    $value->voucher_no,
        		    Helper_Util::strDate('Y-m-d',$value->voucher_time),
        		    $value->waybill_title,
        		);
        	    $total += $value->amount;
        	  }
        	  $sheet [] = array (
        		'总计','','','','','',$total 
        	  );
        	  Helper_ExcelX::array2xlsx ( $sheet, '条件查询' );
    		  exit ();
		   }elseif(request('export')=='export1'){
        	  $exportfeelist = $exportfee->getAll();
    	      $header = array (
    	      	'运单号','部门','发件日','签收日','客户/供应商','发票抬头','金额','目的地','发票号'
        	  );
        	  $sheet = array (
        		 $header 
        	  );
        	  $total = 0;
        	  foreach ( $exportfeelist as $value ) {
        		$sheet [] = array (
        		    $value->order->tracking_no,
        		    @$dpt[$value->order->department_id],
        		    Helper_Util::strDate('Y-m-d',$value->order->record_order_date),
        			Helper_Util::strDate('Y-m-d',$value->order->delivery_time),
        		    $value->fee_type == '1'?@$customer[$value->order->customer_id]:@$supplier[$value->order->channel->supplier_id],
        		    $value->waybill_title,
        		    $value->amount,
        		    $value->order->consignee_country_code,
        		    $value->invoice_no,
        		    
        		);
        	    $total += $value->amount;
        	  }
        	  $sheet [] = array (
        		'总计','','','','',$total 
        	  );
        	  Helper_ExcelX::array2xlsx ( $sheet, '条件查询1' );
    		  exit ();
		   }
    	   $paysum = clone $fee;
    	   $receivesum = clone $fee;
    	   $paysum = $paysum->where('fee_type = "2"')->getSum('amount');
    	   $receivesum = $receivesum->where('fee_type = "1"')->getSum('amount');
    	   $sum = $paysum+$receivesum;
    	   $feelist = $fee->limitPage ( request ( "page", 1 ), request ( 'page_size', 20 ) )
    	              ->fetchPagination ( $pagination )->getAll();
    	   $this->_view ['pagination'] = $pagination;
    	   $this->_view['pay']=$paysum;
    	   $this->_view['receive']=$receivesum;
    	   $this->_view['sum']=$sum;
    	   $this->_view['list']=$feelist;
    	   $this->_view['customer']=$customer;
    	   $this->_view['supplier']=$supplier;
    	   $this->_view['dpt']=$dpt;
	    }
	}
	
	//获取产品渠道
	function actiongetemailtemplate(){
		$order = Order::find('ali_order_no = ?',request('ali_order_no'))->getOne();
		$emailtemplate = EmailTemplate::find('product_id = ?',$order->service_product->product_id)->order('template_name asc')->getAll()->toHashMap('id','template_name');
		$data = array();
		foreach ($emailtemplate as $key => $e){
			$data[] =array(
				'id'=>$key,
				'template_name'=>$e
			);
		}
		return json_encode($data);
	}
	
	//导入成本费用
	function actionoutfee(){
		ini_set ( 'max_execution_time', '0' );
		if (request_is_post ()) {
			set_time_limit ( 0 );
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('statistics/pay'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('statistics/pay'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
			$file->move ( $filename );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $filename,true);
			$sheets =$xls->toHeaderMap ();
			$sheet = Helper_Array::groupBy($sheets, '阿里单号');
			Helper_Array::removeEmpty ( $sheet );
			$error = array ();
			//必填字段
			$required_fields = array (
				'阿里单号',
				'费用名称',
				'金额',
			);
			$currency = request("hiddenoutcurrency");
			$curr = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$currency,time(),time())->getOne();
			if($curr->isNewRecord()){
				return $this->_redirectMessage ( '币种不存在或已过期', '失败', url ( 'statistics/pay' ), 3 );
			}
			foreach ( $sheets as $k => $row ) {
				//判断基础信息不得为空
				foreach ( $required_fields as $field ) {
					if (empty ( $row [$field] )) {
						$error [$k] [$field] = '必填数据不可为空';
					}
				}
				if (! empty ( $row ['阿里单号'] )) {
					$order = Order::find('ali_order_no = ?',$row ['阿里单号'])->getOne();
					if($order-> isNewRecord()){
						$error[$k]['阿里单号'] = $row ['阿里单号'].'订单不存在';
					}
				}
				if (! empty ( $row ['费用名称'] )) {
					$fee_item = FeeItem::find('item_name = ?',$row['费用名称'])->getOne();
					if($fee_item -> isNewRecord()){
						$error[$k]['费用名称'] = $row['费用名称'].'不存在';
					}
				}
			}
			$this->_view ['errors'] = $error;
			
			if (empty ( $error )) {
				foreach ( $sheet as $key => $rows) {
					$order = Order::find('ali_order_no = ?',$key)->getOne();
					$channel = Channel::find('channel_id = ?',$order->channel_id)->getOne();
					$rate = CodeCurrencyItem::getCurrencyRate($currency,time(), $channel->supplier_id);
					$fees = Fee::find('order_id = ? and fee_type ="2" and btype_id = ?',$order->order_id,request('hiddensupplier_id'))->getAll();
					foreach ($fees as $fee){
						if($fee->fee_item_name == '服务费'){
							continue;
						}
					    if (strlen($fee->voucher_no>0) || strlen($fee->invoice_no>0) || (strlen($fee->account_date)>0 && $fee->account_date<strtotime(Config::cbDate()))){
							$newfee = new Fee(array(
								'order_id' => $fee->order_id,
								'fee_type' => $fee->fee_type,
								'fee_item_code' => $fee->fee_item_code,
								'fee_item_name' => $fee->fee_item_name,
								'currency' => $fee->currency,
								'rate' => $fee->rate,
								'quantity' => $fee->quantity,
								'amount' => $fee->amount>0? $fee->amount*-1 : abs($fee->amount),
								'account_date' => time(),
								'btype_id' => $fee->btype_id
							));
							$newfee->save();
						}else {
							Fee::meta()->destroyWhere('fee_id = ?',$fee->fee_id);
						}
					}
					foreach ($rows as $row){
						$fee_item = FeeItem::find('item_name = ?',$row['费用名称'])->getOne();
						$newfee = new Fee(array(
							'order_id' => $order->order_id,
							'fee_type' => '2',
							'fee_item_code' => $fee_item->item_code,
							'fee_item_name' => $fee_item->item_name,
							'currency' => $currency,
							'rate' => $rate,
							'quantity' => '1',
							'amount' => $row['金额'],
							'account_date' => time(),
							'btype_id' => request('hiddensupplier_id')
						));
						$newfee->save();
					}
				}
				 return $this->_redirectMessage ( '导入成本费用', '成功', url ( 'statistics/pay' ), 3 );
			}
		}
	}
	/**
	 * @todo 导入差额
	 * @author 吴开龙
	 * @since 2020-6-11 17:39:53
	 * @param 
	 * @return 
	 * @link #80260
	 */
	function actionDifference(){
		ini_set ( 'max_execution_time', '0' );
		if (request_is_post ()) {
			set_time_limit ( 0 );
			//上传文件开始
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('statistics/pay'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('statistics/pay'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
			$file->move ( $filename );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $filename,true);
			$sheets =$xls->toHeaderMap ();
			//上传文件结束
			$error = array ();
			//必填字段
			$required_fields = array (
				'阿里单号',
				'费用名称',
				'金额',
				'币种',
				//'登账日期'
			);
			foreach ( $sheets as $k => $row ) {
				//判断基础信息不得为空
				//print_r(strtotime($row['登账日期']));exit;
				if(empty ( $row ['阿里单号'])){
					continue;
				}
				foreach ( $required_fields as $field ) {
					if (empty ( $row [$field] )) {
						$error [$k] [$field] = '必填数据不可为空';
					}
				}
				if (! empty ( $row ['阿里单号'] )) {
					$order = Order::find('ali_order_no = ?',$row ['阿里单号'])->getOne();
					if($order-> isNewRecord()){
						$error[$k]['阿里单号'] = $row ['阿里单号'].'订单不存在';
					}
				}
				if (! empty ( $row ['费用名称'] )) {
					$fee_item = FeeItem::find('item_name = ?',$row['费用名称'])->getOne();
					if($fee_item -> isNewRecord()){
						$error[$k]['费用名称'] = $row['费用名称'].'不存在';
					}
				}
				if (! empty ( $row ['币种'] )) {
					$cuu = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$row['币种'],time(),time())->getOne();
					if($cuu -> isNewRecord()){
						$error[$k]['币种'] = $row['币种'].'不存在';
					}
				}
				/* if(!empty ( $row ['登账日期'] ) && strlen(strtotime($row ['登账日期']))!=10){
					$error[$k]['登账日期']= '时间格式不正确，请设置单元格格式为文本类型';
				} */
			}
			//错误输出
			$this->_view ['errors'] = $error;
			if (empty ( $error )) {
				//循环添加数据
				foreach ( $sheets as $key => $row) {
// 					$newamount=0;
					$order = Order::find('ali_order_no = ?',$row['阿里单号'])->getOne();
					$channel = Channel::find('channel_id = ?',$order->channel_id)->getOne();
					$fee_item = FeeItem::find('item_name = ?',$row['费用名称'])->getOne();
					//$date=$order->record_order_date?$order->record_order_date:$order->warehouse_out_time;
// 					if ($row['币种']!='CNY'){
// 						$newamount = Helper_Quote::exchangeRate(time(),$row['金额'], $row['币种']);
// 					}else{
// 						$newamount = $row['金额'];
// 					}
					$rate=1;
					$remark = '';
					if($row['币种'] != 'CNY'){
						//$rate = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$row['币种'],time(),time())->getOne()->rate;
						$rate = CodeCurrencyItem::getCurrencyRate($row['币种'],time(), $channel->supplier_id);
						if(!$rate){
							$rate = 1;
							$remark = '币种不存在或已过期';
						}
					}
					$newfee = new Fee(array(
						'order_id' => $order->order_id,
						'fee_type' => '2',
						'fee_item_code' => $fee_item->sub_code,
						'fee_item_name' => $fee_item->item_name,
						'currency' => $row['币种'],
						'quantity' => '1',
						'rate' => $rate,
						'remark' => $remark,
						'amount' => $row['金额'],
						'account_date' =>time(),
						'btype_id' => request('hiddensupplier_id')
					));
					$newfee->save();
				}
				return $this->_redirectMessage ( '导入差额', '成功', url ( 'statistics/pay' ), 3 );
			}
		}
	}
	
	/**
	 * @todo 监控事件
	 * @author 许杰晔
	 * @since 2020-6-18
	 * @param
	 * @return
	 */
	function actionEvent(){
		$pagination = null;
		//监控事件 
		$select = Event::find('confirm_flag = "1" and tb_event.customer_id = 1  and ((send_flag="0" and tb_event.send_times="2") or send_flag="2") and tb_event.create_time>"1582992000" and status=0')
				  ->joinLeft('tb_order', '', 'tb_order.order_id = tb_event.order_id and left(tb_order.ali_order_no,3)="ALS"')
				  ->where('tb_order.order_status in (?) and IFNULL(return_reason,"") != ?',array('5','10','6','7','8','12'),'{"code":4100,"success":false,"message":"Internal call failed, msg:please do not repeat the check weight operation"}');
		if(request('ali_order_no')){
			$order_nos = explode("\r\n", request('ali_order_no'));
			$order_no = array_filter($order_nos);
			$order_no = array_unique($order_no);
			$select->where('tb_order.ali_order_no in (?) or tb_order.tracking_no in (?)',$order_no,$order_no);
		}
		if(request('event_code')){
			$select->where('event_code = ?',request('event_code'));
		}
		if(request('export') == 'exportlist'){
			ini_set('max_execution_time', '0');
			ini_set('memory_limit', '2G');
			set_time_limit(0);
			$list=clone $select;
			$lists=$list->getAll();
			//创建一个excel空文件，文件名 应付统计
			Helper_ExcelX::startWriter ( 'event_list'  );
			$header = array (
				'订单号',
				'事件时间',
				'重发时间',
				'成功时间',
				'事件代码',
				'事件位置',
				'时区号',
				'失败原因'
			);
			
			//写入表头 内容为$header,addRow为写入内容
			Helper_ExcelX::addRow ($header);
			foreach ($lists as $event){
				$row =array(
					$event->order->ali_order_no,
					Helper_Util::strDate('Y-m-d H:i:s', $event->event_time),
					Helper_Util::strDate('Y-m-d H:i:s', $event->update_time),
					Helper_Util::strDate('Y-m-d H:i:s', $event->success_time),
					$event->event_code,
					$event->event_location,
					$event->timezone,
					$event->return_reason
				);
				Helper_ExcelX::addRow ( $row );
			}
			//写入结束
			Helper_ExcelX::closeWriter ();
			exit ();
		}
		$list=$select->limitPage(request('page',1),request( 'page_size', 25 ))
		->fetchPagination($pagination)
		->getAll();
		
		$this->_view['list']=$list;
		$this->_view['pagination']=$pagination;
	}
	
	/**
	 * @todo 修改费用对账状态
	 * @author 许杰晔
	 * @since 2020-6-18
	 * @param
	 * @return
	 */
	function actionChangeFeeRecon(){
		if (request_is_post ()) {
			$conn = QDB::getConn ();
			$conn->startTrans ();
			$fee_id = post ( 'fee_id' );
			
			$fees = Fee::find ( 'fee_id in (?)', $fee_id )->getAll ();
			//判断有几条数据
			if (count ( $fees ) > 0) {
				//多条数据
				foreach ( $fees as $fee ) {
					$fee->recon_state=1;
					//保存
					$fee->save ();
				}
				
			}
			$conn->completeTrans ();
			echo json_encode ( Fee::find ( 'fee_id in (?)', $fee_id )->asArray ()->getAll () );
			exit ();
		}
	}
	/**
	 * @todo 导入发票信息
	 * @author 许杰晔
	 * @since 2020-8-5
	 * @param
	 * @return
	 * @link #81652
	 */
	function actionInvoiceImport(){	
		set_time_limit ( 0 );
		if (request_is_post ()) {
			$errors = array ();
			$uploader = new Helper_Uploader ();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage ( '未上传文件', '', url ( 'statistics/invoiceimport' ) );
			}
			$file = $uploader->file ( 'file' ); //获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage ( '文件格式不正确：xls、xlsx', '', url ( 'statistics/invoiceimport' ) );
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' ); //缓存路径
			$filename = date ( 'YmdHis' ) . 'invoiceimport.' . $file->extname ();
			$file_route = $des_dir . DS . $filename;
			$file->move ( $file_route );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $file_route, true );
			$sheet = $xls->toHeaderMap ();
			//导入的表中有数据
			//必填字段
			$required_fields = array (
				'ID',
				'账单抬头'
			);
			$result = array();
			
			if (! empty ( $sheet )) {
				foreach ( $sheet as $k => $row ) {				
					//判断基础信息不得为空
					//print_r($row);exit;
					foreach ( $required_fields as $field ) {
						if (! isset ( $row [$field] )) {
							return $this->_redirectMessage ( '失败', '模板字段缺失，请检查', url ( "statistics/invoiceimport" ) );
						}
						if (! strlen ( $row [$field] )) {
							$result[] = '【' . $field . '】不能为空';
							continue 2;
						}
					}
					
					$fee = Fee::find ( 'fee_id = ?', $row ['ID'] )->getOne ();
					if ($fee->isNewRecord ()) {
						$result[] = 'ID'.$row ['ID'] . '-不存在';
						continue;
					}
					
					$fee->invoice_no=$row['发票号'];
					$fee->voucher_no=$row['凭证号'];
					$fee->invoice_time=strtotime(date('Y-m-d'));
					$fee->voucher_time=strtotime(date('Y-m-d'));
					$fee->waybill_title=$row['账单抬头'];
					$fee->save();					
					$result[] = '成功';
				}
				$this->_view ['result'] = $result;
			} else {
				return $this->_redirectMessage ( '失败', '请修改表格填写内容', url ( "statistics/invoiceimport" ) );
			}
		}
		
	}
	/**
	 * @todo 下载导入发票信息模板
	 * @author 许杰晔
	 * @since 2020-8-5
	 * @param
	 * @return
	 * @link #81652
	 */
	function actionDownloadTemp(){
		return $this->_redirect ( QContext::instance ()->baseDir () . 'public/download/导入发票信息.xlsx' );
	}
	/**
	 * @todo   重发事件
	 * @author 许杰晔
	 * @since  2020-8-5
	 * @param  
	 * @return  
	 * @link   #81652
	 */
	function actionResend(){
		$event = Event::find('event_id = ?',request('event_id'))->getOne();
		if(!$event->isNewRecord()){
			$event -> send_times = 1;
			$event -> send_flag = 0;
			$event -> save();
		}
		return $this->_redirectMessage ( '重发事件', '重发中', url ( 'statistics/event' ));
	}
	/**
	 * @todo   移除事件
	 * @author 吴开龙
	 * @since  2020-9-9
	 * @param
	 * @return
	 * @link   #82348
	 */
	function actionResendDel(){
		$event = Event::find('event_id = ?',request('event_id'))->getOne();
		if(!$event->isNewRecord()){
			$event -> status = 1;
			$event -> save();
		}
		return $this->_redirectMessage ( '移除事件', '成功', url ( 'statistics/event' ));
	}
	/**
	 * @todo   已通知
	 * @author 吴开龙
	 * @since  2020-10-14
	 * @param
	 * @return
	 * @link   #83107
	 */
	function actionNotice(){
		$event = Event::find('event_id = ?',request('event_id'))->getOne();
		if(!$event->isNewRecord()){
			$event -> notice_type = 1;
			$event -> save();
		}
		return $this->_redirectMessage ( '已通知', '成功', url ( 'statistics/event' ));
	}
	/**
	 * @todo   一键重发事件
	 * @author 许杰晔
	 * @since  2020-8-5
	 * @param  
	 * @return  
	 * @link   #81652
	 */
	function actionAllResend(){
		foreach (request('event_ids') as $event_id){
			$event = Event::find('event_id = ?',$event_id)->getOne();
			if(!$event->isNewRecord()){
				//1一件重发
				if(request('type') == 1){
					$event -> send_times = 1;
					//重发事件
					$event -> send_flag = 0;
				}elseif (request('type') == 2){
					//2一键移除
					$event -> status = 1;
				}elseif (request('type') == 3){
					//3一键已通知
					$event -> notice_type = 1;
				}
				$event -> save();
			}
		}
		return 'success';
	}
}