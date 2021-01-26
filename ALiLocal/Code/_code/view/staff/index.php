<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
  工作台
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'工作台' => ''
	) 
) )?>
<form method="POST">
	 <h5>
	 	(阿里/泛远/末端)单号:<input style="width:160px;" type='text' value="" name="order_no" autofocus>
		 <button class="btn btn-primary btn-small" id="search">
		 	<i class="icon-search"></i>
		                 搜索
	     </button>
     </h5>
</form>
    
<?PHP $this->_endblock();?>
<script type="text/javascript">
function copy(ali_order_no){
	$('#ali_order_no').val(ali_order_no);
	$("#emailtemplate").val('');
	$('#template_msg').val('');
}
$("#emailtemplate").change(function(){
	var ali_order_no = $('#ali_order_no').val();
	var id = $("#emailtemplate").val();
	if(ali_order_no == ''){
		alert('数据错误');
		return false;
	}
	if(id>0 && ali_order_no != ''){
		$.ajax({
			url:'<?php echo url('product/templateinfo')?>',
			data:{id:id,ali_order_no:ali_order_no},
			type:'post',
			dataType:'json',
			async:false,
			success:function(data){
				if(data.error == 'notemplate'){
				   alert('该模板不存在');
				}else if(data.error == 'noorder'){
				   alert('该订单不存在');
				}else if(data.error == 'nopostal'){
				   alert('邮政信息缺失,请检查是否正确');
				}else if(data.error == 'nodata'){
				   alert('数据缺失');
				}else{
				   $('#template_msg').val(data.message);
				}
			}
		});
	}else{
		$('#template_msg').val('');
	}
});
function sendemail(){
	var id = $("#emailtemplate").val();
	var ali_order_no = $('#ali_order_no').val();
	var message = $('#template_msg').val();
	if(id>0 && ali_order_no != '' && message != ''){
		$.ajax({
			url:'<?php echo url('product/sendtemplate')?>',
			data:{id:id,ali_order_no:ali_order_no,message:message},
			type:'post',
			dataType:'json',
			async:false,
			success:function(data){
				if(data.error=='notemplatzzze'){
					alert('该模板不存在');
				}else if(data.error=='nodata'){
					alert('数据不完整');
				}else{
					if(data.success== 'success'){
					   alert('发送成功');
					   setTimeout(function (){
							window.location.reload();
						}, 1000);
					}else{
					   alert('发送失败，失败原因：'+data.errorinfo);
					}
				}
			}
		});
	}else{
		alert('必填项不能为空');
		return false;
	}
}
function finish(obj){
	var order_id=$(obj).attr('data');
// 	alert(order_id);
	$.ajax({
		url:'<?php echo url('staff/handled')?>',
		data:{order_id:order_id},
		type:'post',
		async:false,
		success:function(data){
// 			alert(data);
			if(data!='success'){
			   alert('订单不存在');
			}else{
				alert('成功');
				$(obj).remove();
			}
		}
	});
}
</script>
