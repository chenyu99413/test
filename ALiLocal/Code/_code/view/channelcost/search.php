<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>渠道成本列表<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<?php

echo Q::control ( "path", "", array (
	"path" => array (
		"产品管理" => "",
		"产品列表" => url ( "product/search" ),
		$product->product_name => "",
		"渠道成本" => "" 
	) 
) )?>

<div class="tabs-container">
	<?php echo Q::control("tabs", "tabs_product", array ("tabs" => $tabs,"active_id" => "4"))?>
	<div class="tabs-panels">
		<div class="panel-body panel-body-noheader panel-body-noborder"
			style="padding: 10px;">
			<div class="FarTool">
				<form method="POST" onsubmit="return checkdata();" target="iframe" id="calcu-form">
				计算方式
				<input type="radio" name="cal_type" value="0" checked="checked">按默认
				<input type="radio" name="cal_type" value="1">按阿里订单号
    				<table>
        				<tbody id="moren">
                    	 	<tr>
                    	 		<th class="required-title">国家</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="country_code" autofocus=""></td>
                        	 	<th class="required-title">城市</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="city" autofocus=""></td>
                        	 	<th class="required-title">邮编</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="zip_code" autofocus=""></td>
                        	 	<th class="required-title">包裹类型</th>
                        	 	<td>
                        	 	<?php
                				echo Q::control ( 'dropdownlist', 'packing_type', array (
                					'items' => array('PAK'=>'PAK','DOC'=>'DOC','BOX'=>'BOX'),
                					'style' => 'width: 172px'
                				) )?>
                        	 	</td>
                    	 	</tr>
                    	 	<tr>
                    	 		<th class="required-title">长</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="length_out" autofocus=""></td>
                        	 	<th class="required-title">宽</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="width_out" autofocus=""></td>
                        	 	<th class="required-title">高</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="height_out" autofocus=""></td>
                        	 	<th class="required-title">重量</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="weight" autofocus=""></td>
                    	 	</tr>
                	 	</tbody>
                	 	<tbody id="aliorder" style="display:none">
                    	 	<tr>
                        	 	<th class="required-title">阿里订单号</th>
                        	 	<td><input style="width:160px;" type="text" value="" name="order_no" autofocus=""></td>
                    	 	</tr>
                	    </tbody>
            	    </table>
            	    <button class="btn btn-info" id="search">
                    		               	计算成本
            	    </button>
            	    <input type="hidden" name="channel_id" value="">
            	    <input type="hidden" name="product_id" value="<?php echo request('id')?>">
            	    <input type="hidden" name="product_name" value="<?php echo $product->product_name?>">
            	    <a class="btn btn-success"
    					href="<?php echo url('channelcost/edit',array("id"=>request("id")))?>">
    					<i class="icon-plus"></i>
    					新建
    				</a>
                </form>
                <iframe id="iframe" name="iframe" style="display:none;"></iframe>
			</div>
			<table class="FarTable" style="width:60%;">
				<thead>
					<tr>
						<th>渠道</th>
						<th>预估成本</th>
						<th width=160>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (ChannelCost::find ( "product_id = ?", request ( "id" ) )->getAll () as $value):?>
					<tr>
						<td><?php echo $value->channel->channel_name?></td>
						<td id="channel-<?php echo $value->channel_id?>" class="calchannel">
							<input type="hidden" name="channel_id" value="<?php echo $value->channel_id?>">
						</td>
						<td>
							<a class="btn btn-mini"
								href="<?php echo url('channelcost/edit',array('id'=>request("id"),"channel_id"=>$value->channel_id))?>">
								<i class="icon-edit"></i>
								编辑
							</a>
							<a class="btn btn-mini btn-danger" href="javascript:void(0);"
								onclick="if(DeleteRow(this)){MessagerProgress('删除');window.location.href='<?php echo url('channelcost/delete',array('id'=>request("id"),"channel_id"=>$value->channel_id));?>';}else return false;">
								<i class="icon-trash"></i>
								删除
							</a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<div class="span8">
    		<div class="FarTool text-center">
    			<a class="btn btn-inverse" href="<?php echo url('product/search')?>">
    				<i class="icon-reply"></i>
    				返回
    			</a>
    		</div>
		</div>
	</div>
</div>
<script>
$(function(){
    $('input[type=radio][name=cal_type]').change(function() {
        if (this.value == 0) {
            $('#moren').css('display','block');
            $('#aliorder').css('display','none');
            $('input[type=radio][name=cal_type]').eq(0).attr("checked",'checked');
            $('input[type=radio][name=cal_type]').eq(1).removeAttr('checked');
        }else if (this.value == 1) {
        	$('#moren').css('display','none');
            $('#aliorder').css('display','block');
        	$('input[type=radio][name=cal_type]').eq(0).removeAttr('checked');
           	$('input[type=radio][name=cal_type]').eq(1).attr("checked",'checked');
        }
    });
})
function checkdata(){
	var cal_type = $('input[type=radio][name=cal_type]:checked').val();
	console.log(cal_type)
	if(cal_type==0){
		var country_code = $('input[name=country_code]').val();
		if(country_code == ""){
			$.messager.alert('', '国家不能为空');
			return false;
		}
		var country_code = $('input[name=city]').val();
		if(country_code == ""){
			$.messager.alert('', '城市不能为空');
			return false;
		}
		var country_code = $('input[name=zip_code]').val();
		if(country_code == ""){
			$.messager.alert('', '邮编不能为空');
			return false;
		}
		var country_code = $('input[name=length_out]').val();
		if(country_code == ""){
			$.messager.alert('', '长不能为空');
			return false;
		}
		var country_code = $('input[name=width_out]').val();
		if(country_code == ""){
			$.messager.alert('', '宽不能为空');
			return false;
		}
		var country_code = $('input[name=height_out]').val();
		if(country_code == ""){
			$.messager.alert('', '高不能为空');
			return false;
		}
		var country_code = $('input[name=weight]').val();
		if(country_code == ""){
			$.messager.alert('', '国家不能为空');
			return false;
		}
	}
	var saveload = layer.load(1);
	var index = parent.layer.getFrameIndex(window.name);
	$('.FarTable tbody tr').find('.calchannel').each(function(){
		var obj = $(this);
		obj.find('span').remove();
		$('#calcu-form').find('input[name="channel_id"]').val(obj.find('input[name="channel_id"]').val());
		console.log(obj.find('input[name="channel_id"]').val());
		var form_data = $('#calcu-form').serialize();
		$.ajax({
			url: '<?php echo url("/calchannelcost")?>',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(data) {
			layer.close(saveload);
			obj.append(data.message)
			if (data.success) {
// 				parent.layer.close(index);
// 				parent.location.reload();
			}
		})
		.fail(function(data) {
			console.log(data);
			layer.close(saveload);
			parent.layer.alert('发生内部错误，暂时无法计算');
		});
	})
}
</script>
<?PHP $this->_endblock();?>

