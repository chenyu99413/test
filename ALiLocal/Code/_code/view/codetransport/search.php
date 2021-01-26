<div class="FarSearch"  class="list-search">
	<form id="currency-search-form" class="list-search-form"
		action="<?php echo url('/list')?>" method="post">
		<table>
			<tbody>
				<tr>
					<th>运输方式编码</th>
					<td>
						<input type="text" name="code" value="<?php echo request('code')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<th>运输方式名称</th>
					<td>
						<input type="text" name="name" value="<?php echo request('name')?>"
							placeholder="模糊查找" style="width: 150px;">
					</td>
					<th>关联产品</th>
					<td>
						<?php
							$product=Product::find()->asArray()->getAll();
							$resl=array();
							foreach ($product as $l){
								$resl[$l['product_id']]=$l['product_chinese_name'];
							}
							echo Q::control ( "myselect", "product_id", array (
								"items" => $resl,
								"selected" => request('product_id'),
								'style'=>'width:180px',
							) )
	                     ?>
					
						
					</td>
					<td>
						<button class="btn btn-small btn-primary" type="submit"
							id="search-currency-btn">
							<i class="icon-search"></i>
							搜索
						</button>
						<button type="button" class="btn btn-small btn-success edit-modal"
							data-toggle="tooltip" data-placement="top" title="新增"
							data-url="<?php echo url('CodeTransport/editmodal')?>" data-w="500px"
							data-h="440px">
							<i class="icon-plus"></i>
							新建
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<div id="currency-list"></div>
<script type="text/javascript">
function getList(page, page_size) {
	var url = "<?php echo url('CodeTransport/list')?>";
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