<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
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

.checkin-table-1 #scan_no {
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

#order-package-list tbody td #scan_no {
	width: 80%;
	margin: 0px;
}
.list{
	width:100%;
}
-->
</style>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<?PHP $this->_block('contents');?>
<?php if(!$total->status):?>
<div class="alert alert-info" style="margin-bottom: 10px;">
	<ol style="margin-bottom: 0px;">
		<li>【回车】切换至下一录入框；</li>
		<li>【shift】提交数据。</li>
		<li>勾选‘不校验原单信息’ 当包裹只有一个的时候才生效</li>
	</ol>
</div>
<table class="table table-bordered checkin-table-1" style="margin-bottom: 10px;">
	<tbody>
		<tr>
			<td style="width: 450px;">
				<input type="text" name="scan_no" id="scan_no" value=""
					autofocus="autofocus" placeholder="请录入【单号】并回车" style="margin: 0px;" />&nbsp;
				<label><input type="checkbox" id="type" value="1">不校验原单信息</label>
			</td>
			<td style="width: auto;" id="scan-msg">-</td>
			<th style="width:100px;text-align:center;font-size: 26px;line-height: 26px;">总单号</th>
			<td style="width:240px;text-align:center;font-size: 26px;line-height: 26px;">
			<?php $return_total_no = date('YmdHis');?>
			<?php if(request('return_total_no')){echo request('return_total_no');}else{echo $return_total_no;}?>
			</td>
		</tr>
	</tbody>
</table>
<div style="margin-bottom: 10px;">
	<!-- <button class="btn btn-small btn-primary"
		style="margin-right: 10px; float: left;" id="submit-data" >
		<i class="icon-save"></i>
		提交数据
	</button> -->
	<div style="clear: both;"></div>
</div>
<form id="list_form" method="post">
<input type="hidden" name="return_total_no" value="<?php if(request('return_total_no')){echo request('return_total_no');}else{echo $return_total_no;}?>" />
<input type="hidden" name="return_total_id" value="<?php echo request('return_total_id')?>" />
<div class="row-fluid">
	<div class="span12">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td id="handle-area">
						<table class="FarTable" id="order-package-list">
							<thead>
								<tr>
									<th style="width: 250px;">末端单号</th>
									<th style="width: 110px;">原件数</th>
									<th style="width: 110px;">原重量</th>
									<th style="width: 110px;">退货件数</th>
									<th style="width: 110px;">退货重量</th>
									<th style="width: 110px;">长</th>
									<th style="width: 110px;">宽</th>
									<th style="width: 110px;">高</th>
									<th style="width: 180px;">其他备注</th>
									<th style="width: auto;">操作</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
<?php endif;?>
	<!-- <div class="span4" style="float:right">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>
						<table class="FarTable" id="order-subcode-list">
							<thead>
								<tr>
									<th style="width: 150px;">子单号</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div> -->
	<div class="row-fluid" style="width:100%">
	<div class="span12">
		<table class="table table-bordered">
			<tr>
				<th>全部单号（子单号）<span id="goods_count">（<?php echo count($all_sub_code)?>）</span></th>
				<th>核对成功
				<!-- <input type="submit" class="btn btn-sm btn-info" value="提交"> --><span id="id3_count">（<?php echo count($true_sub_code)?>）</span></th>
				<th>一票多件单号不全<span id="id2_count">（<?php echo count($no_sub_code)?>）</span></th>
			</tr>
			<tr>
				<td><textarea style="width:200px; height:350px" id="goods" readonly><?php foreach($all_sub_code as $v){echo $v.'&#10;';} ?></textarea></td>
				<td><textarea style="width:200px; height:350px" name="sub_code" id="id3" readonly><?php foreach($true_sub_code as $v){echo $v.'&#10;';} ?></textarea></td>
				<td><textarea style="width:200px; height:350px" id="id2" readonly><?php foreach($no_sub_code as $v){echo $v.'&#10;';} ?></textarea></td>
			</tr>
		</table>
	</div>
