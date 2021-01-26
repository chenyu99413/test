<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
  无主件扫描
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR;?>public/js/jquery.sound.js"></script>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'财务管理' => '',
			'轨迹重查重推' => url ( 'tracking/list' ),
			'订单轨迹' => ''
		) 
	) );
?>
<form method="POST">
<table style="width:95%">
			<tbody>
				<tr>
					<th style="width:50px">总单号</th>
					<td>
						<input name="total_order" type="text" style="width: 200px"
							value="<?php echo date('YmdHis')?>">
					</td>
					
				</tr>           
			</tbody>           
		</table>  
<div class="FarSearch" >
	<table>
		<tr>
			<th>末端单号</th>
			<td>
				<textarea name="tracking_no"  id="tracking_no"  style="resize:both;width: 200px" value=""></textarea>
			</td>
			<td><button class="btn btn-primary btn-small" type="submit" id="search">
			             <i class="icon-search"></i>
			                                         提交
		               </button></td>
		</tr>
	</table>
</div>
</form>

<?PHP $this->_endblock();?>

