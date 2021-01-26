<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
    <script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.browser.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
<script>
function OnReady(id){}
function OnEvent(id, Event, p1, p2, p3, p4){}
</script>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<div>
	<div style="height: 1px; width: 100%; visibility: hidden;">
		<SCRIPT type="text/javascript">insertReport('AF', 'CollapseToolbar=true');</SCRIPT>
	</div>
</div>
<div class="FarSearch" >
	<table id="package">
		<tbody>
			<tr>
				<th>阿里订单号和重量</th>
				<td>
					<input name="kwaiquick_text" type="text" id="kwaiquick_text"  value="" 
					placeholder="请先按Alt+L键锁定焦点，再开始扫描条码"
					style="width: 600px; height: 40px; font-size: 30px; line-height: 30px;">
					<span id="explain" style="margin-left:10px;"></span>
				</td>
				<td>
					<a class="copy hide" style="cursor:pointer;" title="复制错误消息" data-clipboard-target="#explain"><i class="icon icon-copy"></i></a>
				    <input type="hidden" id="flag" value="1">
				</td>
			</tr>
		</tbody>
	</table>
</div>    
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>
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
	document.getElementById("kwaiquick_text").select();
	$('#kwaiquick_text').bind('keyup', function (e) {
		if(e.keyCode ==13){
			$("#kwaiquick_text").blur();
			if($('#flag').val()==$("#kwaiquick_text").val()){
                alert('数据处理中，请不要重复提交');
                document.getElementById("kwaiquick_text").select();
            }else{ 
    			
    			$("#explain").html('');
    			var kwaiquick_text=$("#kwaiquick_text").val();
    			$('#flag').val(kwaiquick_text);
    			$.ajax({
    				url:'<?php echo url('kwaiquick/ajaxin')?>',
    				type:'POST',
					dataType:'json',
					data:{kwaiquick_text : kwaiquick_text},
					success:function(data){
						console.log(data)	
						if(data.message == 'success'){
							$("#explain").html('入库成功').css('color','green');
							// 操作下一条
							document.getElementById("kwaiquick_text").select();
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/rukuchenggong.mp3');
							console.log(data.order_id);
							// 打印
							var order_no = data.order_no;
							console.log(order_no)
							// 操作下一条
							$('#scan_no').select();
							var label_file_name=order_no+"_label.pdf";
							//发票是否存在pdf文件
							var label_pdfexist = pdfisexist(label_file_name);
							farlabelqzprint('Inlabel',label_pdfexist.url,'farlabel');
							
						}else if(data.message == 'orderstatuserror'){
							$("#explain").html('订单状态必须是未入库').css('color','red');
							//语音：订单状态必须是未入库
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/dingdanzhungtaiweiruku.mp3');
							document.getElementById("kwaiquick_text").select();
						}else if(data.message == 'noorder'){
							$("#explain").html('订单不存在').css('color','red');
							//语音：订单不存在
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/dingdanbucunzai.mp3');
							document.getElementById("kwaiquick_text").select();
						}else if(data.message == 'weightiszero'){
							$("#explain").html('包裹重量不能为0').css('color','red');
							//语音：包裹重量不能为0
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/weightiszero.mp3');
							document.getElementById("kwaiquick_text").select();
						}else{
							$("#explain").html('传入数据错误，请检查数据').css('color','red');
							//语音：请检查数据
							$.sound.play('<?php echo $_BASE_DIR?>public/sound/qingjianchashuju.mp3');
							document.getElementById("kwaiquick_text").select();
						}
					}
    			})
            }
		}
	})
})
/**
 * 切换打印机
 * 例如 switchPrinter('EMS')，找到打印机名字中带 EMS的打印机并指定
 */
function switchPrinter(printerName){
	var printers=AF.func("GetPrinters").split(',');
	//搜索打印机
	for (i in printers){
		if(typeof(printers[i]) == 'string' ){
			if (printers[i].indexOf(printerName)>-1){
				printerName=printers[i];
			}
		}
	}
	var setting=AF.func("GetProp", "Print");
	// console.log(setting);
	if (setting.indexOf('<Printer>') > -1){
		setting=setting.replace(/<Printer>.*?<\/Printer>/mg,'<Printer>'+printerName+'</Printer>');
	}else {
		setting=setting.replace('<PrintPage>',"<PrintPage>\r\n<Printer>"+printerName+'</Printer>');
	}
	// console.log(setting);
	AF.func("SetProp", "Print \r\n" + setting);
}
//打印顺序号
var seqnum = '';
function getSeqNum(){
	return seqnum;
}
//判断边长超过122厘米
function checkside(obj){
	if(!isNaN($(obj).val()) && $(obj).val()>122){
		setTimeout(function(){
			if(!confirm("边长大于122厘米,是否继续操作？")){
				$(obj).select();
			}
		},100);
	}
}
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

