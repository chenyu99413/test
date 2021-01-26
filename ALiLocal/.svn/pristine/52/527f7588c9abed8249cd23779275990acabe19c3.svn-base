<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'问题件列表' => url ( 'order/issue' ),'问题件处理' => '' 
	) 
) )?>
<?php $issue_type=array("1"=>"取件异常件","2"=>"库内异常件","3"=>"渠道异常件")?>
<form method="post" style="margin: 0" enctype="multipart/form-data" onsubmit="return check();">
<div id="dialog_send" class="easyui-dialog hide"title="邮件模板"
		data-options="closed:true, modal:true"
		style="width:800px; height: 460px;">
		<div>
        <table class="FarTable">
              <tr>
                    <th class="required-title">阿里单号</th>
                    <td><input id='ali_order_no' type="text" readonly="readonly" required="required"/></td>
              </tr>
              <tr>
              		<th  style="width:100px;" class="required-title">模板</th>
					<td>
					<?php
						echo Q::control ( 'myselect', 'emailtemplate', array (
							'items' => Helper_Array::toHashmap(EmailTemplate::find('product_id = ?',$abnormal_parcel->order->service_product->product_id)->order('template_name asc')->asArray()->getAll(),'id','template_name'),
							'selected' => request ( 'emailtemplate' ),
							'style' => 'width:200px',
							'required'=>"required",
						) )?>
					</td>
              </tr>
              <tr>
                    <th class="required-title" style="width:100px;">标题</th>
                    <td>
                    	<input type="text" id="template_title" style="width:500px">
                    </td>
              </tr>
              <tr>
                    <th class="required-title" style="width:100px;">预览</th>
                    <td><textarea rows="13" id='template_msg' style="width:500px;"></textarea></td>
              </tr>
        </table>
        <table>
        <tr>
		    <td>
		      <button class="btn btn-primary" type="submit" onclick="sendemail()" style="margin-left: 370px">
					发送
				</button>
			</td>
		</tr>
		</table>		
        </div>
    </div>
	<div class="FarSearch">
		<table style="width: 100%">
           <tbody>
               <tr>
                   <th>问题件编号</th><td><?php echo $abnormal_parcel->abnormal_parcel_no?></td>
                   <th>阿里单号</th><td><?php echo $abnormal_parcel->ali_order_no?></td>
                   <th>末端运单号</th><td><?php echo $abnormal_parcel->order->tracking_no?></td>
                   <th></th>
                   <?php if(Helper_ViewPermission::isAudit()):?>
                   <td>
                       <?php if($abnormal_parcel->parcel_flag=='1'):?>
                        <button type="submit" name="parcel_flag" class="btn btn-small btn-info" value="1">
    						<i class="icon-close"></i>
    						关闭
    					</button>
    					<button type="submit" name="parcel_flag" class="btn btn-small btn-info" value="4">
    						<i class="icon-close"></i>
    						延置处理
    					</button>
    					<?php endif;?>
                   </td>
                   <?php endif;?>
               </tr>
                <tr>
                   <th>发起人</th><td><?php echo $abnormal_parcel->abnormal_parcel_operator?></td>
                   <th>发起时间</th><td><?php echo Helper_Util::strDate('Y-m-d H:i', $abnormal_parcel->create_time)?></td>
                  <th>问题类型</th>
                   <td>
                   		<?php
						echo Q::control ( "dropdownlist", "issue_type", array (
							"name" => "issue_type",
							"items" => array (
								"1"=>"取件异常件","2"=>"库内异常件","3"=>"渠道异常件","4"=>"无主件","5"=>"港前异常件"
							),"value" => $abnormal_parcel->issue_type 
						) )?>
                   </td>
                   <th></th>
                   <?php if(Helper_ViewPermission::isAudit()):?>
                   <td>
                        <?php if($abnormal_parcel->parcel_flag=='1'):?>
                            <?php if($abnormal_parcel->order->order_status=='12'):?>
                            <button type="submit" name="parcel_flag" class="btn btn-small btn-info" value="3">
        						<i class="icon-close"></i>
        						关闭并解扣
        					</button>
        					<?php endif;?>
        				<?php else :?>
        					<button type="submit" name="parcel_flag" class="btn btn-small btn-info" value="2">
        						开启
        					</button>
    					<?php endif;?>
                   </td>
                   <?php endif;?>
               </tr>
               <tr>
               <th>跟进</th>
               <td colspan="3" style="border-left: 2px #eee">
					<textarea style="width: 600px" rows="4" name="follow_up_content" id="follow_up_content"></textarea>
               </td>
               <td colspan="4" class="span3" valign="top">
                   <div style="float:left;width:230px;" id="check_problem">
                   <b>核查异常类型</b>
                   <?php
                        echo Q::control ( 'dropdownlist', 'reason_type', array (
                        'items'=>array(''=>'','涉电/磁/液/粉类问题'=>'涉电/磁/液/粉类问题',
                            '无FDA/税号/报关资料'=>'无FDA/税号/报关资料','涉牌/知识产权问题'=>'涉牌/知识产权问题',
                            '无报关服务'=>'无报关服务','超400美金/EMS无服务'=>'超400美金/EMS无服务',
                            '超800美金/中美无服务'=>'超800美金/中美无服务','邮编/城市/国家无服务'=>'邮编/城市/国家无服务',
                            '产品规格不支持'=>'产品规格不支持','黑名单'=>'黑名单','其他'=>'其他'
                        ),
                        'value' => $abnormal_parcel->checkabnormal_type,
                        'style' => 'width:150px;'
                   ) )?>
                   </div>
                   <div style="float:left;width:230px;" id="deadline_set">
                   <b>截止时间</b>
                   <?php echo Q::control('datebox','deadline',array(
                       'value' => Helper_Util::strDate('Y-m-d', $abnormal_parcel->deadline),
                       'style' => 'width:80px'
                   ))
                   ?>
                   </div>
                   <?php if(Helper_ViewPermission::isAudit()):?>
                   <a href="javascript:void(0)" style="margin-right:0px;float:right;" class="btn btn-mini btn-info" onclick="$('#file').click()">上传附件</a>
                   <a class="btn btn-mini btn-success" href="javascript:void(0)" onclick="$('#dialog_send').dialog('open');$('.window-shadow').css('top','106px');$('.panel').css('top','106px');$('#dialog_send').removeClass('hide');copy('<?php echo $abnormal_parcel->ali_order_no?>')">邮件</a>
                   <table class="FarTable">
                       <thead><tr><th>附件</th></tr></thead>
                       <tbody>
                           <?php foreach ($abnormal_parcel->file as $temp):?>
                           <tr>
                               <td><a href="<?php echo $_BASE_DIR.strstr($temp->file_path,'public/upload/files')?>" download="<?php echo $temp->file_name?>"><?php echo $temp->file_name?></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-mini btn-danger" href="javascript:void(0)" data="<?php echo $temp->abnormal_parcel_file_id?>" onclick="removeline(this)"><i class="icon-remove"></i></a></td>
                           </tr>
                           <?php endforeach;?>
                       </tbody>
                   </table>
                   <?php endif;?>
               </td>
               </tr>
           </tbody>
        </table>
        <?php if(Helper_ViewPermission::isAudit()):?>
        <div class="FarTool text-center">
        	<button class="btn btn-small btn-success" id="search">
                                        保存
       		</button>
       	</div>
       	<?php endif;?>
     </div>
     <input type="hidden" name="abnormal_parcel_id" value="<?php echo $abnormal_parcel->abnormal_parcel_id ?>" />
 </form>
 <form enctype="multipart/form-data" method="post" action="<?php echo url('/saveissuefile')?>" style="display:none" id="file_form">
    <input type="file" name="file" id="file">
    <input type="hidden" name="abnormal_parcel_id" value="<?php echo $abnormal_parcel->abnormal_parcel_id ?>" />
 </form>
 	 <h6>跟进历史</h6>
 	 <table class="FarTable">
            <thead>
              <tr>
              	  <th>序号</th>
                  <th>跟进人</th>
                  <th>时间</th>
                  <th>内容</th>
              </tr>
           </thead>
           <tbody>
           	<?php if (isset($abnormal_parcel)): $i=0;?>
           		<?php foreach ($abnormal_parcel->history as $value):?>
           		<?php $i++?>
               <tr>
               	   <td style="width:30px;"><?php echo $i;?></td>
                   <td style="width:70px;"><?php echo $value->follow_up_operator?></td>
                   <td align="center" style="width:130px;"><?php echo Helper_Util::strDate('Y-m-d H:i', $value->create_time)?></td>
                   <?php if($value->is_mail=='1'):?>
                   <td style="color: blue"><?php else :?><td><?php endif;?>  <?php echo $value->follow_up_content?></td>
               </tr>
               	<?php endforeach;?>
             <?php endif;?>
           </tbody>
        </table> 
