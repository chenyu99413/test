<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div style="width: 300px;">
	<form style="margin-bottom: 0px;" id="edit-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 100px;">
				<col style="width: 230px;">
			</colgroup>
			<tbody>
				<?php 
					$cycle = array(''=>'','0'=>'每日','1'=>'每周','2'=>'每月');
					$type = array(''=>'','0'=>'票数','1'=>'实重','2'=>'计费重');
				?>
				<tr>
					<th>周期</th>
					<td>
						<?php
                            echo Q::control ( 'dropdownlist', 'cycle', array (
                            'items'=>$cycle,
                            'value' => $limit_amount->cycle,
                            'style'=>'width:135px',
                         ) )?>
						<input type="hidden" name="limitation_amount_id"
							value="<?php echo request('limitation_amount_id')?>" />
						<input type="hidden" name="channel_id" 
							value="<?php echo request('channel_id')?>" />
					</td>
				</tr>
				<tr>
					<th>类型</th>
					<td>
						<?php
                            echo Q::control ( 'dropdownlist', 'type', array (
                            'items'=>$type,
                            'value' => $limit_amount->type,
                            'style'=>'width:135px',
                         ) )?>
					</td>
				</tr>
				<tr>
					<th>仓库</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownbox', 'department_id', array (
                        'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                        'empty'=>true,
                        'style'=>'width:135px',
                        'value' => $limit_amount->department_id,
                        ) )?>
					</td>
				</tr>
				<tr>
					<th>最大值</th>
					<td>
						<input type="text" name="max_value"
							value="<?php echo $limit_amount->max_value?>" style="width: 125px;">
					</td>
				</tr>
				<tr>
					<th>国家组</th>
					<td>
						<?php
	                        echo Q::control ( 'dropdownbox', 'country_group_id', array (
	                        'items'=>Helper_Array::toHashmap(CodeCountryGroup::find()->asArray()->getAll(), 'id','name'),
	                        'empty'=>true,
	                        'style'=>'width:135px',
	                        'value' => $limit_amount->country_group_id,
                        ) )?>
					</td>
				</tr>
				<tr>
					<th>生效时间</th>
					<td>
						<?php
						echo Q::control ( "datebox", "effect_time", array (
							"value" => Helper_Util::strDate('Y-m-d', $limit_amount->effect_time),
							"style"=>"width:125px",
						    "required"=>"required"
						) )?>
					</td>
				</tr>
				<tr>
					<th>失效时间</th>
					<td>
						<?php
						echo Q::control ( "datebox", "failure_time", array (
							"value" => Helper_Util::strDate('Y-m-d', $limit_amount->failure_time),
							"style"=>"width:125px",
						    "required"=>"required"
						) )?>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="save">
				<i class="icon-save"></i>
				保存
			</button>
		</div>
	</form>
</div>
<?PHP $this->_endblock();?>
<?PHP $this->_block('page_js');?>
<script type="text/javascript">
$(function(){
	// 保存
	$('#save').on('click',function(){
		var saveload = layer.load(1);
		var index = parent.layer.getFrameIndex(window.name);
		var form_data = $('#edit-form').serialize();
		$.ajax({
			url: '<?php echo url("/EditLimitSave")?>',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(data) {
			layer.close(saveload);
			parent.layer.msg(data.message);
			if (data.success) {
				parent.layer.close(index);
				parent.location.reload();
			}
		})
		.fail(function(data) {
			layer.close(saveload);
			parent.layer.alert('发生内部错误，暂时无法修改');
		});
	});
});
</script>
<?PHP $this->_endblock();?>
