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
		'产品管理' => '','UPS账号管理' => url ( 'account/upslist' ),'UPS账号编辑' => '' 
	) 
) )?>
<h5>账号：<?php echo $account->account?></h5>
<form method="post" onsubmit="return account()">
    <div class="FarSearch">
		<table style="width:95%;">
           <tbody>
           		<tr>
                   <th class="required-title">登入账号</th>
                   <td>
                        <input name="userid" type="text" style="width: 100%" required value="<?php echo $account->userid?>">
                   </td>
				   <th class="required-title">登入密码</th>
                   <td>
                        <input name="pwd" type="text" style="width: 100%" required value="<?php echo $account->pwd?>">
                   </td>
                   <th class="required-title">ups账号</th>
				   <td>
				        <input name="account" type="text" style="width: 100%" required value="<?php echo $account->account?>">
				   </td>
               </tr>
               <tr>
                   <th class="required-title">省英文</th>
                   <td>
                        <input name="state" type="text" style="width: 100%" required value="<?php echo $account->state?>">
                   </td>
                   <th class="required-title">邮编</th>
                   <td>
                        <input name="postcode" id="postcode" type="text" style="width: 100%" required value="<?php echo $account->postcode?>">
                   </td>
                   <th class="required-title">联系人</th>
                   <td>
                        <input name="aname" type="text" style="width: 100%" required value="<?php echo $account->aname?>">
                   </td>
               </tr>
               <tr>
                   <th class="required-title">城市英文</th>
                   <td>
                        <input name="city" type="text" style="width: 100%" required value="<?php echo $account->city?>">
                   </td>
				   <th class="required-title">城市中文</th>
                   <td>
                        <input name="city_cn" type="text" style="width: 100%"  value="<?php echo $account->city_cn?>">
                   </td>
                   <th class="required-title">电话</th>
				   <td>
				        <input name="phone" type="text" style="width: 100%" required value="<?php echo $account->phone?>">
				   </td>
               </tr>
               <tr>
                   <th class="required-title">授权码</th>
                   <td>
                        <input name="license" type="text" style="width: 100%" required value="<?php echo $account->license?>">
                   </td>
				   <th class="required-title">国家二字码</th>
                   <td>
                        <input name="countrycode" type="text" style="width: 100%" required value="<?php echo $account->countrycode?>">
                   </td>
               </tr>
               <tr>
				   <th class="required-title">地址英文</th>
				   <td colspan="3">
						<input name="address" type="text" style="width: 100%" required value="<?php echo $account->address?>">
				   </td>
                   <th class="required-title">地址中文</th>
				   <td colspan="3">
						<input name="address_cn" type="text" style="width: 100%"  value="<?php echo $account->address_cn?>">
				   </td>
               </tr>
               <tr>
                   <th class="required-title">公司英文名</th>
                   <td colspan="3">
                        <input name="name" type="text" style="width: 100%"  value="<?php echo $account->name?>">
                   </td>
                   <th class="required-title">经营单位编码</th>
				   <td>
						<input id="business_code" name="business_code" onblur="Bc()" type="text" style="width: 100%"  value="<?php echo $account->business_code?>">
				   </td>
               </tr>
               <tr>
                   <th class="required-title">公司中文名</th>
				   <td colspan="3">
						<input name="sender_cn" type="text" style="width: 100%"  value="<?php echo $account->sender_cn?>">
				   </td>
				   <th class="required-title">社会信用代码</th>
                   <td>
                        <input id="credit_code" name="credit_code" onblur="Cc()" type="text" style="width: 100%"  value="<?php echo $account->credit_code?>">
                   </td>
               </tr>
               <tr>
                   <th class="required-title">三方账号</th>
                   <td>
                        <input name="tp_account" type="text" style="width: 100%"  value="<?php echo $account->tp_account?>">
                   </td>
				   <th class="required-title">三方国家二字码</th>
                   <td>
                        <input name="tp_countrycode" type="text" style="width: 100%"  value="<?php echo $account->tp_countrycode?>">
                   </td>
                   <th class="required-title">三方邮编</th>
				   <td>
				        <input name="tp_postalcode" type="text" style="width: 100%"  value="<?php echo $account->tp_postalcode?>">
				   </td>
               </tr>
               <tr>
                   <th class="required-title">三方公司名称</th>
                   <td>
                        <input name="tp_cname" type="text" style="width: 100%"  value="<?php echo $account->tp_cname?>">
                   </td>
               </tr>
           </tbody>
        </table>
        <div class="FarTool text-center">
        	<button class="btn btn-small btn-success" id="search">
                                        保存修改
       		</button>
       	</div>
     </div>
</form>
<!--判断长度-->
<script type="text/javascript">
function account(){  
	var postcode=$("#postcode").val();
	var cc=$("#credit_code").val().length;
	var bc=$("#business_code").val().length;
/* 	if(cc!=18 || bc!=10){
	   alert("请检查社会信用代码与经营单位编码！");
	   return false;
	} */
	if(!(/^[0-9]{6}$/.test(postcode))){
	   alert("邮编错误！");
	   return false;
	}
	return true;
} 
</script>
<?PHP $this->_endblock();?>