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
			'仓库业务' => '',
			'随货单证核查' => url ( '/goodscheck' ),
			'随货单证扫描' => ''
		) 
	) );
?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<?php 
	$d = array();
	$i=1;
	$first_key='';
	if(MyApp::currentUser('department_id')=='23'){
	    $first_key='青岛 FedEx';
	    $d['青岛 FedEx'] = '青岛 FedEx';
	    $d['中美经济专线'] = '中美经济专线';
	    $d['FAR中美-美东'] = 'FAR中美-美东';
	    $d['FAR中美-美西'] = 'FAR中美-美西';
	    $d['香港DHL'] = '香港DHL';
	}else {
    	foreach(Order::channelgroup() as $k => $v){
    	    if($i=='1'){
    	        $first_key=$k;
    	    }
    		$d[$k] = $k;
    		$i++;
    	}
	}
?>
<?php if(count(@$order_id)):?>

<?php else :?>
<form action="" method="post" onsubmit="return checkdata();">
<table class="table table-bordered checkin-table-1" style="margin-bottom: 10px;margin-left:130px;width:880px;">
	<tbody>
		<tr>
		    <th style="width: 100px;" class='required-title'>随货总单号</th>
			<td style="width: 200px;">
				<input type="text" style="width: 200px;" name="goods_check_no" id="goods_check_no" value="<?php echo $check->goods_check_no?>" readonly="readonly">
				<input type="hidden" style="width: 200px;" name="goods_check_id" id="goods_check_id" value="<?php echo $check->goods_check_id?>" readonly="readonly">
			</td>
			<th style="width: 100px;" class='required-title'>渠道分组</th>
			<td style="width: 120px;">
				<?php echo Channelgroup::find('channel_group_id=?',$check->channel_group_id)->getOne()->channel_group_name;?>
			</td>
		</tr>
	</tbody>
</table>
<div class="row-fluid" style="width:100%">
	<div class="span8">
		<table class="table table-bordered">
			<tr>
				<th>扫描货件（主单号）（总票数）<span id="id4_count">（<?php echo count($c_item)?>）</span></th>
				<th>扫描单证<span id="goods_count">（0）</span></th>
				<th>核对成功<span style="float:right" id="id3_count">（0）</span></th>
				<th>有单无货<span id="id2_count">（0）</span></th>
				<th>有货无单<span id="id1_count">（0）</span></th>
			</tr>
			<tr>
				<td><textarea style="width:200px; height:350px" id="id4" readonly><?php foreach($id4 as $v){
					echo $v.'&#10;';
							}?></textarea></td>
				<td><textarea style="width:200px; height:350px" id="goods"></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id3" name="id3" readonly><?php foreach($id3 as $v){
					echo $v.'&#10;';
							}?></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id2" name="id2" readonly></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id1" name="id1" readonly><?php foreach($id1 as $v){
					echo $v.'&#10;';
							}?></textarea></td>
			</tr>
			<tr>
					<td colspan="5" style="text-align:center;">
						<input type="submit" class="btn btn-sm btn-info" value="提交">
					</td>
				</tr>
		</table>
	</div>
</div>   
</form>
<?php endif;?>
<?PHP $this->_endblock();?>
<script>
$(function(){
	document.getElementById("goods").select();
})
$('#goods').bind('keydown',function(e){
	if(e.which==13){
		//每次编辑都先清空原数据
		$('#id1').val('');
		$('#id2').val('');
		$('#id3').val('');
		var goods = $('#goods').val();
		var arr1 = goods.split('\n'); //arr1是单号
		$('#goods').val('');
		$.each(arr1,function(k,v){
			//IB扫到的单号，例如，420461069205590237757358406483，去掉前八位
			if(v.substring(0,1)==4 && (v.length == 30 || v.length == 32)){
				v = v.substring(8);
			}
			//FEDEX末端单号34位，截取后面12位
			if(v.length == 34){
				v = v.substring(22);
			}
			//FEDEX末端单号第一位是B,去掉
			if(v.substring(0,1).toUpperCase()=='B'){
				v = v.substring(1);
			}
			//FEDEX末端单号最后是0430D,去掉
			if(v.substring(v.length-5).toUpperCase()=='0430D'){
				v = v.substring(0,v.length-5);
			}
			//FEDEX末端单号16位且最后是0430
			if(v.length == 16  && v.substring(v.length-4)=='0430'){
				v = v.substring(0,v.length-4);
			}
			$('#goods').val($('#goods').val()+v+'\n');
		});
		$('#goods').val($('#goods').val().trim('\n'));
		goods = $(this).val();
		var arr1 = goods.split('\n'); //arr2是货物
		var arr1=$.grep(arr1,function(n,i){
			return n;
		},false)
		console.log(arr1)
		var id4 = $('#id4').val();
		var arr2 = id4.split('\n'); //arr2是货物
		var arr2=$.grep(arr2,function(n,i){
			return n;
		},false)
		console.log(arr2)
		var j;
		for(j=0;j<arr2.length;j++){
			arr2[j]= arr2[j].toUpperCase();
		}
		for(j=0;j<arr1.length;j++){
			arr1[j]= arr1[j].toUpperCase();
		}
		var i;
		$.each(arr1,function(k,v){
			i = $.inArray(v, arr2);
			if(i == -1){
				$('#id2').val($('#id2').val()+v+'\n')
			}else{
				$('#id3').val($('#id3').val()+v+'\n')
			}
		});
		$.each(arr2,function(k,v){
			i = $.inArray(v, arr1);
			if(i == -1){
				$('#id1').val($('#id1').val()+v+'\n')
			}
		});
		if($("#goods").val().length>0){
			$('#goods_count').html('（'+arr1.length+'）');
		}
		if($("#id1").val().length>0){
			$('#id1_count').html('（'+$("#id1").val().trim('\n').split('\n').length+'）');
		}
		if($("#id2").val().length>0){
			$('#id2_count').html('（'+$("#id2").val().trim('\n').split('\n').length+'）');
		}
		if($("#id3").val().length>0){
			$('#id3_count').html('（'+$("#id3").val().trim('\n').split('\n').length+'）');
		}
		// 判断最后录入的运单号是否存在
		if($.inArray(arr1[arr1.length-1],arr2)>=0){
			$.sound.play('<?php echo $_BASE_DIR?>public/sound/chenggong.mp3');
		}else{
			$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
		}
	}
	
})
function checkdata(){
	var id1 = $('#id1').val();
	var id2 = $('#id2').val();
	if(id3==''&&id2==''){
		$.messager.alert('', '请先核查！');
		return false;
	}

	return true;
	
}

</script>
