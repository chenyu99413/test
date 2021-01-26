<table class="FarTable tablesorter">
	<thead>
		<tr>
			<th style="width: 200px;">运输方式编码</th>
			<th style="width: 200px;">运输方式名称</th>
			<th style="width: 200px;">关联产品</th>
			<th style="width: 200px;">关联渠道</th>
			<th style="width: 200px;">预报方式</th>
			<th style="width: 200px;">操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($currencys as $currency):?>
		<tr>
			<td style="text-align: center;"><?php echo $currency->code?></td>
			<td style="text-align: center;"><?php echo $currency->name?></td>
			<?php $product = Product::find('product_id=?',$currency->product_id)->getOne()->product_chinese_name;?>
			<td style="text-align: center;"><?php echo $product?></td>
			<?php $channel = Channel::find('channel_id=?',$currency->channel_id)->getOne()->channel_name;?>
			<td style="text-align: center;"><?php echo $channel?></td>
			<td style="text-align: center;"><?php echo $currency->book_type==1?'预报打单':'预报'?></td>
			<td style="text-align: center;">
				<button type="button" class="btn btn-mini btn-success  edit-modal"
					data-toggle="tooltip" data-placement="top" title="修改"
					data-url="<?php echo url('CodeTransport/editmodal',array('id'=>$currency->id))?>"
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
