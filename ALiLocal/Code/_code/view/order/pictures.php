<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<form method="post" enctype="multipart/form-data">
	<table style="width: 450px;">
		<colgroup>
			<col width="100px" />
			<col width="" />
		</colgroup>
		<tbody>
			<tr>
				<th>选择文件</th>
				<td><input id="file" type="file" name="file" accept="application/zip">
					<input type="submit" class="btn btn-small btn-info" name="submit" value="上传" />
				</td>
			</tr>
		</tbody>
	</table>
</form>  
<?PHP $this->_endblock();?>

