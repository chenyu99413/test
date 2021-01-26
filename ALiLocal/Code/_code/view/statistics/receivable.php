<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
应收统计
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="post" action="" style="margin-bottom: 2px;" id="page-top-search">
	<div
		style="border: 1px solid #d4d4d4; background-color: #f2f2f2; padding: 10px;">
		<table>
			<tbody>
				<tr>
					<th>
					<?php
                        echo Q::control ( 'dropdownlist', 'timetype', array (
                        'items'=>array('1'=>'出库日期','2'=>'订单日期','3'=>'登账日','4'=>'核查日期'),
                        'value' => request('timetype', '1' ),
                        'style'=>'width:80px'
                     ) )?>
                    </th>
					<td colspan="3">
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date", date ( 'Y-m-' ) . '01' ),
							"style" => "width: 90px"
						) )?>
						-
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date", date ( 'Y-m-d' ) ),
							"style" => "width: 90px"
						) )?>
					</td>
					<th>阿里订单号</th>
					<td>
						<textarea name="ali_order_no" placeholder="每行一个订单号" cols="" rows="1" style="width:160px;"><?php echo request('ali_order_no')?></textarea>
						<input type="hidden" id="page" name="page" value="<?php echo request('page')?>">
					</td>
					<th>末端单号</th>
					<td>
						<textarea name="tracking_no" placeholder="每行一个末端单号" cols="" rows="1" style="width:160px;"><?php echo request('tracking_no')?></textarea>
					</td>
					<th>账单号</th>
					<td>
						<textarea name="bill_no" placeholder="每行一个账单号" cols="" rows="1" style="width:160px;"><?php echo request('bill_no')?></textarea>
					</td>
					<th>客户</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownbox', 'customer_id', array (
                        'items'=>Helper_Array::toHashmap(Customer::find()->asArray()->getAll(),'customer_id','customer'),
                        'empty' => true, 
                        'value' => request('customer_id'),
                        'style'=>'width:80px'
                     ) )?>
					</td>
					<th>币种</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "currency", array (
							"items" =>  Helper_Array::toHashmap(CodeCurrency::find()->asArray()->getAll(), 'code','code'),
							"empty" => true,
							"value" => request ( "currency"),
						) )?>
					</td>
					<th>订单状态</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "order_status", array (
							"items" =>  Order::$status,
							"empty" => true,
							"value" => request ( "order_status"),
						) )?>
					</td>
				</tr>
				<tr>
					<th>产品</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownbox', 'service_code', array (
                        'items'=>Helper_Array::toHashmap(Product::find()->asArray()->getAll(),'product_name','product_chinese_name'),
                        'empty' => true, 
                        'value' => request('service_code'),
                        'style'=>'width:80px'
                     ) )?>
					</td>
					<th>付款状态</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "status", array (
							"items" => array (
								"0" => "应付未销账",
								"1" => "应付已销账",
								"2" => "全部"
							),
							"value" => request ( "status", "0" ) 
						) )?>
					</td>
					<td colspan="3">
                        <a class="btn btn-small btn-primary receivable" href="javascript:void(0)" onclick="downloadreceivable()">
						     <i class="icon-download"></i>
						     应收账单
				        </a>
						<input type="hidden" name="filter_id"
							value="<?php echo request('filter_id')?>" />
						<button class="btn btn-small btn-info" name="search" id="search">
							<i class="icon-search"></i>
							搜索
						</button>
						<a class="btn btn-small btn-primary" href="javascript:void(0)" onclick="downloadreceive()">
						     <i class="icon-download"></i>
						     导出
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
<div style="clear: both;"></div>
<div>
	<div style="float: left; width: 210px;" id="filter-area">
		<div
			style="border: 1px solid #d4d4d4; background-color: #f2f2f2; width: 200px; padding: 4px;">筛选</div>
		<div
			style="border: 1px solid #d4d4d4; background-color: #ffffff; width: 200px; padding: 4px; border-top: 0px;">
			<input type="text" name="filter-input" value="" placeholder="快速筛选"
				style="width: 176px; margin: 6px 6px 10px;">
			<ul id="filter-list" class="nav nav-list"
				style="overflow: auto; height: 400px;">
				<li class="active">
					<a class="filter-a">全部</a>
				</li>
				<?php foreach ($filters as $filter):?>
				<li>
					<a class="filter-a" data-filter_id="<?php echo $filter['filter_id']?>"><?php echo $filter['filter_name']?></a>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>

	<div class="easyui-tabs" style="width: 1012px;" id="data-area">
		<div title="明细" style="padding: 2px">
			<div id="detail" style="min-height: 420px;"></div>
		</div>
		<div title="汇总" style="padding: 2px">
			<div id="total" style="min-height: 420px;"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$("#search").click(function(){
		$('#page-top-search').find('[name="filter_id"]').val('全部');
		$("#page").val('1');
	})
	// 初始化
	loadDeatil();
	// 标签页切换
	$('#data-area').tabs({
		onSelect: function(title,index){
			// 仅切换时执行
			$("#page").val('1');
			if(title == '明细'){
				loadDeatil();
			} else {
				loadTotal();
			}
		}
	});
	// 左侧快速筛选
	$('[name="filter-input"]').on('keyup change',function(){
		var $filter = $(this);
		var input_value = $filter.val().trim().toUpperCase();
		if (input_value == "") {
			$('#filter-list').find('li').show();
		} else {
			$('#filter-list').find('li').each(function(){
				var li_value = $(this).find('a').html().trim().toUpperCase();
				if (li_value.indexOf(input_value) >= 0) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}
	});
	// 左侧快速筛选点击事件
	$('.filter-a').on('click',function(){
		$('#filter-list').find('li').removeClass('active');
		$(this).closest('li').addClass('active');
		$('#page-top-search').find('[name="filter_id"]').val($(this).data('filter_id'));
		$("#page").val('1');
		loadDeatil();
	});
	// 翻页异步加载
	$('#detail').on('click','.pagination a',function(){
		$("#page").val($(this).text());
		loadDeatil();
		return false;
	});
	// 翻页异步加载
	$('#total').on('click','.pagination a',function(){
		$("#page").val($(this).text());
		loadTotal();
		return false;
	});
});
function loadDeatil(href){
	var $form = $('#page-top-search');
	var url = "<?php echo url('statistics/ReceivableDetail')?>";
// 	$("#detail").load(url,$form.serialize(),function(){
// 		console.log('loadDeatil');
// 		$('#data-area').tabs('select','明细');
// // 		$('#page-top-search').find('[name=filter_id]').val('');
// 	});

	$.ajax({
		url:url,
		data:$form.serialize(),
		type:'post',
		dataType:'html',
		success:function(data){
			$("#detail").html(data);
			$('#data-area').tabs('select','明细');
		}
	})
}
function loadTotal(href){
	var $form = $('#page-top-search');
	var url = "<?php echo url('statistics/ReceivableTotal')?>";
// 	$("#total").load(url,$form.serialize(),function(){
// 		console.log('loadTotal');
// 		$('#data-area').tabs('select','汇总');
// 	});
	$.ajax({
		url:url,
		data:$form.serialize(),
		type:'post',
		dataType:'html',
		success:function(data){
			$("#total").html(data);
			$('#data-area').tabs('select','汇总');
		}
	})
}
function downloadreceive(){
// 	var $form = $('#page-top-search');
	//var url ="<!?php echo url('statistics/Receiveexport')?>";
	//window.location.href="<!?php echo url('statistics/Receiveexport')?>"+'?'+$form.serialize();
	$("#page-top-search").attr("action","<?php echo url('statistics/Receiveexport')?>");
	$("#page-top-search").submit();
	$("#page-top-search").attr("action","<?php echo url('statistics/Receivable')?>");
}
function downloadreceivable(){
// 	var $form = $('#page-top-search');
	//var url ="<!?php echo url('statistics/receivableexport')?>";
	//window.location.href="<!?php echo url('statistics/receivableexport')?>"+'?'+$form.serialize();
	$("#page-top-search").attr("action","<?php echo url('statistics/receivableexport')?>");
	$("#page-top-search").submit();
	$("#page-top-search").attr("action","<?php echo url('statistics/Receivable')?>");
}
</script>
<?PHP $this->_endblock();?>

