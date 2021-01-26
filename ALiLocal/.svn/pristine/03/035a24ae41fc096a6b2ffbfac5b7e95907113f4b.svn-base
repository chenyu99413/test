<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>员工查询<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th width=80>工号</th>
					<td width=120>
						<input type="text" name="staff_code"
							value="<?php echo request("staff_code")?>" />
					</td>
					<th width=80>姓名</th>
					<td width=120>
						<input type="text" name="staff_name"
							value="<?php echo request("staff_name")?>" />
					</td>
					<td>
					   <button class="btn btn-primary btn-small">
                			<i class="icon-search"></i>
                			搜索
                		</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="FarTool">
		<a class="btn btn-success btn-small" href="<?php echo url('staff/edit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
	</div>
	<table class="FarTable FarTable-hover" style="width:60%">
		<thead>
			<tr>
				<th>工号</th>
				<th>姓名</th>
				<th>部门</th>
				<th style="width:150px;">操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($staffs as $staff):?>
			<tr class="<?php echo $staff->status == '1'?'':'error'?>">
				<td><?php echo $staff->staff_code;?></td>
				<td><a target="edit_user_<?php echo $staff->staff_id?>" href="<?php echo url("staff/edit",array("staff_id"=>$staff->staff_id))?>"><?php echo $staff->staff_name;?></a></td>
				<td><?php echo $staff->department->department_name?></td>
				<td>
					<a class="btn btn-mini"
						target="edit_user_<?php echo $staff->staff_id?>"
						href="<?php echo url("staff/edit",array("staff_id"=>$staff->staff_id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<?php if($staff->status == "0"):?>
					<a class="btn btn-mini btn-success"
					    href="javascript:void(0);"
						onclick="if(confirm('确认启用该用户吗')){window.location.href='<?php echo url('staff/interdicted',array('staff_id'=>$staff->staff_id,'status'=>'1'))?>'}else{return false;}">
						<i class="icon-unlock"></i>
						启用
					</a>
					<?php else:?>
					<a class="btn btn-mini btn-danger"
					     href="javascript:void(0);"
						 onclick="if(confirm('确认禁用该用户吗')){window.location.href='<?php echo url('staff/interdicted',array('staff_id'=>$staff->staff_id,'status'=>'0'))?>'}else{return false;}">
						<i class="icon-lock"></i>
						禁用
					</a>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>

<?PHP $this->_endblock();?>