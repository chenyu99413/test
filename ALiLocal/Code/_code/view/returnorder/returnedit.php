<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'退件管理' => '','退件订单管理' => url ( 'returnorder/returnlist' ),'订单编辑' => '' 
	) 
) )?>
<div class="FarSearch" style="line-height: 6px; margin-top:-8px;padding:0;">
	<table>
		<tbody>
			<tr>
				<th>阿里订单号</th>
				<td>
					<?php echo $order->ali_order_no?>
				</td>
				<th>泛远单号</th>
				<td>
					<?php echo $order->far_no?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="panel">
<div class="row">
<form id="form" method="POST">
	<input type="hidden" name="return_order_id" value="<?php echo $order->return_order_id?>">
	<div class="span12">
	<table class="FarTable" style="width: 97%">
	<tr>
        <th style="width: 60px">订单时间</th>
	    <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->create_time)?></td>
	    <th style="width: 60px">退货入库时间</th>
	    <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->return_time)?></td>
	    <th style="width: 90px">入库人</th>
	    <td style="width: 80px"><?php echo $order->scan_name?></td>
	    <th style="width: 60px">确认时间</th>
	    <td style="width: 80px" colspan="3"><?php echo Helper_Util::strDate('m-d H:i', $order->queren_time)?></td>
	</tr>
	<tr>
	   <th style="width: 70px">重发时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->warehouse_out_time)?></td>
	   <th style="width: 70px">签收时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->delivery_time)?></td>
	   <th style="width: 70px">销毁时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->destroy_time)?></td>
	   <th style="width: 70px">退回时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->send_back_time)?></td>
	   <th style="width:60px">备注</th>
	   <td style="width: 100px">
	   <?php foreach ($returnpackage as $rp):?>
	   		<?php echo $rp->note?>
	   <?php endforeach;?>
	   </td>
	</tr>
	</table>
	</div>
	<div class="span7">
        <table class="FarTable" >
        	<caption>收件人</caption>
              <tr>
                  <th>姓名</th>
                  <td><input style="width:90%;" type="text" name="consignee_name1" value="<?php echo $order->consignee_name1?>"></td>
                  <th>公司</th>
                  <td><input style="width:90%;" type="text" name="consignee_name2" value="<?php echo $order->consignee_name2?>"></td>
              </tr>
              <tr>
                  <th>手机号码</th>
                  <td><input style="width:90%;" type="text" name="consignee_mobile" value="<?php echo $order->consignee_mobile?>"></td>
                  <!-- <th></th>
                  <td></td>-->
		   		  <th>电话</th> 
                  <td><input style="width:90%;" type="text" name="consignee_telephone" value="<?php echo $order->consignee_telephone?>"></td>
              </tr>
              <tr>
                  <th>邮箱</th>
                  <td><?php echo $order->consignee_email?></td>
                  <th>税号</th>
                  <td><input style="width:90%" type="text" name="tax_payer_id" value="<?php echo $order->tax_payer_id?>"></td>
              </tr>
              <tr>
                  <th>国家</th>
                  <td><?php echo $order->consignee_country_code?></td>
                  <th>省/州</th>
                  <td><input style="width:90%;" type="text" id="consignee_state_region_code" name="consignee_state_region_code" value="<?php echo $order->consignee_state_region_code?>"></td>
              </tr>
              <tr>
              <?php if(($order->order_status<>'4' && $order->order_status<>'12') || MyApp::checkVisible('edit-order')):?>
                  <th>城市</th>
                  <td><input style="width:90%" type="text" id="consignee_city" name="consignee_city" value="<?php echo $order->consignee_city?>"></td>
                  <th>邮编</th>
                  <td><input style="width:90%;" type="text" id="consignee_postal_code" name="consignee_postal_code" value="<?php echo $order->consignee_postal_code?>"></td>
              <?php else :?>
                  <th>城市</th>
                  <td><?php echo $order->consignee_city?></td>
                  <th>邮编</th>
                  <td><?php echo $order->consignee_postal_code?></td>
              <?php endif;?>
              </tr>
              <tr>
                  <th>地址1</th>
                  <td colspan="3"><input style="width:90%;" type="text" id="consignee_street1" name="consignee_street1" value="<?php echo $order->consignee_street1?>"></td>
              </tr>
              <tr>
                  <th>地址2</th>
                  <td colspan="3"><input style="width:90%;" type="text" id="consignee_street2" name="consignee_street2" value="<?php echo $order->consignee_street2?>"></td>
              </tr>
              <tr>
              </tr>
        </table>
    </div>
    <div class="span4" style="margin: 0 0 0 10px">
		<table class="FarTable">
			<tr>
				<th>渠道</th>
				<td>
					<?php
					echo Q::control("dropdownbox", "channel_id", array(
						"items" => Helper_Array::toHashmap(ReturnChannel::find()->getAll(), "channel_id", "channel_name"),
						"value" => $order->channel_id,
						"style" => "width: 95%",
						"empty" => "true"
					))?>
				</td>
			</tr>
			<tr>
			<th>末端单号</th>
			<td><input name="tracking_no" type="text" style="width: 95%" value="<?php echo $order->tracking_no?>"></td>
			</tr>
			<tr>
				<th>打单账号</th>
				<td><?php echo $order->account?></td>
			</tr>
			<tr>
				<th>关联单号</th>
				<td>
				<input type="hidden" id='ali_order_no' value="<?php echo $order->ali_order_no?>">
				<input name="related_ali_order_no" type="text" placeholder="关联的阿里单号" style="width: 95%" value="<?php echo $order->related_ali_order_no?>">
				</td>
			</tr>
			<tr>
			<th>备注</th>
			<td><textarea name="remark" rows="" cols="" style="width: 185px; height: 20px" ><?php echo $order->remark?></textarea></td>
			</tr>
			<?php if($order->order_status=='1'):?>
			<tr>
			<th>入库信息</th>
			<td><input name="dwsremarks" readonly="readonly" type="text" style="width:95%;" value="<?php echo $order->dwsremarks?>" /></td>
			</tr>
			<?php endif;?>
			<tr>
				<th>阿里单号</th>
				<td>
					<input name="ali_order_no" type="text" placeholder="阿里单号" style="width: 95%" value="<?php echo $order->ali_order_no?>">
				</td>
			</tr>
			<tr>
				<th>是否带电</th>
				<td>
					<?php echo Q::control('RadioGroup','has_battery',array(
                		   'items'=>array(1=>'是',2=>'否'),
						'value'=>$order->has_battery
                	))?>
				</td>
			</tr>
		 	<tr id="battnum" <?php if ($order->has_battery==2){?> style="display: none"<?php }?>>
					<th>带电产品数量</th>
                	<td>
                		<?php echo Q::control('RadioGroup','has_battery_num',array(
                		    'items'=>array(1=>'不超过2个',2=>'2个以上'),
                			'value'=>$order->has_battery_num
                		))?>
                	</td>
			</tr>
			<tr>
				<th>是否为FDA品类</th>
				<td>
					<?php echo Q::control('RadioGroup','is_pda',array(
                		   'items'=>array(1=>'是',0=>'否'),
						'value'=>$order->is_pda
                	))?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
				    <?php if($order->order_status<>'50' && $order->order_status<>'60' && $order->order_status<>'70' && $order->order_status<>'80'): ?>
					<button class="btn btn-small btn-success" id="search">保存</button>
					<?php endif;?>
				</th>
			</tr>
		</table>
	</div>
	<div class="span12">
        <h6 style="margin-top:0px;">FDA制造商信息</h6>
        <table class="FarTable" style="width:97%;margin-top:-5px;">
            <thead>
              <tr>
                   	<th>生产商公司名</th>
                    <th>地址</th>
                    <th>城市</th>
                    <th>邮编</th>
              </tr>
           </thead>
           <tbody>
               <tr>
                    <td><input type="text" style="width:340px;" name="fda_company" value="<?php echo $order->fda_company?>"></td>
					<td><input type="text" style="width:250px;" name="fda_address" value="<?php echo $order->fda_address?>"></td>
					<td><input type="text" style="width:220px;" name="fda_city" value="<?php echo $order->fda_city?>"></td>
					<td><input type="text" style="width:120px;" name="fda_post_code" value="<?php echo $order->fda_post_code?>"></td>
               </tr>
           </tbody>
        </table>
    </div>
    <div class="span12">
        <h6 style="margin-top:0px;">预报产品信息 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;报关类型：<?php echo $order->declaration_type?> 申报总价：<span class="total_amount"><?php echo $order->total_amount?></span> 申报币种：<?php echo $order->currency_code?></h6>
        <table class="FarTable" style="width:97%;margin-top:-5px;">
            <thead>
              <tr>
                   	<th >中文品名</th>
                    <th >英文品名</th>
                    <th >HS Code</th>
                    <th >材质用途</th>
                    <th >数量(pcs)</th>
                    <th >法定单位1</th>
                    <th >法定数量1</th>
                    <th >法定单位2</th>
                    <th >法定数量2</th>
                    <th >申报单价</th>
                    <th style="width:50px;">带电</th>
                    <?php if($order->order_status<>'50' && $order->order_status<>'60' && $order->order_status<>'70' && $order->order_status<>'80'): ?>
                    <th>操作</th>
                    <?php endif;?>
              </tr>
           </thead>
           <tbody>
           <?php $i=0;?>
           <?php foreach ($product as $v): $i++;?>
               <tr class="chanpin" id="<?php echo $v->order_product_id?>">
                   <td ><?php echo $v->product_name_far?$v->product_name_far:$v->product_name?></td>
                   <td ><?php echo $v->product_name_en_far?$v->product_name_en_far:$v->product_name_en?></td>
                   <td  class="hs_code"><?php echo $v->hs_code_far?$v->hs_code_far:$v->hs_code?></td>
                   <td ><?php echo $v->material_use?></td>
                   <td class="product_quantity"><?php echo $v->product_quantity?></td>
                   <td ><?php echo $v->product_unit1_far?></td>
                   <td ><?php echo $v->product_quantity1_far?></td>
                   <td ><?php echo $v->product_unit2_far?></td>
                   <td ><?php echo $v->product_quantity2_far?></td>
                   <td  class="declaration_price"><?php echo $v->declaration_price?></td>
                   <td><?php echo $v->has_battery?'是':''?></td>             
                   <?php if($order->order_status<>'50' && $order->order_status<>'60' && $order->order_status<>'70' && $order->order_status<>'80'): ?>
                   		<td nowrap="nowrap">              
                									<a class="btn btn-mini" href="javascript:void(0);"
                										onclick="EditRow([
                										{'type':'text','required':'true'},
                										{'type':'text','required':'true'},
                										{'type':'text','required':'true'},
                										{'type':'text'},
                										{'type':'text','required':'true'},
                										{'type':'hidden'},
                										{'type':'hidden'},
                										{'type':'hidden'},
                										{'type':'hidden'},
                										{'type':'text','required':'true'},
                										{'type':'select','option':<?php echo str_replace("\"","'",json_encode(array(0=>array('id'=>'2','text'=>'否'),1=>array('id'=>'1','text'=>'是'))));?>}],this);">
                										<i class="icon-pencil"></i>
                										编辑
                									</a>
                									<a class="btn btn-mini btn-danger"
                										href="javascript:void(0);" onclick="DeleteRow(this);">
                										<i class="icon-trash"></i>
                										删除
                									</a>
                	  	</td>
                	 <?php endif;?>
               </tr>
           <?php endforeach;?>
           <?php if($order->order_status<>'50' && $order->order_status<>'60' && $order->order_status<>'70' && $order->order_status<>'80'): ?>
           <tr>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td></td>
                								<td>
                									<a class="btn btn-mini btn-success"
                										href="javascript:void(0);"
                										onclick="NewRow([
                										{'type':'text','required':'true'},
                										{'type':'text','required':'true'},
                										{'type':'text','required':'true'},
                										{'type':'text'},
                										{'type':'text','required':'true'},
                										{'type':'hidden'},
                										{'type':'hidden'},
                										{'type':'hidden'},
                										{'type':'hidden'},
                										{'type':'text','required':'true'},
                										{'type':'select','option':<?php echo str_replace("\"","'",json_encode(array(0=>array('id'=>'2','text'=>'否'),1=>array('id'=>'1','text'=>'是'))));?>}],this);" >
                										<i class="icon-plus"></i>
                										新建
                									</a>
                								</td>
                							</tr>
                							<?php endif;?>
           </tbody>
        </table>
    </div>
