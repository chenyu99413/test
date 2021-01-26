<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
总单明细
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'仓库业务' => '','批量轨迹更新' => url ( 'warehouse/totaltrack' ),'总单明细' => '' 
	) 
) )?>
<label>总单单号：<?php echo request('total_list_no')?></label>
<form method="POST">
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>总单单号</th>
				<th>阿里单号</th>
				<th>末端单号</th>
				<th>最新轨迹</th>
                <th>最新轨迹时间</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($totalorder as $order):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $order->total_no?></td>
				<td><?php echo $order->ali_order_no ?></td>
				<td><?php echo $order->tracking_no?></td>
				<?php $route=Route::find('tracking_no=?',$order->tracking_no)->order('time desc')->getOne()?>
            	<td><?php echo $route->description?></td>
            	<td><?php echo Helper_Util::strDate('m-d H:i', $route->time)?></td>
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

