<div class="FarSearch"  class="list-search">
	<form id="currency-search-form" class="list-search-form"
		action="<?php echo url('/list')?>" method="post">
		<table>
			<tbody>
				<tr>
					<th>费用代码</th>
					<td>
						<input type="text" name="sub_code" value="<?php echo request('sub_code')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<th>阿里代码</th>
					<td>
						<input type="text" name="item_code" value="<?php echo request('item_code')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<th>费用名称</th>
					<td>
						<input type="text" name="item_name" value="<?php echo request('item_name')?>"
							placeholder="模糊查找" style="width: 150px;">
					</td>
					<th>客户</th>
					<td>
						<?php 
                        echo Q::control ( 'myselect', 'customs_code', array (
                        	'items'=>Helper_Array::toHashmap(Customer::find()->getAll(),'customs_code','customer'),
                        	'empty'=>true,
                        	'style'=>'width:130px',
                        	'value' => request('customs_code'),
                        ) )?>
					</td>
					<td>
						<button class="btn btn-small btn-primary" type="submit"
							id="search-currency-btn">
							<i class="icon-search"></i>
							搜索
						</button>
						<button type="button" class="btn btn-small btn-success edit-modal"
							data-toggle="tooltip" data-placement="top" title="新增"
							data-url="<?php echo url('feeitem/editmodal')?>" data-w="500px"
							data-h="440px">
							<i class="icon-plus"></i>
							新建
						</button>
						<a type="button" id="daochu" class="btn btn-small btn-primary" ><i class="icon-download"></i> 导出</a>
						<a class="btn btn-info btn-small" href="javascript:void(0)" onclick="if($('#customs_code').val()){fileimport();}else{ $.messager.alert('', '请先选择客户');}"><i class="icon-upload"></i> 导入 </a>
						<a type="button" href="<?php echo $_BASE_DIR?>public/download/费用项批量导入模板.xlsx" class="btn btn-small btn-primary" ><i class="icon-download"></i> 下载模板</a>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<div id="currency-list"></div>
<form id="fromimport" action="<?php echo url('/import')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileimport">
    <input type="hidden"  name="customs_code_import" id="customs_code_import">
</form>
<script type="text/javascript">
function fileimport(){
	$('#fileimport').click();
	var customs_code_val = $('#customs_code').val();
	$('#customs_code_import').val(customs_code_val);
}
$("#fileimport").change(function(){
	$("#fromimport").submit();	
})
$('#daochu').click(function(){
	window.location.href="<?php echo url('/export')?>"   
})
function getList(page, page_size) {
	var url = "<?php echo url('feeitem/list')?>";
	var form = document.getElementById('currency-search-form');
	var page = page || '1';
	var page_size = page_size || '30';
	var formData = new FormData(form);
	formData.append('page', page);
	formData.append('page_size', page_size);
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'html',
		data: formData,
		processData: false,
		contentType: false
	})
	.done(function (data) {
		$('#currency-list').html(data);
	});
}
$(function () {
	// 初始化
	getList();
	// 
	$('#currency-search-form').on('submit',function(e){
		e.preventDefault();
		getList();
	});
});
</script>