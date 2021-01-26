<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
扫描国内快递单号
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR;?>public/js/jquery.sound.js"></script>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="FarSearch" >
	<table>
		<tr>
			<th>国内快递 </th>
			<td>
				<?php
						$logs=CodeLogistics::find()->asArray()->getAll();
						$resl=array();
						foreach ($logs as $l){
							$resl[$l['id']]=$l['name'].'['.$l['code'].']';
						}
						echo Q::control ( "myselect", "logistics_id", array (
							"items" => $resl,
							"selected" => request('logistics_id'),
							'style'=>'width:180px',
						) )
				?>
						
			</td>
			<th>国内快递单号 </th>
			<td>
				<input name="reference_no" type="text" id="reference_no"  style="width: 200px" value="" autofocus="autofocus"><span id="explain" style="margin-left:10px;"></span>
			</td>
			<td>
    			<a class="btn btn-mini btn-info" href="<?php echo url('warehouse/scantotallist')?>">
        			<i class="icon-reply"></i> 返回
        		</a>
        		<button type="button" class="btn btn-mini btn-primary" id="save">
					<i class="icon-save"></i>
					保存
				</button>
    		</td>
		</tr>
	</table>
</div>
<div style="float:left; width: 500px;">
		<table id="success_no_table" class="FarTable">
		<thead>
			<tr>
				<th>成功单号<span id="success_count">(0)</span></th>
				<th>扫描时间</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div style="float:right; width: 500px;">
		<table id="fail_no_table" class="FarTable">
		<thead>
			<tr>
				<th>不存在单号<span id="fail_count">(0)</span></th>
				<th>扫描时间</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
//判断是否在前面加0
function getNow(s) {
	return s < 10 ? '0' + s: s;
}
	$(function(){
		$("#reference_no").select();
		//扫描阿里单号
		$('#reference_no').on('keydown', function (e) {
			if (e.keyCode == 13) {
				$("#reference_no").blur();
				if($('#reference_no_tr_'+$("#reference_no").val()).text()){
					$("#reference_no").select();
					$("#explain").html('重复').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chongfu.mp3');//已扫描
					return false;
				}
				$.ajax({
					url:'<?php echo url('warehouse/ScanReferenceNo')?>',
					type:'POST',
					dataType:'json',
					data:{reference_no:$("#reference_no").val()},
					success:function(data){
						var myDate = new Date();            
						 
						var year=myDate.getFullYear();        //获取当前年
						var month=myDate.getMonth()+1;   //获取当前月
						var date=myDate.getDate();            //获取当前日
						 
						 
						var h=myDate.getHours();              //获取当前小时数(0-23)
						var m=myDate.getMinutes();          //获取当前分钟数(0-59)
						var s=myDate.getSeconds();
						 
						var now=year+'-'+getNow(month)+"-"+getNow(date)+" "+getNow(h)+':'+getNow(m)+":"+getNow(s);
						if(data.message=='noreferenceno'){
							$("#reference_no").select();
							$("#explain").html('不存在').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bucunzai.mp3');//不存在
							$('#fail_no_table').find('tbody').append("<tr class='tr_"+$("#reference_no").val()+"'><td id='reference_no_tr_"+$("#reference_no").val()+"'name='reference_no' style='text-align:center;'>"+$("#reference_no").val()+"</td></tr>");
							$('#fail_no_table').find('.tr_'+$("#reference_no").val()).append("<td name='time'>"+now+"</td>");
							$('#fail_count').html('('+$('#fail_no_table').find('tbody > tr').length+')');
						}else if(data.message=='repetition'){
							$("#reference_no").select();
							$("#explain").html('重复').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chongfu.mp3');//已扫描
						}else if(data.message=='success'){
							$("#reference_no").select();
							$("#explain").html('成功').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');//成功
							$('#success_no_table').find('tbody').append("<tr class='tr_"+$("#reference_no").val()+"'><td id='reference_no_tr_"+$("#reference_no").val()+"'name='reference_no' style='text-align:center;'>"+$("#reference_no").val()+"</td></tr>");
							$('#success_no_table').find('.tr_'+$("#reference_no").val()).append("<td name='time'>"+now+"</td>");
							$('#success_count').html('('+$('#success_no_table').find('tbody > tr').length+')');
						}
					}
				})
			}
		});

		$('#save').on('click',function(){
			$("#reference_no").val('');
			if($('#logistics_id').val()==''){
				alert('国内快递不能为空');return false;
			}
			submitData();
			
		});
		function submitData(){
			$('#save').attr('disabled','true');
			var json_str = '{';
			json_str += '"logistics_id":"'+$('#logistics_id').val();
			json_str += '",';
			json_str += '"success-list":[';
		    $('#success_no_table').find('tbody').find('tr').each(function (i, e) {
		        if (i != 0) {
		            json_str += ',';
		        }
		        json_str += '{"reference_no":"' + $(this).find('[name="reference_no"]').text().trim() + '","time":"'+$(this).find('[name="time"]').text()+'"';
		        json_str += '}';
		    });
		    json_str += '],';
		    json_str += '"fail-list":[';
		    $('#fail_no_table').find('tbody').find('tr').each(function (i, e) {
		        if (i != 0) {
		            json_str += ',';
		        }
		        json_str += '{"reference_no":"' + $(this).find('[name="reference_no"]').text().trim() + '","time":"'+$(this).find('[name="time"]').text()+'"';
		        json_str += '}';
		    });
		    json_str += ']}';
		    console.log(json_str);
		    $.ajax({
				url:'<?php echo url('warehouse/ScanReferenceNoSave')?>',
				type:'POST',
				dataType:'json',
				data:{jsonstr: json_str},
				success:function(data){
					if(data.message=='nodata'){
						alert('数据为空');
						$('#save').removeAttr('disabled');
					}else if(data.message=='success'){
						alert('保存成功');
						window.location.href='<?php echo url("warehouse/scantotallist")?>';
					}
				}
			})
		}
		
	});
	
</script>
