<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
邮件模板编辑
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
if (request('id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'邮件模板管理' => '',
			'邮件模板列表' => url ( 'product/emailtemplate' ),
			'邮件模板编辑' => url ( 'product/templateedit', array (
				'id' => $template->id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'邮件模板管理' => '',
			'邮件模板列表' => url ( 'product/emailtemplate' ),
			'新建邮件模板' => url ( 'product/templateedit' ) 
		) 
	) );
}
?>
<form method="post">
	<div class="FarSearch" style="padding:5px;">
	<div>
		<table>
			<tbody>
				<tr>
					<th class="required-title">模板名称</th>
    				<td>
    					<input name="template_name" id="template_name" type="text" style="width: 150px" required="required" value="<?php echo $template->template_name?>">
    				</td>
				</tr>				
				<tr>
					<th class="required-title">产品</th>
    				<td>
    					<?php
    					$items = Helper_Array::toHashmap(Product::find()->getAll(), "product_id", "product_chinese_name");
    					array_unshift($items,'通用模板');
						echo Q::control("dropdownbox", "product_id", array(
							"items" => $items,
							"value" => $template->product_id,
							"style" => "width: 150px",
						))?>
    				</td>
				</tr>
				<tr>
					<th>模板属性:</th>
					<td>
						<?php
						
						echo Q::control("dropdownbox", "template_type", array(
							"items" => array(1=>'港前',2=>'港后'),
							"value" => $template->template_type,
							"style" => "width: 160px",
							"empty"=>true
						))?>
					</td>
				</tr>
				<tr>
				    <th>模板字段</th>
				    <td >
    				    <span style="color:red;">
    				                阿里单号:ali_order_no;末端单号:tracking_no;国内单号:reference_no;网络:network_code;末端渠道网络:trace_network_code;目的国家:consignee_country_code;目的地联系电话:servicetel
    				    </span><br/>
    				    <span style="color:red;">目的地作息时间:servicesch;目的地其它联系信息:customtel;最新轨迹信息1:track1;最新轨迹信息2:track2;最新轨迹信息3:track3;产品名称:service_name;仓库:warehouse;申报品名:good_name</span>
				    </td>
				</tr>
				<tr>
				    <th class="required-title">模板标题</th>
    				<td>
    					<textarea rows="" required="required" style="width:900px;height: 15px" name='template_title'><?php echo $template->template_title ?></textarea>
    				</td>
				</tr>
				<tr>
				    <th class="required-title">模板内容</th>
    				<td>
    					<textarea rows="13" required="required" style="width:900px;" name='template_text'><?php echo $template->template_text ?></textarea>
    				</td>
				</tr>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('product/emailtemplate')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>