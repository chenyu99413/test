<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    退件
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>

<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'订单管理' => '','退件列表' => url ( 'order/returnlist' ),'批量退件' => '' 
	) 
) )?>
<form action="<?php echo url('order/orderreturnbatch')?>" method="post" enctype="multipart/form-data">
    <div class="FarSearch">
		<table style="width:100%">
           <tbody>
               <tr>
               		<th>退件范围</th>
                   <td>
                    <?php
						echo Q::control ( "dropdownbox", "return_status", array (
							"items" => array('1'=>'全部退','2'=>'部分退'),
							"style" => "width:70%"
						) )?>
                   </td>
                   <th>退件状态</th>
                   <td>
                    <?php
						echo Q::control ( "dropdownbox", "state", array (
							"items" => array('1'=>'待退货','2'=>'已退货'),
							"style" => "width:70%"
						) )?>
                   </td>
                   <th>货物流向</th>
                   <td>
                    <?php
						echo Q::control ( "dropdownbox", "cargo_direction", array (
							"items" => array('1'=>'快递退货','2'=>'换单重发','3'=>'班车退回','4'=>'客户自取'),
							"style" => "width:70%"
						) )?>
                   </td>
                   <th>国内/外退件</th>
                   	<td>
	                    <?php
							echo Q::control ( "dropdownbox", "flag", array (
								"items" => array('1'=>'国内退件','2'=>'国外退件'),
								"style" => "width:70%"
							) )?>
                   	</td>
               </tr>
               <tr>
               		<th>文件上传
               			<a class=""
							href="<?php echo $_BASE_DIR?>public/download/批量退件模板.xls">
							下载模板
						</a>
               		</th>
					<td colspan="6">
						<input type="file" name="file">
					</td>
					
				</tr>
           </tbody>
        </table>
        
     </div>
     <div class="FarTool text-center">
        	<button class="btn btn-small btn-success" id="search">
                                        保存
       		</button>
       	</div>
     
</form>  
	<?php if (!empty($error)):?>
	<table class="table-bordered table">
		<tr>
			<th>行数</th>
			<th>错误</th>
		</tr>
		<?php foreach ($error as $i => $err):?>
		<tr>
			<td><?php echo $i?></td>
			<td><?php print_r($err)?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
<?PHP $this->_endblock();?>

