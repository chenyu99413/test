<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
供应商列表
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="post">
	<div class="FarTool">
		<a class="btn btn-success" target="_blank" href="<?php echo url('/edit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
	</div>
	<div class="tabs-container"
		style="min-width: 1148px; padding-top: 12px;">
		<?php echo Q::control ( "tabs", "description", array ("tabs" => $tabs,"active_id" => $active_id))?>
  		<div class="tabs-panels">
			<div style="padding: 10px;"
				class="panel-body panel-body-noheader panel-body-noborder">
            	<table class="FarTable">
            		<thead>
            			<tr>
            				<th>供应商名称</th>
            				<th>合同号</th>
            				<th>合同签订时间</th>
            				<th>合同到期时间</th>
            				<th>状态</th>
            				<th>操作</th>
            			</tr>
            		</thead>
            		<tbody>
                		<?php $items = array ('0' => '合作','1' => '不合作','2' => '待定');  foreach ($suppliers as $supplier):?>
                		<tr>
            				<td>
            					<a target="_blank" href="<?php echo url('/edit',array('supplier_id'=>$supplier->supplier_id))?>"><?php echo $supplier->supplier?></a>
            				</td>
            				<td><?php echo $supplier->contract_code?></td>
            				<td><?php echo Helper_Util::strDate ( 'Y-m-d',$supplier->contract_date)?></td>
            				<td><?php echo Helper_Util::strDate ( 'Y-m-d',$supplier->contract_expiration_date)?></td>
            				<td><?php echo @$items[$supplier->status]?></td>
            				<td>
            					<a class="btn btn-mini" target="_blank"
            						href="<?php echo url('/edit',array('supplier_id'=>$supplier->supplier_id))?>">
            						<i class="icon-edit"></i>
            						编辑
            					</a>
            				</td>
            			</tr>
                		<?php endforeach;?>
            		</tbody>
            	</table>
	       </div>
	   </div>
	</div>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>