<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    网点入库
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR;?>public/js/jquery.sound.js"></script>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="FarSearch" >
	<table>
		<tbody>
			<tr>
				<th>(阿里/快递)单号</th>
				<td>
					<input name="ali_order_no" type="text" id="ali_order_no" style="width: 200px" value=""><span id="explain" style="margin-left:10px;"></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
    
<?PHP $this->_endblock();?>
<script type="text/javascript">
	$(function(){
		document.getElementById("ali_order_no").focus();
		//扫描阿里单号
		$('#ali_order_no').on('keyup', function (e) {
			if (e.keyCode == 13) {
				$("#explain").html('')
				$.ajax({
					url:'<?php echo url('/Pickupnetworkin')?>',
					type:'POST',
					data:{scan_no:$("#ali_order_no").val()},
					dataType:'json',
					success:function(json){
						$.sound.play('<?php echo $_BASE_DIR?>public/sound/' + json.sound);
						if (json.status) {
							$("#explain").html(json.msg).css('color','green');
						}else{
							$("#explain").html(json.msg).css('color','red');
						}
						$("#ali_order_no").select();
					}
				})
			}
		});
	})
</script>

