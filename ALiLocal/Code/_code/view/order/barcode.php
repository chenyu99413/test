<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>
<div class="container">
	<div class="span3">
	<h4>单号（每个单号一行）</h4>
	<form action="" method="post">
	<textarea rows="" style="width:250px;height: 360px;" cols="" id="wcode" name="wcode"></textarea><br>
	<a class="btn btn-primary" id="previewLabel">预览</a> 
	<a class="btn btn-primary" id="printLabel">打印</a> 
	</form>
	</div>
	<div class="span8">
		<div style="height: 450px; width: 95% " id = "preview-window">
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	//加载可信证书
	qz.security.setCertificatePromise(function(resolve, reject) {
    	$.ajax("<?php echo $_BASE_DIR?>qz/demo/assets/signing/digital-certificate.txt").then(resolve, reject);
	});
	//签名
	qz.security.setSignaturePromise(function(toSign) {
        return function(resolve, reject) {
        	$.post("<?php echo $_BASE_DIR?>qz/demo/assets/signing/sign-message.php", {request: toSign}).then(resolve, reject);
        };
    });
	startConnection();
})
$('#previewLabel').click(function(){
	$("#preview-window").empty();
	var codes=[];
	str=$('#wcode').val().trim();
	if(str.length <1){
		alert('请输入单号');
		return ;
	}
	codes=str.replace(/\r/,'').split("\n");
	$.ajax({
		url:'<?php echo url('order/getordernos')?>',
		type:'POST',
		dataType:'json',
		data:{ali_order_nos : codes.join(',')},
		success:function(data){
			var table_str='';
			$.each(data,function(key,value){
				table_str+='<embed src="'+value.url+'"/>';
			})
			$("#preview-window").append(table_str);
		}
	})
})
$('#printLabel').click(function(){
	$("#preview-window").empty();
	var codes=[];
	str=$('#wcode').val().trim();
	if(str.length <1){
		alert('请输入单号');
		return ;
	}
	codes=str.replace(/\r/,'').split("\n");
	$.ajax({
		url:'<?php echo url('order/getordernos')?>',
		type:'POST',
		dataType:'json',
		data:{ali_order_nos : codes.join(',')},
		success:function(data){
			var table_str='';
			$.each(data,function(key,value){
				table_str+='<embed src="'+value.url+'"/>';
			})
			$("#preview-window").append(table_str);
			$.each(data,function(key,value){
				farlabelqzprint('Inlabel',value.url,'farlabel');
			})
		}
	})
})
//查找小标签打印机，打印面单
function farlabelqzprint(print_type,url,type){
	qz.printers.find(print_type).then(function(datas) {
		var config = getUpdatedConfig(type);
		config.setPrinter(datas);
		var printData = [
			{ type: 'pdf', data: url }
		];
		qz.print(config, printData);
	}).catch(function(e){
		//未匹配到zebra面单打印机 使用默认打印机打印面单
		qz.printers.getDefault().then(function(datas) {
			console.log(url)
			var config = getUpdatedConfig(type);
			config.setPrinter(datas);
			var printData = [
				{ type: 'pdf', data: url }
			];
			qz.print(config, printData);
		});
	});
}
/// QZ Config ///
var cfg = null;
function getUpdatedConfig(type) {
    if (cfg == null) {
        cfg = qz.configs.create(null);
    }
    updateConfig(type);
    return cfg
}

function updateConfig(type) {
    var pxlSize = null;
    if (true) {
        if(type=='farlabel'){
    		pxlSize = {
                width: 64,
                height: 19
            };
    	}
        
	}

    var pxlMargins = $("#pxlMargins").val();
    if (true) {
        if(type=='farlabel'){
        	pxlMargins = {
                	top: 0,
                    right: 0,
                    bottom: 0,
					left: 0
            };
        }
    }
    var orient='portrait';//portrait 纵向| landscape横向 | reverse-landscape
    var copies = 1;
    var jobName = null;
    if ($("#rawTab").hasClass("active")) {
        copies = $("#rawCopies").val();
        jobName = $("#rawJobName").val();
    } else {
        copies = $("#pxlCopies").val();
        jobName = $("#pxlJobName").val();
    }
    cfg.reconfigure({
		altPrinting: $("#rawAltPrinting").prop('checked'),
		encoding: $("#rawEncoding").val(),
		endOfDoc: $("#rawEndOfDoc").val(),
		perSpool: $("#rawPerSpool").val(),
		colorType: $("#pxlColorType").val(),
		copies: copies,
		density: $("#pxlDensity").val(),
		duplex: $("#pxlDuplex").prop('checked'),
		interpolation: $("#pxlInterpolation").val(),
		jobName: jobName,
		legacy: false,
		margins: pxlMargins,
		orientation: orient,
		paperThickness: '',
		printerTray:'',
		rasterize: false,
		rotation: '0',
		scaleContent:true,
		size: pxlSize,
		units: 'mm'
	});
}
//判断发票pdf是否存在
function pdfisexist(filename){
    var result = new Object();
	$.ajax({
		url:'<?php echo url('warehouse/pdfisexist')?>',
		type:'POST',
		dataType:'json',
		data:{filename : filename},
		async : false,
		success:function(data){
			result.message = data.message;
			result.url = data.url;
		}
	})
	return result;
}
//创建链接
function startConnection(config) {
    if (!qz.websocket.isActive()) {
        updateState('Waiting', 'default');
        qz.websocket.connect(config).then(function() {
            updateState('Active', 'success');
//             findVersion();
        }).catch(handleConnectionError);
    } else {
        displayMessage('An active connection with QZ already exists.', 'alert-warning');
    }
}
function displayError(err) {
    console.error(err);
    displayMessage(err, 'alert-danger');
}
 /// Helpers ///
function handleConnectionError(err) {
    updateState('Error', 'danger');

    if (err.target != undefined) {
        if (err.target.readyState >= 2) { //if CLOSING or CLOSED
            displayError("Connection to QZ Tray was closed");
        } else {
            displayError("A connection error occurred, check log for details");
            console.error(err);
        }
    } else {
        displayError(err);
    }
}
function updateState(text, css) {
    $("#qz-status").html(text);
    $("#qz-connection").removeClass().addClass('panel panel-' + css);

    if (text === "Inactive" || text === "Error") {
        $("#launch").show();
    } else {
        $("#launch").hide();
    }
}
</script>
<?PHP $this->_endblock();?>

