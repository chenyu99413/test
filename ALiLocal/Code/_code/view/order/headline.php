<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
渠道异常件标签
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
</div>
<form method="POST">
	<div class="FarSearch" style="width: 40%">
		<table>
			<tbody>
				<tr>
					<td>
		               <a class="btn btn-small btn-success" target="_blank" href="<?php echo url('/headlineedit')?>"><i class="icon-plus"></i> 新建</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable" style="width: 40%">
		<thead>
			<tr>
				<th style="width:20px;">No</th>
				<th style="width:120px;">标签</th>
				<th style="width:60px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($list as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->headline?></td>
				<td>
				    <a class="btn btn-mini btn-primary" target="_blank" href="<?php echo url('/headlineedit',array('headline_id'=>$temp->headline_id))?>">
                       <i class="icon-edit"></i>
                                                                        编辑
                    </a>
				    <a class="btn btn-mini btn-danger" href="<?php echo url('order/hdelete', array('headline_id' => $temp->headline_id))?>">
            	       <i class="icon-trash"></i>
			                                         删除
            	    </a>
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
<div style="clear: both;"></div>
<?PHP $this->_endblock();?>

