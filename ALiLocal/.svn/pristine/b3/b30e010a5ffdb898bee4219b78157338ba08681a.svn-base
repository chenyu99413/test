<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>邮件模板列表<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<form method="POST">
	<div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th>模板属性:</th>
					<td>
						<?php
						echo Q::control ( "myselect", "template_type", array (
							"items" => array(1=>'港前',2=>'港后'),
							"selected" => request('template_type'),
							"style" => "width: 120px",
							"empty" => "true" 
						) )?>
					</td>
					<th>产品:</th>
					<td>
						
						<?php
						$items = Product::find('product_id in (?)',Productdepartmentavailable::availableproductids())->getAll()->toHashMap('product_id','product_chinese_name');
						array_unshift($items,'通用模板');
						echo Q::control ( "myselect", "product_id", array (
							"items" => $items,
							"selected" => request('product_id'),
							"style" => "width: 160px",
							"empty" => "true" 
						) )?>
					</td>
					<th>标题:</th>
					<td width='200px'>
						<input type="text" style="width:100%" name="template_title" placeholder="模糊搜索" value="<?php echo request('template_title')?>"/>
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a class="btn btn-success btn-small" href="<?php echo url('product/templateedit')?>">
							<i class="icon-plus"></i>
							新建
					   </a>
					</td>
				</tr>
			</tbody>
		</table>
		
	</div>
	<table class="FarTable" style="width:50%;">
		<thead>
			<tr>
			    <th style="width:30px;">No</th>
				<th>模板名称</th>			
				<th>模板属性</th>
				<th>产品</th>
				<th style="width:130px;">操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php $i=1; foreach($list as $value):?>
			<tr>
			    <td><?php echo $i++;?></td>
				<td nowrap="nowrap">
					<a
						href="<?php echo url("product/templateedit",array("id"=>$value->id))?>"><?php echo $value->template_name?></a>
				</td>
				<td nowrap="nowrap">
					<?php if($value->template_type){ echo EmailTemplate::$template_type[$value->template_type];}else{echo '';} ?>
				</td>
				<td nowrap="nowrap">
					<?php 
						$items = Helper_Array::toHashmap(Product::find()->getAll(), "product_id", "product_chinese_name");
						array_unshift($items,'通用模板');
						echo $items[$value->product_id];
					?>
				</td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("product/templateedit",array("id"=>$value->id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<input type="button" class="btn btn-small btn-danger" href="javascript:void(0)" data="<?php echo $value->id?>" onclick="del(this)" value="删除">
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>
<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
<script type="text/javascript">
function del(obj){
	if (confirm('是否删除?')) {
    	var id=$(obj).attr('data');
    	$.ajax({
    		url:'<?php echo url('product/templatedel')?>',
    		data:{id:id},
    		type:'post',
    		async:false,
    		success:function(data){
    			if(data!='delsuccess'){
    			   alert('模板不存在');
    			}else{
    				window.location.reload();
    			}
    		}
    	});
	}
}
</script>
<?PHP $this->_endblock();?>

