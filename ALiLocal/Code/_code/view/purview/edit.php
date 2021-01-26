<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>权限编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<style>
.checkbox_purview {
	margin-left: 4px;
}

.checkbox_purview input {
	margin-top: -4px;
}
</style>
<form method="post">
	<div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th width="80" class="required-title">角色名称</th>
					<td>
						<input name="role_name" value="<?php echo $role->role_name?>"
							required="required" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo Q::control('purview','description', array("checked"=>$purviews))?>
	<table class="FarTable">
		<caption>额外权限</caption>
		<tr>
			<td width="100">票件查询</td>
			<td>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="waybill-search-show-profit"
						<?php if (in_array('waybill-search-show-profit', $purviews)) echo 'checked'?>>
					显示毛利
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="waybill-new"
						<?php if (in_array('waybill-new', $purviews)) echo 'checked'?>>
					新建
				</label>
			</td>
		</tr>
		<tr>
			
		</tr>
	</table>
	<div class="FarTool text-center">
		<a class="btn btn-inverse" href="<?php echo url('purview/search')?>">
			<i class="icon-reply"></i>
			返回
		</a>
		<button class="btn btn-primary" type="submit" onclick="Save();">
			<i class="icon-save"></i>
			保存
		</button>
	</div>
	<input type="hidden" name="id" value="<?php echo $role->role_id?>" />
	<input id="purviews_hidden" type="hidden" name="purviews" />
</form>

<script type="text/javascript">
    /**
     * 保存
     */
    function Save(){
    	var json = "";
    	$(".checkbox_purview").each(function(obj){
    		var checkbox = $(this).children().eq(0);
    		if(checkbox.attr("checked") == "checked"){
    			var name= $(this).text().trim();
    			json += '{"name":"'+name+'","path":"'+checkbox.val()+'"},';
    		}
    	});
    	json = "["+json.substring(0,json.length-1)+"]";
    	$("#purviews_hidden").val(json);
    }
</script>

<?PHP $this->_endblock();?>