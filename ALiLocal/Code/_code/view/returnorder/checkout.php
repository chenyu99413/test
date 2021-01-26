<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    包裹出库
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript">
$('body').on('keydown', 'input, select', function(e) {
	if (e.keyCode == 13) {
		return enter2tab(this,e);
	}
});
print=function () {
	var agent = navigator.userAgent.toLowerCase();
	if (agent.indexOf("msie") > 0) {
		return true;
	}
	if (!navigator.mimeTypes["application/supcan-plugin"]) {
		if (agent.indexOf("chrome") > 0){
			$.messager.alert('', '条码打印功能暂时不支持谷歌浏览器');
			//window.open("install_chrome.htm");
		}
		else{
			window.open("<?php echo $_BASE_DIR?>public/supcan/binary/supcan.xpi");
		}
	}
};
function OnReady(id){
	print();
}
function OnEvent(id, Event, p1, p2, p3, p4){}
</script>
<div>
	<div style="height: 1px; width:1px ">
		<SCRIPT type="text/javascript">insertReport('AF', 'CollapseToolbar=true');</SCRIPT>
	</div>
	<div style="height: 1px; width:1px ">
		<SCRIPT type="text/javascript">insertUpload('AF1', 'fileTypes=pdf');</SCRIPT>
	</div>
