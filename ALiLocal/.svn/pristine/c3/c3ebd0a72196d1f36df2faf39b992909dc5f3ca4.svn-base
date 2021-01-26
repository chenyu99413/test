<div class="FarSearch"  class="list-search">
	<form id="relationship-search-form" class="list-search-form"
		action="<?php echo url('/productrelationshiplist')?>" method="post">
		<table>
			<tbody>
				<tr>
					<th>产品代码</th>
					<td>
						<input type="text" name="ali_product" value="<?php echo request('ali_product')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<td>
						<button class="btn btn-small btn-primary" type="submit"
							id="search-relationship-btn">
							<i class="icon-search"></i>
							搜索
						</button>
						<button type="button" class="btn btn-small btn-success edit-modal"
							data-toggle="tooltip" data-placement="top" title="新增"
							data-url="<?php echo url('code/productrelationshipeditmodal')?>" data-w="500px"
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
<div id="relationship-list"></div>
<script type="text/javascript">

function getList(page, page_size) {
	var url = "<?php echo url('code/productrelationshiplist')?>";
	var form = document.getElementById('relationship-search-form');
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
		$('#relationship-list').html(data);
	});
}
$(function () {
	// 初始化
	getList();
	// 
	$('#relationship-search-form').on('submit',function(e){
		e.preventDefault();
		getList();
	});
});
</script>