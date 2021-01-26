<?php //布局设定 ，参考 view/_layouts下面的文件 ?>
<?PHP $this->_extends('_layouts/kp_layout'); ?>
<?PHP $this->_block('title');?>
    取件员档案
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
	<form action="" method="post">
	<div class="weui-form">
		<div class="weui-form__text-area">
<!-- 			<h2 class="weui-form__title">档案</h2> -->
			<div class="weui-form__desc"><img alt="" src="<?php echo $m->img_url?>" width="80"></div>
		</div>
		<div class="weui-form__control-area">
			<div class="weui-cells__group weui-cells__group_form">
				<div class="weui-cells weui-cells_form">
					<div class="weui-cell weui-cell_active">
						<div class="weui-cell__hd">
							<label class="weui-label">微信号</label>
						</div>
						<div class="weui-cell__bd">
							<input id="js_input" class="weui-input" placeholder="填写本人微信号" value="<?php echo $m->wechat_no?>" readonly="readonly">
						</div>
					</div>
					<div class="weui-cell weui-cell_active">
						<div class="weui-cell__hd">
							<label class="weui-label">姓名</label>
						</div>
						<div class="weui-cell__bd">
							<input id="js_input" name="name" class="weui-input" placeholder="填写本人真实姓名，以便审核" value="<?php echo $m->name?>" required="required">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="weui-form__tips-area">
			<p class="weui-form__tips"><?php if ($m->name && $m->status ==0) :?>审核中<?php endif?></p>
		</div>
		<?php if ($m->status ==0) :?>
		<div class="weui-form__opr-area">
			<input class="weui-btn weui-btn_primary " value="提交审核" type="submit">
		</div>
		<?php else:?>
		<div class="weui-form__opr-area">
			<a class="weui-btn weui-btn_primary "  href="<?php echo url('/pkscan')?>">取件扫描</a>
		</div>
		<?php endif?>
		<div class="weui-form__extra-area">
			<div class="weui-footer">
				<p class="weui-footer__links">
					<a href="http://www.far800.com" class="weui-footer__link">www.far800.com</a>
				</p>
				<p class="weui-footer__text">Copyright © 2020 FAR International</p>
			</div>
		</div>
	</div>
	</form>
</div>
<?PHP $this->_endblock();?>

