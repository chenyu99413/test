<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
   解扣验证
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
				<th>阿里单号</th>
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
				$("#related_ali_order_no").html('')
				$.ajax({
					url:'<?php echo url('order/releaseverify')?>',
					type:'POST',
					data:{ali_order_no:$("#ali_order_no").val()},
					dataType:'json',
					success:function(data){
						if(data.code=='12'){
							$("#explain").html('扣件').css('color','green');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/koujian.mp3');//扣件
						}else if(data.code=='related_ali_order_no'){
							$("#explain").html('有新单号: '+data.related_ali_order_no).css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/youxindanhao.mp3');//有关联单号
						}else if(data.code=='huandan'){
							$("#explain").html('新单号: '+data.related_ali_order_no).css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/huandanchongfa.mp3');//换单重发
						}else if(data.code=='daituihuoqingchuli'){
							$("#explain").html('待退货请处理').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/daituihuoqingchuli.mp3');//待退货，请处理
						}else if(data.code=='1'){
							$("#explain").html('未入库').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/weiruku.mp3');//未入库
						}else if(data.code=='2'){
							$("#explain").html('已取消').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yiquxiao.mp3');//已取消
						}else if(data.code=='3'){
							$("#explain").html('已退货').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/yituihuo.mp3');//已退货
						}else if(data.code=='4'){
							$("#explain").html('已支付').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yizhifu.mp3');//已支付
						}else if(data.code=='5'){
							$("#explain").html('已入库').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yiruku.mp3');//已入库
						}else if(data.code=='6'){
							$("#explain").html('已打印').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yidayin.mp3');//已打印
						}else if(data.code=='7'){
							$("#explain").html('已出库').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yichuku.mp3');//已出库
						}else if(data.code=='8'){
							$("#explain").html('已提取').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yitiqu.mp3');//已提取
						}else if(data.code=='9'){
							$("#explain").html('已签收').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yiqianshou.mp3');//已签收
						}else if(data.code=='10'){
							$("#explain").html('已核查').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yihecha.mp3');//已核查
						}else if(data.code=='11'){
							$("#explain").html('待退货').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/daituihuo.mp3');//待退货
						}else if(data.code=='13'){
							$("#explain").html('已结束').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yijieshu.mp3');//已结束
						}else if(data.code=='notexist'){
							$("#explain").html('单号不存在').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/danhaobucunzai.mp3');//单号不存在
						}else if(data.code=='abnormal'){
							$("#explain").html('异常').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yichang.mp3');//未支付
						}
						else{
							$("#explain").html('无法验证，请联系技术人员').css('color','red');
						}
						if(!isEmpty(data.msg)){
							$("#explain").html(data.msg).css('color','red');
						}
						$("#ali_order_no").select();
					}
				})
			}
		});
	})
	
	
	function isEmpty(v) {
	    switch (typeof v) {
		    case 'undefined':
		        return true;
		    case 'string':
		        if (v.replace(/(^[ \t\n\r]*)|([ \t\n\r]*$)/g, '').length == 0) return true;
		        break;
		    case 'boolean':
		        if (!v) return true;
		        break;
		    case 'number':
		        if (0 === v || isNaN(v)) return true;
		        break;
		    case 'object':
		        if (null === v || v.length === 0) return true;
		        for (var i in v) {
		            return false;
		        }
		        return true;
	    }
	    return false;
	}
</script>

