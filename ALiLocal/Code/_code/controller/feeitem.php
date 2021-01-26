<?php
/**
 * @todo 费用项管理
 * @author 吴开龙
 * @since 2020-6-12 16:40:03
 * @param 
 * @return json
 * @link #80363
 */
class Controller_FeeItem extends Controller_Abstract {
	/**
	 * @todo 费用项管理首页
	 * @author 吴开龙
	 * @since 2020-6-12 16:40:03
	 * @param 
	 * @return json
	 * @link #80363
	 */
	function actionSearch() {
	}
	/**
	 * @todo 费用项管理列表
	 * @author 吴开龙
	 * @since 2020-6-12 16:40:03
	 * @param
	 * @return json
	 * @link #80363
	 */
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
	
		$select = FeeItem::find ();
	
		if (request ( 'sub_code' )) {
			$sub_code = request ( 'sub_code' );
			$select->where ( 'sub_code = ?', $sub_code );
		}
		if (request ( 'item_code' )) {
			$item_code = request ( 'item_code' );
			$select->where ( 'item_code = ?', $item_code );
		}
		if (request ( 'item_name' )) {
			$item_name = request ( 'item_name' );
			$select->where ( 'item_name like ?', "%{$item_name}%" );
		}
		if (request ( 'customs_code' )) {
			$customs_code = request ( 'customs_code' );
			$select->where ( 'customs_code like ?', "%{$customs_code}%" );
		}
	
		$feeitems = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->getAll ();
	
