<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>员工编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'员工管理' => '',
			'员工查询' => url ( 'staff/search' ),
			'员工编辑' => ''
		) 
	) );
?>
<form method="POST" onsubmit="return Save();">
	<div class="row-fluid">
		<div class="span7 FarSearch">
			<table>
				<tbody>
					<tr>
						<th width=80 class="required-title">工号</th>
						<td width=120>
							<input id="staff_code" type="text" type="text"
								name="staff_code"
								value="<?php echo $staff->staff_code;?>"
								style="ime-mode: disabled" required="required" />
						</td>
					</tr>
					<tr>
						<th class="required-title">姓名</th>
						<td>
							<input type="text" id="user_name" name="staff_name" autocomplete="off"
								value="<?php echo $staff->staff_name?>" maxlength="10"
								required="required" />
						</td>
					</tr>
					<tr>
						<th class="required-title">密码</th>
						<td>
							<input type="text" name="password" autocomplete="off"
								value="<?php echo $staff->password?>" required="required" />
						</td>
					</tr>
					<tr>
						<th class="required-title">部门</th>
						<td>
							<?php
						      echo Q::control ( "dropdownbox", "department", array (
							"items" => Helper_Array::toHashmap ( department::find ()->getAll (), "department_id", "department_name" ),
							"value" => $staff->department_id,
							"empty" => "true","style" => "width:150px" 
						) )?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="span5">
		          业务相关部门
			<label style="margin-left: 4px;">
				<input id="check_all" type="checkbox" style="margin-top: -4px;"
					onclick="CheckAll(this);" />
				全选
			</label>
			<div class="easyui-panel" style="padding: 5px">
				<ul id="relevant_tree" class="easyui-tree"
					data-options="url:'<?php echo url('department/departmenttree',$relevants)?>',method:'get',checkbox:true,cascadeCheck:false"></ul>
			</div>
			权限角色
			<div class="FarTool easyui-panel" style="padding: 5px">
			<?php foreach($roles as $role):?>
			    <label style="margin-left: 4px;">
					<input type="checkbox" name="role[]" style="margin-top: -4px;"
						value="<?php echo $role['id']?>"
						<?php echo $role['checked']?"checked='checked'":''?> />
					<?php echo $role['name']?>
				</label>
			<?php endforeach;?>
			</div>
		</div>
	</div>
	<div class="row text-center">
		<a class="btn btn-inverse" href="<?php echo url('staff/search')?>">
			<i class="icon-reply"></i>
			返回
		</a>
		<button class="btn btn-primary" type="submit">
			<i class="icon-save"></i>
			保存
		</button>
	</div>
	<input id="relevant_hidden" type="hidden" name="relevant" />
	<input id="staff_id" type="hidden" name="staff_id" value="<?php echo $staff->staff_id?>" />
</form>

<script type="text/javascript">
/**
 * 全选
 */
function CheckAll(obj){
	var check = obj.checked ? "check" : "uncheck";
	var roots = $("#relevant_tree").tree("getRoots");
	for(var i=0;i<roots.length;i++){
		var notes = $("#relevant_tree").tree("getChildren", roots[i]);
		for(var i=0;i<notes.length;i++){
			$("#relevant_tree").tree(check,notes[i].target);
		}
	}
}
/**
 * 保存 
 */
function Save(){
	if($("#relevant_tree").tree('getChecked').length == 0){
		$.messager.alert('Error', '业务相关部门必须选择');
		return false;
	}
	var flag=false;
	//相关部门
	var relevant = "";
	$($("#relevant_tree").tree("getChecked")).each(function(){
		relevant += $(this)[0].id+",";
	});
	$("#relevant_hidden").val(relevant.substring(0,relevant.length-1));
	//判断员工工号是否重复
	$.ajax({
		type:'post',
		url:'<?php echo url('staff/codecheck')?>',
		data:{'staff_code':$("#staff_code").val(),'staff_id':$("#staff_id").val()},
		async:false,
		success:function(data){
			if(data=='success'){
				flag=true;
			}else{
				$.messager.alert('Error', '用户名已存在');
			}
		}
	});
	return flag;
}
</script>
<?PHP $this->_endblock();?>