<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    订单查询
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet"
	href="<?php echo $_BASE_DIR?>public/css/tablesorter.css">
<?php function showFN(){return request('parameters') =='warehouse_out' || request('parameters') =='wait_to_send'|| request('parameters') =='sent';} ?>
<style>
td{
     word-break: break-all;white-space:nowrap;
}
th{
     word-break: break-all;white-space:nowrap;
}
.tabs li a.tabs-inner{
	padding:0 5px;
}
.badge {
	padding-left:5px;
	padding-right:5px;
}
</style>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.browser.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.sound.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/supcan/binary/dynaload.js"></script>
	<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/dependencies/sha-256.min.js"></script>
<script type="text/javascript" src="<?php echo $_BASE_DIR?>qz/demo/js/qz-tray.js"></script>
<?php 
	$dem=Department::find('department_id=?',MyApp::currentUser('department_id'))->getOne();//获取所属仓库
?>
<div>
	<div style="height: 1px; width: 100%; visibility: hidden;">
		<SCRIPT type="text/javascript">insertReport('AF', 'CollapseToolbar=true');</SCRIPT>
	</div>
</div>
<form method="POST" id="searchForm" style="margin-bottom:0px;">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
					<?php
                            echo Q::control ( 'dropdownlist', 'timetype', array (
                            'items'=>array('1'=>'订单时间','2'=>'支付时间','3'=>'入库时间','4'=>'出库时间'),
                            'value' => request('timetype'),
                            'style'=>'width:80px'
                         ) )?>
				    </th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date')?>" style="width: 120px;">
					</td>
					<th>到</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="end_date"
						value="<?php echo request('end_date')?>" style="width: 120px;">
					</td>
					<th><?php
                            echo Q::control ( 'dropdownlist', 'ordertype', array (
                            'items'=>array('1'=>'(阿里/泛远/末端/总单/客户)单号','2'=>'子单号','3'=>'已退回支付原末端运单号'),
                            'value' => request('ordertype'),
                            'style'=>'width:180px'
                         ) )?>
				    </th>
					<td>   
					   <input name="order_no" type="text" style="width: 105px"
							value="<?php echo request('order_no')?>">
                    </td>
					<th>快递单号</th>
					<td>
						<input name="reference_no" type="text" style="width: 105px"
							value="<?php echo request('reference_no')?>">
					</td>
					<th>目的国</th>
					<td>
						<input name="consignee_country_code" type="text" style="width: 30px"
							value="<?php echo request('consignee_country_code')?>">
					</td>
					<th style="margin-left: 10px">
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
							<i class="icon-download"></i>
							导出
						</button>
                        <a class="btn btn-small btn-info" href="javascript:void(0);" onclick="$('#dialog_search').dialog('open');$('.window-shadow').css('top','106px');$('.panel').css('top','106px');$('#dialog_search').removeClass('hide');"> 高级搜索 </a>
                        <a class="btn btn-small btn-danger" target="_blank" onclick="batch_export_label()">
							批量导出面单
						</a>
				  	</th>
				</tr>           
			</tbody>           
		</table>  
	</div>
	<div id="batch_issue" class="easyui-dialog hide"title="批量扣件"
		data-options="closed:true, modal:true"
		style="width: 40%; height: 200px;">
		<table>
			<tbody>
				<tr>
				    <th>问题类型</th>
				    <td>
				    	<label><input type="radio" name="issue_type" value="1" > 取件异常件</label>
				        <label><input type="radio" name="issue_type" value="2" checked> 库内异常件</label>
				        <label><input type="radio" name="issue_type" value="3"> 渠道异常件</label>
				    </td>
				</tr>
				<tr id="wu" style="display:none">
					<th>截止时间</th>
					<td>
						<?php
						echo Q::control ( "datebox", "deadline", array (
							"value" => request ( "deadline" ),
							"style"=>"width:80px"
						) )?>
					</td>
				</tr>
				<tr>
				    <th>详情</th>
				    <td>
				        <textarea id="issue_detail" name="detail" rows="3" Style="width: 400px"></textarea>
				    </td>
				</tr>
			</tbody>
		</table>
    	<div class="FarTool text-center">
    		<a target="_blank" class="btn btn-primary" onclick="ajaxbatch_issue()">
    			确定
    		</a>
    	</div>
	</div>
	<div id="emil" class="easyui-dialog hide"title="批量发送邮件"
		data-options="closed:true, modal:true"
		style="width: 30%; height: 100px;">
		<table>
			<tbody>
				
				
				<tr>
				    <th>邮件模板</th>
				    <td>
				        <?php
				        $items = Helper_Array::toHashmap(EmailTemplate::find('product_id=0')->getAll(), "id", "template_name");
						echo Q::control("dropdownbox", "emil_id", array(
							"items" => $items,
							"style" => "width: 150px",
						))?>
						<a target="_blank" class="btn btn-primary" onclick="ajaxemil()">
			    			确定
			    		</a>
				    </td>
				</tr>
			</tbody>
		</table>
    	<div class="FarTool text-center">
    		
    	</div>
	</div>
	<div id="dialog_search" class="easyui-dialog hide"title="高级搜索"
		data-options="closed:true, modal:true"
		style="width: 65%; height: 395px;">
		<div class="span4">
        <table class="FarTable">
        	  <tr>
                    <th>(阿里/泛远/末端/总单/客户/子/国内)单号</th>
					<td>   
                        <textarea id="waybill_codes" name="waybill_codes" rows="14" placeholder="每行一个单号"
							style="width: 90%"><?php echo request("waybill_codes")?></textarea>
					</td>
              </tr>
        </table>
        <table>
        <tr>
		    <td>
		      <button class="btn btn-primary" type="submit" onclick="waybillSearch()" style="margin-left: 150px">
					<i class="icon-search"></i>
					搜索
				</button>
			</td>
		</tr>
		</table>		
        </div>
		<div class="span4">
        <table class="FarTable">
        	  <tr>
                  <th style="width: 100px"><input type="checkbox" id="need_pick_up" name="need_pick_up" value="1" <?php echo request("need_pick_up") != null ? "checked='checked'" : "" ?>>上门取件</th>
    			  <th style="padding-left: 10px"><input  type="checkbox" id="negative_profit" name="negative_profit" value="1" <?php echo request("negative_profit") != null ? "checked='checked'" : ""?>>负毛利</th>
              </tr>
              <tr>
                  <th>部门</th>
                  <td><?php
                        echo Q::control ( 'myselect', 'department_id', array (
                        'items'=>RelevantDepartment::relateddepartments(),
                        'selected' => @$department_id?$department_id:array(),
                        'style' => 'width:135px',
                        'multiple' => 'multiple'
                        ) )?>
                   </td>
              </tr>
              <tr>
                  <th>产品</th>
                  <td><?php
                        echo Q::control ( 'myselect', 'service_code', array (
                        	'items'=>Product::find('product_id in (?)',Productdepartmentavailable::availableproductids())->getAll()->toHashMap('product_name','product_chinese_name'),
                        	'selected' => @$service_code?$service_code:array(),
	                        'style' => 'width:135px',
	                        'multiple' => 'multiple'
                        ) )?>
                   </td>
              </tr>
              <tr>
                  <th>客重</th>
                  <td><input style="width: 50px" type="text" id="weight_cost_out_start" name="weight_cost_out_start"  value="<?php echo  request('weight_cost_out_start')?>">
                        -
                        <input style="width: 50px" type="text" id="weight_cost_out_end" name="weight_cost_out_end"  value="<?php echo  request('weight_cost_out_end')?>"  >
                   </td>
              </tr>
              <tr>
                  <th>包裹类型</th>
                  <td><?php
                        echo Q::control ( 'dropdownbox', 'packing_type', array (
                        	'items'=>array('BOX'=>'BOX','DOC'=>'DOC','PAK'=>'PAK'),
                        	'empty'=>true,
                        	'value' => request('packing_type'),
                        ) )?>
                   </td>
              </tr>
              <tr>
                  <th>网络</th>
                  <td><?php
                    echo Q::control ( 'dropdownbox', 'network_code', array (
                    	'items' => Channel::find()->setColumns('network_code')->getAll()->toHashMap('network_code','network_code'),
                            'empty'=>true,
                            'value'=>request('network_code'),
                    ) )?>
                  </td>
              </tr>
              <tr>
                  <th>渠道</th>
                  <td><?php
                    echo Q::control ( 'myselect', 'channel_id', array (
                    		'items' =>Channel::find('channel_id in (?)',Channeldepartmentavailable::availablechannelids())->getAll()->toHashMap('channel_id','channel_name'),
                    		'selected' => @$channel_id?$channel_id:array(),
	                    	'style' => 'width:135px',
	                    	'multiple' => 'multiple'
                    ) )?>
                  </td>
              </tr>
              <!-- <tr>
                  <th>供应商</th>
                  <td><?php
                    echo Q::control ( 'dropdownbox', 'supplier_id', array (
                    	'items' =>Supplier::find()->getAll()->toHashMap('supplier_id','supplier'),
                        'empty'=>true,
                        'value'=>request('supplier_id'),
                    ) )?>
                  </td>
              </tr>-->
              <tr>
                  <th>报关类型</th>
                  <td><?php
                    echo Q::control('dropdownbox','declaration_type',
                    array(
                    	'items' =>array('QT'=>'QT','DL'=>'DL'),
                    	'empty' => true,
                    	'value' => request('declaration_type'),
                    ) )?>
                    </td>
              </tr>
              <tr>
                  <th>发件人信息</th>
                  <td>
                  <input style="width: 250px" type="text" id="sender" name="sender"  value="<?php echo  request('sender')?>">
                  </td>
              </tr>
              <tr>
                  <th>入库部门</th>
                  <td><?php
                  		$departments=array();
                  		$department = Department::find ( 'department_id=?', MyApp::currentUser ( 'department_id' ) )->getOne ();
                  		if ($department->department_name == '连云港仓') {
                  			$departments=RelevantDepartment::relateddepartments();
                  		}else{
                  			$departments=Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name');
                  		}
                        echo Q::control ( 'dropdownbox', 'warehouse_in_department_id', array (
                        'items'=>$departments,
                        'empty'=>true,
                        'style'=>'width:130px',
                        'value' => request('warehouse_in_department_id'),
                        ) )?>
                   </td>
              </tr>
              <tr>
                    <th>目的国</th>
					<td>
						<input id='consignee_country_code1' name="consignee_country_code1" type="text" style="width: 30px"
							value="<?php echo request('consignee_country_code1')?>">
					</td>
              </tr>
              <tr>
                    <th>是否带电</th>
					<td>
						<?php
                    echo Q::control('dropdownbox','has_battery1',
                    array(
                    'items' => array(1=>'是',2=>'否'),
                    'empty' => true,
                    'value' => request('has_battery'),
                    ) )?>
					</td>
              </tr>
              <tr>
                    <th>客户</th>
					<td>
						<?php
                    echo Q::control('myselect','customer_id1',
                    array(
                    	'items' => Customer::find()->getAll()->toHashMap('customer_id','customer'),
                    	'selected' => @$checked?$checked:array(),
                    	'style' => 'width:135px',
                    	'multiple' => 'multiple'
                    ) )?>
					</td>
              </tr>
        </table>
    </div>
    </div>
    <div id="dialog_save" class="easyui-dialog hide"title="复制订单"
		data-options="closed:true, modal:true"
		style="width:450px; height: 200px;">
		<div class="span4">
        <table class="FarTable">
        	  <tr>
                    <th class="required-title">新订单号</th>
					<td><input type="text" required="required" id="new_ali_order_no" name="new_ali_order_no" value="<?php request('new_ali_order_no')?>"></td>
              </tr>
              <tr>
              		<th class="required-title">客户</th>
					<td><?php
                    echo Q::control ( 'dropdownbox', 'customer_id', array (
                    	'items' => Customer::find()->getAll()->toHashMap('customer_id','customer'),
                            'empty'=>true,
                            'required'=>"required",
                            'value'=>request('supplier_id'),
                    ) )?></td>
              </tr>
              <tr>
              		<th>部门</th>
					<td><?php
                    echo Q::control ( 'dropdownbox', 'copy_department_id', array (
                    		'items' =>$dpms,
                            'empty'=>true,
                            'value'=>request('copy_department_id'),
                    ) )?></td>
              </tr>
        </table>
        <table>
        <tr>
		    <td>
		      <button class="btn btn-primary" type="submit" onclick="savenew_ali()" style="margin-left: 150px">
					保存
				</button>
			</td>
		</tr>
		</table>		
        </div>
    </div>
    <input type="hidden" id='hidden_order_id' value="">
    <input type="hidden" id='hidden_new_ali_order_no' value="">
    <input type="hidden" id='hidden_customer_id' value="">
    <div>
    <?php if(!request('parameters')):?>
    <?php if(Helper_ViewPermission::isAudit()):?>
   
		<a class="btn btn-danger" target="_blank" onclick="batch_issue()" style="margin: 5px 5px">
			批量扣件
		</a>
		<a class="btn btn-info" target="_blank" onclick="batch_release()">
			批量解扣
		</a>
		<a class="btn btn-danger" target="_blank" onclick="emil()" style="margin: 5px 5px">
			批量发邮件
		</a>
		<a class="btn btn-info" target="_blank" onclick="batch_gettrace()">
			批量重查轨迹
		</a>
		<?php if(MyApp::checkVisible('order-return-true')):?>
		<a class="btn btn-danger" target="_blank" onclick="batch_affirm()">
			批量确认轨迹
		</a>
		<?php endif;?>
	<?php endif;?>
	<?php endif;?>
	<?php if(request('parameters') == 'sent'):?>
	<?php if(Helper_ViewPermission::isAudit()):?>
		<a class="btn btn-danger" target="_blank" onclick="other()" style="margin: 5px 5px">
			批量转其它
		</a>
		<?php if(MyApp::checkVisible('order-transfer-sign')):?>
		<a class="btn btn-info" target="_blank" onclick="transfersign()" style="margin: 5px 5px">
			批量转已签收
		</a>
	<?php endif;?>
	<?php endif;?>
	<?php endif;?>
	<?php if(request('parameters') == 'prove'):?>
		<?php if(Helper_ViewPermission::isAudit()):?>
			<?php if(MyApp::checkVisible('order-transfer-paid')):?>
					<a class="btn btn-danger" target="_blank" onclick="transferpaid()" style="margin: 5px 5px">
						批量转已支付
					</a>
			<?php endif;?>
		<?php endif;?>
	<?php endif;?>	
	<?php if(request('parameters') =='warehouse_out' || request('parameters') =='wait_to_send' || request('parameters') == 'sent') :?>
    	<?php if(Helper_ViewPermission::isAudit()):?>
	    	<?php if(MyApp::checkVisible('order-return-paid')):?>
		    	<a class="btn btn-info" target="_blank" onclick="returnpaid()" style="margin: 5px 5px">
					批量转已支付
				</a>
			<?php endif;?>
		<?php endif;?>
    <?php endif;?>                       
	<?php if(request('parameters') == 'paid'):?>
	<?php if(Helper_ViewPermission::isAudit()):?>
	<?php if(MyApp::checkVisible('order-return-checked')):?>
		<a class="btn btn-danger" target="_blank" onclick="returnchecked()" style="margin: 5px 5px">
			批量转已核查
		</a>
	<?php endif;?>
	<?php endif;?>
	<?php endif;?>
	</div>
	<div class="tabs-container " style="min-width: 1148px;border-bottom:0px;margin-bottom: 10px">
       <?php
		echo Q::control ( "tabs", "description", array (
			"tabs" => $tabs,"active_id" => $active_id 
		) );
		?>
		<div class="tabs-panels">
			<div class="panel-body panel-body-noheader panel-body-noborder"
				style="padding: 0px;">
            </div>
		</div>
		<div style="width: 100%;overflow: scroll;" id='table-cont'>
			<table id="myTable" class="FarTable tablesorter" style="max-width: 7100px;margin-top:0px;">
            		<thead>
            			<tr>
            				<th class="x_scroll x_scroll_th"><input type="checkbox" onchange="selectall(this)"></th>
            				<th class="x_scroll x_scroll_th">No</th>
            				<th class="x_scroll x_scroll_th">操作</th>
            				<th class="x_scroll x_scroll_th">阿里订单号</th>
            				<th>部门</th>
            				<?php if (request('parameters') =='hold') :?>
            				<th>扣外状态</th>
            				<?php endif?>
            				<?php if(request('parameters') =='' || request('parameters') =='hold'):?>
            				<th>状态</th>
            				<?php endif?>
            				<?php if (request('parameters') =='hold') :?>
            				<th>问题</th>
            				<?php endif?>
            				<?php if (request('parameters') =='') :?>
            				<th>泛远单号</th>
            				<th>客户单号</th>
            				<?php endif?>
            				<?php if(request('parameters') =='' || showFN() || request('parameters') =='sign'):?>
            				<th>末端运单号</th>
            				<?php endif?>
            				<?php if(request('parameters') =='' || request('parameters') =='wait_to_send' || request('parameters') =='sent' || request('parameters') =='sign'):?>
            				<th>总单单号</th>
            				<?php endif?>
        					<th>订单时间</th>
        					<?php if(request('parameters') =='' || request('parameters')== 'no_package'):?>
                            <th>客户</th>
        					<th>销售产品</th>
        					<?php endif;?>
            				<?php if (request('parameters') =='' || request('parameters') =='warehouse_in' || request('parameters') =='prove' || request('parameters') =='hold') :?>
                            <th>入库时间</th>
                            <?php endif?>
                            <?php if(request('parameters') == 'warehouse_in'):?>
                            <th>客户</th>
                            <th>销售产品</th>
                            <?php endif;?>
                            <?php if (request('parameters') =='') :?>
                            <th>入库人</th>
                            <?php endif?>
                            <?php if (request('parameters') =='' || request('parameters') =='prove' || request('parameters') =='paid' || request('parameters') =='hold') :?>
                            <th>核查时间</th>
                            <?php endif?>
                            <?php if(request('parameters') == 'prove'):?>
                            <th>客户</th>
                            <th>销售产品</th>
                            <?php endif;?>
                            <?php if (request('parameters') =='') :?>
                            <th>核查人</th>
                            <?php endif?>
                            <?php if (request('parameters') =='' || request('parameters') =='paid' || showFN() || request('parameters') =='hold' || request('parameters') =='termination') :?>
                            <th>支付时间</th>
                            <?php endif?>
                            <?php if(request('parameters') == 'paid'):?>
                            <th>客户</th>
                            <th>销售产品</th>
                            <?php endif;?>
                            <?php if(request('parameters') =='' || showFN() || request('parameters') =='sign' || request('parameters') =='termination'):?>
                            <th>出库时间</th>
                            <?php endif?>
            				<?php if (request('parameters') =='') :?>
                            <th>出库人</th>
                            <?php endif?>
                            <?php if(request('parameters') =='' || request('parameters') =='sent'):?>
                            <th>预派时间</th>
                            <?php endif;?>
                            <?php if (request('parameters') =='' || request('parameters') =='sign') :?>
                            <th>签收时间</th>
                            <?php endif?>
            				<?php if (request('parameters') =='' || request('parameters') =='sign') :?>
                            <th>妥投天数</th>
                            <?php endif?>
            				<?php if (request('parameters') =='' || showFN() || request('parameters') =='termination') :?>
                            <th>最新轨迹</th>
                            <th>地点</th>
                            <th>最新轨迹时间</th>
                            <?php endif?>
            				<?php if (request('parameters') !='hold') :?>
            				<th>问题</th>
            				<?php endif?>
            				<?php if (request('parameters') =='cancel') :?>
            				<th>取消原因</th>
            				<?php endif?>
            				<?php if (request('parameters') =='wait_to_return' || request('parameters') =='returned') :?>
            				<th>退货原因</th>
            				<?php endif?>
            				<?php if (request('parameters') =='returned') :?>
            				<th>退货时间</th>
            				<th>快递公司</th>
            				<th>快递单号</th>
            				<?php endif?>
            				<?php if(request('parameters') =='' || showFN() || request('parameters') =='sign' || request('parameters') =='termination'):?>
                            <th>网络</th>
                            <?php endif?>
            				<th>目的地</th>
            				<?php if(!(request('parameters') =='no_package' || request('parameters') =='warehouse_in' || request('parameters') =='cancel' || request('parameters') =='returned' || request('parameters') =='wait_to_return')):?>
            				<th>应收偏远</th>
            				<?php endif?>
            				<?php if (request('parameters') =='') :?>
    						<th>包裹类型</th>
    						<?php endif?>
    						<th>件数</th>
    						<?php if (request('parameters') =='') :?>
    						<?php if ($dem->status==0) :?>
            				<th>收入</th>
            				<th>成本</th>
            				<th>毛利</th>
            				<th>毛利率</th>
            				<?php endif;?>
            				<th>收货实重</th>
            				<th>收货体积重</th>
                            <th>收货计费重</th>
                            <th>预报实重</th>
                            <?php endif?>
                            <?php if (request('parameters') =='') :?>
                            <th>出货实重</th>
                            <th>账单重量</th>
                            <th>出货体积重</th>
                            <?php endif?>
                            <?php if (request('parameters') =='' || showFN() || request('parameters') =='sign') :?>
                            <th>计费重</th>
                            <?php endif?>
                            <?php if(request('parameters') =='' || showFN() || request('parameters') =='sign'):?>
                            <?php if(!(request('parameters') == '' || request('parameters') == 'no_package' || request('parameters') == 'warehouse_in' || request('parameters') == 'prove' || request('parameters') == 'paid')):?>
                            <th>客户</th>
                            <th>销售产品</th>
                            <?php endif;?>
                            <th>出货渠道</th>
                            <th>供应商</th>
                            <?php endif?>
                            <?php if (!(showFN() || request('parameters') =='sign')) :?>
                            <th>申报总价</th>
                            <?php endif?>
                            <th>报关</th>
                            <?php if (request('parameters') =='') :?>
                            <th>发件公司</th>
                            <th>发件人</th>
                            <th>发件人电话</th>
                            <th>发件人邮箱</th>
                            <th>发件地址</th>
                            <th>收件公司</th>
                            <th>收件人</th>
                            <th>收件人电话</th>
                            <th>收件人邮箱</th>
                            <th>收件人城市</th>
                            <th>收件人邮编</th>
                            <th>收件地址</th>
                            <?php endif?>
            				<?php if (!(request('parameters') =='sign' || request('parameters') =='warehouse_in' || request('parameters') =='prove' || request('parameters') =='paid' || showFN())) :?>
            				<th>上门取件</th>
            				<?php endif?>
            				<?php if (request('parameters') =='') :?>
                            <th>国内快递单号</th>
                            <?php endif?>
                            <th>订单备注</th>
            			</tr>
            		</thead>
            		<tbody>
            		<?php $i=1;$status=Order::$status?>
            		<?php foreach ($orders as $order):?>
            			<tr>
            				<td class="x_scroll x_scroll_td"><input type="checkbox" class="ids" name="ids[]" value="<?php echo $order->order_id?>"></td>
            				<td class="x_scroll x_scroll_td"><a href="javascript:void(0)" onClick="farbilpdf(<?php echo $order->order_id?>)"><?php echo $i++ ?></a></td>
            				
            				<td class="x_scroll x_scroll_td">
            				<?php if($order->customer->customs_code=="ALCN"):?>
            					 <a class="btn btn-mini btn-primary" target="_blank" href="<?php echo url('order/cainiaoevent', array('order_id' => $order->order_id))?>">
            						<span>事件</span>    
            					</a>
            				<?php else:?>
            					<?php  $is_refund = Event::find('order_id=? and ((event_code in ("WAREHOUSE_OUTBOUND","DELIVERY") and ifnull(reason,"")!="") or event_code="DELIVERY_TO_FLIGHT") and send_flag=1',$order->order_id)->getOne();?>
            				    <a class="btn btn-mini btn-primary" target="_blank" href="<?php echo url('order/event', array('order_id' => $order->order_id))?>">
            						<?php if (!$is_refund->isNewRecord()){?>
            						<span style="color:#FF8800">事件</span>
            						<?php }else{?>
            						<span>事件</span>
            						<?php }?>
            					</a>
            				<?php endif;?>	
            					<a class="btn btn-mini btn-info" target="_blank" href="<?php echo url('order/trace', array('order_id' => $order->order_id))?>">
            						轨迹
            					</a>
            					<?php if(Helper_ViewPermission::isAudit()):?>
            					<?php if(request('parameters') ==''):?>
            					<a id='copy_order_id' class="btn btn-mini btn-success" href="javascript:void(0);" onclick="$('#dialog_save').dialog('open');$('.window-shadow').css('top','106px');$('.panel').css('top','106px');$('#dialog_save').removeClass('hide');copy('<?php echo $order->order_id?>','<?php echo $order->ali_order_no?>')">复制 </a>
            					<?php endif;?>
            					<a class="btn btn-mini btn-danger" target="_blank" href="<?php echo url('order/orderreturn', array('ali_order_no' => $order->ali_order_no,'return_id'=>''))?>">
            						退件
            					</a>
            					<?php if ($order->order_status=='12'):?>
            					<a class="btn btn-mini btn-info" href="<?php echo url('order/release', array('order_id' => $order->order_id,'parameters' => request('parameters')))?>">
				                                                            解扣
				       		    </a>
				       		    <?php endif;?>
				       		    <?php endif;?>
            				</td>
            				<td class="x_scroll x_scroll_td">
            				    <?php if($order->getACount()>0):?>
            				    <a  target="_blank" style="color: #FF0000;font-weight:bold"
            					    href="<?php echo url('order/detail', array('order_id' => $order->order_id))?>">
        					    <?php elseif ($order->getBCount()>0):?>
        					    <a  target="_blank" style="color: #00FF00;font-weight:bold"
            					    href="<?php echo url('order/detail', array('order_id' => $order->order_id))?>">
        					    <?php else :?>
        					    <a  target="_blank"
            					    href="<?php echo url('order/detail', array('order_id' => $order->order_id))?>">
        					    <?php endif;?>
            					    <?php echo $order->ali_order_no ?>
            					</a>
            				</td>
            				<td>
            					<?php echo $order->department_id?$dpms[$order->department_id]:''?>
            				</td>
            				<?php if (request('parameters') =='hold') :?>
            				<td><?php echo $status[$order->order_status_copy]?></td>
            				<?php endif;?>
            		        <?php if(request('parameters') =='' || request('parameters') =='hold'):?>
            				<td ><?php echo $status[$order->order_status]?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='hold') :?>
            				<td>
            					<?php if($order->getBCount()>0):?>
            					    <?php if($order->getBCount() != '1'):?>
                					<a target="_blank" <?php if($order->getACount() == 0):?> style="color:#C0C0C0;" <?php endif;?> href="<?php echo url('/issue',array('ali_order_no'=>$order->ali_order_no,'parcel_flag'=>0))?>">
                					<?php else : $issue=Abnormalparcel::find('ali_order_no = ?',$order->ali_order_no)->getOne();?>
                					<a target="_blank" <?php if($order->getACount() == 0):?> style="color:#C0C0C0;" <?php endif;?> href="<?php echo url('/issuehistory',array('abnormal_parcel_id'=>$issue->abnormal_parcel_id))?>">
                					<?php endif;?>
                					<?php echo $order->getBCount()?$order->getBCount():''?>
            					    </a>
                				<?php endif;?>
            				</td>
            				<?php endif;?>
            				<?php if (request('parameters') =='') :?>
            				<td><a href="javascript:void(0)" data="<?php echo $order->ali_order_no?>" account="<?php echo $order->account?>" onclick="printlabel(this,3)"><?php echo $order->far_no?></a></td>
            				<td><?php echo $order->order_no?></td>
            				<?php endif;?>
            				<?php $subcodes=Subcode::find('order_id=?',$order->order_id)->asArray()->getAll();?>
            				<?php $code = Subcode::find('sub_code=?',$order->tracking_no)->getOne();//判断主单号在sub_code表里是否存在其?>
            				<?php if(request('parameters') =='wait_to_send'|| request('parameters') =='sent' || request('parameters') =='sign'):?>         				
            				<td id="deal_info_one<?php echo $order->order_id?>">
                                <?php if($order->channel->network_code=="EMS" || $order->channel->network_code=="USPS"):?>
                                <a target="_blank" href="https://t.17track.net/en#nums=<?php echo $order->tracking_no?>">
                                <?php elseif($order->channel->network_code=="FEDEX" || $order->channel->trace_network_code=="FEDEX") :?>
                                <a target="_blank" href="https://www.51tracking.com/cn/<?php echo $order->tracking_no?>">
                                <?php elseif($order->channel->trace_network_code=="DHL") :?>
                                <a target="_blank" href="https://www.dhl.com/en/express/tracking.html?AWB=<?php echo $order->tracking_no?>&brand=DHL">
                                <?php elseif($order->channel->trace_network_code=="DHLE") :?>
                                <a target="_blank" href="https://ecommerceportal.dhl.com/track/?locale=en">
                                <?php else :?>
                                <a target="_blank" href="https://www.ups.com/track?loc=en_US&tracknum=<?php echo $order->tracking_no?>&requester=WT/trackdetails">
                                <?php endif;?>
                                <?php echo $order->tracking_no?>
                                </a>
                                <?php if (count($subcodes)>1 || (count($subcodes)&&$code->isNewRecord()&&$order->service_code=='CNUS-FY')):?>
                                	<a style="text-decoration:none" onclick="opendeal(<?php echo $order->order_id?>)">展开</a>
                                <?php endif;?>
                            </td>
                            <td id="deal_info_box<?php echo $order->order_id?>" style="display: none">
            					<?php if (count($subcodes)>0){
            					    foreach ($subcodes as $p){?>
            					    	<?php echo $p['sub_code']?><br>
            						<?php }?>
            						<a style="text-decoration:none" onclick="closedeal(<?php echo $order->order_id?>)">收缩</a>
            					<?php }?>
        					</td>
					
            				<?php endif;?>
            				<?php if(request('parameters') =='' || request('parameters') =='warehouse_out'):?>
            				<td id="deal_info_one<?php echo $order->order_id?>">
								<a href="javascript:void(0)" data="<?php echo $order->ali_order_no?>" account="<?php echo $order->account?>" onclick="printlabel(this,2)">
									<?php echo $order->tracking_no?>
								</a>
            					<?php if (count($subcodes)>1 || count($subcodes)&&($code->isNewRecord()&&$order->service_code=='CNUS-FY')):?>
                                	<a style="text-decoration:none" onclick="opendeal(<?php echo $order->order_id?>)">展开</a>
                                <?php endif;?>
            				</td>
            				<td id="deal_info_box<?php echo $order->order_id?>" style="display: none">
            					<?php if (count($subcodes)>0){
            					    foreach ($subcodes as $p){?>
            					    	<?php echo $p['sub_code']?><br>
            						<?php }?>
            						<a style="text-decoration:none" onclick="closedeal(<?php echo $order->order_id?>)">收缩</a>
            					<?php }?>
        					</td>
            				<?php endif;?>
            				<?php if(request('parameters') =='' || request('parameters') =='wait_to_send' || request('parameters') =='sent' || request('parameters') =='sign'):?>
            				<td><?php echo $order->total_list_no?></td>
            				<?php endif?>
            				<td align="center" title="<?php echo Helper_Util::strDate('m-d H:i:s', $order->create_time)?>"><?php echo Helper_Util::strDate('m-d H:i', $order->create_time)?></td>
            				<?php if (request('parameters') =='' || request('parameters')== 'no_package') :?>
            				<td><?php echo $order->customer->customer?></td>
            				<td align="center"><?php echo $order->service_product->product_chinese_name ?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='' || request('parameters') =='warehouse_in' || request('parameters') =='prove' || request('parameters') =='hold') :?>
            				<td align="center"><?php echo Helper_Util::strDate('m-d H:i', $order->far_warehouse_in_time)?></td>
            				<?php endif;?>
            				<?php if(request('parameters') == 'warehouse_in'):?>
            				<td><?php echo $order->customer->customer?></td>
                            <td align="center"><?php echo $order->service_product->product_chinese_name ?></td>
                            <?php endif;?>
            				<?php if (request('parameters') =='') :?>
            				<td><?php echo $order->far_warehouse_in_operator?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='' || request('parameters') =='prove' || request('parameters') =='paid' || request('parameters') =='hold') :?>
            				<td align="center" <?php echo time()-$order->warehouse_confirm_time>86400 && empty($order->payment_time)?'style="color:red;"':''?>><?php echo Helper_Util::strDate('m-d H:i', $order->warehouse_confirm_time)?></td>
            				<?php endif;?>
            				<?php if(request('parameters') == 'prove'):?>
            				<td><?php echo $order->customer->customer?></td>
                            <td align="center"><?php echo $order->service_product->product_chinese_name ?></td>
                            <?php endif;?>
                            <?php if (request('parameters') =='') :?>
            				<td><?php echo Event::find('order_id=? and event_code="CONFIRM"',$order->order_id)->getOne()->operator?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='' || request('parameters') =='paid' || showFN() || request('parameters') =='hold' || request('parameters') =='termination') :?>
            				<td align="center"><?php echo Helper_Util::strDate('m-d H:i', $order->payment_time)?></td>
            				<?php endif;?>
            				<?php if(request('parameters') == 'paid'):?>
            				<td><?php echo $order->customer->customer?></td>
                            <td align="center"><?php echo $order->service_product->product_chinese_name ?></td>
                            <?php endif;?>
            				<?php if(request('parameters') =='' || showFN() || request('parameters') =='sign' || request('parameters') =='termination'):?>
            				<td align="center"><?php echo Helper_Util::strDate('m-d H:i', $order->warehouse_out_time)?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='') :?>
            				<td><?php echo Event::find('order_id=? and event_code="WAREHOUSE_OUTBOUND"',$order->order_id)->getOne()->operator?></td>
            				<?php endif;?>
            				<?php if(request('parameters') =='' || request('parameters') =='sent'):?>
                            <td align="center"><?php echo Helper_Util::strDate('m-d H:i', $order->present_time)?></td>
                            <?php endif;?>
            				<?php if (request('parameters') =='' || request('parameters') =='sign') :?>
            				<td align="center"><?php echo Helper_Util::strDate('m-d H:i', $order->delivery_time)?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='' || request('parameters') =='sign') :?>
            				<td><?php if($order->delivery_time && $order->carrier_pick_time):?><?php echo round((($order->delivery_time-$order->carrier_pick_time)/86400),1)?><?php endif;?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='' || showFN() || request('parameters') =='termination') :?>
            				<?php $route=Route::find('tracking_no=?',$order->tracking_no)->order('time desc')->getOne()?>
            				<td><?php echo $route->description?></td>
            				<td><?php echo $route->location?></td>
            				<td align="center"><?php echo Helper_Util::strDate('m-d H:i', $route->time)?></td>
            				<?php endif;?>
            				<?php if (request('parameters') !='hold') :?>
            				<td>
            				    <?php if($order->getBCount()>0):?>
            					    <?php if($order->getBCount() != '1'):?>
                					<a target="_blank" <?php if($order->getACount() == 0):?> style="color:#C0C0C0;" <?php endif;?> href="<?php echo url('/issue',array('ali_order_no'=>$order->ali_order_no,'parcel_flag'=>0))?>">
                					<?php else : $issue=Abnormalparcel::find('ali_order_no = ?',$order->ali_order_no)->getOne();?>
                					<a target="_blank" <?php if($order->getACount() == 0):?> style="color:#C0C0C0;" <?php endif;?> href="<?php echo url('/issuehistory',array('abnormal_parcel_id'=>$issue->abnormal_parcel_id))?>">
                					<?php endif;?>
                					<?php echo $order->getBCount()?$order->getBCount():''?>
            					    </a>
                				<?php endif;?>
            				</td>
            				<?php endif;?>
                           
            				<?php if (request('parameters') =='cancel') :?>
            				<td><?php echo $order->reason_name?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='wait_to_return' || request('parameters') =='returned') :?>
            				<td><?php echo $order->reason_name?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='returned') :?>
            				<?php $return_order=Returned::find('ali_order_no=?',$order->ali_order_no)->getOne()?>
            				<td><?php echo Helper_Util::strDate('m-d H:i', $return_order->create_time)?></td>
            				<td><?php echo $return_order->express_company?></td>
            				<td><?php echo $return_order->express_no?></td>
            				<?php endif;?>
            				<?php if(request('parameters') =='' || showFN() || request('parameters') =='sign' || request('parameters') =='termination'):?>
                            <td><?php echo $order->channel->network_code?></td>
                            <?php endif;?>
            				<td><?php if(($order->order_status=='6' || $order->order_status=='7' || $order->order_status=='8'|| $order->order_status=='9')):?>
            				<a href="javascript:void(0)" data="<?php echo $order->ali_order_no?>" account="<?php echo $order->account?>" onclick="printlabel(this,1)"><?php echo $order->consignee_country_code?></a>
            				<?php else : echo $order->consignee_country_code?>
            				<?php endif;?></td>
            				<?php if(!(request('parameters') =='no_package' || request('parameters') =='warehouse_in' || request('parameters') =='cancel' || request('parameters') =='returned' || request('parameters') =='wait_to_return')):?>
            				<td><?php if(Fee::find('order_id=? and fee_item_code="logisticsExpressASP_EX0020"',$order->order_id)->getOne()->isNewRecord()):?><?php else: ?>有<?php endif;?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='') :?>
            				<td><?php echo $order->packing_type?></td>
            				<?php endif;?>
            				<td><?php echo Farpackage::find('order_id=?',$order->order_id)->getSum('quantity')?></td>
            				<?php if (request('parameters') =='') :?>
            				<?php if ($dem->status==0) :?>
            				<?php $fee_in=Fee::find('order_id=? and fee_type= "1"',$order->order_id)->getSum('amount')?>
            				<?php $fee_out=Fee::find('order_id=? and fee_type= "2"',$order->order_id)->getSum('amount')?>
            				<td align="right"><?php echo $fee_in?round($fee_in,2):''?></td>
            				<td align="right"><?php echo $fee_out?round($fee_out,2):''?></td>
            				<td align="right"><?php if($fee_in && $fee_out):?><?php echo round($fee_in-$fee_out,2)?><?php endif;?></td>
            				<td align="right"><?php if($fee_in && $fee_out):?><?php echo round(($fee_in-$fee_out)/$fee_in,4)*100?><?php echo '%'?><?php endif;?></td>
            				<?php endif;?>
            				<!--             				应收总实重 -->
            				<td align="right"><?php echo $order->weight_actual_in?$order->weight_actual_in:''?></td>
							<!--             				应收总体积重 -->
            				<td align="right"><?php echo $order->total_volumn_weight?$order->total_volumn_weight:''?></td>
            				<!--             				应收总计费重 -->
            				<td align="right"><?php echo $order->weight_income_in?$order->weight_income_in:''?></td>
            				<!--             				应付预报实重(标签重) -->
            				<td align="right"><?php echo $order->weight_label?$order->weight_label:''?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='') :?>
            				<td align="right"><?php echo $order->weight_actual_out?$order->weight_actual_out:''?></td>
            				<td align="right"><?php echo $order->weight_bill?$order->weight_bill:''?></td>
            				<!--             				应付总体积重 -->
            				<td align="right"><?php echo $order->total_out_volumn_weight?$order->total_out_volumn_weight:''?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='' || showFN() || request('parameters') =='sign') :?>
            				<td align="right"><?php echo $order->weight_cost_out?$order->weight_cost_out:''?></td>
            				<?php endif;?>
            				<?php if(request('parameters') =='' || request('parameters') =='warehouse_out' || request('parameters') =='wait_to_send' || request('parameters') =='sent' || request('parameters') =='sign'):?>
                				<?php if(!(request('parameters') == '' || request('parameters') == 'no_package' || request('parameters') == 'warehouse_in' || request('parameters') == 'prove' || request('parameters') == 'paid')):?>
                				<td><?php echo $order->customer->customer ?></td>
                                <td align="center"><?php echo $order->service_product->product_chinese_name ?></td>
                                <?php endif;?>
            				<td><?php echo $order->channel->channel_name?></td>
            				<td><?php echo $order->channel->supplier->supplier?></td>
            				<?php endif;?>
            				<?php if (!(showFN() || request('parameters') =='sign')) :?>
            				<td align="right"><?php echo $order->total_amount?round($order->total_amount,2):''?></td>
            				<?php endif;?>
            				<td><?php echo $order->declaration_type=='DL'?$order->declaration_type:(($order->total_amount>700 || $order->weight_actual_in>70)?'强制':$order->declaration_type)?></td>
            				<?php if (request('parameters') =='') :?>
            				<td><?php echo $order->sender_name2?></td>
            				<td><?php echo $order->sender_name1?></td>
            				<td><?php echo $order->sender_mobile?$order->sender_mobile:$order->sender_telephone?></td>
            				<td><?php echo $order->sender_email?></td>
            				<td><?php echo $order->sender_state_region_code.$order->sender_city.$order->sender_street1.$order->sender_street2?></td>
            				<td><?php echo $order->consignee_name2?></td>
            				<td><?php echo $order->consignee_name1?></td>
            				<td><?php echo $order->consignee_mobile?$order->consignee_mobile:$order->consignee_telephone?></td>
            				<td><?php echo $order->consignee_email?></td>
            				<td><?php echo $order->consignee_city?></td>
            				<td><?php echo $order->consignee_postal_code?></td>
            				<td><?php echo $order->consignee_country_code.'&nbsp;'.$order->consignee_state_region_code.'&nbsp;'.$order->consignee_city.'&nbsp;'.$order->consignee_street1.'&nbsp;'.$order->consignee_street2?></td>
            				<?php endif;?>
            				<?php if (!(request('parameters') =='sign' || request('parameters') =='warehouse_in' || request('parameters') =='prove' || request('parameters') =='paid' || showFN())) :?>
            				<td style="width:70px;"><?php echo $order->need_pick_up=='1'?'是':$order->reference_no?></td>
            				<?php endif;?>
            				<?php if (request('parameters') =='') :?>
            				<td><?php echo $order->reference_no?></td>
            				<?php endif;?>
            				<td><?php echo $order->remark?></td>
            			</tr>
            		<?php endforeach;?>
            		</tbody>
            	</table>
            	</div>
    </div>
    <input id="hidden_consignee_country_code1" type="hidden" name="consignee_country_code1" value="<?php echo request('consignee_country_code1')?>">
    <input id="search_flag" type="hidden" name="search_flag" value="<?php echo request('search_flag')?>">
    <input id="parameters" type="hidden" name="parameters" value="<?php echo $parameters?>">
    <input id="need_pick_up1" type="hidden" name="need_pick_up" value="<?php echo request('need_pick_up')?>">
    <input id="negative_profit1" type="hidden" name="negative_profit" value="<?php echo request('negative_profit')?>">
    <input id="service_code1" type="hidden" name="service_code" value="<?php echo request('service_code')?>">
    <input id="channel_id1" type="hidden" name="channel_id" value="<?php echo request('channel_id')?>">
    <input id="supplier_id1" type="hidden" name="supplier_id" value="<?php echo request('supplier_id')?>">
    <input id="packing_type1" type="hidden" name="packing_type" value="<?php echo request('packing_type')?>">
    <input id="network_code1" type="hidden" name="network_code" value="<?php echo request('network_code')?>">
    <input id="declaration_type1" type="hidden" name="declaration_type" value="<?php echo request('declaration_type')?>">
    <input id="sender1" type="hidden" name="sender" value="<?php echo request('sender')?>">
    <input id="waybill_codes1" type="hidden" name="waybill_codes" value="<?php echo request('waybill_codes')?>">
    <input type="hidden" id="weight_cost_out_start1" name="weight_cost_out_start"  value="<?php echo request('weight_cost_out_start')?>">
    <input type="hidden" id="weight_cost_out_end1" name="weight_cost_out_end"  value="<?php echo request('weight_cost_out_end')?>"  >
    <input type="hidden" id="has_battery" name="has_battery"  value="<?php echo request('has_battery')?>"  >
    <input type="hidden" id="customer_id2" name="customer_id1"  value="<?php echo request('customer_id1')?>"  >
    <input id="department" type="hidden" name="department_id" value="<?php echo request('department_id')?>">
    <input id="warehouse_in_department" type="hidden" name=warehouse_in_department_id value="<?php echo request('warehouse_in_department_id')?>">
    </form>
	<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
