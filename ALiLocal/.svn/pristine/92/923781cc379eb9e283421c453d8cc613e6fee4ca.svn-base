<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form action="" method="post">

<div class="row-fluid" style="width:100%">
		<table class="table table-bordered">
			<tr>
				<th>阿里单号</th>
				<th>包裹类型</th>
				<th>操作</th>
			</tr>
			<tr>
				<td>
					<textarea  id="ali_order_no" name="ali_order_no" style="width:200px; height:400px" autofocus="autofocus"></textarea>
				</td>
				<td>
					<?php
				echo Q::control ( 'dropdownlist', 'packing_type', array (
					'items' => array('PAK'=>'PAK','DOC'=>'DOC','BOX'=>'BOX'),
					'style' => 'width: 180px'
				) )?>
				</td>
				<td><input type="submit" value="提交"  class="btn btn-small btn-success" /></td>
			</tr>
		</table>
</div>   
</form>
<?PHP $this->_endblock();?>
<script>
//修改部门事件
$('#account').bind('input',function(){
	var account = $(this).val()
	window.location.href = "<?php echo url('/comparison')?>"+"?account="+account;
})
$('#goods').bind('input',function(){
	//每次编辑都先清空原数据
	$('#id1').val('');
	$('#id2').val('');
	$('#id3').val('');
	var account = $('#order_id').val();
	var arr1 = account.split('\n'); //arr1是单号
	var arr1=$.grep(arr1,function(n,i){
		return n;
	},false)
	console.log(arr1)
	var goods = $(this).val();
	var arr2 = goods.split('\n'); //arr2是货物
	var arr2=$.grep(arr2,function(n,i){
		return n;
	},false)
	console.log(arr2)
	var i;
	$.each(arr1,function(k,v){
		i = $.inArray(v, arr2);
		if(i == -1){
			$('#id3').val($('#id3').val()+v+'\n')
		}else{
			$('#id1').val($('#id1').val()+v+'\n')
		}
	});
	$.each(arr2,function(k,v){
		i = $.inArray(v, arr1);
		if(i == -1){
			$('#id2').val($('#id2').val()+v+'\n')
		}
	});
})
</script>
