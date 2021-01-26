<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
  单票核查
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript" src="<?php echo $_BASE_DIR;?>public/js/jquery.sound.js"></script>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div class="service_product" style="width:100%;text-align:center; ">
    <span id="service_product" style="font-size:30px;"></span>
</div>
<div class="FarSearch" >
	<table>
		<tr>
			<th>阿里订单号</th>
			<td>
				<input name="ali_order_no" type="text" id="ali_order_no"  style="width: 200px" value=""><span id="explain" style="margin-left:10px;"></span>
			</td>
		</tr>
	</table>
</div>
<div id="dialog_hold" class="easyui-dialog" title="扣件" data-options="closed:true, modal:true" style="width: 350px; height: 230px;">
		<table style="margin-top: 5px; margin-bottom: 5px; width: 100%;">
			<tbody>
			    <tr>
			        <th style="text-align: right;">类型</th>
					<td style="padding: 1px 10px 2px;">
					   <?php
                            echo Q::control ( 'dropdownlist', 'reason_type', array (
                            'items'=>array(''=>'','涉电/磁/液/粉类问题'=>'涉电/磁/液/粉类问题',
                                '无FDA/税号/报关资料'=>'无FDA/税号/报关资料','涉牌/知识产权问题'=>'涉牌/知识产权问题',
                                '无报关服务'=>'无报关服务','超400美金/EMS无服务'=>'超400美金/EMS无服务',
                                '超800美金/中美无服务'=>'超800美金/中美无服务','邮编/城市/国家无服务'=>'邮编/城市/国家无服务',
                                '产品规格不支持'=>'产品规格不支持','黑名单'=>'黑名单','其他'=>'其他'
                            ),
                            'value' => request('reason_type')
                         ) )?>
					</td>
			    </tr>
				<tr>
					<th style="text-align: right;">原因</th>
					<td style="padding: 1px 10px 2px;">
						<textarea style="width: 80%; height: 100px;"  name="reason" id="reason"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<button class="btn btn-info" type="submit" onclick="savehold()" id="savehold">
							<i class="icon-ok"></i>
							保存
						</button>
					</td>
				</tr>
			</tbody>
		</table>
