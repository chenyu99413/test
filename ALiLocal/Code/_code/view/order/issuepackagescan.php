<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    扣件扫描
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="post">
    <div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th>阿里单号</th>
					<td>
						<input type="text" Style="width: 200px"
							name="ali_order_no"
							value=""
							required="required" />
					</td>
				</tr>
				<tr>
				    <th>问题类型</th>
				    <td>
				    	<label><input type="radio" name="issue_type" value="1" > 取件异常件</label>
				        <label><input type="radio" name="issue_type" value="2" checked> 库内异常件</label>
				        <label><input type="radio" name="issue_type" value="3"> 渠道异常件</label>
				    </td>
				</tr>
				<tr id="wu" style="display:none">
					<th>
						截止时间
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "deadline", array (
							"value" => request ( "deadline" ),
							"style"=>"width:80px"
						) )?>
					</td>
				</tr>
				<tr>
				    <th>详情</th>
				    <td>
				        <textarea name="detail" rows="3" Style="width: 400px"></textarea>
				    </td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="FarTool text-center">
		<button type="submit" class="btn btn-primary">
			<i class="icon-save"></i>
			保存
		</button>
	</div>
</form>
<script type="text/javascript">
$('body').on('keydown', 'input, select', function(e) {
	if (e.keyCode == 13) {
		return enter2tab(this,e);
	}
});
//渠道异常件截止时间
$('input[type=radio][name=issue_type]').change(function() {
    if (this.value == 3) {
        $('#wu').removeAttr('style');
    }else if (this.value == 1) {
    	$('#wu').attr('style','display:none');
    }else if (this.value == 2){
    	$('#wu').attr('style','display:none');
    }
});
</script>
<?PHP $this->_endblock();?>

