<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
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
					<th>扫描时间</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date')?>" style="width: 125px;">
					</td>
					<th>到</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="end_date"
						value="<?php echo request('end_date')?>" style="width: 125px;">
					</td>
				    <th>操作人</th>
					<td>
						<input type="text" name="operation_name" placeholder="模糊搜索" value="<?php echo request('operation_name')?>"/>
					</td>
					<th>仓库</th>
					<td>
						<?php
	                        echo Q::control ( 'dropdownbox', 'department_id', array (
	                        'items'=>$department,
	                        'empty'=>true,
	                        'style'=>'width:70px',
	                        'value' => request('department_id'),
                        ) )?>
					</td>
					<th>国内快递</th>
					<td>
						<?php
							$logs=CodeLogistics::find()->asArray()->getAll();
							$resl=array();
							foreach ($logs as $l){
								$resl[$l['id']]=$l['name'].'['.$l['code'].']';
							}
							echo Q::control ( "myselect", "logistics_id", array (
								"items" => $resl,
								"selected" => request('logistics_id'),
								'style'=>'width:180px',
							) )
	                     ?>
					</td>
					<th>总单号</th>
					<td>
						<input type="text" name="total_no"  value="<?php echo request('total_no')?>"/>
					</td>
					<th>快递单号</th>
					<td><textarea rows="1" name="reference_no" placeholder="每行一个单号"><?php echo request('reference_no')?></textarea></td>
				</tr>
				<tr>
					<td colspan="3">
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
						<a class="btn btn-success btn-small" href="<?php echo url('warehouse/scanreferenceno')?>" >
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
				<th>No</th>
				<th>总单号</th>
				<th>仓库</th>
				<th>国内快递</th>
				<th style="width:160px;">扫描时间</th>
				<th>总包裹数</th>
				<th>操作人</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			$i=1; foreach ($sacnlist as $temp):
			$count = ScanTotalDetail::find('total_id = ?',$temp->id)->getCount();
		?>
		<tr>
			<td><?php echo $i++?></td>
			<td><a target="_blank" href="<?php echo url('warehouse/scantotaldetail',array('id'=>$temp->id))?>"><?php echo $temp->total_no?></a></td>
			<td><?php echo $department[$temp->department_id]?></td>
			<td><?php echo $logistics[$temp->logistics_id]?></td>
			<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->scan_no_time)?></td>
			<td style="text-align:right;"><?php echo $count?></td>
			<td><?php echo $temp->operation_name?></td>
			<td>
						<a class="btn btn-small btn-info editmodal" data-id="<?php echo $temp->id?>" data-toggle="tooltip" data-placement="top">
    						修改
    					</a>
			</td>
	
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>	
</form>
	
	<div id="change-postmode-modal" class="modal hide fade" tabindex="-1"
            aria-hidden="true" >
        <div class="modal-body" style="height: 260px;">
           <form action="<?php echo url('/changescan')?>" method="post" onsubmit="return checkdata();">
           <input type="hidden" name="scan_id" id="scan_id" value="">
                <table class="table table-bordered table-condensed">
                    <caption>修改物流方式</caption>
                    <tr>
                        <th style="width: 100px;">物流方式:</th>
                        <td style="width: 250px;">
                             <?php
								$logs=CodeLogistics::find()->asArray()->getAll();
								$resl=array();
								foreach ($logs as $l){
									$resl[$l['id']]=$l['name'].'['.$l['code'].']';
								}
								echo Q::control ( "myselect", "lgt_id", array (
									"items" => $resl,
									"selected" => request('lgt_id'),
									'style'=>'width:180px',
								) )
							?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right; ">                          
                            <input type="submit" value="保存" class="btn btn-sm btn-danger" >
                            <input type="button" value="取消" class="btn btn-sm" onclick="return $('#change-postmode-modal').modal('hide');" >
                        </td>
                    </tr>
                    
                </table>
            </form>
        </div>
    </div>
    
<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
?>
<script type="text/javascript">


    $('.editmodal').click(function(){
        var id=$(this).data('id');
        $('#scan_id').val(id);
        $('#change-postmode-modal').modal('show');
    });


function checkdata(){
	if($('#lgt_id').val()==''){
		alert('国内快递不能为空');return false;
	}
	return true;
}
</script>
<?PHP $this->_endblock();?>