</form>
	<div class="span12">
    <div style="width:19%;float:left;clear:both">
        <h6 style="margin-top:0px;">原始出库包裹信息</h6>
        <table class="FarTable" style="width:98%;margin-top:-5px;">
           <thead>
              <tr>
                  <th>数量</th>
                  <th>长</th>
                  <th>宽</th>
                  <th>高</th>
                  <th>客重</th>
              </tr>
           </thead>
           <tbody>
               <?php foreach ($faroutpackage as $value):?>
               <tr>
                   <td style="text-align: right"><?php echo $value->quantity_out?></td>
                   <td style="text-align: right"><?php echo $value->length_out?></td>
                   <td style="text-align: right"><?php echo $value->width_out?></td>
                   <td style="text-align: right"><?php echo $value->height_out?></td>
                   <td style="text-align: right"><?php echo $value->weight_out?></td>
               </tr>
               <?php endforeach;?>
           </tbody>
        </table>
    </div>
    <div style="width:39%;float:left" class="in_table" id="handle-area">
    <h6 style="margin-top:0px;float:left;">退货入库包裹信息</h6>
    
           <form id="package1">
   		<table class="FarTable" style="width:98%;margin-top:-5px;float:left">
           <thead>
              <tr>
                  <th width="40px">数量</th>
                  <th width="40px">长</th>
                  <th width="40px">宽</th>
                  <th width="40px">高</th>
                  <th width="40px">实重</th>
