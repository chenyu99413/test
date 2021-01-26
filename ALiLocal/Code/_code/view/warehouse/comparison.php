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
			'包裹交货核查' => url ( '/totallist' ),
			'包裹交货核查扫描' => ''
		) 
	) );
?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<?php 
	$d = array();
	$i=1;
	$first_key='';

    	foreach(Order::channelgroup() as $k => $v){    		
    	    if($i=='1'){
    	        $first_key=$k;
    	    }    		
    		$d[$k] = $k;
    		$i++;
    	}
	
?>
<?php if(count(@$order_id)):?>
<span style="font-size: 20px">未成功的：(<?php echo count(@$order_id)?>)</span><a href="<?php echo  url('/totallist')?>" style="font-size: 20px;float: right">返回</a><br>
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
<form action="" method="post" onsubmit="return checkdata();">
<table class="table table-bordered checkin-table-1" style="margin-bottom: 10px;margin-left:130px;width:880px;">
	<tbody>
		<tr>
		    <th style="width: 100px;" class='required-title'>总单号</th>
			<td style="width: 120px;">
				<input type="text" name="total_list_no" id="total_list_no" value="<?php echo request('total_list_no')?>" readonly="readonly">
				<input type="hidden" name="flag" id="flag" value="<?php echo request('flag')?>">
			</td>
			<th style="width: 100px;" class='required-title'>渠道分组</th>
			<td style="width: 120px;">
				<?php
				echo Q::control ( 'dropdownlist', 'account', array (
					'items' => $d,
					'value' => request('account',$first_key),
					'style' => 'width: 120px'
				) )?>
			</td>
			<th style="width: 70px;" class='required-title'>发件日</th>
			<td style="width: 120px;">
				<?php
				echo Q::control ( "datebox", "record_order_date", array (
					"value" => Helper_Util::strDate('Y-m-d', request('record_order_date',time())),
					"style"=>"width:100px","required"=>true
				) )?>
			</td>
			<th  style="width: 40px;">国家</th>
			<td>
				<input class="easyui-combotree" id="code_word_two" name="code_word_two[]" data-options="url:'<?php echo url('warehouse/codewordtwotree',array('checked'=>request('code_word_two')))?>'
						, method:'get'
						, multiple:true ,panelHeight:'360px',width:'145px'">
			    <input id="code_word_two_hidden" name="code_word_two" value="<?php echo request('code_word_two')?>" type="hidden">
			</td>
		</tr>
	</tbody>
</table>
<div class="row-fluid" style="width:100%">
	<div class="span8">
		<table class="table table-bordered">
			<tr>
				<th>系统单号(包含子单号)<span>（<?php echo count($order)?>）</span></th>
				<th>扫描货物<span id="goods_count">（0）</span></th>
				<th>核对成功
				<?php
				echo Q::control ( 'dropdownlist', 'sort', array (
					'items' => array('D3'=>'D3','S1'=>'S1'),
					'value' => request('sort','D3'),
					'style' => 'width: 80px'
				) )?>
				<input type="submit" value="提交"  class="btn btn-small btn-success" /><span style="float:right" id="id1_count">（0）</span></th>
				<th>有货无单<span id="id2_count">（0）</span></th>
				<th>有单无货<span id="id3_count">（0）</span></th>
			</tr>
			<tr>
				<td>
					<textarea readonly id="order_id" style="width:200px; height:350px"><?php foreach($order as $v){
							echo $v['sub_code'].'&#10;';
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
	var code_word_two = $('#code_word_two').combotree('getValues');
	window.location.href = "<?php echo url('/comparison')?>"+"?account="+account+"&code_word_two="+code_word_two;
})
$(function(){
    $("#code_word_two").combotree({
       onChange:function(){
    	   var code_word_two = $('#code_word_two').combotree('getValues');
    	   var code_word_two_hidden = $('#code_word_two_hidden').val();
   	       if(code_word_two != code_word_two_hidden){
    		   var account = $('#account').val();
    		   $('#code_word_two_hidden').val(code_word_two);
    		   window.location.href = "<?php echo url('/comparison')?>"+"?code_word_two="+code_word_two+"&account="+account;
    	   }
  	    }    
    });

    var flag=$("#flag").val();
    if(flag==1){
        $("#account").attr('readonly','readonly');
        $("#record_order_date").attr('readonly','readonly');
        $("#code_word_two").attr('readonly','readonly');
    }
})

$('#goods').bind('keydown',function(e){
	if(e.which==13){
		var goods = $(this).val();
		var arr2 = goods.split('\n');
		//判断是否有重复
		if(arr2.indexOf (arr2[arr2.length-1]) != arr2.length-1 && arr2.indexOf (arr2[arr2.length-1]) != -1){
			console.log('重复')
			arr2.splice(arr2.length-1,1)
			$(this).val(arr2.join('\n'))
			$.sound.play('<?php echo $_BASE_DIR?>public/sound/chongfu.mp3');
		}else{
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
				$('#goods').val($('#goods').val()+v+'\n');
			});
			$('#goods').val($('#goods').val().trim('\n'));
			goods = $(this).val();
			var arr2 = goods.split('\n'); //arr2是货物
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
				$.sound.play('<?php echo $_BASE_DIR?>public/sound/chenggong.mp3');
				console.log('chenggong')
			}else{
				$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
				console.log('cuowu')
			}
		}
		
		
	}
	
})
function checkdata(){
	var sub_code = $('#id1').val();
	var result = '';
	var goods = $('#id1').val();
	var sub_code = goods.split('\n'); //arr2是货物
	var sub_code=$.grep(sub_code,function(n,i){
		return n;
	},false)
	console.log(sub_code)
	$.ajax({
		url:'<?php echo url('warehouse/checkonetomany')?>',
		type:'POST',
		dataType:'json',
		data:{sub_code : sub_code},
		async : false,
		success:function(data){
			console.log(data)
			result = data.message;
			var info = '';
			if(data.message=='payattentiononetomany'){
				$.each(data.sub_code, function(i, j){
					info += "<br/>";
					console.log(i)
					console.log(j)
					console.log(sub_code)
// 					if($.inArray(i,sub_code)<0){
// 						i = '<span style="color:red">'+i+'</span>'
// 					}
					info += "主单号：<br/>"+i+"<br/>";
					
					info += "子单号：<br/>";
					$.each(j, function(m, n){
						info += n+'<br/>';
					});
				});
				$.messager.alert('', '系统无法提交数据'+info);
				$.sound.play('<?php echo $_BASE_DIR;?>public/sound/payattentiononetomany.mp3');//请注意一票多件
			}
		}
	})
	console.log(result)
	if(result =='payattentiononetomany'){
		return false;
	}else{
		return true;
	}
	
}
</script>
