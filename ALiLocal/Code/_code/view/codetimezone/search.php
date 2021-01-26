<div class="FarSearch"  class="list-search">
	<form id="currency-search-form" class="list-search-form"
		action="<?php echo url('/list')?>" method="post">
		<table>
			<tbody>
				<tr>
					<th>国家二字码</th>
					<td>
						<input type="text" name="code_word_two" value="<?php echo request('code_word_two')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<th>城市代码</th>
					<td>
						<input type="text" name="city" value="<?php echo request('city')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<td>
						<button class="btn btn-small btn-primary" type="submit"
							id="search-currency-btn">
							<i class="icon-search"></i>
							搜索
						</button>
						<button type="button" class="btn btn-small btn-success edit-modal"
							data-toggle="tooltip" data-placement="top" title="新增"
							data-url="<?php echo url('codetimezone/editmodal')?>" data-w="500px"
							data-h="440px">
							<i class="icon-plus"></i>
							新建
						</button>
						<a type="button" id="daochu" class="btn btn-small btn-primary" ><i class="icon-download"></i> 导出</a>
						<a class="btn btn-info btn-small" href="javascript:void(0)" onclick="fileout2()"><i class="icon-upload"></i> 导入 </a> 
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<div id="currency-list"></div>
<form id="formout2" action="<?php echo url('/import')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileout2">
</form>
<script type="text/javascript">
function fileout2(){
	$('#fileout2').click();
}
$("#fileout2").change(function(){
	$("#formout2").submit();	
})
$('#daochu').click(function(){
	window.location.href="<?php echo url('/export')?>"   
})
function getList(page, page_size) {
	var url = "<?php echo url('codetimezone/list')?>";
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