<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>渠道分组列表<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('channel/channelgroupedit');?>">
			<i class="icon-add"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:50%;">
		<thead>
			<tr>
				<th width=200>渠道分组名称</th>
				<th width=120>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($list as $item):?>
			<tr>
				<td>
					<a href="<?php echo url('channel/channelgroupedit',array('channel_group_id'=>$item->channel_group_id))?>"><?php echo $item->channel_group_name;?></a>
				</td>
				<td>
					<a class="btn btn-mini"
						href="<?php echo url("channel/channelgroupedit",array('channel_group_id'=>$item->channel_group_id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<a class="btn btn-mini btn-danger"
						href="javascript:void(0)" onclick="del(this)" data="<?php echo $item->channel_group_id?>">
						<i class="icon-remove"></i>
						删除
					</a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>

<script type="text/javascript">
	/**
 	 * 删除渠道分组--（不能删除分组下有绑定渠道的组）
	 */
	function del(obj){
		$.messager.confirm('删除渠道分组','确认删除？',function(r){
			if(r){
				$.ajax({
					url:'<?php echo url('/channelgroupdel')?>',
					data:{channel_group_id:$(obj).attr('data')},
					type:'post',
					success:function(data){
						if(data=='fail'){
							$.messager.alert('删除失败','该渠道分组已绑定渠道，无法删除');
						}
						if(data=='error'){
							$.messager.alert('删除失败','异常错误，渠道分组不存在！');
						}
						if(data=='success'){
							window.location.reload();
						}
					}
				})
			}
		})
	}
</script>

<?PHP $this->_endblock();?>