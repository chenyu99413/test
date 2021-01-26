<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">国家二字码</th>
			<th style="width: 200px;">城市代码</th>
			<th style="width: 200px;">时区</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($timezones as $timezone):?>
		<tr>
			<td style="text-align: center;"><?php echo $timezone->code_word_two?></td>
			<td style="text-align: center;"><?php echo $timezone->city?></td>
			<td style="text-align: center;"><?php echo $timezone->timezone?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('codetimezone/editmodal',array('id'=>$timezone->id))?>"
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
