<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php echo Q::control ( 'path', '', array (
		'path' => array (
			'退件管理' => '',
			'退件出库总单' => url ( '/returnouttotal' ),
			'退件出库扫描' => ''
		) 
	) );
?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<div class="alert alert-info" style="margin-bottom: 10px;">
	<ol style="margin-bottom: 0px;">
		<li>【重发】只能扫描新子单号</li>
		<li>【销毁、退货】只能扫描原子单号</li>
	</ol>
</div>
<form action="" method="post" onsubmit="return checkdata();">
<table class="table table-bordered checkin-table-1" style="margin-bottom: 10px;margin-left:130px;width:880px;">
	<tbody>
		<tr>
		    <th style="width: 100px;" class='required-title'>总单号</th>
			<td style="width: 120px;">
				<input type="text" name="return_total_no" id="return_total_no" value="<?php echo request('return_total_no',date('YmdHis'))?>" readonly="readonly">
				<input type="hidden" name="return_out_total_id" id="return_out_total_id" value="<?php echo request('return_out_total_id')?>">
			</td>
			<th style="width: 100px;" class='required-title'>货件流向</th>
			<td style="width: 120px;">
				<?php
				echo Q::control ( 'dropdownlist', 'type', array (
					'items' => array('1'=>'重发','2'=>'销毁','3'=>'退货'),
					'value' => request('type'),
					'style' => 'width: 120px'
				) )?>
			</td>
		</tr>
	</tbody>
</table>
<div class="row-fluid" style="width:100%">
	<div class="span8">
		<table class="table table-bordered">
			<tr>
				<th>全部子单号<span id="id0_count">（0）</span></th>
				<th>扫描货物<span id="goods_count">（0）</span></th>
				<th>核对成功
				<input type="submit" id="tijiao" value="提交"  class="btn btn-small btn-success" /><span style="float:right" id="id1_count">（0）</span></th>
				<th>有货无单<span id="id2_count">（0）</span></th>
				<th>有单无货<span id="id3_count">（0）</span></th>
			</tr>
			<tr>
				<td>
					<textarea readonly id="id0" style="width:200px; height:350px"></textarea>
				</td>
				<td><textarea style="width:200px; height:350px" id="goods"><?php foreach ($subcode as $o){
					//echo $o->sub_code.'&#10;';
				};?></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id1" name="sub_code" readonly></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id2" readonly></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id3" readonly></textarea></td>
			</tr>
		</table>
	</div>
</div>   
</form>
<?PHP $this->_endblock();?>
<script>
//修改货件流向
$('#type').bind('change',function(){
	$('#id0').val('');
	$('#goods').val('');
	$('#id1').val('');
	$('#id2').val('');
	$('#id3').val('');
	$('#goods').focus();
})
$(function(){
	$('#goods').focus();
    var flag=$("#flag").val();
    if(flag==1){
        $("#account").attr('readonly','readonly');
        $("#record_order_date").attr('readonly','readonly');
        $("#code_word_two").attr('readonly','readonly');
    }
})
$('#goods').bind('keydown',function(e){
	if(e.which==13){
		$('#goods_count').html('（'+$("#goods").val().trim('\n').split('\n').length+'）');
		var order_no = $(this).val();
		var type = $('#type').val();
		console.log(type)
		$.ajax({
			url:'<?php echo url('/scanoutajax')?>',
			type:'POST',
			dataType:'json',
			data:{order_no : order_no,type:type},
			success:function(data){
				//每次编辑都先清空原数据
				$('#id0').val('');
				$('#id0_count').html('（0）');
				$('#id1').val('');
				$('#id1_count').html('（0）');
				$('#id2').val('');
				$('#id2_count').html('（0）');
				$('#id3').val('');
				$('#id3_count').html('（0）');
				if(data.tracking_no1.length != 0){
					$.each(data.tracking_no1,function(k,v){
						$('#id1').val($('#id1').val()+v+'\n')
						$('#id1_count').html('（'+$("#id1").val().trim('\n').split('\n').length+'）');
						$('#id0').val($('#id0').val()+v+'\n')
						$('#id0_count').html('（'+$("#id0").val().trim('\n').split('\n').length+'）');
						if(data.tracking_no4 == v){
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/chenggong.mp3');
						}
					});
				}
				if(data.tracking_no2.length != 0){
					$.each(data.tracking_no2,function(k,v){
						$('#id2').val($('#id2').val()+v+'\n')
						$('#id2_count').html('（'+$("#id2").val().trim('\n').split('\n').length+'）');
						if(data.tracking_no4 == v){
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/youhuowudan.mp3');
						}
					});
				}
				if(data.tracking_no3.length != 0){
					$.each(data.tracking_no3,function(k,v){
						$('#id3').val($('#id3').val()+v+'\n')
						$('#id3_count').html('（'+$("#id3").val().trim('\n').split('\n').length+'）');
						$('#id0').val($('#id0').val()+v+'\n')
						$('#id0_count').html('（'+$("#id0").val().trim('\n').split('\n').length+'）');
						if(data.tracking_no4 == v){
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/youdanwuhuo.mp3');
						}
					});
				}
			}
		})
	}
})




function checkdata(){
	var id1 = $('#id1').val();
	var id2 = $('#id2').val();
	var id3 = $('#id3').val();
	var type = $('#type').val();
	var return_out_total_id = $('#return_out_total_id').val();
	var return_total_no = $('#return_total_no').val();
	console.log(return_out_total_id)
	if(id3!=''){
		$.messager.alert('', '有错误订单');
		return false;
	}
	if(id1==''){
		$.messager.alert('', '请先核对订单');
		return false;
	}
	$("#tijiao").attr('disabled',true);
	$.ajax({
		url:'<?php echo url('/scanoutsubmit')?>',
		type:'POST',
		dataType:'json',
		data:{order_no : id1,type : type, return_out_total_id : return_out_total_id,return_total_no:return_total_no},
		async : false,
		success:function(data){
			console.log(data)
			$.messager.alert('', '保存成功');
			$("#tijiao").attr('disabled',false);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			$("#tijiao").attr('disabled',false);
        }
	})
	return false
}
</script>
