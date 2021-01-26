<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
发件人备注项
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('order/editsendcontact');?>">
			<i class="icon-add"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:70%;">
		<thead>
			<tr>
				<th width=150>发件人公司</th>
				<th width=200>备注项</th>
				<th width=160>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($contact as $item):?>
			<tr id='<?php echo $item->id?>'>
				<td><?php echo $item->sender_company?></td>
				<td><?php echo $item->comment?></td>
				<td>
				    <a class="btn btn-mini"
						href="<?php echo url("order/editsendcontact",array("id"=>$item->id))?>">
						<i class="icon-edit"></i>
						编辑</a>
					<a class="btn btn-mini btn-danger" href="javascript:void(0);" onclick="DeleteRow(this);">
						<i class="icon-trash"></i>
						删除</a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>

<script type="text/javascript">
/**
 *删除数据
 */
function DeleteBefore(obj){
	$.ajax({
		url:"<?php echo url('order/delsend')?>",
		type:"POST",
		data:{"id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
		success:function(msg){
		}
	});
}
</script>

<?PHP $this->_endblock();?>