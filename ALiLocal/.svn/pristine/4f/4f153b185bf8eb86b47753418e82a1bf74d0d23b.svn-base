<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
  代码管理
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="FarSearch"  class="list-search">
	<form id="currency-search-form" class="list-search-form"
		action="<?php echo url('/list')?>" method="post">
		<table>
			<tbody>
				<tr>
					<th>代码</th>
					<td>
						<input type="text" name="code" value="<?php echo request('code')?>"
							placeholder="精确查找" style="width: 150px;">
					</td>
					<th>名称</th>
					<td>
						<input type="text" name="name" value="<?php echo request('name')?>"
							placeholder="模糊查找" style="width: 150px;">
					</td>
					<td>
						<button class="btn btn-small btn-primary" type="submit"
							id="search-currency-btn">
							<i class="icon-search"></i>
							搜索
						</button>
						<button type="button" class="btn btn-small btn-success edit-modal"
							data-toggle="tooltip" data-placement="top" title="新增"
							data-url="<?php echo url('codecurrency/editmodal')?>" data-w="90%"
							data-h="90%">
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
	var url = "<?php echo url('codecurrency/list')?>";
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
<script type="text/javascript">
$(function () {
	// 点击加载
	$('#code_left').find('a').on('click', function (e) {
		e.preventDefault();
		var url = $(this).data('url');
		$('#code_left').find('li').removeClass('active');
		$(this).closest('li').addClass('active');
		var load_search = layer.load(1);
		$('#code_right').load(url, function () {
			layer.close(load_search);
		});
	});
	// 加载
	var type = '<?php echo request('type','logistics')?>';
	$('#left_'+type).addClass('active').find('a').click();// 要在 on 绑定 click 事件后 调用

	$('body').on('click', '.edit-modal', function (e) {
		e.preventDefault();
		var url = $(this).data('url');
		var w = $(this).data('w');
		var h = $(this).data('h');
		layer.open({
			type: 2,
			title: '编辑',
			maxmin: true,
			shadeClose: true,
			area: [w, h],
			content: url
		});
	});
});
</script>
<?PHP $this->_endblock();?>