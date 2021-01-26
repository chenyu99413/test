<?php
class Controller_Price extends Controller_Abstract {
	/**
	 * 价格列表
	 */
	function actionSearch(){
	    
	}
	/**
	 * 价格编辑
	 */
	function actionEdit(){
	    $priceManage = Pricemanage::find ( "price_manage_id = ?", request ( "price_manage_id" ) )->getOne ();
	    if (request_is_post ()) {
	        if (request ( "price_name" )) {
	            $check_name=Pricemanage::find('price_name=?',request ( "price_name" ))->getOne();
	            if(!$check_name->isNewRecord()){
	                if($check_name->price_manage_id!=request ( "price_manage_id" )){
	                    return $this->_redirectMessage ( "价格表", "保存失败,价格名称已存在", url ( "price/edit", array (
	                        "price_manage_id" => $priceManage->price_manage_id,
	                        "active_tab" => "0"
	                    ) ) );
	                }
	            }
	            $priceManage->price_name=request ( "price_name" );
	            $priceManage->save ();
	            return $this->_redirectMessage ( "价格表", "保存成功", url ( "price/edit", array (
	                "price_manage_id" => $priceManage->price_manage_id,
	                "active_tab" => "0"
	            ) ) );
	        }
	    }
	    $this->_view ["tabs"] = $this->createTabs ( $priceManage );
	    $this->_view ["tabs_type"] = $this->createTabsType ();
	    $this->_view ["tabs_partition"] = $this->createTabsPartition ();
	    $this->_view ["priceManage"] = $priceManage;
	    $this->_view ["prices"] = Price::find ( "price_manage_id = ? and boxing_type = ? and partition_code = ?", $priceManage->price_manage_id, request ( "type", "BOX" ), request ( "partition", "1" ) )->getAll ();
	}
	/**
	 * 创建标签 包装类型
	 *
	 * @return 标签列
	 */
	function createTabsType() {
	    return array (
	        array (
	            "id" => "BOX",
	            "title" => "BOX",
	            "href" => url ( "price/edit", array (
	                "price_manage_id" => request ( "price_manage_id" ),
	                "type" => "BOX",
	                "active_tab" => "1"
	            ) )
	        ),
	    	array (
	    		"id" => "PAK",
	    		"title" => "PAK",
	    		"href" => url ( "price/edit", array (
	    			"price_manage_id" => request ( "price_manage_id" ),
	    			"type" => "PAK",
	    			"active_tab" => "1"
	    		) )
	    	),
	        array (
	            "id" => "DOC",
	            "title" => "DOC",
	            "href" => url ( "price/edit", array (
	                "price_manage_id" => request ( "price_manage_id" ),
	                "type" => "DOC",
	                "active_tab" => "1"
	            ) )
	        )
	    );
	}
	
