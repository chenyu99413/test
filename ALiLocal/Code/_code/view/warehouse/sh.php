<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					 <th>
					出库时间
				    </th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date')?>" style="width: 130px;">
					</td>
					<th>到</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="end_date"
						value="<?php echo request('end_date')?>" style="width: 130px;">
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
					<th>产品</th>
                  	<td><?php
                        echo Q::control ( 'dropdownbox', 'service_code', array (
                        'items'=>Helper_Array::toHashmap(Order::find("ali_testing_order !='1'")->setColumns('service_code')->asArray()->getall(),'service_code','service_code'),
                        'value' => request('service_code','US-FY'),
                        ) )?>
                   	</td>
                   	<th>阿里订单号</th>
					<td><textarea cols="" rows="" name='ali_order_no' style="width: 140px" placeholder="每行一个阿里单号"><?php echo request('ali_order_no')?></textarea></td>
				    <th>运单号</th>
					<td><textarea cols="" rows="" name='tracking_no' style="width: 140px" placeholder="每行一个末端运单号"><?php echo request('tracking_no')?></textarea></td>
				</tr>
				<tr>
				   <td colspan="2">
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
				<th>阿里订单号</th>
				<th>运单号</th>
				<th>收件人姓名</th>
				<th>收件人电话</th>
				<th>收件人公司</th>
				<th>收件人地址</th>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($order)):?>
		<?php $i=1; foreach ($order as $temp):?>
		    <tr>
				<td><?php echo $i ?></td>
				<td><?php echo $temp['ali_order_no']?></td>
				<td><?php echo $temp['tracking_no']?></td>
				<td><?php echo $temp['consignee_name1']?></td>
				<td><?php echo $temp['consignee_mobile']?></td>
				<td><?php echo trim($temp['consignee_name2'])?$temp['consignee_name2']:$temp['consignee_name1'].' '.'CO.,LTD'?></td>
				<td><?php echo $temp['consignee_street1'].' '.$temp['consignee_street2']?></td>
			</tr>
		<?php $i++; endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</form>    
<?PHP $this->_endblock();?>