</div>
</div>
</form>
<?PHP $this->_endblock();?>
<script>
//阿里单号录入回车执行
$('#scan_no').on('keydown', function (e) {
	var scan_no = $.trim($('#scan_no').val())
	var return_total_id = $('input[name="return_total_id"]').val()
	if (e.which == 13 && scan_no.length > 0) {
		$(this).blur();
		$('#order-package-list').find('tbody').html('');
		$('#order-subcode-list').find('tbody').html('');
		$.ajax({
            type: "POST",
            url: "<?php echo url('/inscan')?>",
            data: {
            	scan_no : scan_no,return_total_id:return_total_id
            },
            dataType: "json",
            success: function (json) {
                console.log()
                if(json.code == 0){
					//获取数据失败
					$('#scan-msg').html(json.msg)
                    $('#scan-msg').css('color', '#f00'); // 红
					$('#scan_no').select();
					if(json.msg == '未找到订单'){
						$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowudanhaobucunzai.mp3');
					}else{
						$.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
					}
                }else{
					$('#scan-msg').html('扫描成功,共'+json.outpackage.length+'个包裹')
                    $('#scan-msg').css('color', 'rgb(70, 136, 71)'); // 绿
                	//包裹数据
    				$.each(json.outpackage,function(k,v){
    					var tr_html = '<tr>';
    					tr_html += '<td><input type="hidden" name="tracking_no[]" value="'+v.tracking_no+'" />'+v.tracking_no+'</td>';
    					tr_html += '<td><input type="hidden" name="return_package_id[]" value="'+v.return_package_id+'" />'+v.quantity_out+'</td>';
    					tr_html += '<td><input type="hidden" name="order_id[]" value="'+v.order_id+'" />'+v.weight_out+'</td>';
    					tr_html += '<td><input type="text" style="width:80%;" name="quantity_out[]" value="'+v.quantity_out+'" /></td>';
    					tr_html += '<td><input type="text" style="width:80%;" name="weight_out[]" value="'+v.weight_out+'" /></td>';
    					tr_html += '<td><input type="text" style="width:80%;" name="length_out[]" value="'+v.length_out+'" /></td>';
    					tr_html += '<td><input type="text" style="width:80%;" name="width_out[]" value="'+v.width_out+'" /></td>';
    					tr_html += '<td><input type="text" style="width:80%;" name="height_out[]" value="'+v.height_out+'" /></td>';
    					tr_html += '<td><input type="text" style="width:80%;" name="note[]" value="" /></td>';
    					tr_html += '<td><input type="hidden" name="type[]" value="" /><span onclick="tijiao(this)" class="btn btn-mini btn-success"><i class="icon-save"></i></span></td>';
    					tr_html += '</tr>';
    					$('#order-package-list').find('tbody').append(tr_html);
    				})
//                 	$.each(json.subcode,function(k,v){
//                 		var tr_html2 = '<tr>';
//     					tr_html2 += '<td style="text-align:center">'+v+'</td>';
//     					tr_html2 += '</tr>';
//     					$('#order-subcode-list').find('tbody').append(tr_html2);
//                 	})
                    
    				if($("#type").is(":checked")){
    					//勾选了‘不校验原单信息’ 只有当包裹只有一个的时候才生效
    					if(json.outpackage.length == 1){
        					//这个赋值参数只能当包裹只有一个的时候才可以用
    						$('input[name="type[]"]').parent().parent().find('[name="type[]"]').val(scan_no);
    						submit();
    					}
    				}else{
    					//未勾选‘不校验原单信息’
    					$("input[name='quantity_out[]']").eq(0).select();
    	            	$.sound.play('<?php echo $_BASE_DIR?>public/sound/chenggongqingheduixinxi.mp3');
    				}
                }
                
            }
		})
	}
})
// 操作区键盘操作
$('#handle-area').on('keydown', ':input', function (event) {
    //console.log(event);
    if (!event.ctrlKey && !event.shiftKey && event.which == 13) {
        // enter : 下一个 input (新建行时询问是新建，还是录入数量)
        event.preventDefault();
        keydownEnter($(this), event);
    }
//     if (event.shiftKey && event.which == 13) {
//         // shift + enter : 上一个 input
//         event.preventDefault();
//         keydownShiftEnter($(this), event);
//     }
    if (event.shiftKey && event.ctrlKey) {
		return false
    }
    if (event.shiftKey && event.which == 16) {
        // shift : 提交
        event.preventDefault();
        $(this).parent().parent().find('[name="type[]"]').val($.trim($('#scan_no').val()));
        submit();
    }
});
//处理回车
function keydownEnter($this, event) {
    console.log('keydownEnter');
    switch ($this.attr('name')) {
        case 'quantity_out[]':
	        $this.closest('tr').find('[name="weight_out[]"]').select();
            break;
        case 'weight_out[]':
	        $this.closest('tr').find('[name="length_out[]"]').select();
            break;
        case 'length_out[]':
	        $this.closest('tr').find('[name="width_out[]"]').select();
            break;
        case 'width_out[]':
	        $this.closest('tr').find('[name="height_out[]"]').select();
            break;
        case 'height_out[]':
	        $this.closest('tr').find('[name="note[]"]').select();
            break;
        case 'note[]':
//         	if ($this.closest('tr').next().length > 0) {
//         		// 中间行，切换至下一行
//         		$this.closest('tr').next().find('[name="quantity_out[]"]').select();
//             }else{
//                 //最后一个input的时候停掉
// 				return false
//             }
        	//在备注框按回车直接提交
        	$this.parent().parent().find('[name="type[]"]').val($.trim($('#scan_no').val()));
            submit();
            break;
        default:
            console.log('错误1');
    }
}
//处理 shift + enter
function keydownShiftEnter($this, event) {
    switch ($this.attr('name')) {
        case 'quantity_out[]':
            if ($this.closest('tr').is(':first-child')) {
                // 第一行第一个，什么都不做
                break;
            } else {
                // 中间行第一个，切换至上一行的 height
                $this.closest('tr').prev().find('[name="note[]"]').select();
            }
            break;
        case 'note[]':
        	$this.closest('tr').find('[name="height_out[]"]').select();
            break;
        case 'height_out[]':
            $this.closest('tr').find('[name="width_out[]"]').select();
            break;
        case 'width_out[]':
            $this.closest('tr').find('[name="length_out[]"]').select();
            break;
        case 'length_out[]':
            $this.closest('tr').find('[name="weight_out[]"]').select();
            break;
        case 'weight_out[]':
            $this.closest('tr').find('[name="quantity_out[]"]').select();
            break;
        default:
            console.log('错误2');
    }
}
//提交数据按钮点击事件
// $('#submit-data').on('click',function(){
// 	if(!$('#order-package-list').find('tbody').find('tr').is(':first-child')){
// 		$('#scan-msg').html('请扫描');
//         $('#scan-msg').css('color', '#f00'); // 红
//         $('#scan_no').select();
//        $.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
//         return false;
// 	}
// 	$('#submit-data').attr('disabled',true);
// 	submit();
// });
//提交单条数据
function tijiao(tthis){
	//把扫描的单号赋值给form的隐藏元素，传给后台使用
	$(tthis).parent().find('[name="type[]"]').val($.trim($('#scan_no').val()));
	submit();
};
//提交数据
function submit(){
	$.ajax({
        type: "POST",
        url: "<?php echo url('/returninajax')?>", //+'/subcodes/'+subcodes
        data: $('#list_form').serialize(),
        dataType: "json",
        success: function (json) {
        	$('#submit-data').attr('disabled',false);
        	$('#scan-msg').html(json.msg);
            $('#scan_no').select();
            if(json.code == 0){
                $("input[name='type[]']").val('');
	           	$('#scan-msg').css('color', '#f00'); // 红
	            $.sound.play('<?php echo $_BASE_DIR?>public/sound/cuowu.mp3');
            }else{
                //追加到显示子单号区域
            	$('#goods').val('');
				$('#goods_count').html('（0）');
				$('#id2').val('');
				$('#id2_count').html('（0）');
				$('#id3').val('');
				$('#id3_count').html('（0）');
				$.each(json.sub_code,function(k,v){
					console.log(111)
					$('#id3').val($('#id3').val()+v+'\n')
					$('#id3_count').html('（'+$("#id3").val().trim('\n').split('\n').length+'）');
					$('#goods').val($('#goods').val()+v+'\n')
					$('#goods_count').html('（'+$("#goods").val().trim('\n').split('\n').length+'）');
				})
				$.each(json.no_sub_code,function(k,v){
					console.log(222)
					$('#id2').val($('#id2').val()+v+'\n')
					$('#id2_count').html('（'+$("#id2").val().trim('\n').split('\n').length+'）');
					$('#goods').val($('#goods').val()+v+'\n')
					$('#goods_count').html('（'+$("#goods").val().trim('\n').split('\n').length+'）');
				})


				
                $('#scan-msg').css('color', 'rgb(70, 136, 71)'); // 绿
                $('#order-package-list').find('tbody').empty();
                $('input[name="return_total_id"]').val(json.data.return_total_id);
            	$.sound.play('<?php echo $_BASE_DIR?>public/sound/chenggong.mp3');
            }
        }
    });
}
</script>
