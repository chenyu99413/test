<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<div>
<form action="" method="post" class="FarSearch">
<table>
<tr>
	<th>网络</th>
	<td>
	   <?php
        echo Q::control ( 'dropdownbox', 'network_code', array (
        'items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_code'),
        'empty'=>true,
        'style'=>'width:75px',
        'value' => request('network_code'),
        ) )?>
	</td>
	<th>阿里代码</th>
	<td>
		<input type="text" name="ali_code" value="<?php echo request('ali_code')?>">
	</td>
	<th>关键字</th>
	<td>
		<input type="text" name="keyword" value="<?php echo request('keyword')?>">
	</td>
	
	<th>
		<button class="btn btn-small btn-primary"><i class="icon-search"></i> 搜索</button>
		<a class="btn btn-small btn-success" target="_blank" href="<?php echo url('/matchrulesedit')?>"><i class="icon-plus"></i> 新建</a>
		<a class="btn btn-small btn-warning" id="order_import" data-toggle="tooltip"
						  data-placement="top" title="导入" href="<?php echo url('/matchrulesimport')?>">
						  <i class="icon-cloud-upload"></i>
						         导入
					    </a>
		<button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
							<i class="icon-download"></i>
							导出
						</button>
	</th>
</tr>
</table>
</form>
<table class="FarTable">
<thead>
<tr>
	<th>网络</th>
	<th>自动</th>
	<th>阿里代码</th>
	<th style="width:400px">关键字</th>
	<th style="width:200px">中文描述</th>
	<th style="width:230px">英文描述</th>
	<th style="width:230px">是否优先匹配</th>
	<th style="width:230px">序号</th>
	<th style="width:110px;">操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($rules as $row):?>
<tr>
	<td><?php echo $row->network_code?></td>
	<td>
		<?php if ($row->auto):?>
		<i class="icon icon-ok"></i>
		<?php endif;?>
	</td>
	<td><?php echo $row->ali_code?></td>
	<td><?php echo $row->keyword?></td>	
	<td><?php echo $row->cn_desc?></td>
	<td><?php echo $row->en_desc?></td>
	<td><?php echo $row->is_priority==1?'是':'否'?></td>
	<td><?php echo $row->sort?></td>
	<td>
		<a class="btn btn-mini btn-info" target="_blank" href="<?php echo url('/matchrulesedit',array('route_matchrules_id'=>$row->id))?>"><i class="icon icon-edit"></i></a>
		<a class="btn btn-mini btn-danger" onclick="delrows(this)" data="<?php echo $row->id?>" href="javascript:void(0)"><i class="icon icon-remove"></i>删除</a>
	</td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
    function delrows(obj){
    	$.messager.confirm('删除','确定要删除？',function(data){
			if(data){
				window.location.href="<?php echo url('/matchrulesdel')?>"+"?route_matchrules_id="+$(obj).attr('data');
			}
        })
    }
</script>
