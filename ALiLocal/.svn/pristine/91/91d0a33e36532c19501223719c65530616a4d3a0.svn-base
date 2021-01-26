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
			action="" enctype="multipart/form-data">
			<div>
			客户：
			<?php
                    echo Q::control('dropdownbox','customer_id',
                    array(
                    'items' => Helper_Array::toHashmap(Customer::find()->asArray()->getAll(),'customer_id','customer'),
                    'empty' => true,
                    'value' => request('customer_id'),
                    'style' => 'height:100%'
                    ) )?>
			<input type="file" name="file" style="border: 1px solid #ccc;">
			<!-- 
			<span class="required-title">样式：</span>
			<?php
                 echo Q::control ( 'dropdownbox', 'type', array (
                 'items'=>array('0'=> '品名横向', '1' => '品名纵向'),
                 'style'=>'height:25px;',
                 'required' => 'required'
            ) )?>
            -->
			<button type="submit" class="btn btn-small btn-primary" id="submit">
				<i class="icon-cloud-upload"></i>
				上传
			</button>
			下载模板：<a href="<?php echo $_BASE_DIR?>public/download/订单导入-横向.xlsx">横向</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $_BASE_DIR?>public/download/订单导入-纵向.xlsx">纵向</a>
			</div>
		</form>
	</div>
</div>
<div class="span12" style="margin-left:0px;">
	<?php if(!empty($errors)):?>
	<table class="FarTable" style="width:97%;margin-top:-5px;">
		<tr>
			<th>订单号</th>
			<th>错误信息</th>
		</tr>
		<?php foreach ($errors as $k => $error):?>
		<tr>
			<td><?php echo $k;?></td>
			<td>
			<?php foreach ($error as $k2 => $e):?>
				<?php $k3 = $k2+1;echo $k3.'、'.$e.' &nbsp;';?>
			<?php endforeach;?>
			</td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
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

