<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
包裹入库
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<style>
<!--
.checkin-table-1 #scan_no_type {
	width: 150px;
	height: 32px;
	margin: 0;
	font-size: 20px;
	line-height: 26px;
	vertical-align: middle;
}

.checkin-table-1 input {
	width: 320px;
	height: 30px;
	font-size: 26px;
	line-height: 26px;
	vertical-align: middle;
}

#scan-msg {
	font-size: 26px;
	line-height: 26px;
	vertical-align: middle;
}

.order-extend-info-label {
	width: 60px;
	margin: 0px;
	text-align: right;
}

#order-package-list tbody td {
	padding: 5px;
	text-align: center;
}

#order-package-list tbody td input {
	width: 80%;
	margin: 0px;
}
-->
</style>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.browser.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>
<script>
function OnReady(id){}
function OnEvent(id, Event, p1, p2, p3, p4){}
</script>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
	<div style="height: 1px; width: 100%; visibility: hidden;">
		<SCRIPT type="text/javascript">insertReport('AF', 'CollapseToolbar=true');</SCRIPT>
	</div>
</div>
<div class="alert alert-info" style="margin-bottom: 10px;">
	<ol style="margin-bottom: 0px;">
		<li>【回车】切换至下一录入框；【 shift + 回车】切换至上一录入框；【 + 】新建行；【 - 】删除当前行。</li>
		<li>【shift】提交数据，并自动打印泛远面单。</li>
		<li>&nbsp;&nbsp;请将输入法切换至英文状态。</li>
	</ol>
</div>
<div id="dialog_save" class="easyui-dialog hide"title="无主件"
		data-options="closed:true, modal:true"
		style="width:430px; height: 270px;">
		<div class="span4">
        <table class="FarTable">
        	  <tr>
                    <th class="required-title">原因</th>
					<td>
					   <textarea id='reson' placeholder="请输入:国内快递名称+发件人+发件人电话+几票几件等信息" rows="5" ></textarea>
					</td>
              </tr>
        </table>
        <table>
        <tr>
		    <td>
		      <button class="btn btn-info" type="submit" id="nomaster" onclick="saveissue()" style="margin-left: 150px">
		            <i class="icon-check"></i>
					保存
				</button>
			</td>
		</tr>
		</table>		
        </div>
</div>
<table class="table table-bordered checkin-table-1" style="margin-bottom: 10px;">
	<tbody>
		<tr>
			<td style="width: 350px;">
				<input type="text" name="scan_no" id="scan_no" value=""
					autofocus="autofocus" placeholder="请录入【单号】并回车" style="margin: 0px;" />
				<input type="hidden" name="order_id" id="order_id" value="" />
				<input type="hidden" id="service_code" value="">
				<input type="hidden" id="order_no" value="">
				<input type="hidden" id="product_type" value="">
				<input type="hidden" id="ratio" value="">
				<input type="hidden" id="consignee_country_code" value="">
			</td>
			<td style="width: auto;" id="scan-msg">-</td>
		</tr>
	</tbody>
</table>
<div style="margin-bottom: 10px;">
	<button class="btn btn-small btn-success" style="float: right;"
		id="print-farlabel" >
		<i class="icon-print"></i>
		打印泛远面单
	</button>
	<button class="btn btn-small btn-info" style="margin-right: 10px; float: left;"
		id="add-line">
		<i class="icon-plus"></i>
		添加行
	</button>
	<button class="btn btn-small btn-primary"
		style="margin-right: 10px; float: left;" id="submit-data">
		<i class="icon-save"></i>
		提交数据
	</button>
	<div style="margin-left:10px; float:left"><h5 style="margin:2px 0px;" id="weight_income_in"></h5></div>
	<div style="margin-left:10px; float:left"><span style="font-size:30px;color:red;" id="product"></span></div>
	<div style="clear: both;"></div>
