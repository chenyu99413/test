<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>渠道列表<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="post">
<div class="FarSearch"  style="width:60%;">
		<table style="width:95%">
			<tbody>
				<tr>
					<th>是否启用</th>
					<td>
						<?php
						$arr = array('2'=>'否','1'=>'是');
						echo Q::control ( "dropdownbox", "channel_status", array (
							"items" => $arr,
							"value" => request('channel_status'),
							"style" => "width: 165px",
							"empty" => "true" 
						) )?>
					</td>
					<th>
					   <button class="btn btn-primary" type="submit" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a class="btn btn-success" target="_blank" href="<?php echo url('/edit')?>">
							<i class="icon-plus"></i>
							新建
						</a>
				  	</th>
				</tr>           
			</tbody>           
		</table>  
	</div>
	<table class="FarTable" style="width:60%;">
		<thead>
			<tr>
				<th>编号ID</th>
				<th>渠道名称</th>
				<th>网络</th>
				<th>末端网络</th>
				<th>打单方式</th>
				<th>渠道分组</th>
				<th>供应商</th>
				<th>标签标记</th>
				<th width=160>操作</th>
			</tr>
		</thead>
		<tbody>
    		<?php foreach ($channels as $channel):?>
    		<tr>
    			<td><?php echo $channel->channel_id?></td>
				<td>
					<a target="_blank" href="<?php echo url('/edit',array('channel_id'=>$channel->channel_id))?>"><?php echo $channel->channel_name?></a>
				</td>
				<td>
					<?php echo $channel->network_code?>
				</td>
				<td>
				    <?php echo $channel->trace_network_code?>
				</td>
				<td><?php echo $channel->print_method?Channel::$method[$channel->print_method]:''?></td>
				<td><?php echo $channel->channelgroup->channel_group_name?></td>
				<td><?php echo $channel->supplier->supplier?></td>
				<td><?php echo $channel->label_sign?></td>
				<td style="width:150px">
					<a class="btn btn-mini btn-info" target="_blank"
						href="<?php echo url('/edit',array('channel_id'=>$channel->channel_id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<a class="btn btn-mini" href="<?php echo url('staff/sender',array('channel_id'=>$channel->channel_id))?>"><i class="icon-search"></i> 查看发件人</a>
				</td>
			</tr>
    		<?php endforeach;?>
		</tbody>
	</table>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>