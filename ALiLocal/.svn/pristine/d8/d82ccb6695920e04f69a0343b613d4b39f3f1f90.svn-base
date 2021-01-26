<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <style type="text/css">
        .table>tbody>tr>td,.table>tbody>tr>th{
            border:0px;
        }
        input:enabled{
            width:90%;
        }
    </style>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<th>阿里单号</th>
					<td>
						<input style="width:160px;" type='text' value="<?php echo request('order_no')?>" name="order_no" >
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
<form method="post" action="<?php echo url('/savereplace')?>" onsubmit="return check();">
	<div class="row-fluid" style="width:100%">
	<div class="span8">
		<table class="table">
			<tr>
				<th>主单号</th>
				<td><input type="text" value="<?php echo $order->tracking_no?>" readonly></td>
				<th>新主单号</th>
				<td><input type="text" required name="new_tracking_no"></td>
				<th></th>
			</tr>
			<tr>
				<th>子单号</th>
				<td><textarea style="width:90%; height:350px" readonly><?php echo implode("\r\n", $subcodes)?></textarea></td>
				<th>新子单号</th>
				<td><textarea style="width:90%; height:350px" required name="new_subcode_no"></textarea></td>
				<td style="line-height: 250px;text-align:center"><button class="btn btn-info btn-small" id="search">
			                                        替换
		               </button></td>
			</tr>
		</table>
	</div>
</div>   
<input type="hidden" name="order_id" value="<?php echo $order->order_id?>" id="order_id">
</form>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	function check(){
		if($('#order_id').val()!='' && $('#order_id').val()!=undefined){ 
			return true;
		}else{ 
			return false;
		}
	}
</script>
