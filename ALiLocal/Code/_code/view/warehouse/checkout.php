<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    包裹出库
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
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
				<th>阿里订单号</th>
				<td>
					<input name="ali_order_no" type="text" id="ali_order_no" style="width: 200px" value="">
					<input type="checkbox" name="is_print_oldtrackingno" value="1" style="margin-left:10px;">
					<span>扫描原末端运单号</span>
					<span id="explain" style="margin-left:10px;"></span>
				</td>
				<td>
					<a class="copy hide" style="cursor:pointer;" title="复制错误消息" data-clipboard-target="#explain"><i class="icon icon-copy"></i></a>
				    <input type="hidden" id="flag" value="1">
				</td>
				<td id="tracking_no" style="color: red;font-size:20px">
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	function displayError(err) {
	    console.error(err);
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
        })
		document.getElementById("ali_order_no").select();
		//扫描阿里单号
		endConnection();
		startConnection();
		var timer;
		$('#ali_order_no').bind('keyup', function (e) {
			clearTimeout(timer);
			timer = setTimeout(function() {          
    			if(e.keyCode ==13){
        			//截取单号
    				var ali_order_no=$("#ali_order_no").val();
    				//IB扫到的单号，例如，420461069205590237757358406483，去掉前八位
    				if(ali_order_no.substring(0,1)==4 && (ali_order_no.length == 30 || ali_order_no.length == 32)){
    					ali_order_no = ali_order_no.substring(8);
    				}
    				//FEDEX末端单号34位,截取后面12位
    				if(ali_order_no.length == 34){
    					ali_order_no = ali_order_no.substring(22);
    				}
    				$("#ali_order_no").val(ali_order_no);
    				
    				$("#ali_order_no").blur();
    				if($('#flag').val()=='2'){
                    	alert('数据处理中，请不要重复提交');
                    }else{ 
        				$('#flag').val('2');
        				$("#explain").html('');
        				$("#tracking_no").html('');
        				//var ali_order_no=$("#ali_order_no").val();
        				$.ajax({
        					url:'<?php echo url('warehouse/checkorder')?>',
        					type:'POST',
        					dataType:'json',
        					data:{ali_order_no:ali_order_no,is_print_oldtrackingno:$('input[name="is_print_oldtrackingno"]:checked').val()},
        					success:function(data){
        						$('.copy').show();
        						if(typeof data.message !== 'undefined'){
        							$('#flag').val('1');
        						}
        						 if(data.message=='notexist'){
        							$("#explain").html('单号不存在').css('color','red');
        							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/danhaobucunzai.mp3');//单号不存在
        							$("#ali_order_no").select();
        						}else if(data.message=='cnusbjfyforbidcheckout'){
        							$("#explain").html('中美无忧-包机专线 暂时禁止标签打印出库操作').css('color','red');
        							$("#ali_order_no").select();
        						}else if(data.message=='cuufalse'){
        							$("#explain").html('请检查币种是否存在或到期').css('color','red');
        							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bizhongshezhiyichang.mp3');//币种不存在
        							$("#ali_order_no").select();
        						}else {
        							$("#product").html('产品名称:'+data.product).css('color','red');
        							$("#label_remark").html('打印要求:'+data.label_remark).css('color','red');
            						if(data.message=='hasbattery'){
            							$("#explain").html('不支持带电产品').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/buzhichidaidianchanpin.mp3');//不支持带电产品
            							$("#ali_order_no").select();
            						}else if(data.message=='checkout'){
            							if (confirm('已打印过面单，是否继续打印?')) {
            								$('.copy').hide();
            								MessagerProgress('数据处理中...');
            								//计算费用，保存面单
            								calculatefee(ali_order_no,data.channel_id);
            							}
            						}else if(data.message=='koujian'){
            							$("#explain").html('已扣件，请联系客服中心处理').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yikoujianqinglianxikefuzhongxinchuli.mp3');//订单未支付
            							$("#ali_order_no").select();
                					}else if(data.message=='notpay'){
            							$("#explain").html('订单未支付').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/weizhifu.mp3');//订单未支付
            							$("#ali_order_no").select();
            						}else if(data.message=='notsamewarehouse'){
            							$("#explain").html('失败,不是本仓包裹').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bushibencangbaoguo.mp3');//不是本仓包裹
            							$("#ali_order_no").select();
            						}else if(data.message=='inbgcomplete'){
            							$("#explain").html('订单出库包裹数据不全').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qinglianxikefuzhongxinchuli.mp3');//数据不完整
            							$("#ali_order_no").select();
            						}else if(data.message=='fuelnotexist'){
            							$("#explain").html('没有设置燃油').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qinglianxikefuzhongxinchuli.mp3');//没有设置燃油
            							$("#ali_order_no").select();
            						}else if(data.message=='overdeclarethreshold'){
            							$("#explain").html(data.reason).css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/miandanshibai.mp3');//超出申报限制
            							$("#ali_order_no").select();
            						}else if(data.message=='overholdflag'){
            							$("#explain").html(data.reason).css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/miandanshibai.mp3');//超出限制
            							$("#ali_order_no").select();
            						}else if(data.message=='overtotalcostweight'){
            							$("#explain").html(data.reason).css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/miandanshibai.mp3');//超出整票计费重限制
            							$("#ali_order_no").select();
            						}else if(data.message=='qingdaocangerror'){
            							$("#explain").html('青岛仓只能操作假发专线订单').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qingdaocangzhinengcaozuojiafazhuanxiandingdan.mp3');//没有设置燃油
            							$("#ali_order_no").select();
            						}else if(data.message=='youbianwfw'){
            							$("#explain").html('邮编无服务').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/youbianwufuwu.mp3');//邮编无服务
            							$("#ali_order_no").select();
            						}else if(data.message=='chengshiwfw'){
            							$("#explain").html('城市无服务').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chengshiwufuwu.mp3');//邮编无服务
            							$("#ali_order_no").select();
            						}else if(data.message=='guojiawfw'){
            							$("#explain").html('国家无服务').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/guojiawufuwu.mp3');//邮编无服务
            							$("#ali_order_no").select();
            						}else if(data.message=='aliordernonotexist'){
            							$("#explain").html('已退回支付订单不存在').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/miandanshibai.mp3');//对应订单号不存在
            							$("#ali_order_no").select();
            						}else if(data.message=='aliordernocannotprint'){
            							$("#explain").html('已退回支付订单不能打印').css('color','red');
            							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/miandanshibai.mp3');//此订单不能打印
            							$("#ali_order_no").select();
            						}else{
            							$('.copy').hide();
            							MessagerProgress('数据处理中...');
            							//计算费用，保存面单
            							calculatefee(data.ali_order_no,data.channel_id);
        						}
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
			url:'<?php echo url('warehouse/checkout')?>',
			type:'POST',
			dataType:'json',
			data:{ali_order_no:ali_order_no,special_packing_fee_count:$("#special_packing_fee_count").val()},
			success:function(data){
				MessagerProgress("close");
				$("#package").nextAll().remove();
				if(data==null){
					$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
					$("#ali_order_no").select();
					return false;
				}
				if(data.message=='productnotexist'){
					$("#explain").html('产品不存在').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chanpinbucunzai.mp3');//产品不存在
					$("#ali_order_no").select();
				}else if(data.message=='channelnotexist'){
					$("#explain").html('渠道成本不存在').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qudaochengbenbucunzai.mp3');//渠道成本不存在
					$("#ali_order_no").select();
				}else if(data.message=='channelcostoverthreshold'){
					$("#explain").html('渠道需优化').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/channelcostoverthreshold.mp3');//渠道需优化
					$("#ali_order_no").select();
				}else if(data.message=='nousechannel'){
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
				}else if(data.message=='pricenotexist'){
					$("#explain").html('运费计算失败').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/jisuanyunfeishibai.mp3');//运费计算失败
					$("#ali_order_no").select();
				}else if(data.message=='productnumovertwenty'){
					$("#explain").html('订单含产品数量超过20个的产品，请联系客服处理。').css('color','red');
					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/productnumovertwenty.mp3');//中美空派USPS，订单含产品数量超过20个的产品，请联系客服处理
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
					$("#tracking_no").html('物流单号: '+data.tracking_no);
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
				$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
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
			data:{ali_order_no : ali_order_no},
			success:function(data){
				console.log(ali_order_no);
				var other_pdfexist = pdfisexist(data.tracking_no);
				//面单pdf文件存储路径
				var url = other_pdfexist.miandanurl;
				if (qz.websocket.isActive()) {
					if(data.network_code=="EMS" && account == 'EMS'){
						qzprint(url,'A4',null);
					}else if (data.network_code=="EMS" && account == 'EUB') {
						zebraordhlqzprint('Zebra',url,'EUB',null);
					}else if (data.network_code=="FEDEX" && account=='FEDEX') {
						zebraordhlqzprint('Zebra',url,'FEDEX',other_pdfexist.url);
					}else if (data.network_code=="DHL" || (data.network_code=="FEDEX" && account !='FEDEX') || data.network_code=="DHLE"  || account =='DHL') {					
						zebraordhlqzprint('DHL',url,'EUUS',other_pdfexist.url);				
					}else if (data.network_code=="US-FY") {
						zebraordhlqzprint('Zebra',url,'US-FY',null);
	    			}else{
	    				zebraordhlqzprint('Zebra',url,null,other_pdfexist.url);
	    			}
				}else{
				 	qz.websocket.connect({ retries: 5, delay: 1 }).then(function() {
					 	if(data.network_code=="EMS" && account == 'EMS'){
							qzprint(url,'A4',null);
						}else if (data.network_code=="EMS" && account == 'EUB') {
							zebraordhlqzprint('Zebra',url,'EUB',null);
						}else if (data.network_code=="FEDEX" && account=='FEDEX') {
							zebraordhlqzprint('Zebra',url,'FEDEX',other_pdfexist.url);
						}else if (data.network_code=="DHL" || (data.network_code=="FEDEX" && account !='FEDEX') || data.network_code=="DHLE"  || account =='DHL') {					
							zebraordhlqzprint('DHL',url,'EUUS',other_pdfexist.url);				
						}else if (data.network_code=="US-FY") {
							zebraordhlqzprint('Zebra',url,'US-FY',null);
		    			}else{
		    				zebraordhlqzprint('Zebra',url,null,other_pdfexist.url);
		    			}
		            })
				}
			}
		})
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

        var pxlMargins = '';
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

        cfg.reconfigure({
                            altPrinting: false,//使用CUPS命令行参数打印指定的文件。对Windows没有影响。
                            encoding: '',//字符集
                            endOfDoc: '',//表示页面结尾以控制假脱机的字符
                            copies: copies,//页数
                            duplex: false,//双面打印，可以通过传递字符串值来指定双面样式
                            jobName: jobName,//显示在打印队列中的名称。
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

    function startConnection(){
        if (!qz.websocket.isActive()) {
        	qz.websocket.connect({ retries: 5, delay: 1 }).then(function() {
            	console.log('START QZ');
        	});
        } 
    }

    function endConnection(){
        if (qz.websocket.isActive()) {
            qz.websocket.disconnect().then(function() {
            	console.log('CLOSE QZ');
        	});
        } 
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
        console.log('FN:'+filename);
		$.ajax({
			url:'<?php echo url('warehouse/allpdfisexist')?>',
			type:'POST',
			dataType:'json',
			data:{filename : filename},
			async : false,
			success:function(data){
				//面单url
				result.miandanurl = data.miandanurl;
				//发票url
				result.url = data.url;
			}
		})
		return result;
	}
	//电脑默认打印机打印
	function qzprint(url,type,other_url){
		qz.printers.getDefault().then(function(datas) {
			var config = getUpdatedConfig(type);
			config.setPrinter(datas);
			var printData = [
				{ type: 'pdf',data: url }
			];
			qz.print(config, printData).then(function(){
		    	qz.printers.getDefault().then(function(datas) {
		        	if(other_url){
						var config = getUpdatedConfig('A4');
						config.setPrinter(datas);
						var printData = [
							{ type: 'pdf', data: other_url }
						];
						qz.print(config, printData);
		        	}
				});
		    }).catch(function(e){
		    	console.error(e);
			});
		})
	}
	//查找斑马打印机，打印面单
	function zebraordhlqzprint(print_type,url,type,other_url){
		qz.printers.find(print_type).then(function(datas) {			
			var config = getUpdatedConfig(type);		
			config.setPrinter(datas);
			var printData = [
				{ type: 'pdf',data: url }
			];
			console.log(url);
		        qz.print(config, printData).then(function(){
		        	qz.printers.getDefault().then(function(datas) {
			        	if(other_url){
							var config = getUpdatedConfig('A4');
							config.setPrinter(datas);
							var printData = [
								{ type: 'pdf', data: other_url }
							];
							qz.print(config, printData);
			        	}
					});
		        }).catch(function(e){
		        	console.error(e);
				});
		    }).catch(function(e){
		    	//未匹配到zebra面单打印机 使用默认打印机打印面单
		    	qz.printers.getDefault().then(function(datas) {
		    		var config = getUpdatedConfig('A4');
					config.setPrinter(datas);
					var printData = [
						{ type: 'raw', format: 'pdf', flavor: 'file', data: url, options: opts }
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
		    		}).catch(function(e){
		    			console.error(e);
					});
		    	});
		    });
		}
</script>

