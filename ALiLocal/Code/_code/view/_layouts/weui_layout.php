<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
<meta charset="UTF-8">
<meta name="viewport"
	content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta content="telephone=no" name="format-detection" />
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<title></title>
	<link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/2.3.0/weui.min.css"/> 
<script type="text/javascript" src="https://www.far800.com/link/far800/jquery-1.9.1.min.js"></script>
<?php $this->_block('head'); ?><?php $this->_endblock(); ?>
</head>
<body>
	<div class="container">
		<div class="page grid js_show">
			<?php $this->_block('contents'); ?><?php $this->_endblock(); ?>
			<div class="page__ft">
				<div class="weui-footer" style="background-color: #f8f8f8; margin-top: 5px;">
					<p class="weui-footer__links">
						
					</p>
					<p class="weui-footer__text">Copyright &copy; 2020 泛远国际物流</p>
				</div>
			</div>
		</div>
	</div>
	<div id="loadingToast" style="display: none;">
		<div class="weui-mask_transparent"></div>
		<div class="weui-toast">
			<i class="weui-loading weui-icon_toast"></i>
			<p class="weui-toast__content">数据加载中</p>
		</div>
	</div>
	<div class="js_dialog" id="page-dialog" style="display: none;">
		<div class="weui-mask"></div>
		<div class="weui-dialog">
			<div class="weui-dialog__bd"></div>
			<div class="weui-dialog__ft">
				<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">知道了</a>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
$(function(){
	$('#page-dialog').on('click', '.weui-dialog__btn', function(){
		$(this).parents('.js_dialog').fadeOut(200);
	});
});
</script>
</html>