<script type="text/javascript">
$(document).ready(function(){ 
		//第一列不进行排序(索引从0开始)
	    $.tablesorter.defaults.headers = {0: {sorter:false},2: {sorter:false}}
	    $("#myTable").tablesorter(); 
	    $("#search").click(function(){
	    	$("#search_flag").val("0");
	    });
	    
});

function opendeal(obj){
    if($("#deal_info_box"+obj).is(":hidden")){
    	$("#deal_info_box"+obj).show();
    	$("#deal_info_one"+obj).hide();
    }else{
    	$("#deal_info_box"+obj).hide();
    	$("#deal_info_one"+obj).show();
    }
}
function closedeal(obj){
    if($("#deal_info_one"+obj).is(":hidden")){
    	$("#deal_info_box"+obj).hide();
    	$("#deal_info_one"+obj).show();
    }else{
    	$("#deal_info_box"+obj).show();
    	$("#deal_info_one"+obj).hide();
    }
}

function waybillSearch(){
	$("#search_flag").val("1");
	$("#hidden_consignee_country_code1").val($("#consignee_country_code1").val());
	$("#waybill_codes1").val($("#waybill_codes").val());
   	$("#service_code1").val($("#service_code").val());
   	$("#channel_id1").val($("#channel_id").val());
   	$("#supplier_id1").val($("#supplier_id").val());
    $("#packing_type1").val($("#packing_type").val());
   	$("#declaration_type1").val($("#declaration_type").val());
   	$("#sender1").val($("#sender").val());
   	$("#network_code1").val($("#network_code").val());
    $("#weight_cost_out_start1").val($("#weight_cost_out_start").val());
   	$("#weight_cost_out_end1").val($("#weight_cost_out_end").val());
   	$("#has_battery").val($("#has_battery1").val());
   	$("#customer_id2").val($("#customer_id1").val());
   	if($('#need_pick_up').attr('checked')=='checked'){
		   $("#need_pick_up1").val($("#need_pick_up").val());
	}else{
		$("#need_pick_up1").val('');
	}
	if($('#negative_profit').attr('checked')=='checked'){
		   $("#negative_profit1").val($("#negative_profit").val());
	}else{
		$("#negative_profit1").val('');
	}
	$("#department").val($("#department_id").val());
	$("#warehouse_in_department").val($("#warehouse_in_department_id").val());
   	$("#searchForm").submit();
}
function copy(order_id,ali_order_no){
	$('#hidden_order_id').val(order_id);
	$('#hidden_new_ali_order_no').val(ali_order_no);
// 	alert($('#hidden_order_id').val()+'/'+$('#hidden_new_ali_order_no').val());
}
function savenew_ali(){
	if($('#new_ali_order_no').val()=='' || $('#customer_id').val()==''){
	   $.messager.alert('', '必填项不能为空');
	   return false;
	}
	$('#hidden_new_ali_order_no').val($('#new_ali_order_no').val());
	$.ajax({
		url:'<?php echo url('order/savenewali')?>',
		data:{order_id:$('#hidden_order_id').val(),ali_order_no:$('#hidden_new_ali_order_no').val(),customer_id:$('#customer_id').val(),copy_department_id:$('#copy_department_id').val()},
		type:'post',
		async:false,
		success:function(data){
			if(data=='samealiorderno'){
			   $.messager.alert('', '新单号不能与原单号相同');
			}else if(data=='noorder'){
			   $.messager.alert('', '无相关信息');
			}else if(data=='nopaytime'){
			   $.messager.alert('', '无支付时间');
			}else if(data=='saved'){
			   $.messager.alert('', '该单号已存在请更改单号');
			}else if(data=='success'){
			   alert('复制订单成功');
			   setTimeout(function (){
				  window.location.reload();
			   },1000);
			}
		}
	});
	
}
	/**
	 *  点击tabs设置隐藏框值 
	 */	 
	function TabSwitch(code){
		$("#parameters").val(code);
		$("#searchForm").trigger("submit");
	}
	/**
	 *	批量退回已支付
	 */
	function returnpaid(){
		if($(".ids:checked").length>0){
			var dropIds = new Array();  
			$(".ids").each(function(){
				if($(this).prop('checked')){
					dropIds.push($(this).val());  
				}
			});
			$.ajax({
				url:'<?php echo url('order/returnpaid')?>',
				data:{order_ids:dropIds},
				type:'post',
				async:false,
				success:function(data){
					   alert('成功');
					   setTimeout(function (){
						  window.location.reload();
					   },1000);
	 			}
			});
		}else{
			alert("请选择订单");
			return false;
		}
	}
	/**
	*打印功能
	**/
	function printlabel(obj,type){
		var ali_order_no=$(obj).attr('data');
		var account=$(obj).attr('account');
		$.ajax({
			url:'<?php echo url('warehouse/gettrackingno')?>',
			type:'POST',
			dataType:'json',
			data:{ali_order_no:ali_order_no},
			success:function(data){
				if(type==1){
					//发票
					var others_file_name=data.tracking_no+"_others.pdf";
				}else if(type==2){
					//面单
					var others_file_name=data.tracking_no+".pdf";
				}else if(type==3){
					//小标签
					var others_file_name=data.order_no+"_label.pdf";
				}
				console.log(others_file_name)
				//发票是否存在pdf文件
				var other_pdfexist = pdfisexist(others_file_name);
				console.log(other_pdfexist.message)
				if(other_pdfexist.message!='noexist'){
					console.log(others_file_name);
					window.open( other_pdfexist.url, "_blank");
				}else{
					alert('PDF不存在');
				}
			}
		})
	}
    //泛远面单
    function farbilpdf(order_id){
    	$.ajax({
			url : '<?php echo url("order/getfarlabel")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				order_id : order_id
			},
			})
			.done(function (data) {
				window.open( data.url, "_blank");
			})
			.fail(function (data) {
				layer.close(order_id);
				layer.alert('发生内部错误，暂时无法打印');
			});
    }
    $('#table-cont').data('slt',{sl:$(this).scrollLeft()-1,st:$(this).scrollTop()-2}).scroll(function(){
    	var scrollTop = $(this).scrollTop()-2;
    	var scrollLeft = $(this).scrollLeft()-1;
    	d=$('#table-cont').data('slt');
    	if(d.sl!=scrollLeft){
        	$('.x_scroll_td').css({'background-color':'#fff','transform':'translate3d('+scrollLeft+'px,0,0)'});
        	$('.x_scroll_th').css({'background-color':'#f4f4f4','background-image':'linear-gradient(#FFF 0px, #F2F2F2 100%)','transform':'translate3d('+scrollLeft+'px,0,0)'});
        }
        if(d.st!=scrollTop){
        	$(this).find('thead').css({'transform':'translateY('+scrollTop+'px)','z-index':'100'});
        }
        $('#table-cont').data('slt',{sl:$(this).scrollLeft(),st:$(this).scrollTop()-2})
    })
    $(function(){
        var height_window=$(window).height();//浏览器当前窗口可视区域高度
        var height_table=0.6*height_window;//计算调节表格高度
        $('#table-cont').height(height_table);
    })
    $(function(){
    	//渠道异常件截止时间
        $('input[type=radio][name=issue_type]').change(function() {
            if (this.value == 3) {
                $('#wu').removeAttr('style');
                
                $('#__cnVeryCalendarContainer').css('z-index','9002')
            }else if (this.value == 1) {
            	$('#wu').attr('style','display:none');
            	$('#__cnVeryCalendarContainer').css('display','none')
            }else if (this.value == 2){
            	$('#wu').attr('style','display:none');
            	$('#__cnVeryCalendarContainer').css('display','none')
            }
        });
    })
    function selectall(obj){
    	$(".ids").each(function(){
    		$(this).prop('checked',$(obj).prop('checked'))
    	});
    }
    function batch_issue(){
    	if($(".ids:checked").length>0){
        	$('#batch_issue').dialog('open');
        	$('.window-shadow').css('top','106px');
        	$('.panel').css('top','106px');
        	$('#batch_issue').removeClass('hide');
    	}else{
    		alert("请选择订单");
    		return false;
    	}
    }
    //批量发送邮件
    function emil(){
    	if($(".ids:checked").length>0){
        	$('#emil').dialog('open');
        	$('.window-shadow').css('top','106px');
        	$('.panel').css('top','106px');
        	$('#emil').removeClass('hide');
    	}else{
    		alert("请选择订单");
    		return false;
    	}
    }
    //批量转入其他
    function other(){
    	if($(".ids:checked").length<=0){
    		alert("请选择订单");
    		return false;
    	}
		layer.confirm('确定批量转入其他', {
    		btn : ['确定', '取消']
    	}, function (index, layero) {
        	//选中数据
    		var batchIds = new Array();  
    		$(".ids").each(function(){
    			if($(this).prop('checked')){
    				batchIds.push($(this).val());  
    			}
    		});
    		$.ajax({
			url : '<?php echo url("order/other")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				batchIds : batchIds
			},
			})
			.done(function (data) {
				if (data=='success') {
					layer.msg('批量转入成功');
					window.location.reload()
				}else{
					layer.msg('批量转入失败');
				}
			})
			.fail(function (data) {
				layer.close(batchload);
				layer.alert('发生内部错误，暂时无法转入');
			});
        })
		
    }

    //批量转成已支付
    function transferpaid(){
    	if($(".ids:checked").length<=0){
    		alert("请选择订单");
    		return false;
    	}
		layer.confirm('确定批量转成已支付', {
    		btn : ['确定', '取消']
    	}, function (index, layero) {
        	//选中数据
    		var batchIds = new Array();  
    		$(".ids").each(function(){
    			if($(this).prop('checked')){
    				batchIds.push($(this).val());  
    			}
    		});
    		$.ajax({
			url : '<?php echo url("order/transferpaid")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				batchIds : batchIds
			},
			})
			.done(function (data) {
				if (data=='success') {
					layer.msg('批量转成已支付成功');
					window.location.reload()
				}else{
					layer.msg('批量转成已支付失败');
				}
			})
			.fail(function (data) {
				layer.close(batchload);
				layer.alert('发生内部错误，暂时无法批量转成已支付');
			});
        })
		
    }

    //批量退回已核查
    function returnchecked(){
    	if($(".ids:checked").length<=0){
    		alert("请选择订单");
    		return false;
    	}
		layer.confirm('确定批量退回已核查', {
    		btn : ['确定', '取消']
    	}, function (index, layero) {
        	//选中数据
    		var batchIds = new Array();  
    		$(".ids").each(function(){
    			if($(this).prop('checked')){
    				batchIds.push($(this).val());  
    			}
    		});
    		$.ajax({
			url : '<?php echo url("order/returnchecked")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				batchIds : batchIds
			},
			})
			.done(function (data) {
				if (data=='success') {
					layer.msg('批量退回已核查成功');
					window.location.reload()
				}else{
					layer.msg('批量退回已核查失败');
				}
			})
			.fail(function (data) {
				layer.close(batchload);
				layer.alert('发生内部错误，暂时无法批量退回已核查');
			});
        })
		
    }
    //批量转签收
    function transfersign(){
    	if($(".ids:checked").length<=0){
    		alert("请选择订单");
    		return false;
    	}
		layer.confirm('确定批量转签收', {
    		btn : ['确定', '取消']
    	}, function (index, layero) {
        	//选中数据
    		var batchIds = new Array();  
    		$(".ids").each(function(){
    			if($(this).prop('checked')){
    				batchIds.push($(this).val());  
    			}
    		});
    		$.ajax({
			url : '<?php echo url("order/transfersign")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				batchIds : batchIds
			},
			})
			.done(function (data) {
				if (data=='success') {
					layer.msg('批量转签收成功');
					window.location.reload()
				}else{
					layer.msg('批量转签收失败');
				}
			})
			.fail(function (data) {
				layer.close(batchload);
				layer.alert('发生内部错误，暂时无法批量转签收');
			});
        })
		
    }
    //发送邮件 ajax
    function ajaxemil(){
    	var batchIds = new Array();  
		$(".ids").each(function(){
			if($(this).prop('checked')){
				batchIds.push($(this).val());  
			}
		});
		var emil_id = $('#emil_id').val();
        layer.confirm('确定批量发送邮件', {
    		btn : ['确定', '取消']
    	}, function (index, layero) {
    		
    		layer.close(index);
    		var batchload = layer.load(1);
    		
//     		$.ajax({
//    			url : '<?php echo url("order/batchemil")?>',
//     			type : 'POST',
//     			dataType : 'json',
//     			data : {
//     				batchIds : batchIds,
//     				emil_id : emil_id
//     			},
//     		})
//     		.done(function (data) {
//     			layer.close(batchload);
//     			if (data=='success') {
//     				layer.msg('批量发送成功');
//     				location.reload();
//     			}else{
//     				layer.msg('批量发送失败');
//     			}
//     		})
//     		.fail(function (data) {
//     			layer.close(batchload);
//     			layer.alert('发生内部错误，暂时无法发送');
//     		});

    		window.location.href='<?php echo url("order/batchemil")?>'+'/emil_id/'+emil_id+'/batchIds/'+batchIds


    		
    	})
    }
    function ajaxbatch_issue(){
    	var batchIds = new Array();  
		$(".ids").each(function(){
			if($(this).prop('checked')){
				batchIds.push($(this).val());  
			}
		});
		var issue_type = $('input[name=issue_type]:checked').val();
		var deadline = $('input[name=deadline]').val();
		var detail = $('#issue_detail').val();
        layer.confirm('确定批量扣件', {
    		btn : ['确定', '取消']
    	}, function (index, layero) {
    		
    		layer.close(index);
    		var batchload = layer.load(1);
    		
    		$.ajax({
    			url : '<?php echo url("order/batchissue")?>',
    			type : 'POST',
    			dataType : 'json',
    			data : {
    				batchIds : batchIds,
    				issue_type :issue_type,
    				deadline : deadline,
    				detail : detail
    			},
    		})
    		.done(function (data) {
    			layer.close(batchload);
    			if (data=='success') {
    				layer.msg('批量扣件成功');
    				location.reload();
    			}else{
    				layer.msg('批量扣件失败');
    			}
    		})
    		.fail(function (data) {
    			layer.close(batchload);
    			layer.alert('发生内部错误，暂时无法扣件');
    		});
    	})
    }

    function batch_release(){
    	if($(".ids:checked").length>0){
			var batchIds = new Array();  
			$(".ids").each(function(){
				if($(this).prop('checked')){
					batchIds.push($(this).val());  
				}
			});
            layer.confirm('确定批量解扣', {
        		btn : ['确定', '取消']
        	}, function (index, layero) {
        		
        		layer.close(index);
        		var batchload = layer.load(1);
        		
        		$.ajax({
        			url : '<?php echo url("order/batchrelease")?>',
        			type : 'POST',
        			dataType : 'json',
        			data : {
        				batchIds : batchIds
        			},
        		})
        		.done(function (data) {
        			layer.close(batchload);
        			if (data=='success') {
        				layer.msg('批量解扣成功');
        				location.reload();
        			}else{
        				layer.msg('批量解扣失败');
        			}
        		})
        		.fail(function (data) {
        			layer.close(batchload);
        			layer.alert('发生内部错误，暂时无法解扣');
        		});
        	})
    	}else{
    		alert("请选择订单");
    		return false;
    	}
    }
    
    function batch_affirm(){
    	if($(".ids:checked").length>0){
			var batchIds = new Array();  
			$(".ids").each(function(){
				if($(this).prop('checked')){
					batchIds.push($(this).val());  
				}
			});
            layer.confirm('确定批量确认？', {
        		btn : ['确定', '取消']
        	}, function (index, layero) {
        		
        		layer.close(index);
        		var batchload = layer.load(1);
        		
        		$.ajax({
        			url : '<?php echo url("order/batchaffirm")?>',
        			type : 'POST',
        			dataType : 'json',
        			data : {
        				batchIds : batchIds
        			},
        		})
        		.done(function (data) {
        			layer.close(batchload);
        			if (data=='success') {
        				layer.msg('批量确认成功');
        				location.reload();
        			}else{
        				layer.msg('批量确认失败');
        			}
        		})
        		.fail(function (data) {
        			layer.close(batchload);
        			layer.alert('发生内部错误，暂时无法重查');
        		});
        	})
    	}else{
    		alert("请选择订单");
    		return false;
    	}
    }
    
    function batch_gettrace(){
    	if($(".ids:checked").length>0){
			var batchIds = new Array();  
			$(".ids").each(function(){
				if($(this).prop('checked')){
					batchIds.push($(this).val());  
				}
			});
            layer.confirm('确定批量重查', {
        		btn : ['确定', '取消']
        	}, function (index, layero) {
        		
        		layer.close(index);
        		var batchload = layer.load(1);
        		
        		$.ajax({
        			url : '<?php echo url("order/batchgettrace")?>',
        			type : 'POST',
        			dataType : 'json',
        			data : {
        				batchIds : batchIds
        			},
        		})
        		.done(function (data) {
        			layer.close(batchload);
        			if (data=='success') {
        				layer.msg('批量重查成功');
        				location.reload();
        			}else{
        				layer.msg('批量重查失败');
        			}
        		})
        		.fail(function (data) {
        			layer.close(batchload);
        			layer.alert('发生内部错误，暂时无法重查');
        		});
        	})
    	}else{
    		alert("请选择订单");
    		return false;
    	}
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
    //批量导出面单
	function batch_export_label(){
		var waybill_codes = $('#waybill_codes').val();
		if(waybill_codes == ""){
			$.messager.alert('', '(阿里/泛远/末端/总单/客户/子/国内)单号不能为空');
			return false;
		}
		if(confirm('确定批量导出吗？')){
			waybill_codes=waybill_codes.replace(/\r/,'').split("\n");
			window.location.href='<?php echo url("order/outputlabel")?>'+'/waybill_codes/'+waybill_codes
		}
	}
</script>
<?PHP $this->_endblock();?>