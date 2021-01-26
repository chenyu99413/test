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
			<th>库位条码 </th>
			<td>
				<input name="warehouse_code" type="text" id="warehouse_code" autofocus="autofocus" style="width: 200px">
			</td>
			<th>国内快递单号/ALS单号/末端单号 </th>
			<td>
				<input name="reference_no" type="text" id="reference_no"  style="width: 200px" value=""><span id="explain" style="margin-left:10px;"></span>
			</td>
			<td>
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
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	$(function(){
		$("#warehouse_code").select();
		//扫描库位条码
		$('#warehouse_code').on('input', function (e) {
			$.ajax({
				url:'<?php echo url('warehouse/posscan')?>',
				type:'POST',
				dataType:'json',
				data:{type:1,warehouse_code:$("#warehouse_code").val()},
				success:function(data){
					if(data.code){
						$("#reference_no").select();
						$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');//成功
					}else{
						$("#warehouse_code").val('').select();
						$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bucunzai.mp3');//不存在
					}
				}
			})
		})

		//扫描单号
		$('#reference_no').on('input', function (e) {
			$("#explain").html('')
			if($("#warehouse_code").val().length == 0){
				alert('请先扫描库位')
				$("#warehouse_code").select();
				$("#reference_no").val('');
				return false
			}
			if($('#reference_no_tr_'+$("#reference_no").val()).text()){
				$("#reference_no").select();
				$("#explain").html('重复').css('color','red');
				$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chongfu.mp3');//已扫描
				return false;
			}
			$.ajax({
				url:'<?php echo url('warehouse/posscan')?>',
				type:'POST',
				dataType:'json',
				data:{type:2,reference_no:$("#reference_no").val()},
				success:function(data){
					$("#reference_no").select();
					if(data.code){
						$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');//成功
						$('#success_no_table').find('tbody').append("<tr><td id='reference_no_tr_"+$("#reference_no").val()+"'name='reference_no' style='text-align:center;'>"+$("#reference_no").val()+"</td></tr>");
						$('#success_count').html('('+$('#success_no_table').find('tbody > tr').length+')');
					}else{
						$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bucunzai.mp3');//不存在
						$('#fail_no_table').find('tbody').append("<tr><td id='reference_no_tr_"+$("#reference_no").val()+"'name='reference_no' style='text-align:center;'>"+$("#reference_no").val()+"</td></tr>");
						$('#fail_count').html('('+$('#fail_no_table').find('tbody > tr').length+')');
					}
				}
			})
		})
		

		$('#save').on('click',function(){
			$("#reference_no").val('');
			if($('#warehouse_code').val()==''){
				$("#warehouse_code").select();
				alert('库位不能为空');
				return false;
			}
			submitData();
			
		});
		function submitData(){
			var json_str = '{';
			json_str += '"warehouse_code":"'+$('#warehouse_code').val();
			json_str += '",';
			json_str += '"success-list":[';
		    $('#success_no_table').find('tbody').find('tr').each(function (i, e) {
		        if (i != 0) {
		            json_str += ',';
		        }
		        json_str += '{"reference_no":"' + $(this).find('[name="reference_no"]').text().trim() + '"';
		        json_str += '}';
		    });
		    json_str += '],';
		    json_str += '"fail-list":[';
		    $('#fail_no_table').find('tbody').find('tr').each(function (i, e) {
		        if (i != 0) {
		            json_str += ',';
		        }
		        json_str += '{"reference_no":"' + $(this).find('[name="reference_no"]').text().trim() + '"';
		        json_str += '}';
		    });
		    json_str += ']}';
		    console.log(json_str);
		    $.ajax({
				url:'<?php echo url('warehouse/posscan')?>',
				type:'POST',
				dataType:'json',
				data:{type:3,jsonstr: json_str},
				success:function(data){
					if(data.code){
						alert('保存成功');
						window.location.href='<?php echo url("warehouse/posscan")?>';
					}else{
						alert(data.msg);
					}
				}
			})
		}
		
	});
	
</script>
