<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
启程扫描
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR;?>public/js/jquery.sound.js"></script>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php echo Q::control ( 'path', '', array (
		'path' => array (
			'仓库业务' => '',
			'包裹启程扫描列表' => url ( '/totaloutlist' ),
			'包裹启程扫描' => ''
		) 
	) );
?>
<div class="service_product" style="width:100%;text-align:center; ">
    <span id="service_product" style="font-size:30px;"></span>
</div>
<div class="FarSearch" >
	<table>
		<tr>
		    <th style="width: 100px;">总单号</th>
			<td style="width: 120px;">
				<input type="text" name="total_no" id="total_no" value="<?php echo request('total_no')?>" readonly="readonly">
			</td>
			<th>(阿里/末端/交货核查总单/抵达总单)单号 </th>
			<td>
				<input name="ali_order_no" type="text" id="ali_order_no"  style="width: 200px" value="" autofocus="autofocus"><span id="explain" style="margin-left:10px;"></span>
			</td>
			<td>
    			<a class="btn btn-mini btn-info" href="<?php echo url('warehouse/totaloutlist')?>">
        			<i class="icon-reply"></i> 返回
        		</a>
    		</td>
		</tr>
	</table>
</div>
<font size="2" color="blue" face="verdana"><?php echo '已扫单号'?></font>
<div style="width: 800px;">
		<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>总单号</th>
				<th>扫描时间</th>
				<th>扫描单号</th>
				<th>阿里订单号</th>
				<th>末端运单号</th>
				<th>件数</th>
				<th>实重</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1;foreach ($order as $temp):
		if($temp->order->order_id){
		   $far_out_package = Faroutpackage::find('order_id = ?',$temp->order->order_id)->getSum('quantity_out');
		   $far_package = Farpackage::find('order_id = ?',$temp->order->order_id)->getSum('quantity');
		}?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->total_no?></td>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->create_time)?></td>
				<td><?php echo ($temp->flag=='0')?$temp->ali_order_no:$temp->tracking_no?></td>
				<td><?php echo $temp->ali_order_no?></td>
				<td><?php echo $temp->tracking_no?></td>
				<td><?php echo ($far_out_package>0)?$far_out_package:$far_package?></td>
				<td><?php echo ($temp->order->weight_actual_out)?sprintf('%.2f',$temp->order->weight_actual_out):sprintf('%.2f',$temp->order->weight_actual_in)?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	$(function(){
		document.getElementById("ali_order_no").focus();
		//扫描阿里单号
		$('#ali_order_no').on('keydown', function (e) {
			if (e.keyCode == 13) {
				if($("#explain").html()=='处理中。。。'){
					alert('处理中，请不要重复提交');
					return false;
				}				
				$("#explain").html('处理中。。。');
				var v = $("#ali_order_no").val();
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
				$("#ali_order_no").val(v);
				
				$.ajax({
					url:'<?php echo url('warehouse/packageout')?>',
					type:'POST',
					dataType:'json',
					data:{ali_order_no:$("#ali_order_no").val(),total_no:$("#total_no").val()},
					success:function(data){
						if(data.message=='notexists'){
							$("#explain").html('失败，有货无单').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/cuowu.mp3');//有单无货
						}else if(data.message=='stateeeror'){
							$("#explain").html('失败，订单状态错误').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/cuowu.mp3');//订单状态错误
						}else if(data.message=='ckerror'){
							$("#explain").html('失败，该交货核查总单下有不是本仓包裹').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/cuowu.mp3');//订单状态错误
						}else if(data.message=='wancheng'){
							$("#explain").html('失败，请先完成总单').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/cuowu.mp3');//请先完成总单
						}else if(data.message=='已扣件'){
							$("#explain").html('成功，有单有货').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yikoujian.mp3');//订单状态错误
							setTimeout(function (){
								window.location.reload();
							}, 1000);
						}else if(data.message=='已取消'){
							$("#explain").html('成功，有单有货').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yiquxiao.mp3');//订单状态错误
							setTimeout(function (){
								window.location.reload();
							}, 1000);
						}else if(data.message=='待退货'){
							$("#explain").html('成功，有单有货').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/daituihuo.mp3');//订单状态错误
							setTimeout(function (){
								window.location.reload();
							}, 1000);
						}else{
							$("#explain").html('成功，有单有货').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');//有单有货
							setTimeout(function (){
								window.location.reload();
							}, 1000);
						}
					}
				})
			}
		});
	})
	
</script>
