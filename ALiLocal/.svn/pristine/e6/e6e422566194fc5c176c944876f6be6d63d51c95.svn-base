<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
渠道通讯录
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
</div>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<th>渠道</th>
					<td>
						<?php
						echo Q::control("dropdownbox", "channel_id", array(
							"items" => Helper_Array::toHashmap(Channel::find()->getAll(), "channel_id", "channel_name"),
							"value" => request('channel_id'),
							"style" => "width: 95%",
							"empty" => "true"
						))?>
					</td>
					<th>国家</th>
					<td>
						<input name="code_word_two" type="text" style="width: 50px"
							value="<?php echo request('code_word_two')?>">
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a class="btn btn-small btn-success" target="_blank" href="<?php echo url('/bookedit')?>"><i class="icon-plus"></i> 新建</a>
					   <a class="btn btn-small btn-warning" id="order_import" data-toggle="tooltip"
						  data-placement="top" title="导入" href="<?php echo url('/bookimport')?>">
						  <i class="icon-cloud-upload"></i>
						         导入
					    </a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th style="width:20px;">No</th>
				<th style="width:40px;">渠道</th>
				<th style="width:20px;">国家</th>
				<th style="width:120px;">目的地联系电话</th>
				<th style="width:120px;">目的地作息时间</th>
			    <th style="width:120px;">目的地其它联系信息</th>
				<th style="width:70px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($list as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->channel->channel_name?></td>
				<td><?php echo $temp->code_word_two?></td>
				<td><?php echo nl2br($temp->servicetel)?></td>
				<td><?php echo nl2br($temp->servicesch)?></td>
				<td><?php echo nl2br($temp->customtel)?></td>
				<td>
				    <a class="btn btn-mini btn-primary" target="_blank" href="<?php echo url('/bookedit',array('book_id'=>$temp->book_id))?>">
                       <i class="icon-edit"></i>
                                                                        编辑
                    </a>
				    <a class="btn btn-mini btn-danger" href="<?php echo url('product/bookdelete', array('book_id' => $temp->book_id))?>">
            	       <i class="icon-trash"></i>
			                                         删除
            	    </a>
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
<div style="clear: both;"></div>
<?PHP $this->_endblock();?>