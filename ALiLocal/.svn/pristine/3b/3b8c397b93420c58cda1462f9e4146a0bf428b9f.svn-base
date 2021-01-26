<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<style>
	.display{display:none}
</style>
<div style="width:100%;height:200px;line-height:80px;" >
	<form method="post" enctype="multipart/form-data">
	<table>
			<tbody>
				<tr>
					 <th class="required-title">客户</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "customs_code", array (
							"items" =>Helper_Array::toHashmap(Customer::find()->getAll(), 'customs_code','customer'),
						    "empty" =>true,
						    "required"=>'required'
						) )?>&nbsp;&nbsp;&nbsp;
					</td>
				    <th class="required-title">事件代码</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "event_code1", array (
						    "items" =>Event::$s_event,
						    "value"=>request('event_code'),
							"style" => "width:205px",
						    "empty" =>true,
						    "required"=>'required'
						) )?>
						<?php
						echo Q::control ( "dropdownbox", "event_code2", array (
						    "items" =>Event::$pl_event,
						    "value"=>request('event_code'),
							"style" => "width:205px;display:none",
						    "empty" =>true,
						    "required"=>'required'
						) )?>
						&nbsp;&nbsp;&nbsp;
					</td>
					
					<th>事件时间</th>
					<td>
						<input class="easyui-datetimebox"
						name="event_time"
						value="<?php echo request('event_time')?>"
						style="width: 150px"/>&nbsp;&nbsp;&nbsp;
					</td>
															
					<th>船期</th>
					<td>
						<input class="easyui-datebox"
						name="sailling_date"
						value="<?php echo request('sailling_date')?>"
						style="width: 150px"/>&nbsp;&nbsp;&nbsp;
					</td>
					
					<td>
						<input type="checkbox" name="fail" value="1"><span style="color: red;font-size:15px">失败事件</span> &nbsp;&nbsp;&nbsp;
					</td>
					<td colspan="7">
						  <input type="file" name="file"  accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                	      <button type="submit" class='btn btn-info'                                             
                	          onclick="this.disabled='disabled';MessagerProgress('<?php echo "导入中......"?>');$(form).submit()">  
                	     		<span><?php echo '导入'?></span>
                	      </button> 
                	      <a href='<?php echo url('warehouse/DownloadTemp')?>' class="btn btn-success">
                	       <i class="icon-cloud-download"></i>
                	                       下载模板
                	      </a>
					</td>

				</tr>
		
			</tbody>
	</table>
	</form>
</div>
<?php if (!empty($result)):?>
<table class="table-bordered table">
	<tbody>
		<tr>
			<th><?php echo '行数' ?></th>
			<th><?php echo '导入情况' ?></th>
		</tr>
		<?php foreach ($result as  $i => $re):?>
		<tr>
			<td><?php echo $i+2?></td>
			<td style="<?php echo $re == '成功'?'color:green;':'color:red;'?>"><?php print_r($re)?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php endif;?>
<?PHP $this->_endblock();?>
<script>
	$('#customs_code').change(function(){
		if($(this).val() == 'ALPL'){
			$('#event_code2').css('display','inline');
			$('#event_code1').css('display','none');
			$('#event_code2').attr('name','event_code');
			$('#event_code1').attr('name','event_code2');
		}else{
			$('#event_code2').css('display','none');
			$('#event_code1').css('display','inline');
			$('#event_code2').attr('name','event_code2');
			$('#event_code1').attr('name','event_code');
		}
	})
</script>
