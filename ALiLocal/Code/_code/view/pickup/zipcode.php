<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
取件邮编
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<th>起始邮编</th>
					<td><input type="text" name='zip_code_low' value='<?php echo request('zip_code_low')?>'></td>
					<th>截止邮编</th>
					<td><input type="text" name='zip_code_high' value='<?php echo request('zip_code_high')?>'></td>
					<th>取件网点</th>
					<td>
						<?php
    					echo Q::control("dropdownbox", "pick_company", array(
    						"items" => $relevant_department_names,
    						"value" => request('pick_company'),
    						"empty" => "true"
    					))?>
					</td>
					<?php 
					$warehouse_code=Helper_Array::toHashmap(CodeWarehouse::find()->getAll(), 'warehouse', 'warehouse');
					?>
					<th>仓库代码</th>
					<td>
						<?php
    					echo Q::control("dropdownbox", "warehouse_code", array(
    						"items" => $warehouse_code,
    						"value" => request('warehouse_code'),
    						"empty" => "true"
    					))?>
					</td>
					<th>产品</th>
					<td>
						<?php
    					echo Q::control("dropdownbox", "product", array(
    						"items" => $product,
    						"value" => request('product'),
    						"empty" => "true"
    					))?>
					</td>
					<td>
						<button class="btn btn-primary btn-small" id="search">
				             <i class="icon-search"></i>
				                                         搜索
			             </button>
			             <a class="btn btn-small btn-success" href="<?php echo url('pickup/editzipcode')?>">
							  <i class="icon-plus"></i>
							      新增
						 </a>
						 <a class="btn btn-small btn-danger" href="<?php echo url('pickup/batchzipcodeimport')?>">
							批量导入
						 </a>
			             <button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
							批量导出
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th style="width:60px;">No</th>
				<th style="width:100px;">起始邮编</th>
				<th style="width:100px;">截止邮编</th>
				<th style="width:100px;">省份</th>
				<th style="width:100px;">城市</th>
				<th style="width:100px;">取件网点</th>
				<th style="width:200px;">仓库代码</th>
				<th>产品</th>
				<th style="width:120px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($zip_code as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->zip_code_low ?></td>
				<td><?php echo $temp->zip_code_high ?></td>
				<td><?php echo $temp->province ?></td>
				<td><?php echo $temp->area ?></td>
				<td><?php echo $temp->pick_company ?></td>
				<td><?php echo $temp->warehouse ?></td>
				<td><?php echo $temp->service_code?$product[$temp->service_code]:''?></td>
				<td>
					<a class="btn btn-mini btn-info" href="<?php echo url('pickup/editzipcode',array('zip_code_id'=>$temp->zip_code_id))?>">
						<i class="icon-edit"></i>
							修改
					</a>
					<a class="btn btn-mini btn-danger"
						href="javascript:void(0)" onclick="del(this)" data="<?php echo $temp->zip_code_id?>">
						<i class="icon-remove"></i>
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
function del(obj){
	$.messager.confirm('删除邮编','确认删除？',function(r){
		if(r){
			$.ajax({
				url:'<?php echo url('pickup/delzipcode')?>',
				data:{zip_code_id:$(obj).attr('data')},
				type:'post',
				success:function(data){
					if(data=='error'){
						$.messager.alert('删除失败','异常错误，渠道分组不存在！');
					}
					if(data=='success'){
						window.location.reload();
					}
				}
			})
		}
	})
}
</script>
<?PHP $this->_endblock();?>

