<?php //布局设定 ，参考 view/_layouts下面的文件 ?>
<?PHP $this->_extends('_layouts/kp_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="https://www.far800.com/link/far800/jquery-1.9.1.min.js"></script>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="page__bd" style="height: 100%;">
<form action="" method="post" id="form">
	<div class="weui-cells__title">
		已扫描
		<a class="weui-btn weui-btn_mini weui-btn_warn" style="float: right" href="javascript:finish()">√ 扫描完成</a>
	</div>
	<div id="scan_container">
	</div>
	<div class="weui-footer weui-footer_fixed-bottom ">
		<div class="button-sp-area ">
			<a class="weui-btn  weui-btn_primary" href="javascript:scan();" style="color: white;" >扫描</a>
		</div>
	</div>
	<textarea rows="" style="display: none;" id="scan_codes" name="scan_codes" cols=""></textarea>
	<input type="hidden" name="wechat_id" value="<?php echo request('wechat_id')?>">
</form>
</div>

<script type="text/javascript">
function g(id){
	return document.getElementById(id);
}
<?php
$ts = time ();
if (isset ( $message )) {
	echo 'alert("' . $message . '");';
}
?>
var scan_codes={};
//验证
wx.config(<?php echo Helper_WX::jsConfig(array('scanQRCode'))?>);
function scan(){
	wx.scanQRCode({
	    needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
	    scanType: ["barCode"], // 可以指定扫二维码还是一维码，默认二者都有
	    success: function (res) {
		    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
		    if(result.indexOf(',')==-1){
			    alert('无法识别单号，请重新扫描');
			    scan();
		    }else{
			    updateList(result.split(',')[1]);
		    }
		}
	});
}
function updateList(code){
	if (typeof(scan_codes[code]) != 'undefined' ){
	    alert('单号重复: '+code);
	    return;
	}
    scan_codes[code]=code;
	$('#scan_container').prepend('<div class="weui-cell "><div class="weui-cell__bd"><p>'+code+'</p></div></div>');
	$('#scan_codes').val(JSON.stringify(scan_codes));
}
function finish(){
	var i = 0;
	$.each(scan_codes,function(k,v){
		if(k.substring(0,3) != 'ALS'){
			i++;
		}
	});
	
	if(i == 0){
		 alert('请扫描快递单号');
		 return;
	}
	if(i > 1){
		 alert('只能扫描一个快递单号');
		 return;
	}
	$('#form').submit();
}
wx.ready(function(){
});
wx.error(function(res){
	document.getElementById('container').innerHTML='<br><br><br><br>时光隧道不是很稳定，请稍后再找未来的你。';
});

</script>
<?PHP $this->_endblock();?>

