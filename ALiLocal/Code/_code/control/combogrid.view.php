<input type="text" class="easyui-combogrid" <?php echo @$attr?>
	data-options='
        panelWidth: 500,
        value:"<?php echo @$value?>",
        idField: "<?php echo @$idField ?>",
        textField: "<?php echo @$textField?>",
        <?php if (!empty($onChange)):?>
        onChange: <?php echo $onChange?>,
        <?php endif;?>
        <?php if (!empty($onLoadSuccess)):?>
        onLoadSuccess: <?php echo $onLoadSuccess?>,
        <?php endif;?>
        <?php if (!empty($data)):?>
        data: <?php echo $data?>,
        <?php endif;?>
        <?php if (!empty($url)):?>
        url: "<?php echo $url?>",
        <?php endif;?>
        <?php if (!empty($mode)):?>
        mode:"<?php echo $mode?>",
        <?php endif;?>
        <?php if (!empty($onSelect)):?>
        onSelect:<?php echo $onSelect?>,
        <?php endif;?>
        <?php if (!empty($onHidePanel)):?>
        onHidePanel:<?php echo $onHidePanel?>,
        <?php endif;?>
        
        <?php if (!empty($multiple)):?>
        multiple:<?php echo $multiple?>,
        <?php endif;?>
        delay:400,
        invalidMessage:"请检查此项是否填写正确",
        missingMessage:"请填写此项并检查此项是否填写正确",
        selectOnNavigation:false,
        columns: [<?php echo json_encode($columns)?>],
        <?php if (isset($required) && $required!='false'):?>
        required:true,
        <?php endif;?>
        fitColumns: true'
		<?php if (!empty($validType)):?>
		validType="<?php echo $validType?>",
        <?php endif;?> />

<span id="<?php echo  $id.'_addon'?>"><a target="_blank" href="<?php echo url('customs/edit',array('id'=>$value)); ?>"><?php echo $addon_text?></a></span>

