<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">仓库</th>
			<th style="width: 200px;">仓库代码</th>
			<th style="width: 200px;">仓库英文名称</th>
			<th style="width: 200px;">仓库联系人</th>
			<th style="width: 200px;">仓库联系电话</th>
			<th style="width: 200px;">仓库地址</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($codewarehouses as $cw):?>
		<tr>
			<td style="text-align: center;"><?php echo $cw->department_name?></td>
			<td style="text-align: center;"><?php echo $cw->warehouse?></td>
			<td style="text-align: center;"><?php echo $cw->warehouse_enname?></td>
			<td style="text-align: center;"><?php echo $cw->warehouse_contact?></td>
			<td style="text-align: center;"><?php echo $cw->warehouse_mobile?></td>
			<td style="text-align: center;"><?php echo $cw->warehouse_address?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('/editmodal',array('id'=>$cw->id))?>"
					data-w="500px" data-h="440px">
					<i class="icon-edit"></i>
					修改
				</button>
			</td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php echo Q::control ( 'ajaxpagination', '', array ('pagination' => $pagination) );?>
<script type="text/javascript">
$("[data-toggle='tooltip']").tooltip();
</script>
