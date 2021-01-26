<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
总单列表
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
						<?php
                            echo Q::control ( 'dropdownlist', 'timetype', array (
                            'items'=>array('0'=>'创建日期','1'=>'发件日期'),
                            'value' => request('timetype'),
                            'style'=>'width:80px'
                         ) )?>
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date",date('Y-m-d')),
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
					<th>总单单号</th>
					<td><textarea rows="1" name="total_list_no" placeholder="每行一个单号"><?php echo request('total_list_no')?></textarea></td>
					<th>仓库</th>
					<td><?php
                        echo Q::control ( 'dropdownbox', 'department_id', array (
                        'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                        'empty'=>true,
                        'style'=>'width:70px',
                        'value' => request('department_id'),
                        ) )?>
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a class="btn btn-success btn-small" href="<?php echo url('warehouse/comparison',array('flag'=>'0'))?>" >
			             <i class="icon-plus"></i>
			                                         新建
		               </a>
		               <button class="btn btn-info btn-small" name="exp" value="exp">
		                  <i class="icon-download"></i>
		                                                      导出
		               </button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>
	   <label>
	       <span>合计数：</span><span>票数：<?php echo $order_count?></span>
	       <span>件数：<?php echo $far_out_package?></span> <span>计费重：<?php echo sprintf('%.2f',$weight_cost_out)?></span>
	   </label>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>发件日</th>
				<th>仓库</th>
				<th>总单单号</th>
				<th>渠道分组</th>
				<th>国家</th>
				<th>总票数</th>
				<th>总件数</th>
				<th>总计费重</th>
				<th>操作人</th>
				<th>操作日期</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1;$package_sum = 0; foreach ($total_list as $temp):?>
		  <?php $order = Order::find('total_list_no = ?',$temp->total_list_no)->setColumns('order_id');
    	        $orders = $order->asArray()->getAll();
    	        $order_id = Helper_Array::getCols($orders, 'order_id');
    	        $order_count = count($order_id);
    	        $weight_cost_out = $order->getSum('weight_cost_out');
    	        $far_out_package = 0;
    	        if($order_count>0){
    	             $far_out_package = Faroutpackage::find('order_id in (?)',$order_id)->getSum('quantity_out');
    	        }
	      ?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->record_order_date)?></td>
				<td><?php echo $temp->department_id?$dpms[$temp->department_id]:''?></td>
				<td>
				    <a  target="_blank"
				        href="<?php echo url('warehouse/totaldetail', array('total_list_no' => $temp->total_list_no))?>">
            					    <?php echo $temp->total_list_no?>
            	    </a>
            	</td>
				<td><?php echo $temp->channel_group->channel_group_name?></td>
				<td><?php echo $temp->country_code?></td>
				<td style="text-align:right;"><?php echo $order_count?></td>
				<td style="text-align:right;"><?php echo $far_out_package?></td>
				<td style="text-align:right;"><?php echo sprintf('%.2f',$weight_cost_out)?></td>
				<td><?php echo $temp->operation_name?></td>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->operation_time)?></td>
				<td><?php if($temp->status == '0'):?>
				    <a class="btn btn-mini "
				       href="<?php echo url('warehouse/comparison',array('total_list_no' => $temp->total_list_no,'account'=>urlencode($temp->channel_group->channel_group_name),
				       	'record_order_date'=>date('Y-m-d',$temp->record_order_date),'sort'=>$temp->sort,'country_code'=>$temp->country_code,
				                                                         'flag'=>'1'))?>">
				    <i class="icon-edit"></i>             
				                        继续核查
				    </a>
				    <a class="btn btn-mini btn-danger" href="<?php echo url('warehouse/finished', array('total_list_no' => $temp->total_list_no))?>">
                                                                        完成
				    </a>
				    <a class="btn btn-mini btn-info"
				       href="<?php echo url('warehouse/newcomparison',array('total_list_no' => $temp->total_list_no,))?>">
				    <i class="icon-edit"></i>             
				                        随货单证核查
				    </a>
				    <?php if($order_count == 0 && $far_out_package == 0):?>
				    <input type="button" class="btn btn-small btn-danger" href="javascript:void(0)" data="<?php echo $temp->total_list_no?>" onclick="del(this)" value="删除">
				    <?php endif;?>
				    <?php endif;?>
				    
				    
				</td>
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
function del(obj){
	if (confirm('是否删除?')) {
    	var total_list_no=$(obj).attr('data');
    	$.ajax({
    		url:'<?php echo url('warehouse/deltotal')?>',
    		data:{total_list_no:total_list_no},
    		type:'post',
    		async:false,
    		success:function(data){
    			if(data!='delsuccess'){
    			   alert('订单不存在');
    			}else{
    				window.location.reload();
    			}
    		}
    	});
	}
}
</script>
<?PHP $this->_endblock();?>

