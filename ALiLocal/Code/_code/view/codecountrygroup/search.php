<div class="FarSearch"  class="list-search">
	<form id="countrygroup-search-form" class="list-search-form"
		action="<?php echo url('/list')?>" method="post">
		<table>
			<tbody>
				<tr>
					<th>名称</th>
					<td>
						<input type="text" name="name" value="<?php echo request('name')?>"
							placeholder="模糊查找" style="width: 150px;">
					</td>
					<td>
						<button class="btn btn-small btn-primary" type="submit"
							id="search-countrygroup-btn">
							<i class="icon-search"></i>
							搜索
						</button>
						<button type="button" class="btn btn-small btn-success edit-modal"
							data-toggle="tooltip" data-placement="top" title="新建" data-type="countrygroup"
							data-w="550px" data-h="500px"
							data-url="<?php echo url('codecountrygroup/editmodal')?>">
							<i class="icon-plus"></i>
							新建
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<div id="countrygroup-list"></div>
<script type="text/javascript">
function getList(page, page_size) {
	var url = "<?php echo url('codecountrygroup/list')?>";
	var form = document.getElementById('countrygroup-search-form');
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
		$('#countrygroup-list').html(data);
	});
}
$(function () {
	// 初始化
	getList();
	// 
	$('#countrygroup-search-form').on('submit',function(e){
		e.preventDefault();
		getList();
	});
});
</script>