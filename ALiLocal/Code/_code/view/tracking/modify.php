<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>轨迹重查重推<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'财务管理' => '',
			'轨迹重查重推' => url ( 'tracking/list' ),
			'总单明细' => ''
		) 
	) );
?>
<form method="POST">
	<div class="FarSearch" >
		<table style="width:95%">
			<tbody>
				<tr>
					<th>阿里单号</th>
					<td>
						<input name="ali_order_no" type="text" style="width: 200px"
							value="<?php echo request('ali_order_no')?>">
					</td>
					<th>末端单号</th>
					<td>
						<input name="tracking_no" type="text" style="width: 200px"
							value="<?php echo request('tracking_no')?>">
					</td>
					<th>
					   <button class="btn btn-primary btn-small" type="submit" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               
				  	</th>
				</tr>           
			</tbody>           
		</table>  
	</div>
	<table class="FarTable" style="width:100%;">
		<thead>
			<tr>
				<th>阿里单号</th>
				<th>末端单号</th>
				<th>操作结果</th>
				<th>查看轨迹</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($trail_detail as $value):?>
		    <?php $order = Order::find('tracking_no=?',$value->tracking_no)->getOne();?>
		    
			<tr id="<?php echo $value->detail_id?>">
				<td nowrap="nowrap"><?php echo $value->ali_order_no?></td>
				<td nowrap="nowrap"><a href="<?php echo url("order/trace",array("order_id"=>$order->order_id))?>"><?php echo $value->tracking_no?></a></td>
				<td nowrap="nowrap"><?php 
				if($value->status == 0){
					echo '已请求';
				}elseif($value->status == 1){
					echo '已成功';
				}elseif ($value->status == 2){
					echo '已失败';
				}
				?></td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("order/trace",array("order_id"=>$order->order_id))?>">
						<i class="icon-edit"></i>
						查看
					</a>
					<!-- <a class="btn btn-mini btn-danger" href="javascript:void(0);"
						onclick="del(<?php echo $value->detail_id?>);">
						<i class="icon-trash"></i>
						删除
					</a> -->
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

<?PHP $this->_endblock();?>