<!--                   <th width="20px">操作</th> -->
              </tr>
           </thead>
           <tbody>
                <?php foreach ($returnpackage as $package):?>
               <input type="hidden" name="type" value="1" />
               <input type="hidden" name="return_package_id[]" value="<?php echo $package['return_package_id']?>" />
               <tr>
                   <td style="text-align: center;"><input type="text" style="width:90%;" name="quantity[]" value="<?php echo $package['quantity']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="length[]" value="<?php echo $package['length']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="width[]" value="<?php echo $package['width']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="height[]" value="<?php echo $package['height']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="weight[]" value="<?php echo $package['weight']?>"></td>
                   <!-- <td><a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removed(this)"><i class="icon-remove"></i></a></td> -->
               </tr>
               <?php endforeach;?>
               <?php if($order->order_status<>'50' && $order->order_status<>'60' && $order->order_status<>'70' && $order->order_status<>'80'): ?>
               <tr>
                    <td colspan="6">
                        <div style="line-height:25px;" id="savefar">
                        	<a class="btn btn-mini btn-primary" onclick="save(1)" data="in"
                        		style="margin-right: 10px;">
                        		<i class="icon-save"></i>
                        		提交数据
                        	</a>
                    	</div>
                    </td>
               </tr>
               <?php endif;?>
           </tbody>
        </table>
           </form>
    </div>
    <div style="width:39%;float:left" class="out_table">
    <h6 style="margin-top:0px;float:left;">退货出库包裹信息</h6>
           <form id="package2">
    	<table class="FarTable" style="width:98%;margin-top:-5px;float:left">
           <thead>
              <tr>
                  <th width="40px">数量</th>
                  <th width="40px">出库长</th>
                  <th width="40px">出库宽</th>
                  <th width="40px">出库高</th>
                  <th width="40px">出库实重</th>
