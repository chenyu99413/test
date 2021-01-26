<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="panel panel-info">
<!-- 	<div class="panel-heading">华磊订单导入</div> -->
	<div class="panel-body">
		<form style="margin: 0; padding: 0;" class="" method="post"
			action="<?php echo url('warehouse/hlimport') ?>" enctype="multipart/form-data">
			<input type="file" name="file" style="border: 1px solid #ccc;">
			<span class="required-title">产品：</span>
			<?php
                 echo Q::control ( 'dropdownbox', 'service_code', array (
                 'items'=>Helper_Array::toHashmap(Product::find()->asArray()->getAll(),'product_name','product_name'),
                 'empty'=>true,
                 'style'=>'height:25px;',
                 'value' => request('service_code'),
                 'required' => 'required'
            ) )?>
            <span class="required-title">渠道：</span>
            <?php
                 echo Q::control ( 'dropdownbox', 'channel_id', array (
                 'items'=>Helper_Array::toHashmap(Channel::find()->asArray()->getAll(),'channel_id','channel_name'),
                 'empty'=>true,
                 'style'=>'height:25px;',
                 'value' => request('channel_id'),
                 'required' => 'required'
            ) )?>
			<button type="submit" class="btn btn-small btn-primary" id="submit">
				<i class="icon-cloud-upload"></i>
				上传
			</button>
		</form>
	</div>
</div>
<div class="span8" style="margin-left:0px;">
<table class="table table-bordered table-hover table-condensed">
	<caption>
		<span class="label label-warning">导入结果</span>
	</caption>
	<thead>
		<tr>
			<th style="width:30px;">No.</th>
			<th style="width:50px;">原单号</th>
			<th style="width:30px;">状态</th>
			<th style="width:100px;">信息</th>
		</tr>
	</thead>
	<tbody>
	<?php if(@$sheet):?>
		<?php $i=0?>
		<?php foreach ($sheet as $val):?>
		<?php if(!isset($val['原单号'])) continue;?>
		<tr>
			<td style="width:30px;"><?php echo ++$i?></td>
			<td><?php echo $val['原单号']?></td>
			<td><?php echo $val['结果']?></td>
			<td><?php echo $val['信息']?></td>
		</tr>
		<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>
</div>
<?PHP $this->_endblock();?>
<?PHP $this->_block('page_js');?>
<script type="text/javascript">
$(function(){
	$("#submit").click(function(){
		$("#overlay").css('display','block');
	})
})
</script>
<?PHP $this->_endblock();?>

