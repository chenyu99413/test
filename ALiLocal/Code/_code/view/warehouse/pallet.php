<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    编辑托盘
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<style>
.table th, .table td{
	line-height:20px;;
}
</style>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
<script type="text/javascript">
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
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'仓库业务' => '','出库打托' => url ( 'warehouse/palletlist' ),'编辑托盘' => '' 
	) 
) )?>
<div style="height: 1px; width:1px ">
		<SCRIPT type="text/javascript">insertReport('AF', 'CollapseToolbar=true');</SCRIPT>
	</div>
<h3>托盘号：<?php echo $pallet->pallet_no?></h3>
<form>
<div class="row-fluid" style="min-height:420px;">
	<div class="span7">
		<table>
			<tbody>
				<tr style="line-height:50px;">
					<td colspan="2">
						<label>
        					<input type="checkbox" class="locked"  value="1"> 同订单锁定
        				</label>
					</td>
				</tr>
				<tr style="line-height:50px;">
				    <th>渠道：</th>
					<td>
						<?php echo $pallet->channel_name?>
					</td>
				</tr>
				<tr style="line-height:40px;">
				    <th style="width:40px;text-align:left">运单号</th>
					<td>
					   <input  type="text" style="width:250px;" name="tracking_code" id="tracking_code" value="">
					</td>
				</tr>
				<tr id="package_info" style="display:none">
				    <td colspan="2">
				        <table class="FarTable table-bordered">
				            <thead>
				                <tr>
				                    <th>长(cm)</th>
				                    <th>宽(cm)</th>
				                    <th>高(cm)</th>
				                    <th>实重(kg)</th>
				                </tr>
				            </thead>
				            <tbody>
				                <tr style="line-height:30px;">
				                    <td><input type="text" style="width:130px;" class="length" value=""></td>
				                    <td><input type="text" style="width:130px;" class="width" value=""></td>
				                    <td><input type="text" style="width:130px;" class="height" value=""></td>
				                    <td><input type="text" style="width:130px;" class="weight" value=""></td>
				                </tr>
				            </tbody>
				        </table>
				    </td>
				</tr>
			</tbody>
		</table>
		<h4 id="msg" style="color:red"></h4>
	</div>
	<div class="span5">
		<table style="width:90%;">
			<tbody>
			    <tr>
					<td class="span3">
					   <h4>已扫描</h4>
					</td>
					<td>
					   <a class="btn btn-small btn-info" href="javascript:void(0)" onclick="save()"
                    		style="margin-right: 10px;">
                    		<i class="icon-remove"></i>
                    		关闭托盘
                    	</a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table class="FarTable">
							<thead>
								<tr>
									<th>运单号</th>
								</tr>
							</thead>
							<tbody id="tracking_code_list">
							<?php foreach (Subcode::find('pallet_no=?',$pallet->pallet_no)->getAll() as $temp):?>
							    <tr><td><span><?php echo $temp->sub_code?></span>&nbsp;&nbsp;&nbsp;<a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removeline(this)"><i class="icon-remove"></i></a></td></tr>
							<?php endforeach;?>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" id="pallet_no" value="<?php echo $pallet->pallet_no?>">
