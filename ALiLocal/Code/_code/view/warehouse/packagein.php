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
			'包裹抵达扫描列表' => url ( '/totalinlist' ),
			'包裹抵达扫描' => ''
		) 
	) );
?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<?php if(count(@$order_id)):?>
<span style="font-size: 20px">未成功的：</span><a href="<?php echo  url('/totallist')?>" style="font-size: 20px;float: right">返回</a><br>
	<table class="table table-bordered">
		<tr>
			<th>阿里单号</th>
			<th>系统单号</th>
			<th>包裹号</th>
		</tr>
		<?php foreach ($order_id as $v):?>
		<tr>
			<td><?php echo $v['ali_order_no']?></td>
			<td><?php echo $v['far_no']?></td>
			<td><?php echo $v['sub_code']?></td>
		</tr>
		<?php endforeach;?>
	</table>
<?php else :?>
<form action="" method="post">
<table class="table table-bordered checkin-table-1" style="margin-bottom: 10px;margin-left:130px;width:940px;">
	<tbody>
		<tr>
		    <th style="width: 100px;" class='required-title'>包裹抵达总单号</th>
			<td style="width: 120px;">
				<input type="text" name="total_no" id="total_no" value="<?php echo request('total_no')?>" readonly="readonly">
			</td>
			<th style="width: 50px;" class='required-title'>启程仓</th>
			<td style="width: 60px;">
				<?php echo Department::find('department_id = ?',$totalin->out_department_id)->getOne()->department_name?>
			</td>
			<th style="width: 50px;" class='required-title'>抵达仓</th>
			<td style="width: 60px;">
				<?php echo Department::find('department_id = ?',$totalin->in_department_id)->getOne()->department_name?>
			</td>
			<th style="width: 100px;" class='required-title'>包裹启程总单号</th>
			<td style="width: 120px;">
			    <input type="text" name="service_code" id="service_code" value="<?php echo request('service_code')?>" readonly="readonly">
			</td>
		</tr>
	</tbody>
</table>
<div class="row-fluid" style="width:100%">
	<div class="span8">
		<table class="table table-bordered">
			<tr>
				<th>系统单号<span>（<?php echo count($order)?>）</span></th>
				<th>扫描货物<span id="goods_count">（0）</span></th>
				<th>核对成功
				<input type="submit" value="提交"  class="btn btn-small btn-success" /><span style="float:right" id="id1_count">（0）</span></th>
				<th>有货无单<span id="id2_count">（0）</span></th>
				<th>有单无货<span id="id3_count">（0）</span></th>
			</tr>
			<tr>
				<td>
					<textarea readonly id="order_id" style="width:200px; height:350px"><?php foreach($order as $v){
							if($v['flag']=='0'){
							    echo $v['ali_order_no'].'&#10;';
							}else{
							    echo $v['tracking_no'].'&#10;';
							}
						}?></textarea>
				</td>
				<td><textarea style="width:200px; height:350px" id="goods"></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id1" name="sub_code" readonly></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id2" readonly></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id3" readonly></textarea></td>
			</tr>
		</table>
	</div>
</div>   
</form>
<?php endif;?>
<?PHP $this->_endblock();?>
<script>
//修改部门事件
$('#account').bind('change',function(){
	var account = $(this).val();
	window.location.href = "<?php echo url('/packagein')?>"+"?account="+account+"&total_no="+$("#total_no").val();
})

$('#goods').bind('keydown',function(e){
	if(e.which==13){
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
		$('#goods').val('');
		$.each(arr2,function(k,v){
			//IB扫到的单号，例如，420461069205590237757358406483，去掉前八位
			if(v.substring(0,1)==4 && (v.length == 30 || v.length == 32)){
				v = v.substring(8);
			}
			//FEDEX末端单号34位,截取后面12位
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
		var arr2 = goods.split('\n');
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
		if($("#goods").val().length>0){
			$('#goods_count').html('（'+arr2.length+'）');
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
		if($.inArray(arr2[arr2.length-1],arr1)>=0){
			$.ajax({
				url:'<?php echo url('warehouse/checkchannel')?>',
				type:'POST',
				dataType:'json',
				data:{sub_code:arr2[arr2.length-1]},
				success:function(data){
					if(data.message == 'WWW'){
						$.sound.play('<?php echo $_BASE_DIR?>public/sound/meixi.mp3');
					}else if(data.message == 'EEE'){
						$.sound.play('<?php echo $_BASE_DIR?>public/sound/meidong.mp3');
					}else{
						$.sound.play('<?php echo $_BASE_DIR?>public/sound/chenggong.mp3');
					}
				}
			})
		}else{
			$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
		}
	}
	
})
</script>
