<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
包裹抵达扫描总单
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>创建日期</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date"),
							"style"=>"width:90px"
						) )?>
					</td>
					<th>到</th>
					<td>
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date"),
							"style"=>"width:90px"
						) )?>
					</td>
					<th>总单号</th>
					<td><textarea rows="1" name="return_total_no" placeholder="每行一个单号"><?php echo request('return_total_no')?></textarea></td>
					
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <a class="btn btn-success btn-small" href="<?php echo url('/ReturnIn')?>" >
			             <i class="icon-plus"></i>
			                                         新建
		               </a>
		              
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>总单号</th>
				<th>操作人</th>
				<th>操作日期</th>
				<th>状态</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($total as $temp):?>
		 
        		
			<tr>
				<td style="text-align:right;"><a  target="_blank" href="<?php echo url('/Returnlist',array('return_total_id'=>$temp->return_total_id,'return_total_no'=>$temp->return_total_no))?>"><?php echo $temp->return_total_no?></a></td>
				<td style="text-align:right;"><?php echo $temp->operate_name?></td>
				<td style="text-align:right;"><?php echo date('Y-m-d H:i:s',$temp->create_time)?></td>
				<td style="text-align:right;"><?php echo $temp->status ? '已完成': '未完成'?></td>
				<td><?php if($temp->status == '0'):?>
				
				    <a class="btn btn-mini "
				       href="<?php echo url('/ReturnIn',array('return_total_id' => $temp->return_total_id,'return_total_no'=>$temp->return_total_no))?>">
				    <i class="icon-edit"></i>             
				                        继续扫描
				    </a>
				    <a class="btn btn-mini btn-danger" href="<?php echo url('/ReturnStatus', array('return_total_id' => $temp->return_total_id))?>">
                                                                        完成
				    </a>
				    <?php endif;?>
				    
				    
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
<script type="text/javascript">
function del(obj){
	if (confirm('是否删除?')) {
    	var total_no=$(obj).attr('data');
    	$.ajax({
    		url:'<?php echo url('/deltotalin')?>',
    		data:{total_no:total_no},
    		type:'post',
    		async:false,
    		success:function(data){
    			if(data=='delsuccess'){
    				window.location.reload();
    			}else if(data=='delfalse'){
        			alert('该抵达总单下存在已绑定的订单');
    				window.location.reload();
    			}
    		}
    	});
	}
}
</script>
<?PHP $this->_endblock();?>