<!--                   <th width="20px">操作</th> -->
              </tr>
           </thead>
           <tbody>
               <?php foreach ($returnoutpackage as $package):?>
               <input type="hidden" name="type" value="2" />
               <input type="hidden" name="return_package_id[]" value="<?php echo $package['return_package_id']?>" />
               <tr>
                    <td style="text-align: center;"><input type="text" style="width:90%;" name="quantity[]" value="<?php echo $package['quantity']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="length[]" value="<?php echo $package['length']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="width[]" value="<?php echo $package['width']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="height[]" value="<?php echo $package['height']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" name="weight[]" value="<?php echo $package['weight']?>"></td>
                  
                  <!--  <td><button class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removed(this)"><i class="icon-remove"></i></button></td> -->
               </tr>
               <?php endforeach;?>
               <?php if($order->order_status<>'50' && $order->order_status<>'60' && $order->order_status<>'70' && $order->order_status<>'80'): ?>
               <tr>
                    <td colspan="3">
                        <div style="line-height:25px;" id="saveqd">
                        	<a class="btn btn-mini btn-primary" onclick="save(2)" data="out"
                        		style="margin-right: 10px;">
                        		<i class="icon-save"></i>
                        		提交数据
                        	</a>
                    	</div>
                    </td>
               </tr>
               <?php endif;?>
           </tbody>
        </table>
           </form>
    </div>
    </div>