</div>
<div class="row-fluid">
	<div class="span8">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td id="handle-area">
						<table class="FarTable" id="order-package-list">
							<thead>
								<tr>
									<th style="width: 110px;">数量</th>
									<th style="width: 110px;">重量(kg)</th>
									<th style="width: 110px;">长(cm)</th>
									<th style="width: 110px;">宽(cm)</th>
									<th style="width: 110px;">高(cm)</th>
									<th style="width: 110px;">泡重(kg)</th>
									<th style="width: auto;">操作</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<div id="handle-msg" style="margin: 10px;"></div>
						<div class="row-fluid" id="order-extend-info" style="margin-top: 10px;">
							<div class="span3" style="padding: 3px 0;">
								<label class="order-extend-info-label">包裹袋数量</label>
								<input style="width: 80px;" type="text" name="EX0002" value="0" />
							</div>
							<div class="span3" style="padding: 3px 0; margin-left: 10px;">
								<label class="order-extend-info-label">纸箱数量</label>
								<input style="width: 80px;" type="text" name="EX0003" value="0" />
							</div>
							<div class="span3" style="padding: 3px 0; margin-left: 10px;">
								<label class="order-extend-info-label" style="width:80px">异形包装数量</label>
								<input style="width: 80px;" type="text" name="EX0034" value="0" />
							</div>
							<div class="span3" style="padding: 3px 0; margin-left: 10px;">
								<label class="order-extend-info-label">包裹总数量</label>
								<input style="width: 80px;" type="text" name="far_quantity_total"
									value="0" readonly="readonly" />
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="border:1px solid #DDD;padding:5px;margin-bottom:10px;float:right;display:none;" id="ali_no_list" class="span4"></div>
	<div class="span4" style="float:right">
		<table class="table table-bordered table-hover table-condensed table-striped"
			id="order-info">
			<tbody>
				<tr>
					<th style="width: 35%;">阿里单号</th>
					<td style="width: 65%;" class="ali_order_no"></td>
				</tr>
				<tr>
					<th>泛远单号</th>
					<td class="far_no"></td>
				</tr>
				<tr>
					<th>入库要求</th>
					<td class="product_remark" style="color:red;"></td>
				</tr>
				<tr>
					<th>预报包裹数量</th>
					<td class="ali_quantity_total"></td>
				</tr>
			</tbody>
		</table>
		<table class="FarTable" id="order-package-list-ali">
			<thead>
				<tr>
					<td>数量</td>
					<td>重量</td>
					<td>长</td>
					<td>宽</td>
					<td>高</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<script>
var ali_quantity_total = 0;
var far_quantity_total = 0;
var verify_status=true;
var verify_reason='';
var packagelist_tr_html = '<tr>';
packagelist_tr_html += '<td><input type="text" name="quantity[]" value="1" /></td>';
packagelist_tr_html += '<td><input type="text" name="weight[]" value="0" /></td>';
packagelist_tr_html += '<td><input type="text" name="length[]" value="22"  /></td>';
packagelist_tr_html += '<td><input type="text" name="width[]" value="22" /></td>';
packagelist_tr_html += '<td><input type="text" name="height[]" value="2.22"  /></td>';
packagelist_tr_html += '<td><input type="text" name="jipao[]" value="0" readonly="readonly" /></td>';
packagelist_tr_html += '<td><button class="btn btn-mini btn-danger remove-line"><i class="icon-remove"></i></button></td>';
packagelist_tr_html += '</tr>';