</div>
<div id="dialog_has_battery" class="easyui-dialog" title="订单是否带电" data-options="closed:true, modal:true" style="width: 350px; height: 260px;">
		<table style="margin-top: 5px; margin-bottom: 5px; width: 100%;">
			<tbody>
				<tr>
					<th id="batt">是否支持带电——内件是否带电 </th>
                	<td>
                		<?php echo Q::control('RadioGroup','has_battery',array(
                		    'items'=>array(1=>'是',2=>'否'),
                			'value'=>request('has_battery')?request('has_battery'):2
                		))?>
                	</td>
			    </tr>
			    <tr id="battnum" style="display: none">
					<th id="battnum-title">带电产品数量</th>
                	<td>
                		<?php echo Q::control('RadioGroup','has_battery_num',array(
                		    'items'=>array(1=>'不超过2个',2=>'2个以上'),
                			'value'=>request('has_battery_num')
                		))?>
                	</td>
			    </tr>
			    <tr class="is_pda is_pda_a" style="display:none">
					<th style="color:red">是否FDA品类</th>
                	<td>
                		<?php echo Q::control('RadioGroup','is_pda',array(
                		    'items'=>array(1=>'是',0=>'否'),
                			'value'=>request('is_pda')?request('is_pda'):0
                		))?>
                	</td>
			    </tr>
			    <!-- <tr class="is_pda" id="fda_company" style="display:none">
					<th id="pda1">FDA公司名</th>
                	<td>
                		<input type="text" name="fda_company" value="<?php echo request('fda_company')?>" />
                	</td>
			    </tr>
			    <tr class="is_pda" id="fda_address" style="display:none">
					<th id="pda2">FDA城市</th>
                	<td>
                		<input type="text" name="fda_address" value="<?php echo request('fda_address')?>" />
                	</td>
			    </tr>
			    <tr class="is_pda" id="fda_city" style="display:none">
					<th id="pda3">FDA公司名</th>
                	<td>
                		<input type="text" name="fda_city" value="<?php echo request('fda_city')?>" />
                	</td>
			    </tr>
			    <tr class="is_pda" id="fda_post_code" style="display:none">
					<th id="pda4">FDA邮编</th>
                	<td>
                		<input type="text" name="fda_post_code" value="<?php echo request('fda_post_code')?>" />
                	</td>
			    </tr> -->
				<tr>
					<td colspan="2" style="text-align: center;">
						<button class="btn btn-info" type="submit" onclick="return savehasbattery()" id="entersavebattery">
							<i class="icon-ok"></i>
							保存
						</button>
					</td>
				</tr>
			</tbody>
		</table>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	$(function(){
		document.getElementById("ali_order_no").focus();
		//扫描阿里单号
		$('#ali_order_no').on('keydown', function (e) {
			if (e.keyCode == 13) {
				$("#ali_order_no").blur();
				$("#explain").html('');
				$(".product_block").remove();
				$.ajax({
					url:'<?php echo url('warehouse/confirmone')?>',
					type:'POST',
					dataType:'json',
					data:{ali_order_no:$("#ali_order_no").val()},
					success:function(data){
						console.log(data)
						if(data==null){
							$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
	    					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
	    					$("#ali_order_no").select();
	    					return false;
						}
						
						if(data.message=='notexists'){
							$("#ali_order_no").select();
							$("#explain").html('单号不存在').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/danhaobucuzai.mp3');//异常
							return false;
						}
						if(data.message=='cuufalse'){
							$("#ali_order_no").select();
							$("#explain").html('请检查币种是否存在或到期').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bizhongshezhiyichang.mp3');//异常
							return false;
						}
	        			
	    				if(data.message=='formulaerror'){
	    					$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
	    					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
	    					$("#ali_order_no").select();
	    					return false;
	    				}
						$("#service_product").html('产品：'+data.service_product.product_chinese_name+'  '+data.service_product.product_name).css('color','red');
                        if(data.service_product.confirm_remark==null){
                        	data.service_product.confirm_remark='';
                        }
						var tr='<div class="product_block"><span style="color:red;font-size:25px;">目的国：'+data.destination+' '+data.country
						      +'</span><span style="color:red;font-size:25px;margin-left:20px;">总计费重：'+data.weight_income_in
					          +'</span><span style="color:red;font-size:25px;margin-left:20px;">申报总价：'+data.amount
					          +'</span><span style="color:red;font-size:25px;margin-left:20px;">报关：'+data.is_declaration
					          +'</span><span style="color:red;font-size:25px;margin-left:20px;">强制报关：'+data.must_declaration
					          +'</span><div style="width:600px;float:right;margin-top:10px;"><span style="color:red;font-size:20px;">核查要点：'+data.service_product.confirm_remark
					          +'</span></div>';
						if(data.message=='confirmed'){
							$("#ali_order_no").select();
							$("#explain").html('失败，已核查').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yihecha.mp3');//已核查
						}else if(data.message=='repeat'){
							$("#ali_order_no").select();
							$("#explain").html('该订单已有同类核查异常类型，请查询问题件！').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/tongleihechayichang.mp3');//同类核查异常
					    }else if(data.message=='issued'){
							$("#ali_order_no").select();
							$("#explain").html('失败，已扣件').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yikoujianqinglianxikefuzhongxinchuli.mp3');//已扣件
						}else if(data.message=='notsamewarehouse'){
							$("#ali_order_no").select();
							$("#explain").html('失败，不是本仓包裹').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/bushibencangbaoguo.mp3');//不是本仓包裹
						}else if(data.message=='error'){
							$("#ali_order_no").select();
							$("#explain").html('失败').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/yichang.mp3');//异常
						}else if(data.message=='fedexerror'){
							$("#ali_order_no").select();
							$("#explain").html('失败，全球假发专线地址字符总数不能超过70个').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/jiafazhuanxiandizhi1zongchangchao70.mp3');//全球假发专线地址1加地址2字符总数超过70的订单
						}else if(data.message=='yisipianyuan'){
							$("#ali_order_no").select();
							$("#explain").html('省州疑似偏远，请联系客服处理').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/shengzhouyisipianyuanqinglianxikefuchuli.mp3');//疑似偏远
						}else if(data.message=='hasissue'){
							$("#ali_order_no").select();
							$("#explain").html('有问题件未处理').css('color','red');
							$.sound.play('<?php echo $_BASE_DIR;?>public/sound/youwentijianweichuli.mp3');//港前异常件
						}else{
// 							if(data.success_message=='mandatory_declaration'){
// 								$("#explain").html('强制报关件,需扣件').css('color','red');
// 								$.sound.play('<--?php echo $_BASE_DIR;?>public/sound/qiangzhibaoguanjian.mp3');//强制报关件,需扣件
// 							}
							if(data.success_message=='declaration'){
								$("#explain").html('报关件，请审核报关资料').css('color','red');
								$.sound.play('<?php echo $_BASE_DIR;?>public/sound/baoguanjianqingshenhebaoguanziliao.mp3');//报关件，请审核报关资料
							}
							if(data.success_message=='checkfda'){
								$("#explain").html('美国 眼镜，核查FDA证书').css('color','red');
								$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qingshenhefdazhengshu.mp3');//美国 眼镜，核查FDA证书
							}
							if(data.success_message=='checkfda1'){
								$("#explain").html('美国 睫毛，核查FDA证书').css('color','red');
								$.sound.play('<?php echo $_BASE_DIR;?>public/sound/qingshenhefdazhengshu.mp3');//美国 眼镜，核查FDA证书
							}
							if(data.success_message=='taxno'){
								$("#explain").html('巴西，核查是否有税号').css('color','red');
								$.sound.play('<?php echo $_BASE_DIR;?>public/sound/jianchashuihao.mp3');//巴西，核查是否有税号
							}
							if(data.service_product.check_has_battery=='1'){
								$("#ali_order_no").blur();
								$("#dialog_has_battery").dialog("open");
								$("#has_battery_"+data.has_battery).prop("checked",true);
								if(data.has_battery=='1'){
									$("#batt").css('color','red');
									$('#battnum').removeAttr('style');
									$("#battnum-title").css('color','red');
									$("input[name='has_battery_num'][value="+data.has_battery_num+"]").prop("checked",true);
								}else{
									$("#batt").css('color','');
									$("#battnum-title").css('color','');
									$('#battnum').attr('style','display:none');
								}
								//回车可保存 是否带电
								$("#entersavebattery").focus();
							}
							if(data.service_product.is_pda=='1'){
								$(".is_pda_a").removeAttr('style');
								console.log(data)
								if(data.is_pda=='1'){
									$(".is_pda").removeAttr('style');
// 									$("#pda1").css('color','red');
// 									$("#pda2").css('color','red');
// 									$("#pda3").css('color','red');
// 									$("#pda4").css('color','red');
									$("input[name='is_pda'][value='1']").prop("checked",true);
// 									$("input[name='fda_company']").val(data.fda_company);
// 									$("input[name='fda_address']").val(data.fda_address);
// 									$("input[name='fda_city']").val(data.fda_city);
// 									$("input[name='fda_post_code']").val(data.fda_post_code);
								}else{
									$("input[name='is_pda'][value='0']").prop("checked",true);
								}
							}else{
								$('.is_pda_a').attr('style','display:none');
								if(data.is_pda=='1'){
									$("input[name='is_pda'][value='1']").prop("checked",true);
								}else{
									$("input[name='is_pda'][value='0']").prop("checked",true);
								}
							}
						  tr+='<table class="FarTable" style="width:500px;"><thead><tr><th>中文品名</th><th>英文文品名</th><th>商品数量</th><th>申报单价</th></tr></thead><tbody id="product_body">';
						  $.each(data.product, function(i, item){  
							 tr+="<tr><td>"+item.product_name+"</td><td>"+item.product_name_en+"</td><td>"+item.product_quantity+"</td><td>"+item.declaration_price+"</td></tr>";     
						  }); 
							tr+='</tbody></table><div><a class="btn btn-small btn-success" href="javascript:void(0);" style="margin-right:5px;" onclick="save()" id="confirm">核查成功</a>  <a class="btn btn-small btn-danger" href="javascript:void(0);" style="margin-right:5px;" onclick="cancel()" id="cancel">取消</a><a class="btn btn-small btn-warning" href="javascript:void(0);" onclick="hold()" id="hold">扣件</a></div></div>';
						}
					    $(".FarSearch").after(tr);
					    if(data.service_product.check_has_battery!='1'){
					    	$("#confirm").focus();
					    }
					},
					error: function (XMLHttpRequest, textStatus, errorThrown)
			        {
						$("#explain").html('存在生效费用项无法计算，请联系系统管理员。').css('color','red');
    					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/formulaerror.mp3');//存在生效费用项无法计算，请联系系统管理员
    					$("#ali_order_no").select();
    					return false;
			        }
				});
			}
		});
	});
	function save(){
		$("#confirm").blur();
		if($('#confirm').prop('disabled')){
            alert('数据处理中，请不要重复提交');
        } else {
        	$('#confirm').prop('disabled',true).html('处理中。。。');
    		$.ajax({
    			url:'<?php echo url('warehouse/saveconfirmone')?>',
    			type:'POST',
    			dataType:'json',
    			data:{ali_order_no:$("#ali_order_no").val()},
    			success:function(data){
    				if(data.message=='nophoto'){
    					$("#explain").html('没有照片。').css('color','red');
    					$.sound.play('<?php echo $_BASE_DIR;?>public/sound/nophoto.mp3');//没有照片
    					$("#ali_order_no").select();
    					$('#confirm').prop('disabled',true).html('核查失败');
    					return false;
    				}
        		   $("#ali_order_no").select();
        		   $(".product_block").remove();
        		   $("#service_product").html('');
        		   $("#ali_order_no").select();
        		   $("#explain").html('成功').css('color','green');
        		   $.sound.play('<?php echo $_BASE_DIR;?>public/sound/chenggong.mp3');
    			}
    		});
        }
	}
	function savehold(){
        $('#savehold').prop('disabled',true).html('处理中。。。');
		$.ajax({
			url:'<?php echo url('warehouse/savehold')?>',
			type:'POST',
			dataType:'json',
			data:{ali_order_no:$("#ali_order_no").val(),reason:$("#reason").val(),reason_type:$("#reason_type").val()},
			success:function(data){
				if(data.message=='repeat'){
					alert('该订单已有同类核查异常类型，请查询问题件！');
					$("#dialog_hold").dialog("close");
					$('#savehold').removeAttr('disabled').html('保存');
					return false;
				}
				window.location.href = '<?php echo url('/confirmone')?>';
			}
		})
	}
	function savehasbattery(){
		if($('input[name="has_battery"]:checked').val()==1){
		    console.log($('input[name="has_battery_num"]:checked').val())
		    if(!$('input[name="has_battery_num"]:checked').val()){
				$.messager.alert('', '选择带电时带电产品数量必填');
				return false;
			}
	    }
// 		if($('input[name="is_pda"]:checked').val()==1){
// 		    if(!$('input[name="fda_company"]').val() || !$('input[name="fda_address"]').val() || !$('input[name="fda_city"]').val() || !$('input[name="fda_post_code"]').val()){
// 				$.messager.alert('', '选择PDA品类时数据必填');
// 				return false;
// 			}
// 	    }
		$.ajax({
			url:'<?php echo url('warehouse/savehasbattery')?>',
			type:'POST',
			dataType:'json',
			data:{ali_order_no:$("#ali_order_no").val(),has_battery:$("input[name='has_battery']:checked").val(),has_battery_num:$("input[name='has_battery_num']:checked").val(),is_pda:$("input[name='is_pda']:checked").val(),fda_company:$("input[name='fda_company']").val(),fda_address:$("input[name='fda_address']").val(),fda_city:$("input[name='fda_city']").val(),fda_post_code:$("input[name='fda_post_code']").val()},
			success:function(data){
				$("#dialog_has_battery").dialog("close");
// 				$.messager.alert('', '订单是否带电保存成功');
				$("#confirm").focus();
			}
		})
	}
	function cancel(){
		window.location.href = '<?php echo url('/confirmone')?>';
	}
	function hold(){
		$("#dialog_hold").dialog("open");
	}
    $('#reason_type').change(function(){
    	var reason = $('#reason_type option:selected').text();
    	$('#reason').val(reason);
    });
    $('input[name="has_battery"]').click(function(){
        var has_battery = $(this).val();
        console.log(has_battery)
        if(has_battery==1){
   			$('#battnum').removeAttr('style');
        }else{
        	$('#battnum').attr('style','display:none');
        }
    })
//     $('input[name="is_pda"]').click(function(){
//         var has_battery = $(this).val();
//         console.log(has_battery)
//         if(has_battery==1){
// 			$("#pda1").css('color','red');
// 			$("#pda2").css('color','red');
// 			$("#pda3").css('color','red');
// 			$("#pda4").css('color','red');
//    			$('#fda_company').removeAttr('style');
//    			$('#fda_address').removeAttr('style');
//    			$('#fda_city').removeAttr('style');
//    			$('#fda_post_code').removeAttr('style');
//         }else{
//         	$('#fda_company').attr('style','display:none');
//         	$('#fda_address').attr('style','display:none');
//         	$('#fda_city').attr('style','display:none');
//         	$('#fda_post_code').attr('style','display:none');
//         }
//     })
</script>
