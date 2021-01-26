<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'系统管理' => '',
			'轨迹匹配规则' => url ( 'staff/matchrules' ),
			'轨迹匹配规则编辑' => ''
		) 
	) );
?>
 <form method="POST" onsubmit="return Save();">
	<div class="row-fluid">
		<table style="width:60%;">
			<tbody>
				<tr>
					<th class="required-title">网络</th>
	                <td>
	                	 <?php 
					        echo Q::control ( 'dropdownbox', 'network_code', array (
					        'items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_code'),
					        'style'=>'width:200px',
					        'value' => $info->network_code,
					        ) )?>
	                </td>
				</tr>
				<tr>
				    <th class="required-title">阿里代码</th>
                	<td>
                		<input type="text" style="width:200px;" name="ali_code" required="required" value="<?php echo $info->ali_code?>">
                	</td>
                </tr>
                <tr>
                	<th>自动发送</th>
                	<td>
                		<input type="checkbox" name="auto" value="1" <?php if ($info->auto){echo 'checked';}?>>
                	</td>
				</tr>
				<tr>
					<th>是否优先匹配</th>
                	<td>
                		<?php echo Q::control('RadioGroup','is_priority',array(
                		    'items'=>array(1=>'是',2=>'否'),
                			'value'=>$info->is_priority
                		))?>
                	</td>
                </tr>
                <tr>
					<th>排序</th>
                	<td><input type="number" step="1" name="sort" value="<?php echo $info->sort?>"></td>
                </tr>
				<tr>
                	<th class="required-title">关键字</th>
                	<td>
                		<input type="text" style="width:500px;" name="keyword" required="required" value="<?php echo $info->keyword?>">
                	</td>
				</tr>
				<tr>
					<th class="">中文描述</th>
					<td>
						<textarea  rows="3" style="width:500px;" name="cn_desc"  ><?php echo $info->cn_desc?></textarea>
					</td>
				</tr>
				<tr>
					<th class="">英文描述</th>
					<td>
						<textarea  rows="3"  style="width:500px;" name="en_desc" ><?php echo $info->en_desc?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="row text-center">
    		<button class="btn btn-primary" type="submit">
    			<i class="icon-save"></i>
    			保存
    		</button>
	</div>
	</div>
	<input type="hidden" name="route_matchrules_id" value="<?php echo $info->id?>">
</form>   
<?PHP $this->_endblock();?>

