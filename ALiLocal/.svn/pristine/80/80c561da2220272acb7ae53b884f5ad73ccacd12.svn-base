<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    新建问题件
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'订单管理' => '','问题件列表' => url ( 'order/issue' ),'新建问题件' => '' 
	) 
) )?>
<form method="post">
    <div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th>阿里单号</th>
					<td>   
                        <textarea id="waybill_codes" name="waybill_codes" rows="10" placeholder="每行一个单号"
							style="width: 90%" required="required"><?php echo request("waybill_codes")?></textarea>
					</td>
				</tr>
				<tr>
				    <th>问题类型</th>
				    <td>
				    	<label><input type="radio" name="issue_type" value="1" > 取件异常件</label>
				        <label><input type="radio" name="issue_type" value="2" > 库内异常件</label>
				        <label><input type="radio" name="issue_type" value="3" checked> 渠道异常件</label>
				    </td>
				</tr>
				<tr id="wu">
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
$(function(){
	$('form').submit(function(){
		var flag=true;
		var waybill_codes=$("#waybill_codes").val().split(/[(\r\n)\r\n]+/);
		//alert(waybill_codes);
		$.ajax({
			url:'<?php echo url('/checkissueparcelalino')?>',			
			type:'POST',
			dataType:'json',
			data:{waybill_codes:waybill_codes},
			async:false,	
			success:function(data){			
				if(data.code=='false'){
					flag=false;
					$.messager.alert('',data.message);
				}
			}
		})
		return flag;
	})
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
})
</script>
<?PHP $this->_endblock();?>

