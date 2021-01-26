<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
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
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
						渠道分组
					</th>
					<td>
						<?php
                            echo Q::control ( 'dropdownlist', 'channel_group', array (
                            'items'=>$d,
                            'value' => request('channel_group'),
                            'style'=>'width:120px'
                         ) )?>
					</td>
					<th>
						发件日
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "record_order_date", array (
							"value" => request ( "record_order_date" ,date('Y-m-d',time())),
							"style"=>"width:90px"
						) )?>
					</td>
					<th>
						排序
					</th>
					<td>
						<?php
                            echo Q::control ( 'dropdownlist', 'sort', array (
                            'items'=>array('D3'=>'D3','S1'=>'S1'),
                            'value' => request('sort','D3'),
                            'style'=>'width:90px'
                         ) )?>
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
							<i class="icon-download"></i>
							导出
						</button>
					</td>
				</tr>           
			</tbody>           
		</table>  
	</div>
	<table class="FarTable" style="width:40%;">
	   <thead>
	       <tr><th style="width:60px;">No.</th><th>阿里单号</th></tr>
	   </thead>
	   <tbody>
	       <?php $i=1; foreach ($orders as $order):?>
	       <tr><td><?php echo $i++?></td><td><?php echo $order->ali_order_no?></td></tr>
	       <?php endforeach;?>
	   </tbody>
	</table>
</form>
    
<?PHP $this->_endblock();?>

