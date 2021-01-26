<div style="overflow: scroll; height: 370px;" id="scroll-fee-list-talbe">
	<table class="FarTable" style="margin: 0; width: 1500px; table-layout: fixed;"
		id="fee-list-talbe">
		<thead>
			<tr>
				<th style="width: 50px;">No.</th>
				<th style="width: 90px;">订单日期</th>
				<th style="width: 150px;">阿里单号</th>
				<th style="width: 160px;">泛远单号</th>
				<th style="width: 150px;">跟踪单号</th>
				<th style="width: 80px;">仓库</th>
				<th style="width: 150px;">供应商</th>
				<th style="width: 150px;">渠道</th>
				<th style="width: 100px;">出库日期</th>
				<th style="width: 180px;">费用代码</th>
				<th style="width: 150px;">费用名称</th>
				<th style="width: 50px;">数量</th>
				<th style="width: 90px;">单价</th>
				<th style="width: 80px;">金额</th>
				<th style="width: 80px;">币种</th>
			</tr>
		</thead>
		<tbody>
		<?php $page_amount = 0;?>
		<?php $currency_arr = array();$currency_amount_arr = array();?>
		<?php foreach ($fees as $key => $fee):?>
		<tr>
			<td style="text-align: center;"><?php echo $key+1?></td>
			<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d', $fee['order_create_time']) ?></td>
			<td style="text-align: center;">
				<a
					href="<?php echo url('order/detail',array('order_id'=>$fee['order_id']))?>">
					<?php echo $fee['ali_order_no']?>
				</a>
			</td>
			<td style="text-align: center;"><?php echo $fee['far_no']?></td>
			<td style="text-align: center;"><?php echo $fee['tracking_no']?></td>
			<td ><?php $department = Department::find()->getAll()->toHashMap('department_id','department_name');
			     echo $department[$fee['department_id']]?>
			</td>
			<td ><?php echo @$supplier[$fee['btype_id']]?@$supplier[$fee['btype_id']]:'无'?></td>
			<td ><?php echo @$channel[$fee['channel_id']]?@$channel[$fee['channel_id']]:'无'?></td>
			<td ><?php echo Helper_Util::strDate('Y-m-d', $fee['warehouse_out_time'])?></td>
			<td ><?php echo @$fee_item[$fee['fee_item_code']]['sub_code']?></td>
			<td ><?php echo @$fee_item[$fee['fee_item_code']]['item_name']?></td>
			<td style="text-align: right;"><?php echo $fee['quantity']?$fee['quantity']:1?></td>
			<td style="text-align: right;"><?php echo sprintf('%.2f',$fee['amount']/($fee['quantity']?$fee['quantity']:1)) ?></td>
			<td style="text-align: right;"><?php echo $fee['amount']?></td>
			<td><?php echo $fee['currency']?></td>
			<?php 
				if(in_array($fee['currency'], $currency_arr)){
					$currency_amount_arr[$fee['currency']] += $fee['amount'];
				}else{
					$currency_arr[] = $fee['currency'];
					$currency_amount_arr[$fee['currency']] = $fee['amount'];
				}
			?>
		</tr>
		<?php endforeach;?>
		<?php $page_amount = '';
			if(count($currency_arr)>0 && count($currency_amount_arr)>0){
				foreach ($currency_arr as $currency){
					$page_amount .= $currency.':'.sprintf('%.2f',$currency_amount_arr[$currency]).';';
				}
			}
		?>
	</tbody>
	</table>
</div>
本页总计：<?php echo $page_amount;?>&nbsp;总计：<?php echo $sum_total?>
<div style="height: 5px;"></div>
<?php $this->_control ( "paginationa", "my-pagination", array ( "pagination" => $pagination, "nofloat" => "false" ) );?>
<script type="text/javascript">
$('#fee-list-talbe').floatThead({
    scrollContainer: function($table){
        return $table.closest('#scroll-fee-list-talbe');
    }
});
</script>
