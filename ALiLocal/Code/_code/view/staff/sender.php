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

<form method="POST" id="searchForm" style="margin-bottom:0px;">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<?php if(request('return_channel_id')):?>
					<th>
				    	退货渠道
				    </th>
				    <td>
				    	<?php echo Q::control('dropdownbox','return_channel_id',array(
    					    'items'=>Helper_Array::toHashmap(ReturnChannel::find()->getAll(),'channel_id','channel_name'),
    					    'value'=>request('return_channel_id'),
    					    'style'=>'width:150px',
    					    "empty"=>true
    					))?>
				    </td>
					<?php else:?>
					<th>
				    	渠道
				    </th>
				    <td>
				    	<?php echo Q::control('dropdownbox','channel_id',array(
    					    'items'=>Helper_Array::toHashmap(Channel::find()->getAll(),'channel_id','channel_name'),
    					    'value'=>request('channel_id'),
    					    'style'=>'width:150px',
    					    "empty"=>true
    					))?>
				    </td>
					<?php endif;?>
				    
				    <td>
				    	<button class="btn btn-primary btn-small" id="search">
				             <i class="icon-search"></i>
				                                         搜索
			            </button>
				    	<a class="btn btn-small btn-success" target="_blank" href="<?php echo url('/senderEdit')?>"><i class="icon-plus"></i> 新建</a>
				    </td>
				</tr>
			</tbody>
		</table>
	</div>
</form>


<table class="FarTable">
<thead>
<tr>
	<th>发件人代码</th>
	<th>姓名</th>
	<th>公司</th>
	<th>电话</th>
	<th>国家</th>
	<th>省</th>
	<th>市</th>
	<th>区县</th>
	<th>详细地址</th>
	<th>邮编</th>
	<th>邮箱</th>
	<th>操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($senders as $sender):?>
    <tr>
        <td>
            <a target="_blank" href="<?php echo url('/senderEdit',array('sender_id'=>$sender->sender_id))?>">
                <?php echo $sender->sender_code?>
            </a>
        </td>
        <td><?php echo $sender->sender_name?></td>
        <td><?php echo $sender->sender_company?></td>
        <td><?php echo $sender->sender_phone?></td>
        <td><?php echo $sender->sender_country?></td>
        <td><?php echo $sender->sender_province?></td>
        <td><?php echo $sender->sender_city?></td>
        <td><?php echo $sender->sender_area?></td>
        <td><?php echo $sender->sender_address?></td>
        <td><?php echo $sender->sender_zip_code?></td>
        <td><?php echo $sender->sender_email?></td>
        <td>
            <a class="btn btn-small btn-primary" target="_blank" href="<?php echo url('/senderEdit',array('sender_id'=>$sender->sender_id))?>">
                <i class="icon-edit"></i>
            </a>
        </td>
    </tr>
<?php endforeach;?>
</tbody>
</table>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	$('#search').click(function(){
		$('#searchForm').submit();
	})
</script>
