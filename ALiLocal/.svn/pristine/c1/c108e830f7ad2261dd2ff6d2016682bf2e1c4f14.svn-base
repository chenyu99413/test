<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>分区列表<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<form method="POST">
	<div class="FarSearch" >
		<table style="width:95%">
			<tbody>
				<tr>
					<th>仓库名</th>
					<td>
						<input name="warehouse_name" type="text" style="width: 200px"
							value="<?php echo request('warehouse_name')?>">
					</td>
					<th>仓库代码</th>
					<td>
						<input name="warehouse_code" type="text" style="width: 200px"
							value="<?php echo request('warehouse_code')?>">
					</td>
					<th>架号</th>
					<td>
						<input name="frame_code" type="text" style="width: 200px"
							value="<?php echo request('frame_code')?>">
					</td>
					<th>
					   <button class="btn btn-primary btn-small" type="submit" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               
				  	</th>
				</tr>           
			</tbody>           
		</table>  
	</div>
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('warehouse/posedit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
		<?php
		echo Q::control ( "uploadfile", "file", array (
			"url" => url ( "warehouse/posimport" ) 
		) )?>
		<a class="btn btn-success" href="<?php echo $_BASE_DIR?>public/download/pos.xls">
			<i class="icon-arrow-down"></i>
			下载模板
		</a>
	</div>
	<table class="FarTable" style="width:100%;">
		<thead>
			<tr>
				<th>部门</th>
				<th>仓库名</th>
				<th>仓库代码</th>
				<th>库号</th>
				<th>区号</th>
				<th>架号</th>
				<th>层号</th>
				<th>位号</th>
				<th>创建人</th>
				<th>创建时间</th>
				<th>备注</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($pos as $value):?>
			<tr id="<?php echo $value->id?>">
				<td nowrap="nowrap"><?php echo Department::find('department_id=?', $value->department_id)->getOne()->department_name;?></td>
				<td nowrap="nowrap"><?php echo $value->warehouse_name?></td>
				<td nowrap="nowrap"><?php echo $value->warehouse_code?></td>
				<td nowrap="nowrap"><?php echo $value->warehouse_no?></td>
				<td nowrap="nowrap"><?php echo $value->area_code?></td>
				<td nowrap="nowrap"><?php echo $value->frame_code?></td>
				<td nowrap="nowrap"><?php echo $value->floor_code?></td>
				<td nowrap="nowrap"><?php echo $value->tag_code?></td>
				<td nowrap="nowrap"><?php echo $value->operation_name?></td>
				<td nowrap="nowrap"><?php echo date('Y-m-d H:i:s',$value->create_time)?></td>
				<td nowrap="nowrap"><?php echo $value->note?></td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("warehouse/posedit",array("id"=>$value->id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<a class="btn btn-mini btn-danger" href="javascript:void(0);"
						onclick="del(<?php echo $value->id?>);">
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
<script type="text/javascript">
	function del(id){
		if(confirm("确认删除吗？")){
			$.ajax({
	               type: "POST",//规定传输方式
	               url: "<?php echo url('warehouse/posdel')?>",//提交URL
	               data: {'id':id},//提交的数据
	               dataType: "json",
	               success: function(data){ //交互成功回调
		               if(data.code){
		            	   $("#"+id).remove();
		               }else{
							alert('删除失败');
							window.location.reload();
		               }
	               }
	            });
        }
	}
</script>
<?PHP $this->_endblock();?>

