<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
  无主件扫描
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR;?>public/js/jquery.sound.js"></script>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="service_product" style="width:100%;text-align:center; ">
    <span id="service_product" style="font-size:30px;"></span>
</div>
<div class="FarSearch" >
	<table>
		<tr>
			<th>国内单号</th>
			<td>
				<input name="reference_no" type="text" id="reference_no"  style="width: 200px" value=""><span id="explain" style="margin-left:10px;"></span>
			</td>
		</tr>
	</table>
</div>

<?PHP $this->_endblock();?>
<script type="text/javascript">
	$(function(){
		document.getElementById("reference_no").focus();
		//扫描国内单号
		$('#reference_no').on('keydown', function (e) {
			if (e.keyCode == 13) {
				$("#explain").html('');
				$.ajax({
					url:'<?php echo url('warehouse/noidscan')?>',
					type:'POST',
					dataType:'json',
					data:{reference_no:$("#reference_no").val()},
					success:function(data){
						console.log(data)
						if(data){
							$("#explain").html('请入库');
							$("#explain").css('color','#57a752');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qingruku.mp3');//请入库
						}else{
							$("#explain").html('无主件');
							$("#explain").css('color','#f00');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/wuzhujian.mp3');//无主件
						}
					}
				});
			}
		});
	});
	
</script>
