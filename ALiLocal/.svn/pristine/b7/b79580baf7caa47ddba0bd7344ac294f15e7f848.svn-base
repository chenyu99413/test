<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    订单查询
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<style>
td {
     word-break: break-all;
}
.tabs li a.tabs-inner{
	padding:0 5px;
}
.badge {
	padding-left:5px;
	padding-right:5px;
}
</style>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.browser.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
	<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>

<form method="POST">
	
	<div class="tabs-container " style="min-width: 1148px;">
		<div style="width: 100%;">
			<table class="FarTable" style="width:320px;max-width: none;">
            		<thead>
            			<tr>
            			    <th width="20px">序号</th>
            				<th width=80px>阿里单号</th>
        					<th width=60px>订单创建时间</th>
            				<th width="20px">操作</th>
            			</tr>
            		</thead>
            		<tbody>
            		<?php $i=1?>
            		<?php foreach ($orders as $order):?>
            			<tr>
            			    <td align="center"><?php echo $i?></td>
            				<td>
        					    <a  target="_blank"
            					    href="<?php echo url('order/detail', array('order_id' => $order->order_id))?>">
            					    <?php echo $order->ali_order_no ?>
            					</a>
            				</td>
            				<td align="center" title="<?php echo Helper_Util::strDate('m-d H:i:s', $order->create_time)?>"><?php echo Helper_Util::strDate('m-d H:i', $order->create_time)?></td>
            				<td align="center">
            					<a class="btn btn-mini btn-danger" target="_blank" href="<?php echo url('order/orderreturn', array('ali_order_no' => $order->ali_order_no,'return_id'=>''))?>">
            						退件
            					</a>
            				</td>
            			</tr>
            			<?php $i++;?>
            		<?php endforeach;?>
            		</tbody>
            	</table>
            	</div>
	</div>
</form>
<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
<script type="text/javascript">

</script>
<?PHP $this->_endblock();?>

