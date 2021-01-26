<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>轨迹重查重推<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<form method="POST">
	<div class="FarSearch" >
		<table style="width:95%">
			<tbody>
				<tr>
					<th>总单号</th>
					<td>
						<input name="total_order" type="text" style="width: 200px"
							value="<?php echo request('total_order')?>">
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
		<a class="btn btn-success" href="<?php echo url('/add')?>">
			<i class="icon-plus"></i>
			新建
		</a>
		<?php
		echo Q::control ( "uploadfile", "file", array (
			"url" => url ( "/trackrecheck" ) 
		) )?>
		<a class="btn btn-success" href="<?php echo url('tracking/DownloadbatchtraceTemp')?>">
			<i class="icon-arrow-down"></i>
			下载模板
		</a>
	</div>
	<table class="FarTable" style="width:100%;">
		<thead>
			<tr>
				<th>总单号</th>
				<th>添加时间</th>
				<th>操作人</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($pos as $value):?>
			<tr id="<?php echo $value->id?>">
				<td nowrap="nowrap"><?php echo $value->total_order?></td>
				<td nowrap="nowrap"><?php echo date('Y-m-d H:i:s',$value->create_time)?></td>
				<td nowrap="nowrap"><?php echo $value->operator?></td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("/Modify",array("total_id"=>$value->id))?>">
						<i class="icon-edit"></i>
						查看
					</a>
					<!-- <a class="btn btn-mini btn-danger" href="javascript:void(0);"
						onclick="del(<?php echo $value->id?>);">
						<i class="icon-trash"></i>
						删除
					</a> -->
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

