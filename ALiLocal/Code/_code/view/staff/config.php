<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
	<form action="" method="post">
	<table class="FarTable">
	<tr>
		<th style="text-align:center;">关账日期</th>
		<td>
			<input size="10" type="number" name="set[closeBalanceDay]" value="<?php echo Config::get('closeBalanceDay')?>">号， 23:59:00
		</td>
	</tr>
	</table>
	<input type="submit" value="保存" class="btn">
	</form>
</div>    
<?PHP $this->_endblock();?>

