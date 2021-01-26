<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'退件渠道管理' => url ( '/returnchannel' ),
			'退件渠道编辑' => url ( '/returnchanneledit' ),
			'退件偏派邮编设置' => ''
		) 
	) );
?>
<div>

<form method="GET" id="searchForm" style="margin-bottom:0px;">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
				    	邮编
				    </th>
				    <td>
				    	<input type="text" name="zip_code" value="<?php echo request('zip_code')?>" />
				    	<input type="hidden" name="channel_id" value="<?php echo request('channel_id')?>" />
				    </td>
				    <td>
				    	<button class="btn btn-primary btn-small" id="search">
				             <i class="icon-search"></i>
				                                         搜索
			            </button>
				    	<a class="btn btn-info btn-small"href="javascript:void(0)" onclick="fileout()"><i class="icon-upload"></i> 导入 </a> 
				    </td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
<table id="table_networkfuel" class="FarTable">
				<thead>
					<tr>
						<th width=120>邮编</th>
						<th width=120>操作</th>
					</tr>
				</thead>
				<tbody>
			    <?php foreach($channelcode as $zip):?>
				<tr id="<?php echo $zip->id?>">
						<td style="text-align: center;"><?php echo $zip->zip_code?></td>
						<td>
							<a class="btn btn-mini" href="javascript:void(0);"
								onclick="EditRow([{'type':'text'}],this);">
								<i class="icon-pencil"></i>
								编辑
							</a>
							<a class="btn btn-mini btn-danger" href="javascript:void(0);"
								onclick="DeleteRow(this);">
								<i class="icon-trash"></i>
								删除
							</a>
						</td>
					</tr>
				<?php endforeach;?>
				<tr>
						<td></td>
						<td>
							<a class="btn btn-mini btn-success" href="javascript:void(0);"
								onclick="NewRow([{'type':'text'}],this);">
								<i class="icon-plus"></i>
								新建
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
<form id="formout" action="<?php echo url('/returnchannelimportcode')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileout">
    <input type="hidden" name="import" value="导入" />
    <input type="hidden" id="" name="channel_id" value="<?php echo request('channel_id')?>">
</form>


</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
$('#search').click(function(){
	$('#searchForm').submit();
})
function fileout(){
	$('#fileout').click();
}
$(function(){
	$("#fileout").change(function(){
		$("#formout").submit();	
	})
})
/**
 * 回调 保存数据
 */
function CallBack(obj,name){
	if(obj==null){
		return false;
	}
	$.ajax({
		url:"<?php echo url('/returnchannelimportcodeadd')?>",
		type:"POST",
		data:{"channel_id":"<?php echo request('channel_id')?>",
			"id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
			"zip_code":$.trim($(obj).children().eq(0).text())},
		success:function(msg){
			$(obj).attr("id",msg);
		}
	});
}
/**
 * 回调 删除数据
 */
function DeleteBefore(obj){
	$.ajax({
		url:"<?php echo url('/ReturnDeleteZip')?>",
		type:"POST",
		data:{"id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
		success:function(msg){
		}
	});
}
</script>
