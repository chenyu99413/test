<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
条码打印
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
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
<div class="FarSearch" >
<form action="post">
	<table id="package">
		<tbody>
			<tr>
				<th>阿里订单号</th>
				<td>
					<input name="ali_order_no" required="required" type="text" id="ali_order_no" style="width: 150px" value="">
				</td>
				<th>件数</th>
				<td>
					<input name="quantity" required="required" type="text" id="order_quantity" style="width: 50px" value="">
				</td>
			</tr>
		</tbody>
	</table>
</form>
</div>
<div style="height: 1px; width:1px ">
		<SCRIPT type="text/javascript">insertReport('AF', 'CollapseToolbar=true');</SCRIPT>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript"> 
$(function(){
	document.getElementById("ali_order_no").focus();
	//扫描阿里单号
	$('#ali_order_no').on('keydown', function (e) {
		if (e.keyCode == 13) {
			$.ajax({
				url:'<?php echo url('warehouse/codeprint')?>',
				type:'POST',
				dataType:'json',
				data:{ali_order_no:$("#ali_order_no").val()},
				success:function(data){
					if(data.message=='nooder'){
					   alert('单号不存在');
					}else if(data.message=='nopackage'){
					   alert('无件数');
					}else if(data.message=='success'){
					   $("#order_quantity").val(data.quantity);
					   $("#order_quantity").focus();
					   //打印条码
// 					   print_label(ali_order_no,data.quantity);
					}
				}
			})
		}
	});
})
$(function(){
	$('#order_quantity').on('keydown',function(e){
		if (e.keyCode == 13) {
			$.ajax({
				url:'<?php echo url('warehouse/quantityprint')?>',
				type:'POST',
				dataType:'json',
				data:{ali_order_no:$("#ali_order_no").val(),quantity:$("#order_quantity").val()},
				success:function(data){
					if(data.message=='errorpackage'){
					   alert('件数错误');
					}else if(data.message=='missingmsg'){
					   alert('数据缺失');
					}else if(data.message=='nooder'){
					   alert('单号不存在');
					}else if(data.message=='success'){
					   //打印条码
					   var ali_order_no = $("#ali_order_no").val();
					   var quantity = $("#order_quantity").val();
					   for(var i=1;i<=quantity;i++){
						   AF.func("Build", "<?php echo $_BASE_DIR?>public/supcan/invoice_order.xml?v=1");
						   if(i==1){
							  AF.func("SetSource", "ds1 \r\n "+'<?php echo $_BASE_DIR?>_tmp/upload/'+ali_order_no+'.json');
						   }else{
							  AF.func("SetSource", "ds1 \r\n "+'<?php echo $_BASE_DIR?>_tmp/upload/'+ali_order_no+'-'+i+'.json');
						   }
// 						   switchPrinter('P1606dn');
						   AF.func("Calc", ""); //填充数据
						   AF.func("Print", "isOpenSysDialog=false");
					   }
					}
				}
			})
		}
	});
})
/**
 * 切换打印机
 * 例如 switchPrinter('EMS')，找到打印机名字中带 EMS的打印机并指定
 */
// function switchPrinter(printerName){
// 	var printers=AF.func("GetPrinters").split(',');
// 	//搜索打印机
// 	for (i in printers){
// 		if(typeof(printers[i]) == 'string' ){
// 			if (printers[i].indexOf(printerName)>-1){
// 				printerName=printers[i];
// 			}
// 		}
// 	}
// 	var setting=AF.func("GetProp", "Print");
// 	// console.log(setting);
// 	if (setting.indexOf('<Printer>') > -1){
// 		setting=setting.replace(/<Printer>.*?<\/Printer>/mg,'<Printer>'+printerName+'</Printer>');
// 	}else {
// 		setting=setting.replace('<PrintPage>',"<PrintPage>\r\n<Printer>"+printerName+'</Printer>');
// 	}
// 	// console.log(setting);
// 	AF.func("SetProp", "Print \r\n" + setting);
// }
</script>
