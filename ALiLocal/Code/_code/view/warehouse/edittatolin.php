<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
包裹抵达扫描编辑
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'包裹抵达扫描管理' => '',
			'包裹抵达扫描列表' => url ( 'warehouse/totalinlist' ),
			'包裹抵达扫描编辑' => ''
		) 
	) );
?>
<form method="post">
	<div class="FarSearch span10" style="padding:5px;">
	<div>
		<table>
			<tbody>
				<tr>
					<th>抵达仓</th>
    				<td><?php echo $dpms[MyApp::currentUser('department_id')]?></td>
    				<th class="required-title">启程仓</th>
    				<td><?php
                        echo Q::control ( 'dropdownbox', 'out_department_id', array (
                        'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                        'empty'=>true,
                        'style'=>'width:70px',
                        'required'=>'required',
                        'value' => request('out_department_id'),
                        ) )?>
					</td>
					<th class="required-title">启程总单号</th>
					<td>
						<?php
						    ($totalout)?$items = Helper_Array::toHashmap($totalout, 'id','text'):$items='';
                			echo Q::control ( 'dropdownlist', 'service_code', array (
                				'items' => $items,
                				'value' => request('service_code'),
                			    'required'=>'required',
                				'style' => 'width: 125px'
                			) )?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('warehouse/totalinlist')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button class="btn btn-primary" name="save" value="save">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
</form>
<?PHP $this->_endblock();?>
<script>
$('#out_department_id').bind('change',function(){
	var out_department_id = $(this).val();
	window.location.href = "<?php echo url('/edittatolin')?>"+"?out_department_id="+out_department_id;
});
</script>