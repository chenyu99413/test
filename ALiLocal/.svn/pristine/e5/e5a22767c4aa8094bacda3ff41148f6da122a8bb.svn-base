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
					<?php
                            echo Q::control ( 'dropdownlist', 'timetype', array (
                            'items'=>array('1'=>'打印时间','2'=>'出库时间'),
                            'value' => request('timetype'),
                            'style'=>'width:80px'
                         ) )?>
				    </th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date',date('Y-m-d').' 00:00')?>" style="width: 130px;">
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
                   	<th>箱号</th>
					<td><textarea cols="" rows="" name='ali_order_no' style="width: 140px" placeholder="每行一个阿里单号"><?php echo request('ali_order_no')?></textarea></td>
				    <th>订单号</th>
					<td><textarea cols="" rows="" name='tracking_no' style="width: 140px" placeholder="每行一个末端运单号"><?php echo request('tracking_no')?></textarea></td>
				</tr>
				<tr> 
				   <th>SKU数量</th>
				   <td>
    				    <input type="number" name="start_product_quantity"
    						value="<?php echo request('start_product_quantity',15)?>" style="width: 120px;">
				   </td>
				   <th>到</th>
				   <td>
    				    <input type="number" name="end_product_quantity"
    					       value="<?php echo request('end_product_quantity')?>" style="width: 120px;">
				   </td>
				   <td colspan="3">
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
				<th>箱号</th>
				<th>订单号</th>
				<th>收件人姓名</th>
				<th>收件人地址</th>
				<th>收件人城市</th>
				<th>收件人州二字码</th>
				<th>收件人邮编</th>
				<th>备注</th>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($order)):?>
		<?php $i=1; foreach ($order as $temp):?>
		<?php $errormessage=''; 
	    if(strlen($temp['consignee_street1'].' '.$temp['consignee_street2'])>225){
	       $errormessage.='收件人地址超长';
	    }
	    if(strlen($temp['consignee_postal_code'])<>'5' && strlen($temp['consignee_postal_code'])<>'9'){
	        $errormessage.=empty($errormessage)?'收件人邮编错误':',收件人邮编错误';
	    }
// 	    if(!strpos(trim($temp['consignee_name1']), ' ')){
// 	        $errormessage.=empty($errormessage)?'收件人姓名格式不正确':',收件人姓名格式不正确';
// 	    }
	    $state='';
	    $states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$temp['consignee_state_region_code'])),strtoupper(str_replace(' ','',$temp['consignee_state_region_code'])))->getOne();
	    if($states->isNewRecord()){
	        $errormessage.=empty($errormessage)?'收件人州错误':',收件人州错误';
	    }else{
	        $state=$states->province_code_two;
	    }
	    ?>
			<tr>
				<td><?php echo $i ?></td>
				<td><a  target="_blank" href="<?php echo url('order/detail', array('order_id' => $temp['order_id']))?>"><?php echo $temp['ali_order_no']?></a></td>
				<td><?php echo $temp['tracking_no']?></td>
				<td><?php echo $temp['consignee_name1']?></td>
				<td><?php echo substr($temp['consignee_street1'].' '.$temp['consignee_street2'], 0,225)?></td>
				<td><?php echo $temp['consignee_city']?></td>
				<td><?php echo $state?></td>
				<td><?php echo $temp['consignee_postal_code']?></td>
				<td><?php echo $errormessage?></td>
			</tr>
		<?php $i++; endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</form>   
<?PHP $this->_endblock();?>

