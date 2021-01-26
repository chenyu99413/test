<div class="row">
<form id="form" method="POST">
	<input type="hidden" name="order_id" value="<?php echo $order->order_id?>">
	<div class="span12">
	<table class="FarTable" style="width: 97%">
	<tr>
	        <th style="width: 60px">上门取件</th>
			<td style="width: 80px">
			<?php if(MyApp::checkVisible('edit-pickup_company')):?>
			 <?php
					echo Q::control("dropdownbox", "need_pick_up", array(
						"items" => array(
						  '1'=>'是',
						),
						"value" => $order->need_pick_up,
						"style" => "width: 60px",
						"empty" => "true"
					))?>
			<?php else :?>
			 <?php echo $order->need_pick_up?'是':''?>
			<?php endif;?>
			</td>
			<th style="width: 70px">取件网点</th>
			<td style="width: 90px">
			<?php if(MyApp::checkVisible('edit-pickup_company')):?>
				<?php
				echo Q::control("dropdownbox", "pick_company", array(
					"items" => $relevant_department_names,
					"value" => $order->pick_company,
					"style" => "width: 90px",
					"empty" => "true"
				))?>
				<?php else :?>
			 <?php echo $order->pick_company?>
			<?php endif;?>
			</td>
			<th>报关类型</th>
			<td><?php
                    echo Q::control('dropdownbox','declaration_type',
                    array(
                    	'items' => array('QT'=>'QT','DL'=>'DL'),
                    'empty' => true,
                    'value' => $order->declaration_type,
                    ) )?></td>
            <th style="width: 60px">订单时间</th>
		    <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->create_time)?></td>
		    <th style="width: 60px">入库时间</th>
		    <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->far_warehouse_in_time)?></td>
		    <th style="width: 90px">入库人</th>
		    <td style="width: 80px"><?php echo $order->far_warehouse_in_operator?></td>
		    <th style="width: 60px">入库单号</th>
		    <td style="width: 80px"><?php echo $order->scan_no_in?></td>
	</tr>
	<tr>
	   <th style="width: 60px">支付时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->payment_time)?></td>
	   <th style="width: 70px">出库时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->warehouse_out_time)?></td>
	   <th style="width: 70px">签收时间</th>
	   <td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->delivery_time)?></td>
	   <th style="width:90px">国内快递单号</th>
	   <td colspan="5"><input name="reference_no" type="text" style="width: 95%" value="<?php echo $order->reference_no?>"></td>
	   <th style="width:60px">备注</th>
	   <td style="width: 100px"><?php echo $order->remarks?></td>
	</tr>
	<tr>
		<th>到库时间</th>
		<td style="color:red"><?php echo trim($date_time,';')?></td>
		<th>关联订单号</th>
		<td><?php echo $other_ali_order_no?></td>
		<?php $posscan = PosScan::find('order_id=?',$order->order_id)->order('pos_scan_id desc')->getOne();?>
		<!-- <th>库位</th>
		<td><?php echo $posscan->warehouse_code?></td>
		<th>扫描人</th>
		<td><?php echo $posscan->scan_name?></td>
		<th>扫描时间</th>
		<td colspan="5"><?php echo $posscan->create_time?date('Y-m-d H:i:s',$posscan->create_time):''?></td> -->
		<th>委托书编号</th>
		<td><?php echo $order->commission_code?></td>
		<th>经营单位编码</th>
		<td><?php echo $order->business_code?></td>
		<th style="width: 60px">核查时间</th>
		<td style="width: 80px"><?php echo Helper_Util::strDate('m-d H:i', $order->warehouse_confirm_time)?></td>
		<td colspan="4"></td>
	</tr>
	</table>
	</div>
    <div class="span4">
        <table class="FarTable">
        	<caption>发件人</caption>
	          <tr>
                  <th>姓名/公司</th><td><?php echo $order->sender_name1.' , '.$order->sender_name2?></td>
              </tr>
              <tr>
                  <th>固定电话</th><td><?php echo $order->sender_telephone?></td></tr>
              <tr>
                  <th>手机号</th><td><?php echo $order->sender_mobile?></td></tr>
              <tr>
                  <th>邮箱</th><td><?php echo $order->sender_email?></td></tr>
              <tr>
                  <th>国家，省，城市</th><td ><?php echo $order->sender_country_code?>, <?php echo $order->sender_state_region_code?>, <?php echo $order->sender_city?>, <?php echo $order->sender_postal_code?></td></tr>
              <tr>
                  <th>地址1</th><td ><?php echo $order->sender_street1?></td></tr>
              <tr>
                  <th>地址2</th><td ><?php echo $order->sender_street2?></td></tr>
              <tr>
                  <th>备注</th><td ><?php $send_context = Contact::find('sender_company = ?',$order->sender_name2)->getOne();
                                            echo $send_context?$send_context->comment:'';?></td></tr>
        </table>
    </div>
    <div style="margin-left:10px" class="span5">
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
                  <th></th>
                  <td></td>
		     <!-- <th>电话</th> 
                  <td><input style="width:90%;" type="text" name="consignee_telephone" value="<?php echo $order->consignee_telephone?>"></td>-->
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
    <div class="span3" style="margin: 0 0 0 10px">
		<table class="FarTable">
			<tr>
				<th>渠道</th>
				<td>
					<?php
					echo Q::control("dropdownbox", "channel_id", array(
						"items" => Helper_Array::toHashmap(Channel::find('channel_id in (?)',Channeldepartmentavailable::availablechannelids($order->customer_id))->getAll(), "channel_id", "channel_name"),
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
				<th>重发单号</th>
				<td>
				<?php 
				$return_order = ReturnOrder::find('order_id=?',$order->order_id)->getOne();
				?>
				<input type="text" style="width: 95%" value="<?php echo $return_order->new_tracking_no?>">
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
			<?php if(Helper_ViewPermission::isAudit()):?>
			<tr>
				<th colspan="2">
				    <?php if($order->order_status<>'6' && $order->order_status<>'7' && $order->order_status<>'8' && $order->order_status<>'9'): ?>
					<button class="btn btn-small btn-success" id="search">保存</button>
					<?php endif;?>
					<?php if($order->order_status=='12'):?>
	       				 <button class="btn btn-small btn-info" name="release" value="release" style="margin-left: 10px;">
				                                        解扣
				       		</button>
	       				<?php endif;?>
				</th>
			</tr>
			<?php endif;?>
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
	<?php if ($order->order_status=='11'):?>
    <div class="span12">
        <h6 style="margin-top:0px;">退货信息</h6>
        <table class="FarTable" style="width:100%;margin-top:-5px;">
            <thead>
              <tr>
                  <th>退货类型</th>
                  <th>原因代码</th>
                  <th>原因名称</th>
                  <th>原因备注</th>
                  <th>收件人</th>
                  <th>手机号</th>
                  <th>固定电话</th>
                  <th>省</th>
                  <th>市</th>
                  <th>地址</th>
                  <th>邮编</th>
                  <th>邮箱</th>
              </tr>
           </thead>
           <tbody>
               <tr>
                   <td><?php echo $order->return_type;?></td>
                   <td><?php echo $order->reason_code;?></td>
                   <td><?php echo $order->reason_name;?></td>
                   <td><?php echo $order->reason_remark;?></td>
                   <td><?php echo $order->return_name1;?></td>
                   <td><?php echo $order->return_mobile;?></td>
                   <td><?php echo $order->return_telephone;?></td>
                   <td><?php echo $order->return_state_region_code;?></td>
                   <td><?php echo $order->return_city;?></td>
                   <td><?php echo $order->return_street1." ".$order->return_street2;?></td>
                   <td><?php echo $order->return_postal_code;?></td>
                   <td><?php echo $order->return_email;?></td>
               </tr>
           </tbody>
        </table>
    </div>
    <?php endif;?>
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
                    <?php if(MyApp::checkVisible('order-product')):?>
                    <?php if(in_array($order->order_status,array('4','10','5','1','14')) ||  (in_array($order->order_status,array('12'))&&!$order->print_time)):?>
                    <th>操作</th>
                    <?php endif;?>
                    <?php endif;?>
              </tr>
           </thead>
           <tbody>
           <?php $i=0;?>
           <?php foreach ($order->product as $v): $i++;?>
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
                   <?php if(in_array($order->order_status,array('4','10','5','1','14')) ||  (in_array($order->order_status,array('12'))&&!$order->print_time)):?>
                   		<td nowrap="nowrap">              
                   		<?php if(Helper_ViewPermission::isAudit()):?>   
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
                							   <?php if(MyApp::checkVisible('order-product')):?>
                									<a class="btn btn-mini btn-danger"
                										href="javascript:void(0);" onclick="DeleteRow(this);">
                										<i class="icon-trash"></i>
                										删除
                									</a>
                								
                								<?php endif;?>
                								<?php endif;?>
                	  	</td>
                	 <?php endif;?>
               </tr>
           <?php endforeach;?>
           <?php if(Helper_ViewPermission::isAudit()):?>   
           <?php if(MyApp::checkVisible('order-product')):?>
           <?php if(in_array($order->order_status,array('4','10','5','1','14')) ||  (in_array($order->order_status,array('12'))&&!$order->print_time)):?>
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
                							<?php endif;?>
                							<?php endif;?>
           </tbody>
        </table>
    </div>
    <?php if($product_copy != ''):?>
    <div class="span12">
        <h6 style="margin-top:0px;">原始产品信息 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;报关类型：<?php echo $order->declaration_type?> 申报总价：<span class="total_amount"><?php echo $order->total_amount?></span> 申报币种：<?php echo $order->currency_code?></h6>
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
                    <th style="width:30px;">带电</th>
              </tr>
           </thead>
           <tbody>
           <?php foreach ($product_copy as $v):?>
               <tr id="<?php echo $v->order_product_id?>">
                   <td ><?php echo $v->product_name_far?$v->product_name_far:$v->product_name?></td>
                   <td ><?php echo $v->product_name_en_far?$v->product_name_en_far:$v->product_name_en?></td>
                   <td ><?php echo $v->hs_code_far?$v->hs_code_far:$v->hs_code?></td>
                   <td ><?php echo $v->material_use?></td>
                   <td><?php echo $v->product_quantity?></td>
                   <td ><?php echo $v->product_unit1_far?></td>
                   <td ><?php echo $v->product_quantity1_far?></td>
                   <td ><?php echo $v->product_unit2_far?></td>
                   <td ><?php echo $v->product_quantity2_far?></td>
                   <td ><?php echo $v->declaration_price?></td>
                   <td><?php echo $v->has_battery?'是':''?></td>
               </tr>
           <?php endforeach;?>
           </tbody>
        </table>
    </div>
    <?php endif;?>
</form>
    <div class="span12">
    <div style="width:19%;float:left;clear:both">
        <h6 style="margin-top:0px;">客户预报包裹信息</h6>
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
               <?php foreach ($order->packages as $value):?>
               <tr>
                   <td style="text-align: right"><?php echo $value->quantity?></td>
                   <td style="text-align: right"><?php echo $value->length?></td>
                   <td style="text-align: right"><?php echo $value->width?></td>
                   <td style="text-align: right"><?php echo $value->height?></td>
                   <td style="text-align: right"><?php echo $value->weight?></td>
               </tr>
               <?php endforeach;?>
           </tbody>
        </table>
    </div>
    <div style="width:39%;float:left" class="in_table" id="handle-area">
        <h6 style="margin-top:0px;float:left;">FAR入库包裹信息</h6>
        <?php if ($order->order_status=='5'):?>
        <a class="btn btn-mini btn-info" href="javascript:void(0)" style="margin-left: 10px;float:left" onclick="add(this)">
    		<i class="icon-plus"></i>
    		添加行
    	</a>
    	<?php endif;?>
    	<div class="span3" style="margin-top:0px;margin-left:5px;float:left;width:55%">
        	<div class="row-fluid">
        		<div class="span4" style="">
        			<label>包裹袋</label>
        			<input style="width: 30px;" type="text" class="pak" value="<?php echo $fee['in_pak'];?>" />
        		</div>
        		<div class="span3" style="width:28%; margin-left:0px;">
        			<label>纸箱</label>
        			<input style="width: 30px;" type="text" class="box_quantity" value="<?php echo $fee['in_box'];?>" />
        		</div>
        		<div class="span3" style="width:28%; margin-left:0px;">
        			<label>异形</label>
        			<input style="width: 30px;" type="text" class="special" value="<?php echo $fee['in_special'];?>" />
        		</div>
        	</div>
	   </div>
	   <a class="btn btn-mini btn-info" style="float:left" href="<?php echo url('order/orderin', array('order_id' => $order->order_id,'pak'=>$fee['in_pak'],'box'=>$fee['in_box'],'special'=>$fee['in_special']))?>">导出</a>
        <table class="FarTable" style="width:98%;margin-top:-5px;float:left">
           <thead>
              <tr>
                  <th width="40px">数量</th>
                  <th width="40px">长</th>
                  <th width="40px">宽</th>
                  <th width="40px">高</th>
                  <th width="40px">实重</th>
                  <th width="60px">单件体积重</th>
                  <th width="60px">单件计费重</th>
                  <th width="60px">计费重小计</th>
                  <?php if ($order->order_status=='5'):?>
                  <th width="20px">操作</th>
                  <?php endif;?>
              </tr>
           </thead>
           <tbody>
                <?php $reweightarr = Helper_Quote::getweightarr($order,1);
                $product = Product::find('product_name=?',$order->service_code)->getOne();
                ?>
                <?php if(count($reweightarr['package'])>0):?>
                <?php foreach ($reweightarr['package'] as $package):?>
               <tr>
                   <td style="text-align: center;"><input type="text" style="width:90%;" class="quantity" value="<?php echo $package['quantity']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="length" value="<?php echo $package['length']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="width" value="<?php echo $package['width']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="height" value="<?php echo $package['height']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="weight" value="<?php echo $package['weight']?>"></td>

                   <td style="text-align: center"><?php echo $package['volumn_weight']?></td>
                   <td style="text-align: center"><?php echo $package['cost_weight']?></td>
                   <td style="text-align: center"><?php echo $package['total_cost_weight']?></td>
                   <?php if ($order->order_status=='5'):?>
                   <td><a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removed(this)"><i class="icon-remove"></i></a></td>
                   <?php endif;?> 
               </tr>
               <?php endforeach;?>
               <tr>
               		<?php if ($order->order_status=='5'):?>
               		<?php if(Helper_ViewPermission::isAudit()):?>
                    <td colspan="6">
                        <div style="line-height:25px;" id="savefar">
                        	<a class="btn btn-mini btn-primary" href="javascript:void(0)" onclick="save(this)" data="in"
                        		style="margin-right: 10px;">
                        		<i class="icon-save"></i>
                        		提交数据
                        	</a>
                    	</div>
                    </td>
                    <?php endif;?>
                    <?php else:?>
                    <td colspan="6"></td>
                    <?php endif;?>
                    <?php if($reweightarr['total_cost_weight']):?>
                    <td>总计：</td>
                    <td style="text-align: center;"><?php echo $reweightarr['total_cost_weight']?></td>
                    <?php endif;?>
                    <?php if ($order->order_status=='5'):?>
                    <td></td>
                    <?php endif;?>
               </tr>
               <?php endif;?>
           </tbody>
        </table>
     </div>
     <div style="width:39%;float:left" class="out_table">
        <h6 style="margin-top:0px;float:left">渠道包裹信息&nbsp;&nbsp;&nbsp;<?php echo $order->packing_type?></h6>
        <?php if ($order->order_status=='4' ):?>
        <a class="btn btn-mini btn-info" href="javascript:void(0)" style="margin-left: 10px;float:left" onclick="add(this)">
    		<i class="icon-plus"></i>
    		添加行
    	</a>
    	<?php endif;?>
    	<div style="margin-top:0px;float:right;width:60%">
        	<div class="row-fluid">
        		<div class="span4" style="">
        			<label>异形</label>
        			<input style="width: 30px;" type="text" class="special" value="<?php echo $fee['out_special'];?>" />
        		</div>
        		<?php if ($order->order_status=='6'):?>
        		<div class="span6" style="">
        			<label>渠道成本重</label>
        			<input style="width: 50px;" type="text" class="weight_cost_out" value="<?php echo $order->weight_cost_out;?>" />
        		</div>
        		<?php endif;?>
        		<a class="btn btn-mini btn-info" href="<?php echo url('order/orderqu', array('order_id' => $order->order_id,'special'=>$fee['out_special']))?>">导出预报数据</a>
    	   </div>
	   </div>
	   <table class="FarTable" style="width:98%;margin-top:-5px;float:left">
           <thead>
              <tr>
                  <th width="40px">数量</th>
                  <th width="40px">出库长</th>
                  <th width="40px">出库宽</th>
                  <th width="40px">出库高</th>
                  <th width="40px">出库实重</th>
                  <th width="60px">出库单件体积重</th>
                  <th width="60px">出库单件计费重</th>
                  <th width="60px">出库计费重小计</th>
                  <?php if ($order->order_status=='4'):?>
                  <th width="20px">操作</th>
                  <?php endif;?>
              </tr>
           </thead>
           <tbody>
               <?php $channel=Channel::find('channel_id=?',$order->channel_id)->getOne();?>
               <?php $weightarr = Helper_Quote::getweightarr($order,2)?>
               <?php if(count($weightarr['package'])>0):?>
               <?php foreach ($weightarr['package'] as $package):?>
               <tr>
                   <td style="text-align: center;"><input type="text" style="width:90%;" class="quantity" value="<?php echo $package['quantity_out']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="length" value="<?php echo $package['length_out']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="width" value="<?php echo $package['width_out']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="height" value="<?php echo $package['height_out']?>"></td>
                   <td style="text-align: center"><input type="text" style="width:90%;" class="weight" value="<?php echo $channel->type==5?round($package['weight_out'],3):round($package['weight_out'],2)?>"></td>
                   <?php if ($order->channel_id):?>
                   <td style="text-align: center"><?php echo $package['volumn_weight']?></td>
                   <td style="text-align: center"><?php echo $package['cost_weight']?></td>
                   <td style="text-align: center"><?php echo $package['total_cost_weight']?></td>
                   <?php else:?>
                   <td style="text-align: center"></td>
                   <td style="text-align: center"></td>
                   <td style="text-align: center"></td>
                   <?php endif;?>
                   <?php if ($order->order_status=='4'):?>
                   
                   <td><button class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removed(this)"><i class="icon-remove"></i></button></td>
                   <?php endif;?>
               </tr>
               <?php endforeach;?>
               <?php endif;?>
               <tr>
               		<?php if ($order->order_status=='4'):?>
               		<?php if(Helper_ViewPermission::isAudit()):?>
                    <td colspan="3">
                        <div style="line-height:25px;" id="saveqd">
                        	<a class="btn btn-mini btn-primary" href="javascript:void(0)" onclick="save(this)" data="out"
                        		style="margin-right: 10px;">
                        		<i class="icon-save"></i>
                        		提交数据
                        	</a>
                    	</div>
                    </td>
                    <?php endif;?>  
                    <?php else:?>
                    <td colspan="3"></td>
					<?php endif;?>   
                    <?php if ($order->channel_id):?>
	                    <?php if ($weightarr['total_label_weight']):?>
	                    <td>总计：</td>
	                    <td>预报实重：</td>
	                    <td style="text-align: center;"><?php echo $weightarr['total_label_weight']?></td>
	                    <?php endif;?>
	                    <?php if ($weightarr['total_cost_weight']):?>
	                    <td>出库计费重：</td>
	                    <td style="text-align: center;"><?php echo $weightarr['total_cost_weight']?></td>
	                    <?php endif;?>
	                    <?php if ($order->order_status=='4'):?>
	                    <td></td>
	                    <?php endif;?>
                    <?php endif;?>
               </tr>
           </tbody>
        </table>
     </div>
     </div>
</div>

<input type="hidden" id="package_quantity" value="<?php echo Farpackage::find('order_id=?',$order->order_id)->getSum('quantity');?>">
<input type="hidden" id="order_id" value="<?php echo $order->order_id;?>">
<input type="hidden" id="order_status" value="<?php echo $order->order_status;?>">
<input type="hidden" id="channel_id" value="<?php echo $order->channel_id;?>">
<input type="hidden" id="product_type" value="<?php echo $product->type?>">
<script type="text/javascript">
    function removed(obj){
		$(obj).parent().parent().remove();
    }
    function add(obj){
        var pks_str='<tr><td style="text-align: right"><input type="text" style="width:90%;" class="quantity" value=""></td><td style="text-align: right"><input type="text" style="width:90%;" class="length" value=""></td><td style="text-align: right"><input type="text" style="width:90%;" class="width" value=""></td>'+
            '<td style="text-align: right"><input type="text" style="width:90%;" class="height" value=""></td><td style="text-align: right"><input type="text" style="width:90%;" class="weight" value="">';
        pks_str += '<td></td><td></td></td><td></td>';
		pks_str += '<td><button class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="removed(this)"><i class="icon-remove"></i></button></td></tr>';
    	$(obj).parent().find('table>tbody').find('tr').eq(-1).before(pks_str);
    }
    //保存
    function save(obj){

    	if($(obj).attr('data')=='in'){
    		if($("#savefar a").hasClass('disabled')) return false;
        	$("#savefar a").addClass('disabled');
		}else{
			if($("#saveqd a").hasClass('disabled')) return false;
        	$("#saveqd a").addClass('disabled');
		}
		var json='';
		var flag=false;
		var table=$(obj).parent().parent().parent().parent();
		if(table.find('tr').slice(0, -1).length <=0){
			flag=true;
		}else{
			var package_quantity=0;
			table.find('tr').slice(0, -1).each(function(){
				if($(this).find('.quantity').val()=='' || $(this).find('.length').val()=='' || $(this).find('.width').val()=='' || $(this).find('.height').val()=="" ||  $(this).find('.weight').val()==""){
					flag=true;
					return false;
				}
				json+='{"quantity":"'+$(this).find('.quantity').val()
	    		+'","length":"'+$(this).find('.length').val()
	    		+'","width":"'+$(this).find('.width').val()
	    		+'","height":"'+$(this).find('.height').val()
	    		+'","weight":"'+$(this).find('.weight').val()+'"},';
				package_quantity+=parseInt($(this).find('.quantity').val());
			});
			if($(obj).attr('data')=='in'){
				json='{"packages":['+json.substring(0,json.length-1)+'],"pak":"'+$(".in_table").find('.pak').val()+'","box":"'+$(".in_table").find('.box_quantity').val()+'","special":"'+$(".in_table").find('.special').val()+'"}';
			}else{
				var weight_cost_out=0;
				if($(".out_table").find('.weight_cost_out').val()!=undefined){
					weight_cost_out=$(".out_table").find('.weight_cost_out').val();
				}
				json='{"packages":['+json.substring(0,json.length-1)+'],"special":"'+$(".out_table").find('.special').val()+'","weight_cost_out":"'+weight_cost_out+'"}';
			}
		}

		if($(obj).attr('data')=='in'){
			//纸箱和包裹袋数量校验
			var pak = Number($(".in_table").find('.pak').val());
			var box_quantity = Number($(".in_table").find('.box_quantity').val());
			var total = pak+box_quantity;
			var quantity = Number($('#package_quantity').val());
			if(total > quantity){
				$.messager.alert('', '包裹袋和纸箱的数量之和不能超出FAR入库包裹信息的包裹数量');
				if($(obj).attr('data')=='in'){
		        	$("#savefar a").removeClass('disabled');
				}
				return false;
			}
		}
// 		if($(obj).attr('data')=='in'){
// 			if($(".in_table").find('.pak').val() > $('#package_quantity').val()){
// 				$.messager.alert('', '包裹袋数量超出预报包裹总数');
// 				return false;
// 			}else if( $(".in_table").find('.box_quantity').val() > $('#package_quantity').val()){
// 				$.messager.alert('', '纸箱数量超出预报包裹总数');
// 				return false;
// 			}else if($(".in_table").find('.special').val() > $('#package_quantity').val()){
// 				$.messager.alert('', '异形数量超出预报包裹总数');
// 				return false;
// 			}
// 		}else{
// 			if($(".out_table").find('.special').val() > $('#package_quantity').val()){
// 				$.messager.alert('', '异形数量超出预报包裹总数');
// 				return false;
// 			}
// 		}
		if(flag){
			$.messager.alert('', '数据不完整，请完善数据');
			if($(obj).attr('data')=='in'){
	        	$("#savefar a").removeClass('disabled');
			}else{
				$("#saveqd a").removeClass('disabled');
			}
		}else{
			if(package_quantity!=$('#package_quantity').val()){
				$.messager.alert('', '包裹数量不一致');
				if($(obj).attr('data')=='in'){
		        	$("#savefar a").removeClass('disabled');
				}else{
					$("#saveqd a").removeClass('disabled');
				}
			}else if($(".out_table").find('.weight_cost_out').val()!=undefined && $(".out_table").find('.weight_cost_out').val()<=0){
				$.messager.alert('', '渠道成本重必须大于0');
				if($(obj).attr('data')=='in'){
		        	$("#savefar a").removeClass('disabled');
				}else{
					$("#saveqd a").removeClass('disabled');
				}
			}else{
				//保存数据
				$.ajax({
					url:'<?php echo url('/savepackages')?>',
					data:{packages:json,type:$(obj).attr('data'),order_id:$("#order_id").val()},
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
		}
		
    }
    $(function(){
    	// 录入过程处理
        $('#handle-area').on('blur change', '[class="weight"],[class="length"],[class="width"],[class="height"]', function (event) {
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
		$('#form').submit(function(){
			if($("input[name=tracking_no]").val()!=''){
				if($("#channel_id").val()==""){
					$.messager.alert('', '末端单号存在时渠道必填');
					return false;
				}
			}

			//判断偏远
		    if($("#consignee_city").val()!='' && $("#consignee_postal_code").val()!=''){
		    	var yisipy=true;
				$.ajax({
					url:'<?php echo url('/checkpy')?>',
					data:{order_id:$("#order_id").val(),consignee_city:$("#consignee_city").val(),consignee_postal_code:$("#consignee_postal_code").val(),consignee_state_region_code:$("#consignee_state_region_code").val(),consignee_street1:$("#consignee_street1").val(),consignee_street2:$("#consignee_street2").val()},
					type:'post',
					async:false,
					success:function(data){
						if(data=='success'){
							alert('偏远');
						}else if(data=='yisi'){
							alert('疑似偏远');
// 							yisipy = false;
						}
					}
				});
// 				if(!yisipy){
// 				   return false;
// 				}
			}
			//判断无服务邮编城市
			var noservic_state=true;
			$.ajax({
					url:'<?php echo url('/checknoservice')?>',
					data:{order_id:$("#order_id").val(),consignee_city:$("#consignee_city").val(),consignee_postal_code:$("#consignee_postal_code").val()},
					type:'post',
					async:false,
					success:function(data){
						if(data=='youbianwfw'){
							alert('邮编无服务');
							noservic_state=false;
						}else if(data=='chengshiwfw'){
							alert('城市无服务');
							noservic_state=false;
						}else if(data=='guojiawfw'){
							alert('国家无服务');
							noservic_state=false;
						}
					}
				})
				if(!noservic_state){
					return false;
				}

		    //关联的阿里单号检测
			if($("input[name=related_ali_order_no]").val()!=''){
				var related_ali_order_no=$("input[name=related_ali_order_no]").val();
				var ali_order_no= $("#ali_order_no").val();
				if(ali_order_no == related_ali_order_no){
					$.messager.alert('', '关联的阿里单号不能为当前阿里单号');
					return false;
				}
				var state=true;
				$.ajax({
					url:'<?php echo url('/checkrelatedaliorderno')?>',
					data:{related_ali_order_no:related_ali_order_no,order_id:$("#order_id").val()},
					type:'post',
					async:false,
					success:function(data){
						if(data!='success'){
							state=false;
							$.messager.alert('', '关联的阿里单号不存在');
						}
					}
				})
				if(!state){
					return false;
				}
			}
			if($('input[name="has_battery"]:checked').val()==1){
			    console.log($('input[name="has_battery_num"]:checked').val())
			    if(!$('input[name="has_battery_num"]:checked').val()){
					$.messager.alert('', '选择带电时带电产品数量必填');
					return false;
				}
		    }
			var hs_code='';
			$(".hs_code").each(function(){
				hs_code+=$(this).html()+',';
			})
			var flag=true;
			$.ajax({
				url:'<?php echo url('/checkhs')?>',
				data:{hs_code:hs_code},
				type:'post',
				async:false,
				success:function(data){
					if(data!='success'){
						flag=true;
						alert('HS编码'+data+'不正确,请留意');
					}
				}
			})
			return flag;
		})
		//上门取件
// 		if($("#need_pick_up").val()=='1'){
// 			$("#pick_company").attr('required','required');
// 			$("#need_pick_up").change(function(){
// 				if($("#need_pick_up").val()!='1'){
// 					$("#pick_company").removeAttr('required');
// 					$("#pick_company option[value='']").attr("selected","selected");
// 				}else{
// 					$("#pick_company").attr('required','required');
// 				}
// 			})
// 		}else{
			$("#need_pick_up").change(function(){
				if($("#need_pick_up").val()!='1'){
					$("#pick_company").removeAttr('required');
					$("#pick_company option[value='']").attr("selected","selected");
				}else{
					$("#pick_company").attr('required','required');
				}
			})
// 		}
					
		$('input[name="has_battery"]').click(function(){
	        var has_battery = $(this).val();
	        console.log(has_battery)
	        if(has_battery==1){
	   			$('#battnum').removeAttr('style');
	        }else{
	        	$('#battnum').attr('style','display:none');
	        }
	    })
    })
    /**
	 * 回调 删除数据
	 */
	function DeleteBefore(obj){
		$.ajax({
			url:"<?php echo url('order/productdel')?>",
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
			url:"<?php echo url('order/productsave')?>",
			type:"POST",
			data:{"order_id":"<?php echo $order->order_id?>",
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
</script>