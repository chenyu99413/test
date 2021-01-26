<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php $this->_block('title2'); ?> <?php $this->_endblock(); ?> 阿里专线 <?php $this->_block('title'); ?> <?php $this->_endblock(); ?></title>
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/easyui.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/icon.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/color.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/easyui-bs.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/bootstrap/bootstrap.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/bootstrap/bootstrap-responsive.min.css">
<!-- <link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/bootstrap.big.css"> -->
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/bootstrap/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR?>public/css/far.css">
<link rel="stylesheet" href="<?php echo $_BASE_DIR;?>public/bootcss/css/chosen.css">

<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery-1.8.0.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.easyui.js"></script>

<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/validate.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.floatThead.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/far.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jqSimpleConnect.js"></script>
<script src="<?php echo $_BASE_DIR?>public/bootcss/js/chosen.jquery.js"></script>
<script src="<?php echo $_BASE_DIR?>public/js/clipboard.min.js"></script>
<script src="<?php echo $_BASE_DIR?>public/js/layer/layer.js" charset="UTF-8"></script>
<!-- <script src="http://oa.far800.com/api/leftbar"></script> -->
<script type="text/javascript">
$.parser.auto=false;
</script>
<?php $this->_block('head'); ?>
<?php $this->_endblock(); ?>
</head>
<?php flush();?>
<body>
	<div class="FarNav">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<a class="brand" href="<?php echo url("default")?>" style="margin-top: -5px;">泛远集团</a>
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse" style="color: black; text-shadow: none; padding: 0px;"> 菜单 </a>
				<div class="nav-collapse collapse">
				<?php echo Q::control('menu','description','')?>
	    			<ul class="nav pull-right" style="margin-right: 70px;">
						<li><a><?php echo $_login_user["staff_name"]?></a></li>
						<li><a href="<?php echo url('staff/loginout')?>">注销</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div style="height: 55px;"></div>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<?php echo $path!=null ? Q::control('path','', $path) : ""?>
				<?php $this->_block('contents'); ?>
				<?php $this->_endblock(); ?>
			</div>
		</div>
	</div>
	<div class="container debug">
		<hr />
		<i>技术中心 @2019 &nbsp;&nbsp;&nbsp;<a target="_blank" href="http://www.beian.miit.gov.cn"><?php echo '浙ICP备12010076号-2'?></a> </i>
	</div>
	<script type="text/javascript">
	$.parser.parse();
	if (location.hash.length && location.hash.substr(0,5)=='#msg='){
		str=decodeURIComponent(location.hash.substr(5));
		$.messager.alert('系统消息',str);
		location.hash='';
	}
	new ClipboardJS('.copy');
	</script>
	<script src="<?php echo $_BASE_DIR?>public/js/global.js"></script>
	<script src="<?php echo $_BASE_DIR?>public/js/bootstrap/bootstrap.js"></script>

	<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/bootstrap/bootstrap-tooltip.js"></script>
	<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/bootstrap/bootstrap-popover.js"></script>
	<script type="text/javascript">var cb_appkey='<?php echo md5(Controller_OA::APIKEY)?>';</script>
<script src="http://oa.far800.com/api/leftbar"></script>
</body>
</html>
