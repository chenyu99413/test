<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    渠道分组编辑
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php echo Q::control ( 'path', '', array (
		'path' => array (
			'渠道管理' => '',
			'渠道分组列表' => url ( 'channel/channelgroup' ),
			'渠道分组编辑' => '#'
			) ) 
		) ?>
<form method="post">
    <div class="FarSearch">
        <table>
            <tr>
                <th>渠道分组名称</th>
                <td><input name="channel_group_name"  type="text" style="width: 250px" required="required" value="<?php echo $channel_group_info->channel_group_name?>"></td>
            	<td>
                	可用部门
                	<label style="margin-left: 4px;"> 
						<input id="check_all" type="checkbox" style="margin-top:0px;"
							onclick="CheckAll(this);" />
						全选
					</label>
                <ul id="department_tree" class="easyui-tree"
					data-options="url:'<?php echo url('department/departmenttree',$department)?>',method:'get',checkbox:true,cascadeCheck:false"></ul></td>

            </tr>
            <tr>
                   <td><button type="submit" class="btn btn-small btn-success"><i class="icon-save"></i> 保存</button></td>
            </tr>
        </table>
    </div>
    <input type="hidden" name="channel_group_id" value="<?php echo $channel_group_info->channel_group_id?>">
    <input type="hidden" name="department_hidden" id="department_hidden">
</form>
<script type="text/javascript">
$(function(){
	$("form").submit(function(){
		//可用部门
		var department = "";
		$($("#department_tree").tree("getChecked")).each(function(){
			department += $(this)[0].id+",";
		});
		$("#department_hidden").val(department.substring(0,department.length-1));
	});
})

/**
 * 全选
 */
function CheckAll(obj){
	var check = obj.checked ? "check" : "uncheck";
	var roots = $("#department_tree").tree("getRoots");
	for(var i=0;i<roots.length;i++){
		var notes = $("#department_tree").tree("getChildren", roots[i]);
		for(var i=0;i<notes.length;i++){
			$("#department_tree").tree(check,notes[i].target);
		}
	}
}
</script>
<?PHP $this->_endblock();?>

