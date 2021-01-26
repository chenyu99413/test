<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>分区表信息<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<?php
	echo Q::control ( "path", "", array (
		"path" => array (
			"仓位管理" => "",
			"仓位列表" => url ( "partition/search" ),
			"仓位编辑" => '' 
		) 
	) );
?>

<form method="post" onSubmit="return Check();">
	<?php echo Q::control ( "tabs", "description", array ( "tabs" => $tabs, "active_id" => request ( "active_tab", "0" ) ) ); ?>
	<div class="tabs-panels">
		<div class="panel-body panel-body-noheader panel-body-noborder"
			style="padding: 10px;">
			<?php if(request ( "active_tab" ,"0") == "0"):?>
			<div class="FarSearch">
				<table>
					<tbody>
						<tr>
							<th class="required-title">所属部门</th>
							<td>
								<?php
		                            echo Q::control ( 'dropdownlist', 'pos[department_id]', array (
		                            'items'=>$dep,
		                            'value' => $pos->department_id,
		                            'style'=>'width:80px'
		                         ) )?>
							</td>
							<th class="required-title">仓库名称</th>
							<td>
								<input id="warehouse_name" type="text"
									name="pos[warehouse_name]"
									value="<?php echo $pos->warehouse_name?>"
									required="required" />
							</td>
							<th class="required-title">仓库代码</th>
							<td>
								<input id="warehouse_code" type="text"
									name="pos[warehouse_code]"
									value="<?php echo $pos->warehouse_code?>"
									required="required" />
							</td>
							<th class="required-title">库号</th>
							<td>
								<input id="warehouse_no" type="text"
									name="pos[warehouse_no]"
									value="<?php echo $pos->warehouse_no?>"
									required="required" />
							</td>
							<th>区号</th>
							<td>
								<input id="area_code" type="text"
									name="pos[area_code]"
									value="<?php echo $pos->area_code?>" />
							</td>
							<th class="required-title">架号</th>
							<td>
								<input id="frame_code" type="text"
									name="pos[frame_code]"
									value="<?php echo $pos->frame_code?>"
									required="required" />
							</td>
						</tr>
						<tr>
							<th>层号</th>
							<td>
								<input id="floor_code" type="text"
									name="pos[floor_code]"
									value="<?php echo $pos->floor_code?>" />
							</td>
							<th>位号</th>
							<td>
								<input id="tag_code" type="text"
									name="pos[tag_code]"
									value="<?php echo $pos->tag_code?>" />
							</td>
							<th>备注</th>
							<td colspan="7">
								<input id="note" type="text" style="width:100%"
									name="pos[note]"
									value="<?php echo $pos->note?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="FarTool text-center">
				<a class="btn btn-inverse"
					href="<?php echo url('warehouse/poslist')?>">
					<i class="icon-reply"></i>
					返回
				</a>
				<button type="submit" class="btn btn-primary" onclick="Save();">
					<i class="icon-save"></i>
					保存
				</button>
			</div>
			<?php else:?>
			
			<?php endif;?>
		</div>
	</div>
</form>

<script type="text/javascript">
	
</script>

<?PHP $this->_endblock();?>

