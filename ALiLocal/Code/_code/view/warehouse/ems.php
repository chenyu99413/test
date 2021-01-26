<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
</div>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
						出库日期：
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date" ),
							"style"=>"width:90px",
						    "required"=>"required"
						) )?>
						-
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date" ),
							"style"=>"width:90px",
						    "required"=>"required"
						) )?>
					</td>
					<th>仓库</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'department_id', array (
							'items' => array (
								'6' => '杭州仓',
								'7' => '上海仓',
							    '8' => '义乌仓',
								'22' => '广州仓',
							    '23' => '青岛仓',
								'24' => '深圳仓',
							    '25' => '南京仓'
							),
							'empty'=>true,
							'value' => request ( 'department_id' ) 
						) )?>
					</td>
					
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <button type="submit" name="export" class="btn btn-small btn-info" value="export">
						 <i class="icon-download"></i>
							导出
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
				<th>仓库</th>
				<th>阿里订单编号</th>
				<th>EMS单号</th>
				<th>目的国</th>
				<th>企业商品货号</th>
				<th>企业商品名称</th>
				<th>商品数量</th>
				<th>商品单价</th>
				<th>HS编码</th>
				<th>商品名称</th>
				<th>总重量</th>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($order)):?>
		<?php $i=1; foreach ($order as $temp):?>
		<?php $product_quantity_sum=Orderproduct::find('order_id=?',$temp['order_id'])->getSum('product_quantity');?>
			<tr>
				<td><?php echo $i ?></td>
				<td><?php echo Department::find('department_id=?',$temp['department_id'])->getOne()->department_name?></td>
				<td>
				    <a target="_blank" href="<?php echo url('order/detail', array('order_id' => $temp['order_id']))?>">
				    <?php echo $temp['ali_order_no']?>
				    </a>
				</td>
				<td><?php echo $temp['tracking_no']?></td>
				<td><?php echo $temp['customs_country_code']?></td>
				<td><?php echo date('Ymd').sprintf("%06d",$i++)?></td>
				<td><?php echo $temp['product_name_far']?></td>
				<td><?php echo $temp['product_quantity']?></td>
				<td><?php echo sprintf('%.2f',$temp['declaration_price'])?></td>
				<td><?php echo $temp['hs_code']?></td>
				<td><?php echo $temp['product_name_far']?></td>
				<td><?php echo sprintf('%.2f',$temp['weight_actual_out']*$temp['product_quantity']/$product_quantity_sum)?></td>
			</tr>
		<?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

