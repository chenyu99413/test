<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
黑名单
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
					<th>邮编</th>
					<td>
						<input name="consignee_postal_code" type="text" style="width: 80px"
							value="<?php echo request('consignee_postal_code')?>">
					</td>
					<th>城市</th>
					<td>
						<input name="consignee_city" type="text" style="width: 120px"
							value="<?php echo request('consignee_city')?>">
					</td>
					<th>省州</th>
					<td>
						<input name="consignee_state_region_code" type="text" style="width: 120px"
							value="<?php echo request('consignee_state_region_code')?>">
					</td>
					<th>国家</th>
					<td>
						<input name="consignee_country_code" type="text" style="width: 50px"
							value="<?php echo request('consignee_country_code')?>">
					</td>
					<th>发件人</th>
					<td>
						<input name="sender_name1" type="text" style="width: 120px"
							value="<?php echo request('sender_name1')?>">
					</td>
					<th>产品</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "product_id", array (
							"items" => Helper_Array::toHashmap ( Product::find ()->getAll (), "product_id", "product_chinese_name" ),
							"value" => request('product_id'),
							"style" => "width: 120px",
							"empty" => "true" 
						) )?>
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a class="btn btn-small btn-success" target="_blank" href="<?php echo url('/blackedit')?>"><i class="icon-plus"></i> 新建</a>
		               <button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
							<i class="icon-download"></i>
							导出
						</button>
		               <a id="import" class="btn btn-small btn-warning" href="<?php echo url('product/bimport')?>">
	                    	<i class="icon-upload"></i>
	                    	导入
                       </a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th style="width:20px;">No</th>
				<th style="width:60px;">邮编</th>
				<th style="width:120px;">城市</th>
				<th style="width:120px;">省州</th>
			    <th style="width:40px;">国家</th>
			    <th style="width:60px;">发件人</th>
				<th style="width:150px;">发件公司</th>
				<th style="">地址</th>
				<th style="width:120px;">品名</th>
				<th style="width:100px;">产品</th>
				<th style="width:120px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($list as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->consignee_postal_code?></td>
				<td><?php echo $temp->consignee_city?></td>
				<td><?php echo $temp->consignee_state_region_code?></td>
				<td><?php echo $temp->consignee_country_code?></td>
				<td><?php echo $temp->sender_name1?></td>
				<td><?php echo $temp->sender_name2?></td>
				<td><?php echo $temp->sender_street1?></td>
				<td><?php echo $temp->product_name?></td>
				<td><?php echo $temp->product->product_chinese_name?></td>
				<td>
				    <a class="btn btn-mini btn-primary" target="_blank" href="<?php echo url('/blackedit',array('blacklist_id'=>$temp->blacklist_id))?>">
                       <i class="icon-edit"></i>
                                                                        编辑
                    </a>
            	    <a class="btn btn-mini btn-danger"
						href="javascript:void(0)" onclick="del(this)" data="<?php echo $temp->blacklist_id?>">
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
		$.messager.confirm('删除','确认删除？',function(r){
			if(r){
				$.ajax({
					url:'<?php echo url('/bdelete')?>',
					data:{blacklist_id:$(obj).attr('data')},
					type:'post',
					success:function(data){
						window.location.reload();
					}
				})
			}
		})
	}
</script>
<?PHP $this->_endblock();?>

