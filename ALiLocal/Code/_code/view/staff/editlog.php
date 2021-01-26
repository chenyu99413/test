<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>员工权限修改日志<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="POST" action="<?php echo url('staff/editlog')?>">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>员工</th>
				    <td>
				    	<input type="text" name="staff_name" placeholder="模糊搜索" value="<?php echo request("staff_name")?>">
				    </td>
				    <th>修改时间</th>
				    <td>
					<?php
					echo Q::control ( "datebox", "start_time", array (
						"value" => request("start_time"),
						"style"=>"width:125px"
					) )?>
					</td>
					<th>到</th>
					<td>
					<?php
					echo Q::control ( "datebox", "end_time", array (
						"value" => request("end_time"),
						"style"=>"width:125px"
					) )?>
					</td>
				    <th>修改人</th>
				    <td>
				    	<input type="text" name="operator" placeholder="模糊搜索"  value="<?php echo request("operator")?>">
				    </td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
<table class="FarTable">
	<thead>
		<tr>
			<th style="width: 80px;">员工</th>
			<th style="width: auto;">修改内容描述</th>
			<th style="width: 135px;">修改时间</th>
			<th style="width: 80px;">修改人</th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0;?>
		<?php foreach ($logs as $log):?>
		<tr>
			<td><?php echo $log->edit_staff_name;?></td>
			<td><?php echo $log->edit_contect;?></td>
			<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $log->edit_time);?></td>
			<td><?php echo $log->operator_name;?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>