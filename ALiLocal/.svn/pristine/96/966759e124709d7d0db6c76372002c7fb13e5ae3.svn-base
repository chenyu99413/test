<label style="margin-left: 4px;">
	<input id="<?php echo @$id?>" type="checkbox"
		value="<?php echo @$value?>"
		<?php echo @$value == "1" ? "checked='checked'" : ''?>
		style="margin-top: -4px;"
		onclick="if($(this).attr('checked') == 'checked'){$('#hidden_<?php echo @$id?>').val('1')}else{$('#hidden_<?php echo @$id?>').val('0')}" />
	<?php echo @$text?>
</label>
<input id="hidden_<?php echo @$id?>" type="hidden"
	name="<?php echo @$name?>"
	value="<?php echo @$value == "1" ? "1" : "0"?>" />
