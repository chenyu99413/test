<div style="overflow: scroll; height: 370px;" id="scroll-fee-list-talbe">
	<table class="FarTable" style="margin: 0; width: 1500px; table-layout: fixed;"
		id="fee-list-talbe">
		<thead>
			<tr>
				<th style="width: 50px;">No.</th>
				<th style="width: 135px;">订单日期</th>
				<th style="width: 120px;">阿里单号</th>
				<th style="width: 135px;">泛远单号</th>
				<th style="width: 150px;">跟踪单号</th>
				<th style="width: 60px;">仓库</th>
				<th style="width: 100px;">客户</th>
				<th style="width: 120px;">产品</th>
				<th style="width: 135px;">出库日期</th>
				<th style="width: 80px;">币种</th>
				<th style="width: 80px;">基础运费</th>
				<th style="width: 80px;">燃油附加费</th>
				<th style="width: 70px;">操作费</th>
				<th style="width: 80px;">其他费用</th>
				<th style="width: 70px;">总费用</th>
			</tr>
		</thead>
		<tbody>
			<?php $currency_arr = array();$currency_amount_arr = array();?>
			<?php $pageTotal = 0;$i=1;?>
			<?php if (isset($order)):?>
			<?php foreach ($order as $o):?>
			<?php $fees = Fee::find('order_id = ?  and fee_type = "1"',$o['order_id'])->group('btype_id')->asArray()->getAll();?>
			<?php foreach ($fees as $v):?>
			<?php $currency_fees = Fee::find('order_id = ?  and fee_type = "1"',$o['order_id'])->group('btype_id,currency')->asArray()->getAll();?>
			<?php foreach ($currency_fees as $currency):?>
			<tr>
			    <td><?php echo $i?></td>
				<td><?php echo $o['create_time']>0?date('Y-m-d',$o['create_time']):''?></td>
				<td>
				    <a href="<?php echo url('order/detail',array('order_id'=>$o['order_id']))?>">
						<?php echo $o['ali_order_no']?>
					</a>
			    </td>
				<td><?php echo $o['far_no']?></td>
				<td><?php echo $o['tracking_no']?></td>
				<td><?php echo $department[$o['department_id']]?>
				</td>
				<td><?php echo @$customer[$v['btype_id']]?@$customer[$v['btype_id']]:'无'?></td>
				<td><?php echo $product[$o['service_code']]?></td>
				<td><?php echo $o['warehouse_out_time']>0?date('Y-m-d',$o['warehouse_out_time']):''?></td>
				<td><?php echo $currency['currency']?></td>
				<?php if (request('timetype')=='3'):?>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and account_date >= ? and account_date <= ? and  fee_item_code="logisticsExpressASP_EX0001"',$v['btype_id'],$currency['currency'],$o['order_id'],strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ))->getSum('amount')?></td>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and account_date >= ? and account_date <= ? and  fee_item_code="logisticsExpressASP_EX0019"',$v['btype_id'],$currency['currency'],$o['order_id'],strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ))->getSum('amount')?></td>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and account_date >= ? and account_date <= ? and  fee_item_code="operating_fee"',$v['btype_id'],$currency['currency'],$o['order_id'],strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ))->getSum('amount')?></td>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and account_date >= ? and account_date <= ? and  fee_item_code not in ("operating_fee","logisticsExpressASP_EX0001","logisticsExpressASP_EX0019")',$v['btype_id'],$currency['currency'],$o['order_id'],strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ))->getSum('amount')?></td>
					<?php $fee=Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and account_date >= ? and account_date <= ?',$v['btype_id'],$currency['currency'],$o['order_id'],strtotime ( request ( 'start_date', date ( 'Y-m-' ) . '01'  ) . ' 00:00:00' ),strtotime ( request ( "end_date", date ( 'Y-m-d' ) ) . ' 23:59:59' ))->getSum('amount');?>
					<td style="text-align: right;"><?php echo $fee?></td>
					<?php else:?>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and  fee_item_code="logisticsExpressASP_EX0001"',$v['btype_id'],$currency['currency'],$o['order_id'])->getSum('amount')?></td>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and  fee_item_code="logisticsExpressASP_EX0019"',$v['btype_id'],$currency['currency'],$o['order_id'])->getSum('amount')?></td>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and  fee_item_code="operating_fee"',$v['btype_id'],$currency['currency'],$o['order_id'])->getSum('amount')?></td>
					<td style="text-align: right;"><?php echo Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1" and  fee_item_code not in ("operating_fee","logisticsExpressASP_EX0001","logisticsExpressASP_EX0019")',$v['btype_id'],$currency['currency'],$o['order_id'])->getSum('amount')?></td>
					<?php $fee=Fee::find('btype_id = ? and currency = ? and order_id = ? and fee_type="1"',$v['btype_id'],$currency['currency'],$o['order_id'])->getSum('amount');?>
					<td style="text-align: right;"><?php echo $fee?></td>
					<?php endif;?>
					<?php 
						if (in_array($currency['currency'], $currency_arr)){
							$currency_amount_arr[$currency['currency']] += $fee;
						}else{
							$currency_arr[] = $currency['currency'];
							$currency_amount_arr[$currency['currency']] = $fee;
						}
					?>
			</tr>
			<?php endforeach;?>
			<?php endforeach;?>
			<?php $i++; endforeach;?>
			<?php endif;?>
			<?php $pageTotal = '';
				if(count($currency_arr)>0 && count($currency_amount_arr)>0){
					foreach ($currency_arr as $arr){
						$pageTotal .= $arr.':'.sprintf('%2.f',$currency_amount_arr[$arr]).';';
					}
				}
			?>
		</tbody>
	</table>
</div>
本页总计：<?php echo $pageTotal;?>&nbsp;总计：<?php echo $sum_total?>
<div style="height: 5px;"></div>
<?php $this->_control ( "paginationa", "my-pagination", array ( "pagination" => $pagination, "nofloat" => "false" ) );?>
<script type="text/javascript">
$('#fee-list-talbe').floatThead({
    scrollContainer: function($table){
        return $table.closest('#scroll-fee-list-talbe');
    }
});
</script>
