<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
取件员管理
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
</div>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				   <th>微信ID</th>
				   <td>
				   	   <input type="text" name="wechat_id" value="<?php echo request('wechat_id')?>" 
				         	  placeholder="精确搜索" />
				   </td>
				   <th>微信号</th>
				   <td>
				   	   <input type="text" name="wechat_no" value="<?php echo request('wechat_no')?>" 
				         	  placeholder="模糊搜索" />
				   </td>
				   <th>取件员姓名</th>
				   <td>
				   	   <input type="text" name="name" value="<?php echo request('name')?>" 
				         	  placeholder="模糊搜索" />
				   </td>
				   <th>性别</th>
				   <td>
				   	   <?php 
	                   echo Q::control ( "dropdownbox", "gender", array (
							"items" => array('男'=>'男','女'=>'女'),
						    "value" => request('gender'),
	                   	    "empty" => true
						) )?>
				   </td>
				   <th>状态</th>
				   <td>
				   	   <?php 
	                   echo Q::control ( "dropdownbox", "status", array (
							"items" => array('0'=>'未认证','1'=>'已认证'),
						    "value" => request('status'),
	                   		"empty" => true
						) )?>
				   </td>
				   <th>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a href="<?php echo url('pickup/editpickupmember')?>" class="btn btn-small btn-success">
		               	  <i class="icon-plus"></i>
		               	      新建
		               </a>
	               </th>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th style="width:40px;">No</th>
				<th style="width:220px;">微信ID</th>
				<th style="width:220px;">微信号</th>
				<th>姓名</th>
				<th>头像</th>
				<th style="width:40px;">性别</th>
				<th style="width:60px;">状态</th>
				<th style="width:160px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $status = array("0"=>"未认证","1"=>"已认证"); $i=1; foreach ($list as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->wechat_id?></td>
				<td><?php echo $temp->wechat_no?></td>
				<td><?php echo $temp->name?></td>
				<td style="text-align:center;">
					<img src="<?php echo $temp->img_url?>"
						style="max-width: 64px; max-height: 64px;" />
				</td>
				<td><?php echo $temp->gender?></td>
				<td><?php echo $status[$temp->status]?></td>
				<td>
					<a href="<?php echo url('pickup/editpickupmember',array('id'=>$temp->id))?>"
					   class="btn btn-mini btn-info">
		               	  <i class="icon-edit"></i>
		               	      修改
	                </a>
	                <a class="btn btn-mini btn-danger" href="javascript:void(0);"
						onclick="if(DeleteRow(this)){MessagerProgress('删除');window.location.href='<?php echo url('pickup/deletemember',array('id'=>$temp->id));?>';}else return false;">
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
$(function(){
	$('.delete').on('click',function(){
		
	});
});
</script>
<?PHP $this->_endblock();?>

