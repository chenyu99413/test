<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>角色编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="post">
	<div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th width="80" class="required-title">角色名称</th>
					<td>
						<input name="role_name" value="<?php echo $role->role_name?>"
							required="required" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo Q::control('purview','description', array("checked"=>$purviews))?>
	<table class="FarTable">
		<caption>额外权限</caption>
		<tr>
			<td width="100">订单明细</td>
			<td>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="edit-order"
						<?php if (in_array('edit-order', $purviews)) echo 'checked'?>>
					修改城市、邮编
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="edit-pickup_company"
						<?php if (in_array('edit-pickup_company', $purviews)) echo 'checked'?>>
					修改取件网点
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-product"
						<?php if (in_array('order-product', $purviews)) echo 'checked'?>>
					订单产品修改
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-return-paid"
						<?php if (in_array('order-return-paid', $purviews)) echo 'checked'?>>
					订单退回已支付
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-transfer-paid"
						<?php if (in_array('order-transfer-paid', $purviews)) echo 'checked'?>>
					批量转成已支付
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-return-checked"
						<?php if (in_array('order-return-checked', $purviews)) echo 'checked'?>>
					批量退回已核查
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-transfer-sign"
						<?php if (in_array('order-transfer-sign', $purviews)) echo 'checked'?>>
					批量转签收
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-return-true"
						<?php if (in_array('order-return-true', $purviews)) echo 'checked'?>>
					订单批量一键确认
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="order-receivable-payment"
						<?php if (in_array('order-receivable-payment', $purviews)) echo 'checked'?>>
					订单应收应付
				</label>
			</td>
		</tr>
		<tr>
			<td width="100">抵达总单</td>
			<td>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="edit-totalorderin"
						<?php if (in_array('edit-totalorderin', $purviews)) echo 'checked'?>>
					查看启程明细
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="totalout-ture"
						<?php if (in_array('totalout-ture', $purviews)) echo 'checked'?>>
					启程确认
				</label>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="totalin-ture"
						<?php if (in_array('totalin-ture', $purviews)) echo 'checked'?>>
					抵达确认
				</label>
			</td>
		</tr>
		<tr>
			<td width="100">退件订单</td>
			<td>
				<label class="checkbox_purview">
					<input type="checkbox" name="purview_path[]"
						value="tuijian-status"
						<?php if (in_array('tuijian-status', $purviews)) echo 'checked'?>>
					退件订单状态修改
				</label>
				
			</td>
		</tr>
	</table>
	<div class="FarTool text-center">
		<a class="btn btn-inverse" href="<?php echo url('role/search')?>">
			<i class="icon-reply"></i>
			返回
		</a>
		<button class="btn btn-primary" type="submit" onclick="Save();">
			<i class="icon-save"></i>
			保存
		</button>
	</div>
	<input type="hidden" name="role_id" value="<?php echo $role->role_id?>" />
	<input id="purviews_hidden" type="hidden" name="purviews" />
</form>
<script type="text/javascript">
    /**
     * 保存
     */
    function Save(){
    	var json = "";
    	$(".checkbox_purview").each(function(obj){
    		var checkbox = $(this).children().eq(0);
    		if(checkbox.attr("checked") == "checked"){
    			var name= $(this).text().trim();
    			json += '{"name":"'+name+'","path":"'+checkbox.val()+'"},';
    		}
    	});
    	json = "["+json.substring(0,json.length-1)+"]";
    	$("#purviews_hidden").val(json);
    }
</script>

<?PHP $this->_endblock();?>