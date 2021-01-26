<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
if (request('id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'取件业务' => '',
			'取件员管理' => url ( 'pickup/pickupmember' ),
			'取件员信息' => url ( 'pickup/editpickupmember', array (
				'id' => $pickupmember->id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'取件业务' => '',
			'取件员管理' => url ( 'pickup/pickupmember' ),
			'新建取件员信息' => url ( 'pickup/editpickupmember' ) 
		) 
	) );
}
?>
<form method="post">
<div style="width:100%;">
    <div class="FarSearch">
		<table>
           <tbody>
               <tr>
                   <th>微信ID</th>
                   <td>
                       <input name="wechat_id" type="text" value="<?php echo $pickupmember->wechat_id?>" />
                   </td>
                   <th>微信号</th>
                   <td>
	                   <input name="wechat_no" type="text" value="<?php echo $pickupmember->wechat_no?>" />
                   </td>
                   <th>取件员姓名</th>
                   <td>
	                   <input name="name" type="text" value="<?php echo $pickupmember->name?>" />
                   </td>
                   <th>性别</th>
                   <td>
                   	   <?php 
	                   echo Q::control ( "dropdownbox", "gender", array (
							"items" => array('男'=>'男','女'=>'女'),
						    "value" => $pickupmember->gender,
						) )?>
                   </td>
                   <th>状态</th>
                   <td>
	                   <?php 
	                   echo Q::control ( "dropdownbox", "status", array (
							"items" => array('0'=>'未认证','1'=>'已认证'),
						    "value" => $pickupmember->status,
						) )?>
                   </td>
                   <th>是否进入上传图片页面</th>
                   <td>
	                   <?php 
	                   echo Q::control ( "dropdownbox", "type", array (
							"items" => array('0'=>'否','1'=>'是'),
						    "value" => $pickupmember->type,
						) )?>
                   </td>
               </tr>
               <tr>
               	   
                   <th>头像</th>
                   <td colspan="9">
                       <input style="width:1000px;height:15px;" name="img_url" type="url" value="<?php echo $pickupmember->img_url?>" />
                   </td>
               </tr>
           </tbody>
        </table>
        <div class="FarTool text-center">
        	<button class="btn btn-small btn-success" id="search">
                                        保存
       		</button>
       	</div>
     </div>
</div>
</form>  
<?PHP $this->_endblock();?>

