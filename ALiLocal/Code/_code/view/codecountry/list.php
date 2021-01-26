<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">国家二字码</th>
			<th style="width: 200px;">国家三字码</th>
			<th style="width: 200px;">英文名称1</th>
			<th style="width: 200px;">英文名称2</th>
			<th style="width: 200px;">中文名称</th>
			<th style="width: 200px;">国家关税代码</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($country as $country1):?>
		<tr>
			<td style="text-align: center;"><?php echo $country1->code_word_two?></td>
			<td style="text-align: center;"><?php echo $country1->code_word_three?></td>
			<td style="text-align: center;"><?php echo $country1->english_name?></td>
			<td style="text-align: center;"><?php echo $country1->english_name2?></td>
			<td style="text-align: center;"><?php echo $country1->chinese_name?></td>
			<td style="text-align: center;"><?php echo $country1->customs_country_code?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('codecountry/editmodal',array('id'=>$country1->id))?>"
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
