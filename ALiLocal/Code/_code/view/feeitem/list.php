<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">费用名称</th>
			<th style="width: 200px;">费用代码</th>
			<th style="width: 200px;">阿里代码</th>
			<th style="width: 200px;">客户</th>
			<th style="width: 200px;">支付方</th>
			<th style="width: 200px;">费用计量单位</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($feeitems as $feeitem):?>
		<tr>
			<td style="text-align: center;"><?php echo $feeitem->item_name?></td>
			<td style="text-align: center;"><?php echo $feeitem->sub_code?></td>
			<td style="text-align: center;"><?php echo $feeitem->item_code?></td>
			<td style="text-align: center;"><?php echo $feeitem->customer->customer?></td>
			<td style="text-align: center;"><?php echo $feeitem->payer=='BUYER'?'买家':($feeitem->payer=='SUPPLIER'?'卖家':'')?></td>
			<td style="text-align: center;"><?php echo $feeitem->fee_unit=='ORDER'?'票':($feeitem->fee_unit=='KG'?'千克':($feeitem->fee_unit=='STERE'?'立方米':''))?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('feeitem/editmodal',array('fee_item_id'=>$feeitem->fee_item_id))?>"
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
