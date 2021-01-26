<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
包裹起程扫描编辑
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
if (request('total_id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'包裹启程扫描管理' => '',
			'包裹启程扫描列表' => url ( 'warehouse/totaloutlist' ),
			'包裹启程扫描编辑' => url ( 'warehouse/edittatolout', array (
				'total_id' => $totalout->total_id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'包裹启程扫描管理' => '',
			'包裹启程扫描列表' => url ( 'warehouse/totaloutlist' ),
			'新建包裹启程扫描' => url ( 'warehouse/edittatolout' ) 
		) 
	) );
}
?>
<form method="post">
	<div class="FarSearch" style="padding:5px;">
	<div>
		<table>
			<tbody>
				<tr>
					<th>启程仓</th>
    				<td><?php echo $dpms[MyApp::currentUser('department_id')]?></td>
				    <?php if ($order_count>0):?>
    				<th class="required-title">抵达仓</th>
    				<td><?php
                        echo Q::control ( 'dropdownbox', 'in_department_id', array (
                        'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                        'empty'=>true,
                        'style'=>'width:70px',
                        'required'=>'required',
                        'readonly'=>'readonly',
                        'value' => $totalout->in_department_id,
                        ) )?>
					</td>
					<th>产品</th>
					<td>
						<input readonly="readonly" class="easyui-combotree" name="product[]" data-options="url:'<?php echo url('warehouse/producttree',array('checked'=>implode(',',request('product',array()))))?>'
								, method:'get'
								, multiple:true,width:'180px',panelHeight:'100px'" value="<?php echo $totalout->service_code?>" />
					</td>
					<?php else: ?>
    				<th class="required-title">抵达仓</th>
    				<td><?php
                        echo Q::control ( 'dropdownbox', 'in_department_id', array (
                        'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                        'empty'=>true,
                        'style'=>'width:70px',
                        'required'=>'required',
                        'value' => $totalout->in_department_id,
                        ) )?>
					</td>
					<th>产品</th>
					<td>
						<input class="easyui-combotree" name="product[]" data-options="url:'<?php echo url('warehouse/producttree',array('checked'=>implode(',',request('product',array()))))?>'
								, method:'get'
								, multiple:true,width:'180px',panelHeight:'100px'" value="<?php echo $totalout->service_code?>" />
					</td>
					<?php endif;?>
					<th>收件人</th>
					<td>
						<input name="consignee_name" type="text" style="width: 110px" 
						       value="<?php echo $totalout->consignee_name?>" />
					</td>
				</tr>
				<tr>
					<th>接收电话</th>
					<td>
						<input name="consignee_phone" type="text" style="width: 110px" 
						       value="<?php echo $totalout->consignee_phone?>" />
					</td>
					<th>接收地址</th>
					<td>
						<input name="consignee_address" type="text" style="width: 110px" 
						       value="<?php echo $totalout->consignee_address?>" />
					</td>
					<th>转运方式</th>
					<td>
						<input name="express_company" type="text" style="width: 110px" 
						       value="<?php echo $totalout->express_company?>" />
					</td>
					<th>转运单号</th>
					<td>
						<input name="express_no" type="text" style="width: 450px" 
						       value="<?php echo $totalout->express_no?>" />
					</td>
				</tr>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('warehouse/totaloutlist')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<?php if($totalout->status == '0'):?>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    		<?php endif;?>
    	</div>
    </div>
	<?php if(request('product')):?>
        <input id="hidden_product" type="hidden" name="product_"
    	value="<?php echo implode(',', request('product',array()))?>" />
    <?php endif;?>
</form>
<?PHP $this->_endblock();?>