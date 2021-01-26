<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
供应商编辑
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
if (request('supplier_id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'供应商管理' => '',
			'供应商列表' => url ( 'supplier/search' ),
			'供应商编辑' => url ( 'supplier/edit', array (
				'supplier_id' => $supplier->supplier_id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'供应商管理' => '',
			'供应商列表' => url ( 'supplier/search' ),
			'新建供应商' => url ( 'supplier/edit' ) 
		) 
	) );
}
?>
<form method="post">
	<div class="FarSearch span10" style="padding:5px;">
	<div class="span5">
		<table>
			<tbody>
				<tr>
					<th class="required-title">供应商名称</th>
    				<td>
    					<input name="supplier" id="supplier" type="text" style="width: 150px" required="required" value="<?php echo $supplier->supplier?>">
    				</td>
				</tr>
				<tr>
				    <th>合同号</th>
					<td>
						<input type="text" name="contract_code" id="contract_code" style="width:150px;"
							value="<?php echo $supplier->contract_code;?>">
					</td>
				</tr>
				<tr>
				<th>合同签订</th>
					<td>
					<?php
					echo Q::control ( 'datebox', 'contract_date', array (
						'value' => Helper_Util::strDate ( 'Y-m-d', $supplier->contract_date ) 
					) )?>
				</td>
				</tr>
				<tr>
					<th>合同到期</th>
					<td>
					<?php
					echo Q::control ( 'datebox', 'contract_expiration_date', array (
						'value' => Helper_Util::strDate ( 'Y-m-d', $supplier->contract_expiration_date ) 
					) )?>
				</td>
				</tr>
				<tr>
				    <th>状态</th>
					<td>
					<?php
					echo Q::control ( 'dropdownbox', 'status', array (
						'items' => array (
							'0' => '合作','1' => '不合作','2' => '待定' 
						),'value' => $supplier->status 
					) )?>
					</td>
					<td>
        				<a class="btn btn-mini btn-success" href="javascript:void(0);" onclick="add(this);" style="margin-left:5px;">
                    		<i class="icon-plus"></i>
                    		<?php echo '添加账单抬头'?>
                    	</a>
                	</td>
				</tr>
				<?php if($title):?>
				<?php foreach ($title as $t):?>
				<tr>
				    <th class="required-title">账单抬头</th>
    				<td>
    					<input name="title_name[]" type="text" style="width: 150px" required="required" value="<?php echo $t['name']?>">
    				</td>
    				<td>
        				<a class="btn btn-mini btn-danger" href="javascript:void(0);" onclick="deleteWares(this);" style='margin-left:5px;'>
                		  <?php echo '删除'?>
                	    </a>
    				</td>
				</tr>
				<?php endforeach;?>
				<?php else :?>
				<tr>
				    <th class="required-title">账单抬头</th>
    				<td>
    					<input name="title_name[]" type="text" style="width: 150px" required="required" value="">
    				</td>
				</tr>
				<?php endif;?>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('supplier/search')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
</form>
<script type="text/javascript">
/**
 * 添加商品页面
 */
function add(obj){
    var tr="<tr><th class='required-title'>账单抬头</th><td><input name='title_name[]' type='text' style='width: 150px' required='required' value=''></td><td><a class='btn btn-mini btn-danger' href='javascript:void(0);' onclick='deleteWares(this);' style='margin-left:5px;'><?php echo '删除'?></a></td></tr>";
    $(obj).parent().parent().parent().append(tr);
}
 
/**
 * 删除商品信息
 */
function deleteWares(obj){
  $(obj).parent().parent().remove();   
}
</script>
<?PHP $this->_endblock();?>