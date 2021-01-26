<?PHP $this->_extends('_layouts/common_layout'); ?>
<?php $this->_block('title');?>登录<?php $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="post">
	<div style="height: 50px;"></div>
	<div class="form-horizontal span6 offset3">
		<div class="control-group">
			<div class="controls">
				<a href="<?php echo Controller_OA::getLoginURL()?>" class="btn btn-primary">使用泛远通行证登录</a>
			</div>
		</div>
		<div style="height: 50px;"></div>
		<div style="text-align: center;"><a target="_blank" href="http://www.beian.miit.gov.cn"><?php echo '浙ICP备12010076号-2'?></a></div>
		<div class="text-center">
			<a style="color: red"
				href="<?php echo url('download/firefoxclient')?>">
				<i class="icon-circle-arrow-down"></i>
				请先下载火狐专用浏览器
			</a>
		</div>
	</div>
</form>

<script type="text/javascript">
	/**
	 * 初始化
	 */
	$(function() {
		document.getElementById("staff_code").focus();
	});
	/**
	 * 验证登录信息
	 **/
	function check(){
		var account=$("#staff_code").val();
		var password=$("#staff_password").val();
		var flag=false;
		$.ajax({
			type:'post',
			url:'<?php echo url('/check')?>',
			data:{'account':account,'password':password},
			async:false,
			success:function(data){
				if(data=='true'){
					flag=true;
				}else{
					$("#error").html(data);
					flag=false;
				}
			}
		});
		return flag;
	}
</script>
<?PHP $this->_endblock();?>
