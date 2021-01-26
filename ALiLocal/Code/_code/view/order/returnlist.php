<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    退件列表
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
						创建日期从：
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date" ),
							"style"=>"width:90px"
						) )?>
					</td>
					<th>到</th>
					<td>
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date"),
							"style"=>"width:90px"
						) )?>
					</td>
					<th>阿里订单号</th>
					<td>
						<input name="ali_order_no" type="text" style="width: 120px"
							value="<?php echo request('ali_order_no')?>">
					</td>
					<th>退件编号</th>
					<td>
						<input name="return_no" type="text" style="width: 100px"
							value="<?php echo request('return_no')?>">
					</td>
					<th>入库仓</th>
					<td>
					    
						<?php
/* 						$item=array (
								'6' => '杭州仓',
								'7' => '上海仓',
							    '8' => '义乌仓',
								'22' => '广州仓',
							    '23' => '青岛仓',
								'24' => '深圳仓',
						        '25' => '南京仓'
							);
						$empty=true;
						if(MyApp::currentUser('department_id')=='23'){
						    $item=array(
						        '23' => '青岛仓'
						    );
						    $empty=false;
						} */
						echo Q::control ( 'dropdownbox', 'department_id', array (
							'items' => $departments,
							'empty'=>true,
							'value' => request ( 'department_id' ) 
						) )?>
					</td>
					<th>退件范围</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'return_status', array (
							'items' => array (
								'1' => '全部退',
								'2' => '部分退'
							),
							'empty'=>true,
							'value' => request ( 'return_status' ) 
						) )?>
					</td>
					<th>退件状态</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'state', array (
							'items' => array (
								'1' => '待退货',
								'2' => '已退货'
							),
							'empty'=>true,
							'value' => request ( 'state' ) 
						) )?>
					</td>
					
				</tr>
				<tr>
				    <th>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               </th>
		               <td>
		               <button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
						 <i class="icon-download"></i>
							导出
					   </button>
					   <?php if(Helper_ViewPermission::isAudit()):?>
					   <a class="btn btn-info btn-small"href="<?php echo url('/orderreturnbatch')?>">
		               		<i class="icon-upload"></i> 批量退件
		               </a> 
		               <?php endif;?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>退件编号</th>
				<th>阿里单号</th>
				<th>取件网点</th>
				<th>入库仓</th>
				<th>退件范围</th>
				<th>退件状态</th>
				<th>发起人</th>
				<th>收件人</th>
				<th>收件人电话</th>
				<th>承运商</th>
				<th>单号</th>
				<th>发起时间</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($returnlist as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><a  target="_blank"
					    href="<?php echo url('order/orderreturn', array('return_id' => $temp->return_id))?>">
					    <?php echo $temp->return_no ?>
					</a>
				</td>
				<td><?php echo $temp->ali_order_no?></td>
				<td><?php echo $temp->order->pick_company?></td>
				<td><?php echo $temp->order->department->department_name?></td>
				<td style="width:50px;"><?php echo $temp->return_status=='1'?"全部退":"部分退"?></td>
				<td><?php echo $temp->state=='1'?"待退货":"已退货"?></td>
				<td><?php echo $temp->return_operator?></td>
				<td><?php echo $temp->consignee_name?></td>
				<td style="width:100px;"><?php echo $temp->consignee_phone?></td>
				<td><?php echo $temp->express_company?></td>
				
				<td style="width:130px;">
					<a target="_blank" href="https://www.kuaidi100.com/?nu=<?php echo $temp->express_no?>">
					<?php echo $temp->express_no?>
					</a>
					</td>
				<td align="center"><?php echo Helper_Util::strDate('Y-m-d H:i', $temp->create_time)?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</form>
<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

