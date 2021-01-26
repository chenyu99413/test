<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>分区表信息<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<?php
	echo Q::control ( "path", "", array (
		"path" => array (
			"产品管理" => "",
			"分区列表" => url ( "partition/search" ),
			"分区编辑" => '' 
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
							<th class="required-title">分区名称</th>
							<td>
								<input id="text_partition_name" type="text"
									name="partitionManage[partition_name]"
									value="<?php echo $partitionManage->partition_name?>"
									required="required" />
							</td>
							<th class="required-title">分区数</th>
							<td>
								<input type="number" name="partitionManage[partition_count]"
									value="<?php echo $partitionManage->partition_count?>" min="1"
									required="required" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="FarTool text-center">
				<a class="btn btn-inverse"
					href="<?php echo url('partition/search')?>">
					<i class="icon-reply"></i>
					返回
				</a>
				<button type="submit" class="btn btn-primary" onclick="Save();">
					<i class="icon-save"></i>
					保存
				</button>
			</div>
			<?php else:?>
			<div class="FarTool">
				<a class="btn btn-inverse"
					href="<?php echo url('partition/search')?>">
					<i class="icon-reply"></i>
					返回
				</a>
				<?php
				echo Q::control ( "uploadfile", "file", array (
					"url" => url ( "/import", array (
						"id" => $partitionManage->partition_manage_id 
					) ) 
				) )?>
				<a class="btn btn-info" href="javascript:void(0)"
					onclick="ExportTable('<?php echo url('common/export')?>','table_partition','<?php echo $partitionManage->partition_name?>_分区表_<?php echo Helper_Util::strDate('Y-m-d', time())?>')">
					<i class="icon-download"></i>
					导出
				</a>
			</div>
			<div style="width: 100%; height: 400px; overflow: scroll;">
				<input type="text" onkeyup="FilterItem(this);" />
				<table id="table_partition" class="FarTable">
					<thead>
						<tr>
							<th width="100">二字码</th>
							<th width="100">邮编</th>
							<th width="100">分区号</th>
							<th>国家中文</th>
							<th>英文</th>
							<th width=160>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($partitionManage->partition as $value):?>
						<tr id="<?php echo $value->partition_id?>">
							<td style="text-align: center">
								<?php echo $value->country_code_two?>
								<input type="hidden" value="<?php echo $value->country_code_two?>" />
							</td>
							<td style="text-align: center">
							    <?php echo $value->postal_code?>
							</td>
							<td style="text-align: center">
								<?php echo $value->partition_code?>
							</td>
							<td><?php echo $value->country->chinese_name?></td>
							<td><?php echo $value->country->english_name?></td>
							<td>
								<a class="btn btn-mini" href="javascript:void(0);"
									onclick="EditRow([{'type':'combogrid','option':'country','url':'<?php echo url('common/countrygrid')?>','checkUrl':'<?php echo url('common/checkcountryexist')?>','required':'true'},{'type':'text'},{'type':'number','precision':'0','min':'0','max':'99','required':'true'},{},{}],this);">
									<i class="icon-pencil"></i>
									编辑
								</a>
								<a class="btn btn-mini btn-danger" href="javascript:void(0);"
									onclick="DeleteRow(this);">
									<i class="icon-trash"></i>
									删除
								</a>
							</td>
						</tr>
						<?php endforeach;?>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<a class="btn btn-mini btn-success" href="javascript:void(0);"
									onclick="NewRow([{'type':'combogrid','option':'country','url':'<?php echo url('common/countrygrid')?>','checkUrl':'<?php echo url('common/checkcountryexist')?>','required':'true'},{'type':'text'},{'type':'number','precision':'0','min':'0','max':'99','required':'true'},{},{}],this);">
									<i class="icon-plus"></i>
									新建
								</a>
								<a><?php // 多个A标签时，不到处按钮文字 ?></a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php endif;?>
		</div>
	</div>
</form>

<script type="text/javascript">
	/**
	 * 回调 删除数据
	 */
	function DeleteBefore(obj){
		$.ajax({
			url:"<?php echo url('partition/del')?>",
			type:"POST",
			data:{"partition_id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
			success:function(msg){
			}
		});
	}
				
	/**
	 * 回调 保存数据
	 */
	function CallBack(obj,name){
		if(obj==null){
			return false;
		}
		$.ajax({
			url:"<?php echo url('partition/save')?>",
			type:"POST",
			data:{"id":"<?php echo request('id')?>",
				"partition":{"partition_id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),"country_code_two":$(obj).children().eq(0).text().toUpperCase(),"postal_code":$.trim($(obj).children().eq(1).text()),"partition_code":$.trim($(obj).children().eq(2).text())}
		},
			success:function(msg){
				$(obj).attr("id",msg);
			}
		});
	}

	/**
	 * 判断价格名称是否重复
	 */
	function Check(){
		var result = true;
		$.ajax({
			url:"<?php echo url('partition/checkname')?>",
			type:"POST",
			data:{"id":"<?php echo request("id")?>","name":$("#text_partition_name").val()},
			async : false,
			success:function(msg){
				if(msg!="true"){
					alert(msg);
					result = false;
				}
			}
		});
		return result;
	}

	/**
	 * 过滤
	 */
	function FilterItem(obj){
		var value = $(obj).val().toUpperCase();
		if(value == ""){
			$("#table_partition tr:gt(0)").each(function(){
				$(this).removeAttr("style");
			});
		}else{
			$("#table_partition tr:gt(0)").each(function(){
				var name = $.trim($(this).children().eq(0).text()).toUpperCase();
				if(name.indexOf(value)==0){
					$(this).removeAttr("style");
				}else{
					$(this).attr("style","display:none");
				}
			});
		}
	}
</script>

<?PHP $this->_endblock();?>

