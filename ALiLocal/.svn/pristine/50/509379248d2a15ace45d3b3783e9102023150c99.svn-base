<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php echo Q::control ( 'path', '', array (
		'path' => array (
			'系统管理' => '',
			'轨迹匹配规则' => url ( 'staff/matchrules' ),
			'轨迹匹配规则导入' => ''
		) 
	) );
?>
<div class="panel panel-info">
	<div class="panel-body">
		<form style="margin: 0; padding: 0;" class="" method="post" enctype="multipart/form-data">
			<input type="file" name="file" >
			<button type="submit" class="btn btn-small btn-primary" onclick="this.disabled='disabled';MessagerProgress('<?php echo "导入中......"?>');$(form).submit()">
				<i class="icon-cloud-upload"></i>
				上传
			</button>
			<a class="btn btn-small btn-warning" href="<?php echo $_BASE_DIR?>public/download/match_rules_import.xlsx">
				<i class="icon-cloud-download"></i>
				下载模板
			</a>
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
			<th style="width:30px;">序号</th>
			<th style="width:30px;">状态</th>
			<th style="width:100px;">信息</th>
		</tr>
	</thead>
	<tbody>
	<?php if(@$sheet):?>
		<?php $i=0?>
		<?php foreach ($sheet as $val):?>
		<tr>
			<td style="width:30px;"><?php echo ++$i?></td>
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

