<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript">
var tns=[];
</script>
<form method="post" >
    <div class="FarSearch">
        <table>
            <tbody>
                <tr>
                    <th>超时天数</th>
                    <td>
                    	<input type="text" name="time" value="<?php echo request('time')?>" />
                    </td>
                    <td>
                        <button class="btn btn-small btn-info" name="search" id="search">
							<i class="icon-search"></i>
							搜索
						</button>
                    </td>
                    <th>设置超时天数</th>
                    <td>
                    	<input type="text" name="time_config" value="<?php echo $time_config->v?>" />
                    </td>
                    <td>
                        <button class="btn-small btn btn-danger" name="do" value="shezhi" id="shezhi">
							<i class="icon-cog"></i>
							设置
						</button>
                    </td>                       
                </tr>   
            </tbody>    
        </table>
    </div>
</form>
<table class="FarTable">
<thead>
<tr>
	<th>阿里单号</th>
	<th>DST</th>
	<th>客户</th>
	<th>产品</th>
	<th>入库时间</th>
	<th>滞留时间(天)</th>
<!-- 	<th>入库时间</th> -->
<!-- 	<th>操作</th> -->
<!-- 	<th>?</th> -->
</tr>
</thead>
<tbody>
<?php foreach ($list as $row):?>
<tr>
	<td><a href="<?php echo url('order/detail',array('order_id'=>$row->order_id))?>"><?php echo $row->ali_order_no?></a></td>
	<td><?php echo $row->consignee_country_code?></td>
	<td>
		<?php echo $row->customer->customer?>
	</td>
	<td><?php echo $row->service_product->product_chinese_name?></td>
	
	<td><?php echo date('Y-m-d H:i:s',$row->warehouse_in_time)?></td>
	<td><?php echo ceil((time() - $row->warehouse_in_time)/86400)?></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
<script type="text/javascript">

</script>
<?PHP $this->_endblock();?>

