<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
自动发送规则
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('product/ruleedit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:50%;">
		<thead>
			<tr>
			    <th style="width:30px;">No</th>
				<th>规则名称</th>
				<th style="width:130px;">操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php $i=1; foreach($list as $value):?>
			<tr>
			    <td><?php echo $i++;?></td>
				<td nowrap="nowrap">
					<a
						href="<?php echo url("product/ruleedit",array("id"=>$value->id))?>"><?php echo $value->automatic_email_rule?></a>
				</td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("product/ruleedit",array("id"=>$value->id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<input type="button" class="btn btn-small btn-danger" href="javascript:void(0)" data="<?php echo $value->id?>" onclick="del(this)" value="删除">
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>
<script type="text/javascript">
function del(obj){
	if (confirm('是否删除?')) {
    	var id=$(obj).attr('data');
    	$.ajax({
    		url:'<?php echo url('product/ruledel')?>',
    		data:{id:id},
    		type:'post',
    		async:false,
    		success:function(data){
    			if(data!='delsuccess'){
    			   alert('模板不存在');
    			}else{
    				window.location.reload();
    			}
    		}
    	});
	}
}
</script>
<?PHP $this->_endblock();?>

