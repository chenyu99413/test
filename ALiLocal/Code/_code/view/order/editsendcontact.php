<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>
发件人信息编辑
<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="post">
	<div class="FarSearch span10" style="padding:5px;">
	<div class="span5">
		<table>
			<tbody>
				<tr>
					<th>发件人公司</th>
    				<td>
    					<input name="sender_company" id="sender_company" type="text" style="width: 300px" value="<?php echo $contact->sender_company?>">
    				</td>
				</tr>
				<tr>
				    <th>发件人备注项</th>
					<td>
						<textarea rows="5" name='comment' style="width: 300px"><?php echo $contact->comment;?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('order/sendcontact')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
</form>
<?PHP $this->_endblock();?>