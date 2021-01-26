<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">代码</th>
			<th style="width: 200px;">名称</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($logistics as $logistic):?>
		<tr>
			<td style="text-align: center;"><?php echo $logistic->code?></td>
			<td style="text-align: center;"><?php echo $logistic->name?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('codelogistics/editmodal',array('logistic_id'=>$logistic->id))?>"
					data-w="340px" data-h="250px">
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
