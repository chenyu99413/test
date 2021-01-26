<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    预报清单
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php 
	$d = array();
	foreach(Order::channelgroup() as $k => $v){
		$d[$k] = $k;
	}
?>
<div>
</div>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th class="required-title">
						发件日期从：
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date" ),
							"style"=>"width:90px","required"=>"true"
						) )?>
					</td>
					<th class="required-title">到</th>
					<td>
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date"),
							"style"=>"width:90px","required"=>"true"
						) )?>
					</td>
					<th class="required-title">SORT</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'sort', array (
							'items' => array (
								'D3' => 'D3',
								'S1' => 'S1'
							),
							'value' => request ( 'sort' ) 
						) )?>
					</td>
					<th class="required-title">渠道分组</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'channel', array (
							'items' => $d,
							'value' => request ( 'channel' ) 
						) )?>
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <button type="submit" name="export" class="btn btn-small btn-info" value="exportprealert">
						<i class="icon-download"></i>
						导出预报清单
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>发件日</th>
				<th>运单号</th>
				<th>件数</th>
				<th>实重</th>
				<th>计费重</th>
				<th>目的地</th>
				<th>包装</th>
				<th>服务类型</th>
				<th>分单号</th>
				<th>是否报关</th>
				<th>SORT</th>
			</tr>
		</thead>
		<tbody>
		<?php if (isset($orders)):?>
		<?php $i=1; foreach ($orders as $temp):
		$weight_arr = Helper_Quote::getweightarr($temp, 3);
		?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td align="center"><?php echo Helper_Util::strDate('Y-m-d H:i', $temp->record_order_date)?></td>
				<td><?php echo $temp->tracking_no?></td>
				<td><?php echo Faroutpackage::find('order_id=?',$temp->order_id)->getSum("quantity_out")?></td>
				<?php if ($is_ogp):?>
				<td align="right"><?php echo $weight_arr['total_label_weight']?></td>
				<td align="right"><?php echo $weight_arr['total_cost_weight']?></td>
				<?php else :?>
				<td align="right"><?php echo $temp->weight_actual_out?></td>
				<td align="right"><?php echo $temp->weight_cost_out?></td>
				<?php endif;?>
				<td><?php echo $temp->consignee_country_code?></td>
				<td>
				<?php if ($temp->packing_type=='PAK'){
					echo "PAK";
				}elseif($temp->packing_type=='DOC'){
					echo "DOC";
				}else{
					echo "BOX";
				}
				?>
				</td>
				<td>1P</td>
				<td>
				<?php foreach ($temp->subcodes as $s){
					if($s->sub_code==$temp->tracking_no){
						continue;
					}else{
						echo $s->sub_code.'<br>';
					}
				}
				?>
				</td>
				<td><?php echo ($temp->declaration_type=='DL' || $temp->total_amount > 700 || $temp->weight_actual_in > 70)?"是":"否"?></td>
				<td><?php echo $temp->sort?></td>
			</tr>
		<?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

