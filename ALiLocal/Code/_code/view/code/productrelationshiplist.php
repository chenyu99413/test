<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">阿里代码</th>
			<th style="width: 200px;">IB代码</th>
			<th style="width: 200px;">操作人</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($products as $product):?>
		<tr>
			<td style="text-align: center;"><?php echo $product->ali_product?></td>
			<td style="text-align: center;"><?php echo $product->ib_product?></td>
			<td style="text-align: center;"><?php echo $product->operator?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('code/productrelationshipeditmodal',array('id'=>$product->id))?>"
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