	/**
	 * 创建标签 分区
	 *
	 * @return 标签列
	 */
	function createTabsPartition() {
	    $type = request ( "type", "BOX" );
	    $result = array ();
	    for($i = 1; $i <= 46; $i ++) {
	        $result [] = array (
	            "id" => $i,
	            "title" => $i==1?$i . "区":$i,
	            "href" => url ( "price/edit", array (
	                "price_manage_id" => request ( "price_manage_id" ),
	                "type" => $type,
	                "partition" => $i,
	                "active_tab" => "1"
	            ) )
	        );
	    }
	    return $result;
	}
	/**
	 * 验证名称
	 */
	function actionCheckname() {
	    $check_name=Pricemanage::find('price_name=?',request ( "name" ))->getOne();
	    if(!$check_name->isNewRecord()){
	        if($check_name->price_manage_id!=request ( "price_manage_id" )){
	            echo ("价格名称已存在");
	        }else{
	            echo ("true");
	        }
	    }else{
	        echo ("true");
	    }
	    exit ();
	}
	/**
	 * 保存
	 */
	function actionSave() {
	    $priceManage = PriceManage::find ( "price_manage_id = ?", request ( "price_manage_id" ) )->getOne ();
	    if (request ( "price" )) {
	    	$p = request ( "price" );
	    	//判断币种
	    	$curr = CodeCurrency::find('code=?',$p["currency_code"])->getOne();
	    	if($curr->isNewRecord()){
	    		echo "币种不存在";
	    		exit;
	    	}
	        $price = Price::find ( "price_id = ?", $p ["price_id"] )->getOne ();
	        $price->price_manage_id = $priceManage->price_manage_id;
	        $price->changeProps ( $p );
	        $price->save ();
	        echo ($price->price_id);
	    }
	    exit ();
	}
	/**
	 * 删除价格
	 */
	function actionDel() {
	    if (request ( "price_id" )) {
	        $price = Price::find ( "price_id = ?", request ( "price_id" ) )->getOne ();
	        $price->destroy ();
	    }
	    exit ();
	}
	/**
	 * 产品价格导出
	 */
	function actionExport() {
	    $data = '[["分区号","起始重量","结束重量","首重重量","首重费用","续重单位","续重费用","产品代码","币种"]';
	    foreach ( Price::find ( 'price_manage_id = ? and boxing_type = ?', request ( "id" ), request ( 'type', 'BOX' ) )->getAll () as $value ) {
	    	$data .= ',["' . $value->partition_code . '","' . $value->start_weight . '","' . $value->end_weight . '","' . $value->first_weight . '","' . $value->first_fee . '","' . $value->additional_weight . '","' . $value->additional_fee . '","' . $value->account . '","' . $value->currency_code . '"]';
	    }
	    $data .= ']';
	    $file_name = (request ( "fileName" ) == null ? time () : request ( "fileName" )) . ".xls";
	    Helper_Excel::array2xls ( json_decode ( $data ), $file_name );
	    exit ();
	}
	/**
	 * 创建标签
	 */
	function createTabs($priceManage) {
	    if ($priceManage->isNewRecord ()) {
	        return array (
	            array (
	                "id" => "1",
	                "title" => "基本信息",
	                "href" => ""
	            )
	        );
	    } else {
	        return array (
	            array (
	                "id" => "0",
	                "title" => "基本信息",
	                "href" => url ( "price/edit", array (
	                    "price_manage_id" => request ( "price_manage_id" ),
	                    "active_tab" => "0"
	                ) )
	            ),
	            array (
	                "id" => "1",
	                "title" => "价格信息",
	                "href" => url ( "price/edit", array (
	                    "price_manage_id" => request ( "price_manage_id" ),
	                    "active_tab" => "1"
	                ) )
	            )
	        );
	    }
	}
	/**
	 * 价格导入
	 */
	function actionImport() {
	    $conn = QDB::getConn ();
	    $conn->startTrans ();
	
	    $url = url ( "price/edit", array (
	        "price_manage_id" => request ( "price_manage_id" ),
	        "type" => request ( "type", "BOX" ),
	        "partition" => request ( "partition", "1" ),
	        "active_tab" => "1"
	    ) );
	
	    //判断价格是否存在
	    $priceManage = PriceManage::find ( "price_manage_id = ?", request ( "price_manage_id" ) )->getOne ();
	    if ($priceManage->isNewRecord ()) {
	        return $this->_redirectMessage ( "价格导入", "导入失败,价格管理ID错误", $url );
	    }
	
	    //读取数据
	    $uploader = new Helper_Uploader ();
	    if (Controller_Common::getFileExtName ( $uploader ) != "xls") {
	        return $this->_redirectMessage ( "价格导入", "导入失败,文件类型不正确,请选择 .xls 类型的文件", $url );
	    }
	    $data = Helper_Excel::readFile ( Controller_Common::readFile ( $uploader ) )->toHeaderMap ();
	
	    //检查内容是否为空或格式是否正确
	    if (empty ( $data ) || ! isset ( $data [0] ["分区号"] ) || ! isset ( $data [0] ["结束重量"] ) ||  ! isset ( $data [0] ["首重重量"] ) || ! isset ( $data [0] ["首重费用"] ) || ! isset ( $data [0] ["续重单位"] ) || ! isset ( $data [0] ["续重费用"]) || ! isset ( $data [0] ["产品代码"]) || ! isset ( $data [0] ["币种"])) {
	        return $this->_redirectMessage ( "价格导入", "导入失败,请检查模板是否正确", $url );
	    }
	    foreach ( $data as $value1 ) {
	    	//判断币种
	    	$curr = CodeCurrency::find('code=?',trim($value1['币种']))->getOne();
	    	if($curr->isNewRecord()){
	    		return $this->_redirectMessage ( "价格导入", "导入失败,币种不存在", $url, 5);
	    	}
	    }
	    
	
	    //保存
	    Price::meta ()->deleteWhere ( "price_manage_id = ? and boxing_type = ?", $priceManage->price_manage_id, request ( "type", "BOX" ) );
	    $start_weight=0;
	    $partition_code=0;
	    foreach ( $data as $value ) {
	        if(!strlen($value["分区号"])){
	            continue;
	        }
	        if ($value ["分区号"] != intval ( $value ["分区号"] ) || $value ["分区号"] < 1 ) {
	            continue;
	        }
	        if($partition_code!=trim($value ["分区号"])){
	            $start_weight=0;
	        }
	        $price = new Price ( array (
	            "price_manage_id" => request ( "price_manage_id" ),
	            "boxing_type" => request ( "type", "BOX" ),
	            "partition_code" => trim($value ["分区号"]),
	            "start_weight" => $value ["起始重量"]?$value ["起始重量"]:$start_weight,
	            "end_weight" => trim($value ["结束重量"]),
	            "first_weight" => trim($value ["首重重量"]),
	            "first_fee" => trim($value ["首重费用"])?trim($value ["首重费用"]):"0",
	            "additional_weight" => trim($value ["续重单位"])?trim($value ["续重单位"]):"0",
	            "additional_fee" => trim($value ["续重费用"])?trim($value ["续重费用"]):"0",
	            'account'=>trim($value['产品代码']),
	        	'currency_code'=>trim($value['币种'])
	        ) );
	        $price->save ();
	        $start_weight=$value ["结束重量"];
	        $partition_code=trim($value ["分区号"]);
	    }
	    $conn->completeTrans ();
	    return $this->_redirectMessage ( "价格导入", "导入成功", $url );
	}
}