</div>
<div class="FarSearch" >
    <table>
		<tbody>
			<tr>
			    <td id="product"></td>
			</tr>
			<tr>
		   		<td id="label_remark"></td>
			</tr>
		</tbody>
	</table>
	<table id="package">
		<tbody>
			<tr>
				<th>阿里订单号/原末端单号</th>
				<td>
					<input name="ali_order_no" type="text" id="ali_order_no" style="width: 200px" value="">
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
<?PHP $this->_endblock();?>
<script type="text/javascript">
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
		document.getElementById("ali_order_no").select();
		//扫描阿里单号
		var timer;
		$('#ali_order_no').bind('keyup', function (e) {
			clearTimeout(timer);
			timer = setTimeout(function() {          
    			if(e.keyCode ==13){
    				$("#ali_order_no").blur();
    				if($('#flag').val()=='2'){
                    	alert('数据处理中，请不要重复提交');
                    }else{ 
        				$('#flag').val('2');
        				$("#explain").html('');
        				var ali_order_no=$("#ali_order_no").val();
        				$.ajax({
        					url:'<?php echo url('/checkorder')?>',
        					type:'POST',
        					dataType:'json',
        					data:{ali_order_no:ali_order_no},
        					success:function(data){
        						$('.copy').show();
        						if(typeof data.message !== 'undefined'){
        							$('#flag').val('1');
        						}
        						 if(data.message=='notexist'){
        							$("#explain").html('单号不存在').css('color','red');
        							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/danhaobucunzai.mp3');//单号不存在
        							$("#ali_order_no").select();
        						}else if(data.message=='退件订单已出库'){
        							$("#explain").html('退件订单已出库').css('color','red');
        							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/danhaobucunzai.mp3');//单号不存在
        							$("#ali_order_no").select();
        						}else if(data.message=='checkout'){
        							if (confirm('已打印过面单，是否继续打印?')) {
        								$('.copy').hide();
        								MessagerProgress('数据处理中...');
        								//计算费用，保存面单
        								calculatefee(ali_order_no,data.channel_id);
        							}
        						}else{
        							$('.copy').hide();
        							MessagerProgress('数据处理中...');
        							//计算费用，保存面单
        							calculatefee(data.ali_order_no,data.channel_id);
    							}
        					}
        				})
        			}
    			}
			}, 200);
		});
	})
	/**
	*计算费用，保存面单
	**/
	function calculatefee(ali_order_no,channel_id){
		$.ajax({
			url:'<?php echo url('/checkout')?>',
			type:'POST',
			dataType:'json',
			data:{ali_order_no:ali_order_no},
			success:function(data){
				MessagerProgress("close");
				$("#package").nextAll().remove();
				if(data==null){
					$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
					$("#ali_order_no").select();
					return false;
				}
				if(data.message=='渠道成本不存在'){
					$("#explain").html('渠道成本不存在').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qudaochengbenbucunzai.mp3');//渠道成本不存在
					$("#ali_order_no").select();
				}else if(data.message=='请先转入待重发'){
					$("#explain").html('请先转入待重发').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bucunzai.mp3');//状态不对
					$("#ali_order_no").select();
				}else if(data.message=='channelcostoverthreshold'){
					$("#explain").html('渠道需优化').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/channelcostoverthreshold.mp3');//渠道需优化
					$("#ali_order_no").select();
				}else if(data.message=='无可用渠道'){
					$("#explain").html('无可用渠道').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/channelcostoverthreshold.mp3');//渠道需优化
					$("#ali_order_no").select();
				}else if(data.message=='channel_id_no'){
					$("#explain").html('指定渠道无权限').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/zhidingqudaowuquanxian.mp3');//指定渠道无权限
					$("#ali_order_no").select();
				}else if(data.message=='wuyouhuaqudao'){
					$("#explain").html('无优化渠道').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/wuyouhuaqudao.mp3');//指定渠道无权限
					$("#ali_order_no").select();
				}else if(data.message=='nopda'){
					$("#explain").html('指定渠道不支持pda品类').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/nopda.mp3');//指定渠道不支持pda品类
					$("#ali_order_no").select();
				}else if(data.message=='nobaoguan'){
					$("#explain").html('指定渠道不支持报关').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/nobaoguan.mp3');//指定渠道不支持报关
					$("#ali_order_no").select();
				}else if(data.message=='has_battery'){
					$("#explain").html('指定渠道带电效验失败').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/zhidingqudaodaidianxiaoyanshibai.mp3');//指定渠道带电效验失败
					$("#ali_order_no").select();
				}else if(data.message=='incomplete'){
					$("#explain").html('数据不完整').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qinglianxikefuzhongxinchuli.mp3');//数据不完整
					$("#ali_order_no").select();
				}else if(data.message=='价格未找到'){
					$("#explain").html('价格未找到').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/jisuanyunfeishibai.mp3');//运费计算失败
					$("#ali_order_no").select();
				}else if(data.message=='formulaerror'){
					$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
					$("#ali_order_no").select();
				}else if(data.message!='true'){
					$("#explain").html('获取面单失败:【渠道错误】' + data.message).css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/miandanshibai.mp3');//获取面单失败
					$("#ali_order_no").select();
				}else{
					$("#explain").html('成功，请检查面单是否有乱码.渠道:'+data.channel_name).css('color','green');
					if(data.hasbattery==1){
						if(data.declaration_type=='DL'){
							//报关，带电，成功
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/baoguandaidianchenggong.mp3');
						}else{
							//带电，成功
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/daidianchenggong.mp3');
						}
					}else{
						if(data.declaration_type=='DL'){
							//报关成功，请检查面单是否有乱码
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/baoguanchenggongqijianchamiandanshifouluanma.mp3');
						}else{
							//成功，请检查面单是否有乱码
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggongqijianchamiandanshifouluanma.mp3');//出库成功
						}
					}
					$("#ali_order_no").select();
					$("#product").html('');
					$("#label_remark").html('');
					//打印面单和invoice
					print_label(data.account,data.pdf_count,ali_order_no,channel_id);
				}
				$('#flag').val('1');
			},
			error: function (XMLHttpRequest, textStatus, errorThrown)
	        {
				MessagerProgress("close");
				$("#explain").html('系统出了点问题，请联系系统管理员。').css('color','red');
				$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
				$("#ali_order_no").select();
				return false;
	        }
		})
	}
	/**
	*打印功能
	**/
	function print_label(account,pdf_count,ali_order_no,channel_id){
		$.ajax({
			url:'<?php echo url('/gettrackingno')?>',
			type:'POST',
			dataType:'json',
			data:{ali_order_no:ali_order_no},
			success:function(data){
				var miandan_file_name=data.tracking_no+".pdf";
				var miandan_pdfexist = pdfisexist(miandan_file_name);
				//面单pdf文件存储路径
				var url = miandan_pdfexist.url;
				
				var others_file_name=data.tracking_no+"_others.pdf";
				//发票是否存在pdf文件
				var other_pdfexist = pdfisexist(others_file_name);
				if(data.network_code=="EMS" && account == 'EMS'){
					qzprint(url,'A4',null);
				}else if (data.network_code=="EMS" && account == 'EUB') {
					zebraordhlqzprint('Zebra',url,'EUB',null);
				}else if (data.network_code=="FEDEX" && account=='FEDEX') {
					zebraordhlqzprint('Zebra',url,'FEDEX',other_pdfexist.url);
				}else if (data.network_code=="DHL" || (data.network_code=="FEDEX" && account !='FEDEX') || data.network_code=="DHLE" || data.network_code=="YWML" || account =='DHL') {
					if(account=='ML051501'){
						qzprint(url,'ML',other_pdfexist.url);
					}else{
						zebraordhlqzprint('DHL',url,'EUUS',other_pdfexist.url);
    				}
				}else if (data.network_code=="US-FY") {
					zebraordhlqzprint('Zebra',url,'US-FY',null);
    			}else{
    				zebraordhlqzprint('Zebra',url,null,other_pdfexist.url);
    			}
			}
		})
	}
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
            if(type==null || type=='FEDEX'){
        		pxlSize = {
                    width: 100,
                    height: 145
                };
        	}
            if(type=='EUUS'){
        		pxlSize = {
                    width: 100,
                    height: 195
                };
        	}
            if(type=='EUB'){
        		pxlSize = {
                    width: 100,
                    height: 145
                };
        	}
            if(type=='US-FY'){
        		pxlSize = {
                    width: 100,
                    height: 175
                };
        	}
		}

        var pxlMargins = $("#pxlMargins").val();
        if (true) {
            if(type=='A4'){
            	pxlMargins = {
                        top: 6,
                        right: 6,
                        bottom: 6,
                        left: 6
                    };
            }
            if(type==null || type=='EUUS' || type=='EUB'){
            	pxlMargins = {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    };
            }
            if(type=='FEDEX'){
                	pxlMargins = {
                            top: 2,
                            right: 0,
                            bottom: 0,
                            left: 6
                        };
            }
            if(type=='US-FY'){
            	pxlMargins = {
                        top: 2,
                        right: 0,
                        bottom: 0,
                        left: 2
                    };
            }
            if(type=='ML'){
            	pxlMargins = {
                        top: 20,
                        right: 6,
                        bottom: 6,
                        left: 6
                    };
            }
        }
        var orient='';
        if(type=='ML'){
        	orient='portrait';
        }
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

    function setPrintFile() {
        setPrinter({ file: $("#askFile").val() });
        $("#askFileModal").modal('hide');
    }

    function setPrintHost() {
        setPrinter({ host: $("#askHost").val(), port: $("#askPort").val() });
        $("#askHostModal").modal('hide');
    }

    function setPrinter(printer) {
        var cf = getUpdatedConfig();
        cf.setPrinter(printer);

        if (typeof printer === 'object' && printer.name == undefined) {
            var shown;
            if (printer.file != undefined) {
                shown = "<em>FILE:</em> " + printer.file;
            }
            if (printer.host != undefined) {
                shown = "<em>HOST:</em> " + printer.host + ":" + printer.port;
            }

            $("#configPrinter").html(shown);
        } else {
            if (printer.name != undefined) {
                printer = printer.name;
            }

            if (printer == undefined) {
                printer = 'NONE';
            }
            $("#configPrinter").html(printer);
        }
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
//                 findVersion();
            }).catch(handleConnectionError);
        } else {
            displayMessage('An active connection with QZ already exists.', 'alert-warning');
        }
    }
	//电脑默认打印机打印
	function qzprint(url,type,other_url){
		qz.printers.getDefault().then(function(datas) {
			var config = getUpdatedConfig(type);
			config.setPrinter(datas);
			var printData = [
				{ type: 'pdf', data: url }
			];
			 qz.print(config, printData).then(function(){
		        	qz.printers.getDefault().then(function(datas) {
						var config = getUpdatedConfig('A4');
						config.setPrinter(datas);
						var printData = [
							{ type: 'pdf', data: other_url }
						];
						qz.print(config, printData);
					});
		        });
		});
	}
	//查找斑马打印机，打印面单
	function zebraordhlqzprint(print_type,url,type,other_url){
			qz.printers.find(print_type).then(function(datas) {
	            var config = getUpdatedConfig(type);
	            config.setPrinter(datas);
		        var printData = [
		          	 { type: 'pdf', data: url }
		        ];
		        qz.print(config, printData).then(function(){
		        	qz.printers.getDefault().then(function(datas) {
						var config = getUpdatedConfig('A4');
						config.setPrinter(datas);
						var printData = [
							{ type: 'pdf', data: other_url }
						];
						qz.print(config, printData);
					});
		        });
		    }).catch(function(e){
		    	//未匹配到zebra面单打印机 使用默认打印机打印面单
		    	qz.printers.getDefault().then(function(datas) {
			    	console.log(url)
		    		var config = getUpdatedConfig('A4');
					config.setPrinter(datas);
					var printData = [
						{ type: 'pdf', data: url }
					];
					qz.print(config, printData).then(function(){
						qz.printers.getDefault().then(function(datas) {
							var config = getUpdatedConfig('A4');
							config.setPrinter(datas);
							var printData = [
								{ type: 'pdf', data: other_url }
							];
							qz.print(config, printData);
						});
		    		});
		    	});
		    });
		}
</script>

