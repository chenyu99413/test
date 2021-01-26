<div class="">
	<ul class="breadcrumb">
		<?php
		$last = array_slice ( $path, - 1, 1, true );
		array_pop ( $path );
		foreach ( $path as $name => $url ) :
			?>
			<?php if (empty($url)):?>
				<li><?php echo $name?> <span class="divider">/</span>
		</li>
			<?php else:?>
				<li>
			<a href="<?php echo $url?>"><?php echo $name?></a>
			<span class="divider">/</span>
		</li>
			<?php endif;?>
		<?php endforeach;?>
		<?php foreach ($last as $name => $url):?>
			<li class="active">
			<a href="<?php echo $url?>"><?php echo $name?></a>
		</li>
		<?php endforeach;?>
		<li class="pull-right">
			<h4 style="margin-top: -1px; margin-right: 50px; color: red"><?php echo @$waybill_code?></h4>
		</li>
	</ul>
</div>