</form>
<script type="text/javascript">
$(function(){
	$('#tracking_code').select();
	$('body').on('keydown', 'input, select', function(e) {
		if (e.keyCode == 13) {
			return enter2tab(this,e);
		}
	});
	$('#tracking_code').bind('keydown', function (e) {
		if(e.keyCode ==13){
			$("#msg").html('');
			//获取上一个扫描的单号
			var last_tracking_code=$("#tracking_code_list").find("tr:last").children().find('span').html();
			$.ajax({
				url:'<?php echo url('/getpackagecount')?>',
				data:{tracking_code:$("#tracking_code").val(),last_tracking_code:last_tracking_code,pallet_no:$("#pallet_no").val(),status:$('.locked').attr("checked")},
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.status=='success'){//运单号不存在
						if(data.count=='1'){//一票一件
							$("#tracking_code_list").append('<tr><td><span>'+$("#tracking_code").val()+'</span>&nbsp;&nbsp;&nbsp;<a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removeline(this)"><i class="icon-remove"></i></a></td></tr>');
							$("#package_info").css("display","none");
							$('.locked').attr("checked",false);
							$("#msg").css("color","green");
							$("#msg").html('录入结果：成功');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');
							$('#tracking_code').val('');
							$('#tracking_code').select();
						}else{//一票多件
							$("#package_info").css("display","");
							//判断录入单号和上一个单号是否属于同一个订单
							if(data.same=='true'){//同一订单
								if($('.locked').attr("checked")=='checked'){
									$("#tracking_code_list").append('<tr><td>'+$("#tracking_code").val()+'</td></tr>');
									$('#tracking_code').val('');
									$('#tracking_code').select();
									$("#msg").css("color","green");
									$("#msg").html('录入结果：成功');
									$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');
								}else{
									//光标移动至长度input
									$("#package_info").find('input').eq(0).select();
								}
							}else{//不同订单
								//取消锁定
								$('.locked').attr("checked",false);
								//光标移动至长度input
								$("#package_info").find('input').eq(0).select();
							}
						}
					}else{
						$("#msg").css("color","red");
						if(data.message=='notexits'){
							$("#msg").html('录入结果：失败，单号不存在');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/danhaobucunzai.mp3');
						}
						if(data.message=='scanned'){
							$("#msg").html('录入结果：失败，单号已扫描');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yisaomiao.mp3');
						}
						if(data.message=='channel_wrong'){
							$("#msg").html('录入结果：失败，渠道不错误');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qudaocuowu.mp3');
						}
						$('#tracking_code').val('');
						$('#tracking_code').select();
					}
				}
			})
		}
	});
	$('.weight').bind('keydown', function (e) {
		if(e.keyCode ==13){
			if($(".length").val()=='' || $(".width").val()=="" || $(".height").val()=="" || $(".weight").val()==""){
				$("#msg").html('录入结果：失败，请填写包裹数据').css("color","red");
				$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yichang.mp3');
				$("#package_info").find('input').eq(0).select();
			}else{
				$.ajax({
					url:'<?php echo url('/savepackageinfo')?>',
					data:{tracking_code:$("#tracking_code").val(),pallet_no:$("#pallet_no").val(),length:$(".length").val(),width:$(".width").val(),height:$(".height").val(),weight:$(".weight").val()},
					type:'post',
					dataType:'json',
					success:function(data){
						if(data.status=='success'){
							$("#msg").css("color","green");
							$("#msg").html('录入结果：成功');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');
							$("#tracking_code_list").append('<tr><td><span>'+$("#tracking_code").val()+'</span>&nbsp;&nbsp;&nbsp;<a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removeline(this)"><i class="icon-remove"></i></a></td></tr>');
							$('#tracking_code').val('');
							$('#tracking_code').select();
						}else{
							$("#msg").css("color","red");
							$("#msg").html('异常：请联系管理员');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yichang.mp3');
							$('#tracking_code').val('');
							$('#tracking_code').select();
						}
					}
				});
			}
		}
	});
})
//关闭托盘
function save(){
	var tracking_codes='';
	if($("#tracking_code_list").find("tr").length>0){
		$("#tracking_code_list").find("tr").each(function(){
			tracking_codes +=$(this).children().find('span').html()+';';
		});
		$.ajax({
			url:'<?php echo url('/savepalletno')?>',
			data:{tracking_codes:tracking_codes,pallet_no:$("#pallet_no").val()},
			type:'post',
			dataType:'json',
			success:function(data){
				//打印标签
				AF.func("Build", "<?php echo $_BASE_DIR?>public/supcan/pallet.xml?v=6");
				AF.func("SetSource", "ds1 \r\n "+'<?php echo url('/printpallet',array('pallet_no'=>$pallet->pallet_no))?>');
				switchPrinter('Zebra');
				AF.func("Calc", ""); //填充数据
				AF.func("CallFunc","");
				AF.func("Print", "isOpenSysDialog=false");
				window.location.href = '<?php echo url('/palletlist')?>';
			}
		});
	}else{
		$.messager.alert('', '空托盘无法关闭');
	}
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
function removeline(obj){
	$(obj).parent().parent().remove();
}
</script>
<?PHP $this->_endblock();?>

