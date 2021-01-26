<table class="FarTable tablesorter" style="table-layout:fixed;">
	<thead>
		<tr>
			<th style="width: 60px;">名称</th>
			<th style="width: 400px;">国家</th>
			<th style="width: 40px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($countrygroups as $group):?>
		<tr>
			<td style="text-align: center;"><?php echo $group->name?></td>
			<td style="word-wrap:break-word;"><?php echo $group->country_codes?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-type="countrygroup"
					data-w="550px" data-h="500px"
					data-url="<?php echo url('codecountrygroup/editmodal',array('countrygroup_id'=>$group->id))?>">
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
