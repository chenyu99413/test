<div>
<?php if (count($rows)==0){
	echo '<h3>无</h3>';
	return;
}?>
<table class="FarTable">
<thead>
	<tr>
	<?php foreach (array_keys(reset($rows)) as $colname):?>
		<th><?php echo $colname?></th>
	<?php endforeach;?>
	</tr>
</thead>
<tbody>
	<?php foreach ($rows as $row):?>
	<tr>
		<?php foreach ($row as $col):?>
		<td><?php echo $col?></td>
		<?php endforeach;?>
	</tr>
	<?php endforeach;?>
</tbody>
</table>
</div>