<script type="text/javascript">
function check(){
	if($('#follow_up_content').val()==''){
		alert("跟进内容必填");
		return false;
	}
}
$(function(){
	$("#file").change(function(){
		$("#file_form").submit();
	});
});
$(function(){
	if($('#issue_type option:selected').text() == '库内异常件'){
	   $('#check_problem').css('display','block');
    }else{
       $('#check_problem').css('display','none');
    }
    if($('#issue_type option:selected').text() == '渠道异常件'){
		$('#deadline_set').css('display','block');
    }else{
        $('#deadline_set').css('display','none');
    }
	$("#issue_type").change(function(){
		var issue_type = $('#issue_type option:selected').text();
		if(issue_type == '库内异常件'){
			$('#check_problem').css('display','block');
		}else{
			$('#check_problem').css('display','none');
		}
		if(issue_type == '渠道异常件'){
			$('#deadline_set').css('display','block');
		}else{
			$('#deadline_set').css('display','none');
		}
	});
	$("#deadline_set").change(function(){
		$('#follow_up_content').val('设置截止时间');
	})
});
function removeline(obj){
	$(obj).parent().parent().remove();
	$.ajax({
		url:'<?php echo url('/delissuefile')?>',
		type:'post',
		data:{abnormal_parcel_file_id:$(obj).attr('data')},
		success:function(){

		}
	});
}
function copy(ali_order_no){
	$('#ali_order_no').val(ali_order_no);
	$("#emailtemplate").val('');
	$("#template_title").val('');
	$('#template_msg').val('');
}
$("#emailtemplate").change(function(){
	var ali_order_no = $('#ali_order_no').val();
	var id = $("#emailtemplate").val();
	if(ali_order_no == ''){
		alert('数据错误');
		return false;
	}
	if(id>0 && ali_order_no != ''){
		$.ajax({
			url:'<?php echo url('product/templateinfo')?>',
			data:{id:id,ali_order_no:ali_order_no},
			type:'post',
			dataType:'json',
			async:false,
			success:function(data){
				if(data.error == 'notemplate'){
				   alert('该模板不存在');
				}else if(data.error == 'noorder'){
				   alert('该订单不存在');
				}else if(data.error == 'nopostal'){
				   alert('邮政信息缺失,请检查是否正确');
				}else if(data.error == 'nodata'){
				   alert('数据缺失');
				}else{
				   $('#template_title').val(data.title);
				   $('#template_msg').val(data.message);
				}
			}
		});
	}else{
		$('#template_msg').val('');
	}
});
function sendemail(){
	var id = $("#emailtemplate").val();
	var ali_order_no = $('#ali_order_no').val();
	var title = $('#template_title').val();
	var message = $('#template_msg').val();
	var abnormal_parcel_id = <?php echo $abnormal_parcel->abnormal_parcel_id?>;
	if(id>0 && ali_order_no != '' && message != ''  && title != '' ){
		$.ajax({
			url:'<?php echo url('product/sendtemplate')?>',
			data:{id:id,ali_order_no:ali_order_no,title:title,message:message,abnormal_parcel_id:abnormal_parcel_id},
			type:'post',
			dataType:'json',
			async:false,
			success:function(data){
				if(data.error=='notemplate'){
					alert('该模板不存在');
				}else if(data.error=='nodata'){
					alert('数据不完整');
				}else{
					if(data.success== 'success'){
					   alert('发送成功');
					   setTimeout(function (){
							window.location.reload();
						}, 1000);
					}else{
					   alert('发送失败，失败原因：'+data.errorinfo);
					}
				}
			}
		});
	}else{
		alert('必填项不能为空');
		return false;
	}
}
</script>
<?PHP $this->_endblock();?>