		$this->_view ['feeitems'] = $feeitems;
		$this->_view ['pagination'] = $pagination;
	}
	/**
	 * @todo 费用项管理修改页面
	 * @author 吴开龙
	 * @since 2020-6-12 16:40:03
	 * @param
	 * @return json
	 * @link #80363
	 */
	function actionEditModal() {
		$feeitem = FeeItem::find ( 'fee_item_id = ?', request ( 'fee_item_id' ) )->getOne ();
		$this->_view ['logs'] = FeeItemLog::find('fee_item_id=?',request ( 'fee_item_id' ))->order('create_time desc')->getAll();
		$this->_view ['currency'] = $feeitem;
	}
	/**
	 * @todo 费用项管理修改方法
	 * @author 吴开龙
	 * @since 2020-6-12 16:40:03
	 * @param
	 * @return json
	 * @link #80363
	 */
	function actionEditSave() {
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		// 保存数据
		$feeitem = new FeeItem();
		//修改判断
		if (request ( 'fee_item_id' )) {
			//无数据判断
			$feeitem = FeeItem::find ( 'fee_item_id = ?', request ( 'fee_item_id' ) )->getOne ();
			if ($feeitem->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
			//判断重复（修改）
			if (request ( 'item_code' )){
				$is_feeitem = FeeItem::find ( 'fee_item_id != ? and (sub_code = ? or item_code = ? or item_name = ?) and customs_code =?', request ( 'fee_item_id' ) , request ( 'sub_code' ), request ( 'item_code' ), request ( 'item_name' ) ,request('customs_code'))->getOne ();
			}else{
				$is_feeitem = FeeItem::find ( 'fee_item_id != ? and (sub_code = ? or item_name = ?) and customs_code =?', request ( 'fee_item_id' ) , request ( 'sub_code' ), request ( 'item_name' ) ,request('customs_code'))->getOne ();
			}
			if(!$is_feeitem->isNewRecord()){
				return $this->_redirectAjax ( false, '编辑数据有重复' );
			}
			//保存原数据 log使用
			$sub_code = $feeitem->sub_code;
			$item_code = $feeitem->item_code;
			$item_name = $feeitem->item_name;
			$payer = $feeitem->payer;
			$fee_unit = $feeitem->fee_unit;
			$customs_code = $feeitem->customs_code;
		}else{
			//判断重复（新增）
			if (request ( 'item_code' )){
				$is_feeitem = FeeItem::find ( '(sub_code = ? or item_code = ? or item_name = ?)  and customs_code =?', request ( 'sub_code' ), request ( 'item_code' ), request ( 'item_name' ) ,request('customs_code'))->getOne ();
			}else{
				$is_feeitem = FeeItem::find ( '(sub_code = ? or item_name = ?)  and customs_code =?', request ( 'sub_code' ), request ( 'item_name' ) ,request('customs_code'))->getOne ();
			}
			if(!$is_feeitem->isNewRecord()){
				return $this->_redirectAjax ( false, '新增数据有重复' );
			}
		}
		$feeitem->sub_code = request ( 'sub_code' );
		$feeitem->item_code = request ( 'item_code' );
		$feeitem->item_name = request ( 'item_name' );
		$feeitem->payer = request ( 'payer' );
		$feeitem->fee_unit = request ( 'fee_unit' );
		$feeitem->customs_code = request ( 'customs_code' );
		$feeitem->save ();
		//添加日志
		$feeitemlog = new FeeItemLog();
		$feeitemlog->fee_item_id = $feeitem->fee_item_id;
		$feeitemlog->staff_id = MyApp::currentUser('staff_id');
		$feeitemlog->staff_name = MyApp::currentUser('staff_name');
		
		//日志内容
		$comment = '';
		if (request ( 'fee_item_id' )) {
			if(request ( 'item_name' ) != $item_name){
				$comment .= '修改费用名称['.$item_name.']为['.request ( 'item_name' ).'];';
			}
			if(request ( 'sub_code' ) != $sub_code){
				$comment .= '修改费用代码['.$sub_code.']为['.request ( 'sub_code' ).'];';
			}
			if(request ( 'item_code' ) != $item_code){
				$comment .= '修改阿里代码['.$item_code.']为['.request ( 'item_code' ).'];';
			}
			if(request ( 'payer' ) != $payer){
				$comment .= '修改支付方['.$payer.']为['.request ( 'payer' ).'];';
			}
			if(request ( 'fee_unit' ) != $fee_unit){
				$comment .= '修改费用计量单位['.$fee_unit.']为['.request ( 'fee_unit' ).'];';
			}
			if(request ( 'customs_code' ) != $customs_code){
				$comment .= '修改客户['.$customs_code.']为['.request ( 'customs_code' ).'];';
			}
		}else{
			if(request ( 'item_name' )){
				$comment .= '新增费用名称['.request ( 'item_name' ).'];';
			}
			if(request ( 'sub_code' )){
				$comment .= '新增费用代码['.request ( 'sub_code' ).'];';
			}
			if(request ( 'item_code' )){
				$comment .= '新增阿里代码['.request ( 'item_code' ).'];';
			}
			if(request ( 'payer' )){
				$comment .= '新增支付方['.request ( 'payer' ).'];';
			}
			if(request ( 'fee_unit' )){
				$comment .= '新增费用计量单位['.request ( 'fee_unit' ).'];';
			}
			if(request ( 'customs_code' )){
				$comment .= '新增客户['.request ( 'customs_code' ).'];';
			}
		}
		if($comment){
			$feeitemlog->comment = $comment;
			$feeitemlog->save();
		}
		return $this->_redirectAjax ( true, '保存成功' );
	}
	
	/**
	 * @todo   费用项管理 导出
	 * @author stt
	 * @since  2020-11-04
	 * @param
	 * @return json
	 * @link   #83417
	 */
	function actionExport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		$select = FeeItem::find ()->getAll();
		//导出字段
		$header = array (
			'费用名称',
			'费用代码',
			'阿里代码',
			'支付方',
			'费用计量单位',
			'客户'
		);
		$sheet = array (
			$header
		);
		foreach ($select as $value){
			//BUYER买家SUPPLIER卖家
			$payer = '';
			if ($value->payer=='BUYER'){
				$payer = '买家';
			}elseif ($value->payer=='SUPPLIER'){
				$payer = '卖家';
			}
			$fee_unit = '';
			//ORDER票KG千克STERE立方米
			if ($value->fee_unit=='ORDER'){
				$fee_unit = '票';
			}elseif ($value->fee_unit=='KG'){
				$fee_unit = '千克';
			}elseif ($value->fee_unit=='STERE'){
				$fee_unit = '立方米';
			}
			//客户名称
			$customer = Customer::find('customs_code=?',$value->customs_code)->getOne();
			$row =array(
				//费用名称
				$value->item_name,
				//费用代码
				$value->sub_code,
				//阿里代码
				$value->item_code,
				//支付方
				$payer,
				$fee_unit,
				$customer->customer
			);
			$sheet [] = $row;
		}
		//导出
		Helper_Excel::array2xls ( $sheet, '费用项管理导出.xlsx' );
		exit ();
	}
	/**
	 * @todo   费用项管理 导入
	 * @author stt
	 * @since  2020-11-04
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
			return $this->_redirectMessage('未上传文件','',url('code/set'), 3 );
		}
		//获得文件对象
		$file = $uploader->file ( 'file' );
		//文件格式
		if (! $file->isValid ( 'xls,xlsx' )) {
			return $this->_redirectMessage('文件格式不正确：xls,xlsx','',url('code/set'), 3 );
		}
		//缓存路径
		$des_dir = Q::ini ( 'upload_tmp_dir' );
		$filename = $des_dir.DS.date ( 'YmdHis' ).'feeitemimport.'.$file->extname ();
		$file->move ( $filename );
		$xls = Helper_Excel::readFile ( $filename,true);
		$sheets =$xls->toHeaderMap ();
		//必填字段
		$required_fields = array (
			'费用名称',
			'费用代码'
		);
		//缺少字段:费用名称或费用代码
		if(!isset($sheets[0]['费用名称']) || !isset($sheets[0]['费用代码'])){
			return $this->_redirectMessage('缺少字段:费用名称、费用代码','',url('code/set'), 3 );
		}
		//某一客户的全量更新
		$feeitem = FeeItem::find('customs_code=?',request('customs_code_import'))->getAll()->destroy();
		foreach ($sheets as $sheet){
			$feeitem = new FeeItem();
			$feeitem->sub_code = $sheet['费用代码'];
			$feeitem->item_code = $sheet['阿里代码'];
			$feeitem->item_name = $sheet['费用名称'];
			$feeitem->customs_code = request('customs_code_import');
			//BUYER买家SUPPLIER卖家
			if ($sheet['支付方']=='买家'){
				$feeitem->payer = 'BUYER';
			}elseif ($sheet['支付方']=='卖家'){
				$feeitem->payer = 'SUPPLIER';
			}
			//ORDER票KG千克STERE立方米
			if ($sheet['费用计量单位']=='票'){
				$feeitem->fee_unit = 'ORDER';
			}elseif ($sheet['费用计量单位']=='千克'){
				$feeitem->fee_unit = 'KG';
			}elseif ($sheet['费用计量单位']=='立方米'){
				$feeitem->fee_unit = 'STERE';
			}
			$feeitem->save();
		}
		//导入成功
		return $this->_redirectMessage ( '导入成功', '成功', url ( 'code/set' ), 3 );
		exit ();
	}
}

?>