</div>
</div>
<?PHP $this->_endblock();?>
<script>
/**
	 * 回调 删除数据
	 */
	function DeleteBefore(obj){
		$.ajax({
			url:"<?php echo url('returnorder/productdel')?>",
			type:"POST",
			data:{"order_product_id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
			success:function(msg){
				var total_amount = 0;
				$('.chanpin').each(function(k,v){
					console.log($(this).children('.hs_code').html());
					total_amount = ($(this).children('.product_quantity').html() * $(this).children('.declaration_price').html())*1 + total_amount*1
				})
				$('.total_amount').html(total_amount.toFixed(2))
			}
		});
	}
				
	/**
	 * 回调 保存数据
	 */
	function CallBack(obj,name){
		if(obj==null){
			return false;
		}
		$.ajax({
			url:"<?php echo url('returnorder/productsave')?>",
			type:"POST",
			data:{"return_order_id":"<?php echo $order->return_order_id?>",
				"price":{
					"order_product_id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
					"product_name_far":$(obj).children().eq(0).text(),
					"product_name_en_far":$(obj).children().eq(1).text(),
					"hs_code_far":$(obj).children().eq(2).text(),
					"material_use":$(obj).children().eq(3).text(),
					"product_quantity":$(obj).children().eq(4).text(),
					"product_unit1_far":$(obj).children().eq(5).text(),
					"product_quantity1_far":$(obj).children().eq(6).text(),
					"product_unit2_far":$(obj).children().eq(7).text(),
					"product_quantity2_far":$(obj).children().eq(8).text(),
					"declaration_price":$(obj).children().eq(9).text(),
					"has_battery":$(obj).children().eq(10).text() == '是' ? '1' : '0'}},
			success:function(msg){
				$(obj).attr("id",msg);
				$(obj).addClass('chanpin');
				$(obj).children().eq(2).addClass('hs_code');
				$(obj).children().eq(4).addClass('product_quantity');
				$(obj).children().eq(9).addClass('declaration_price');
				var total_amount = 0;
				$('.chanpin').each(function(k,v){
					console.log($(this).children('.hs_code').html());
					total_amount = ($(this).children('.product_quantity').html() * $(this).children('.declaration_price').html())*1 + total_amount*1
				})
				$('.total_amount').html(total_amount.toFixed(2))
			}
		});
	}
	 //保存
    function save(type){
        console.log($('#package'+type).serialize())
    	//保存数据
		$.ajax({
			url:'<?php echo url('/savepackages')?>',
			data:$('#package'+type).serialize(),
			type:'POST',
			dataType:'json',
			success:function(data){
				$.messager.alert('',data.msg);
				if(data.status=='true'){
					location.reload();//刷新页面
				}
			}
		});
    }
</script>
<?php if($msg == 1):?>
<script>
$.messager.alert('','修改成功');
</script>
<?php endif;?>