// 循环显示阿里包裹数据
function orderPackageListAli(json){
	ali_quantity_total = 0;
	$('#order-package-list-ali').find('tbody').html('');
	$.each(json, function (k, v) {
		var tr_html = '<tr>';
		tr_html += '<td>'+v.quantity+'</td>';
		tr_html += '<td>'+v.weight+'</td>';
		tr_html += '<td>'+v.length+'</td>';
		tr_html += '<td>'+v.width+'</td>';
		tr_html += '<td>'+v.height+'</td>';
		tr_html += '</tr>';
		$('#order-package-list-ali').find('tbody').append(tr_html);
		ali_quantity_total += v.quantity;
    });
	$('#order-info').find('.ali_quantity_total').html(ali_quantity_total);
}
// 循环显示泛远包裹数据
function orderPackageListFar(json){
	// 初始化表格
	$('#order-package-list').find('tbody').find('tr').remove();// 清空行
	$('#handle-msg').html('');
	$('#order-extend-info').find(':input').val('0');
	addLine();// 添加行
	farQty();
}
// 求和
function farQty(){
	far_quantity_total = 0;
    $('#order-package-list').find('[name="quantity[]"]').each(function(){
        far_quantity_total += Math.round($(this).val().trim());
    });
    $('#order-extend-info').find('[name="far_quantity_total"]').val(far_quantity_total);
}
function saveissue(){
	$('#nomaster').attr("disabled",true);
	var scan_no = $('#scan_no').val();
	var reson = $('#reson').val().trim();
	if(reson.length==0){
		alert('原因不能为空');
    	$('#nomaster').removeAttr('disabled');
	}else{
// 		alert(ali_order_no+'/'+reson);
		$.ajax({
            type: "POST",
            url: "<?php echo url('warehouse/saveissue')?>",
            data: {scan_no : scan_no,reson : reson},
            success: function (data) {
                if(data=='success'){
                    alert('无主件已保存');
                	$('#nomaster').removeAttr('disabled');
                   window.location.reload();
                }else if(data=='f'){
                   alert('保存失败请检查');
                }
            }
        });
	}
}
$(function () {
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
    // 阿里单号录入回车
    $('#scan_no').on('keydown', function (e) {
    	$("#ali_no_list").css('display','none');
        $("#ali_no_list").next().css('clear','none');
        if (e.which == 13) {
            e.preventDefault();
            var scan_no = $(this).val().trim();
            $('#product').html('');
            $.ajax({
                type: "POST",
                url: "<?php echo url('/inscan')?>",
                data: {
                	scan_no : scan_no
                },
                dataType: "json",
                success: function (json) {
                    console.log(json);
                    $('#scan-msg').html(json.msg);
                    $.sound.play('<?php echo $_BASE_DIR?>public/sound/' + json.sound);
                    if (json.status) {
                        $('#scan-msg').css('color', 'rgb(70, 136, 71)'); // 绿
                        $('#order-info').find('.ali_order_no').html(json.data.order.ali_order_no);
                        $('#order-info').find('.far_no').html(json.data.order.far_no);
                        $('#product').html('产品:'+json.data.product.product_chinese_name+' '+json.data.product.product_name);
                        $('#order-info').find('.product_remark').html(json.data.product.remark);
						//产品类型
                        $('#product_type').val(json.data.product.type);
                        $("#service_code").val(json.data.order.service_code);
                        $("#order_no").val(json.data.order.order_no);
                        $("input[name='EX0034']").attr('readonly','readonly');
                        $("input[name='EX0002']").attr('readonly','readonly');
                        $("input[name='EX0003']").attr('readonly','readonly');
                        if($("#service_code").val()=='EMS-FY'){
                            $("input[name='EX0034']").attr('readonly','readonly');
                        }else{
                        	$.each(json.data.formula, function (k, v) {
	                       		 if(v=='异形包装费'){
		                       		$("input[name='EX0034']").removeAttr('readonly'); 
	                       		 }
	                       		if(v=='包装-包裹袋'){
		                       		$("input[name='EX0002']").removeAttr('readonly'); 
	                       		 }
	                       		if(v=='包装-纸箱'){
		                       		$("input[name='EX0003']").removeAttr('readonly');
	                       		 }
                            });
                        }
                        
                        $("#ratio").val(json.data.product.ratio);
                        $("#consignee_country_code").val(json.data.order.consignee_country_code);
                        $('#order_id').val(json.data.order.order_id);
                        orderPackageListAli(json.data.order_package);
                        orderPackageListFar(json.data.far_package);
                        //入库时间校验
                        checkcreattime(json.data.order.create_time);
                    } else {
                        $('#order-info').find('td').html(''); // 清空阿里订单信息
                        $('#order-package-list-ali').find('tbody').html(''); // 清空阿里包裹信息
                        $('#scan-msg').css('color', 'rgb(185, 74, 72)'); // 红
                        $('#scan_no').select();
                        if(json.msg=='单号错误，或包裹数据不存在'){
                        	$('#dialog_save').dialog('open');
                        	$('.window-shadow').css('top','106px');
                        	$('.panel').css('top','106px');
                        	$('#dialog_save').removeClass('hide');
                        }
                        //显示快递单号中阿里订单信息
                        if(json.msg=='请拆包'){
                            var str='<h4>总计：'+json.sum.order_count+'票'+json.sum.package_count+'件</h4>';
                            $.each(json.info,function(i,v){
                            	str+='<p>'+v.ali_order_no+'&nbsp;&nbsp;&nbsp;&nbsp;1票'+v.package_count+'件</p>';
                            })
                            $("#ali_no_list").css('display','block').html(str);
                            $("#ali_no_list").next().css('clear','both');
                        }
                    }
                }
            });
        }
    });

    // 打印
    $('#print-farlabel').on('click',function(){
        var order_no = $("#order_no").val();
        console.log(order_no)
		// 操作下一条
		$('#scan_no').select();
		var label_file_name=order_no+"_label.pdf";
		//发票是否存在pdf文件
		var label_pdfexist = pdfisexist(label_file_name);
		farlabelqzprint('Inlabel',label_pdfexist.url,'farlabel');
    });

    // 录入过程处理
    $('#handle-area').on('blur change', '[name="weight[]"],[name="length[]"],[name="width[]"],[name="height[]"]', function (event) {
    	// 2位小数
    	var str = $(this).val().trim();
    	if(str.substring(0,1)=='0' && str.substring(0,2)!='0.'){
        	return false;
    	}
    	if (!isNaN(parseFloat(str))) {
        	if($("#product_type").val() == '5'){
         	   $(this).val(Math.round(parseFloat(str) * 1000) / 1000);
            }else{
                $(this).val(Math.round(parseFloat(str) * 100) / 100);
            }
    	} else {
    		$(this).val('0');
    	}
    });
    $('#handle-area').on('blur change', '[name="quantity[]"],[name=EX0002],[name=EX0003],[name=EX0034]', function (event) {
    	// 整数
    	var str = $(this).val().trim();
    	if (!isNaN(parseFloat(str))) {
    		$(this).val(Math.round(parseFloat(str)));
    	} else {
    		$(this).val('0');
    	}
    });
    $('#handle-area').on('blur change', '[name="quantity[]"]', function (event) {
    	farQty();
    });
    $('#handle-area').on('blur change', '[name="length[]"],[name="width[]"],[name="height[]"]', function (event) {
        jipao($(this), event);
    });

    // 操作区键盘操作
    $('#handle-area').on('keydown', ':input', function (event) {
        console.log(event.which);
        if (!event.ctrlKey && !event.shiftKey && event.which == 13) {
            // enter : 下一个 input (新建行时询问是新建，还是录入数量)
            event.preventDefault();
            keydownEnter($(this), event);
        }
        if (event.shiftKey && event.which == 13) {
            // shift + enter : 上一个 input
            event.preventDefault();
            keydownShiftEnter($(this), event);
        }
        if (event.shiftKey && event.which == 16) {
            // shift : 提交
            event.preventDefault();
            keydownShift();
            if(verify_status == true){
                if($('#submit-data').prop('disabled')){
                	alert('数据处理中，请不要重复提交');
                } else {
                	submitData();
                }
            }
        }
        if (event.which == 107 || event.which == 78) {
            // + : 新建行
            event.preventDefault();
            addLine();
        }
        if (event.which == 109 || event.which == 68) {
            // - : 删除行，上一行的第一个 input 获取焦点
            event.preventDefault();
            removeLine($(this),event);
        }
    });
    // 点击移除行的按钮
    $('#order-package-list').on('click','.remove-line',function(event){
    	console.log('remove-line');
        removeLine($(this),event);
    });
    // 添加行按钮
    $('#add-line').on('click',function(){
    	console.log('add-line');
        addLine();
    });
    // 提交数据
    $('#submit-data').on('click',function(){
    	if($('#submit-data').prop('disabled')){
            alert('数据处理中，请不要重复提交');
        } else {
        	submitData();
        }
    });
});
// 计算计泡
function jipao($this, event) {
    var length = Math.round(parseFloat($this.closest('tr').find('[name="length[]"]').val().trim()) * 100);
    var width = Math.round(parseFloat($this.closest('tr').find('[name="width[]"]').val().trim()) * 100);
    var height = Math.round(parseFloat($this.closest('tr').find('[name="height[]"]').val().trim()) * 100);
    //定义长宽高数组
    var verify_array=[length,width,height];
    verify_array=verify_array.sort(sortNumber);
    //获取最大边长度
    var verify_max=verify_array[2];
    //
    if(Math.ceil(verify_max/100)<=60 && $("#product_type").val()=="4" || $("#product_type").val()=="5"){
    	$this.closest('tr').find('[name="jipao[]"]').val('');
    }else{
        // 计泡 : 长 x 宽 x 高 / 计泡系数,如果第三位有小数，且不为0时，第二位小数向上进位（不是四舍五入）
        var jipao =Math.ceil((Math.ceil(length/100) * Math.ceil(width/100) * Math.ceil(height/100)) / $("#ratio").val()*100)/100;
        $this.closest('tr').find('[name="jipao[]"]').val(jipao);
    }
}
// 处理回车
function keydownEnter($this, event) {
    console.log('keydownEnter');
    switch ($this.attr('name')) {
        case 'quantity[]':
	        $this.closest('tr').find('[name="weight[]"]').select();
            break;
        case 'weight[]':
            $this.closest('tr').find('[name="length[]"]').select();
            break;
        case 'length[]':
            $this.closest('tr').find('[name="width[]"]').select();
            break;
        case 'width[]':
            $this.closest('tr').find('[name="height[]"]').select();
            break;
        case 'height[]':
            var verify_weight=$this.parent().parent().find('input').eq(1).val();
            var verify_length=$this.parent().parent().find('input').eq(2).val();
            var verify_width=$this.parent().parent().find('input').eq(3).val();
            var verify_height=$this.parent().parent().find('input').eq(4).val();
        	//定义长宽高数组
            var verify_array=[verify_length,verify_width,verify_height];
            verify_array=verify_array.sort(sortNumber);
            if ($this.closest('tr').is(':last-child')) {
                // 最后一行的 height 中回车，对比数量
                farQty();
            	if(far_quantity_total < ali_quantity_total){
                	addLine();
                } else if (far_quantity_total > ali_quantity_total) {
                	$('#handle-msg').html('包裹总数超出预报包裹总数').css('color', 'rgb(185, 74, 72)'); // 红
                	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
                	$('#order-package-list').find('tbody').find('tr').filter(':last').find(':input').eq(0).select();
                } else {
                	$('#handle-msg').html('包裹数量通过验证').css('color', 'rgb(70, 136, 71)'); // 绿
                	$('#order-extend-info').find('[name=EX0002]').select();
                }
            } else {
                // 中间行的 height 中回车，切换至下一行的 weight
                $this.closest('tr').next().find(':input').eq(0).select();
            }
            break;
        case 'EX0002':
        	$('#order-extend-info').find('[name=EX0003]').select();
            break;
        case 'EX0003':
            $('#order-extend-info').find('[name=EX0034]').select();
            break;
        case 'EX0034':
            $('#submit-data').click();
            break;
        default:
            console.log('错误1');
    }
}
//处理Shift
function keydownShift() {
	verify_status=true;
	$('#order-package-list').find('tbody').find('tr').each(function (i, e) {
    	var verify_weight = $(this).find('[name="weight[]"]').val().trim();
        var length = $(this).find('[name="length[]"]').val().trim();
        var width = $(this).find('[name="width[]"]').val().trim();
        var height = $(this).find('[name="height[]"]').val().trim();
        //定义长宽高数组
        var verify_array=[length,width,height];
        verify_array=verify_array.sort(sortNumber);
        //获取最大边长度
        var verify_max=verify_array[2];
        $(this).find('[name="quantity[]"]').select();
    });
}
function sortNumber(a,b){
	return a - b
}
// add line
function addLine() {
    $('#order-package-list').find('tbody').append(packagelist_tr_html);
    $('#order-package-list').find('tbody').find('tr').filter(':last').find(':input').eq(0).select();
}
// remove line
function removeLine($this, event){
	if ($this.closest('tr').is(':first-child')) {
        // 第一行：如果大于一行，则下一行第一个 input 获取焦点；如果只有一行，无动作；
        if ($('#order-package-list').find('tbody').find('tr').length > 1) {
            $this.closest('tr').next().find(':input').eq(0).select();
            $this.closest('tr').remove();
        }
    } else {
        // 非第一行：上一行，第一个选中
        $this.closest('tr').prev().find(':input').eq(0).select();
        $this.closest('tr').remove();
    }
}
//处理 shift + enter
function keydownShiftEnter($this, event) {
    console.log('keydownShiftEnter');
    switch ($this.attr('name')) {
        case 'quantity[]':
            if ($this.closest('tr').is(':first-child')) {
                // 第一行第一个，什么都不做
                break;
            } else {
                // 中间行第一个，切换至上一行的 height
                $this.closest('tr').prev().find('[name="height[]"]').select();
            }
            break;
        case 'weight[]':
        	$this.closest('tr').find('[name="quantity[]"]').select();
            break;
        case 'length[]':
            $this.closest('tr').find('[name="weight[]"]').select();
            break;
        case 'width[]':
            $this.closest('tr').find('[name="length[]"]').select();
            break;
        case 'height[]':
            $this.closest('tr').find('[name="width[]"]').select();
            break;
        case 'EX0002':
        	$('#order-package-list').find('tbody').find('tr').filter(':last').find('[name="height[]"]').select();
            break;
        case 'EX0003':
            $('#order-extend-info').find('[name=EX0002]').select();
            break;
        case 'EX0034':
            $('#order-extend-info').find('[name=EX0003]').select();
            break;
        default:
            console.log('错误2');
    }
}
function submitData() {
	var far_quantity_total = 0;
	var ii=0;
	var jj=0;
	var zz=0;
	$('#order-package-list').find('tbody').find('tr').each(function (i, e) {
		if($(this).find('[name="quantity[]"]').val()==0){
			ii++;
		}
		if($(this).find('[name="weight[]"]').val()==0){
			jj++;
		}
		if($(this).find('[name="weight[]"]').val().substring(0,1)=='0' && $(this).find('[name="weight[]"]').val().substring(0,2)!='0.'){
			zz++;
		}
		if($(this).find('[name="length[]"]').val().substring(0,1)=='0' && $(this).find('[name="length[]"]').val().substring(0,2)!='0.'){
			zz++;
		}
		if($(this).find('[name="width[]"]').val().substring(0,1)=='0' && $(this).find('[name="width[]"]').val().substring(0,2)!='0.'){
			zz++;
		}
		if($(this).find('[name="height[]"]').val().substring(0,1)=='0' && $(this).find('[name="height[]"]').val().substring(0,2)!='0.'){
			zz++;
		}
		far_quantity_total += $(this).find('[name="quantity[]"]').val()*1;
    });
    if(ii>0){
    	$('#handle-msg').html('包裹数量不能为0').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
    }
    if(jj>0){
    	$('#handle-msg').html('包裹重量不能为0').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
    }
    if(zz>0){
    	$('#handle-msg').html('请检查数据').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
    }
	if($('#order-extend-info').find('[name="EX0002"]').val().trim() > ali_quantity_total){
		$('#order-extend-info').find('[name="EX0002"]').select();
		$('#handle-msg').html('包裹袋数量超出预报包裹总数').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	return false;
	}
	if($('#order-extend-info').find('[name="EX0003"]').val().trim() > ali_quantity_total){
		$('#order-extend-info').find('[name="EX0003"]').select();
		$('#handle-msg').html('纸箱数量超出预报包裹总数').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	return false;
	}
	if($('#order-extend-info').find('[name="EX0034"]').val().trim() > ali_quantity_total){
		$('#order-extend-info').find('[name="EX0034"]').select();
		$('#handle-msg').html('异形数量超出预报包裹总数').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	return false;
	}
	if (far_quantity_total > ali_quantity_total) {
    	$('#handle-msg').html('包裹总数超出预报包裹总数').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
    }
	if (far_quantity_total < ali_quantity_total) {
    	$('#handle-msg').html('包裹总数小于预报包裹总数').css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
    }
	if (verify_status==false) {
    	$('#handle-msg').html(verify_reason).css('color', 'rgb(185, 74, 72)'); // 红
    	$.sound.play('<?php echo $_BASE_DIR?>public/sound/butigongfuwu.mp3');
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
    }
	//纸箱和包裹袋数量校验
	var pak = Number($('input[name=EX0002]').val());
	var box_quantity = Number($('input[name=EX0003]').val());
	var total = pak+box_quantity;
	var quantity = Number($('input[name=far_quantity_total]').val());
	if(total > quantity){
		$('#handle-msg').html('包裹袋和纸箱的数量之和不能超出包裹总数量').css('color', 'rgb(185, 74, 72)'); // 红
    	$('#order-package-list').find('tbody').find('tr').filter(':first').find(':input').eq(0).select();
    	return false;
	}
	$('#handle-msg').html('数据验证完成').css('color', 'rgb(70, 136, 71)'); // 绿
	$('#submit-data').prop('disabled',true).html('处理中。。。');
	
    var json_str = '{';
    json_str += '"order_id":"' + $('#order_id').val().trim() + '",'
    json_str += '"EX0002":"' + $('#order-extend-info').find('[name="EX0002"]').val().trim() + '",'
    json_str += '"EX0003":"' + $('#order-extend-info').find('[name="EX0003"]').val().trim() + '",'
    json_str += '"EX0034":"' + $('#order-extend-info').find('[name="EX0034"]').val().trim() + '",'
    json_str += '"package_list":{';
    $('#order-package-list').find('tbody').find('tr').each(function (i, e) {
    	if (i != 0) {
            json_str += ',';
        }
        json_str += '"package' + i + '":'
        json_str += '{"weight":"' + $(this).find('[name="weight[]"]').val().trim() + '"';
        json_str += ',"length":"' + $(this).find('[name="length[]"]').val().trim() + '"';
        json_str += ',"width":"' + $(this).find('[name="width[]"]').val().trim() + '"';
        json_str += ',"height":"' + $(this).find('[name="height[]"]').val().trim() + '"';
        json_str += ',"jipao":"' + $(this).find('[name="jipao[]"]').val().trim() + '"';
        json_str += ',"quantity":"' + $(this).find('[name="quantity[]"]').val().trim() + '"';
        json_str += '}';
    });
    json_str += '}}';
    console.log(json_str);
    var scan_no = $('#scan_no').val();
    $.ajax({
        type: "POST",
        url: "<?php echo url('/insave')?>",
        data: {
            jsonstr: json_str,scan_no: scan_no
        },
        dataType: "json",
        success: function (json) {
        	$('#submit-data').prop('disabled',false).html('<i class="icon-save"></i> 提交数据');
        	$('#scan-msg').html(json.msg);
        	//总计费重显示在页面上
        	$("#weight_income_in").html('总计费重：'+json.weight_income_in);
            $.sound.play('<?php echo $_BASE_DIR?>public/sound/' + json.sound);
            if(json.status){
            	$('#scan-msg').css('color', 'rgb(70, 136, 71)'); // 绿
            	$('#print-farlabel').click();
            } else {
            	$('#scan-msg').css('color', 'rgb(185, 74, 72)'); // 红
            }
            $('#scan_no').select();
        }
    });
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
// 打印顺序号
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
//判断入库时间是否大于一个月
function checkcreattime(time){
	var timestamp = Date.parse(new Date())/1000-30*24*60*60;
	if(!isNaN(time) && time<timestamp){
		setTimeout(function(){
			var con="入库超时,是否继续操作？";
			if($("#service_code").val()=="EMS-FY"){
			    con="EMS入库超时,是否继续操作？";
			}
			if(!confirm(con)){
				$("#scan_no").select();
			}
			if($("#service_code").val()=="EMS-FY"){
			   $.sound.play('<?php echo $_BASE_DIR?>public/sound/emsrukuchaoshi.mp3');
			}else{
				$.sound.play('<?php echo $_BASE_DIR?>public/sound/rukuchaoshi.mp3');
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
		encoding: null,
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

