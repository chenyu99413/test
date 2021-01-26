<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <style type="text/css">
        .box{
/*         	position: absolute; */
/*             left: 0; */
/*             top: 0; */
            width: 17%;
            height: 200px;
        	float:left;
/*             overflow: hidden; */
            padding: 10px;
        	cursor:pointer;
        }
        #grid {
/*             position: relative; */
        }
        .img-thumbnail{display:inline-block;max-width:100%;height:auto;padding:4px;line-height:1.42857143;
background-color:#FFF;border:1px solid #DDD;border-radius:4px;transition:all 0.2s ease-in-out;}
    </style>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'业务管理' => '','订单查询' => url ( 'order/search' ),'订单编辑' => '' 
	) 
) )?>
	<div class="FarSearch" style="line-height: 6px; margin-top:-8px;padding:0;">
		<table>
			<tbody>
				<tr>
					<th>阿里订单号</th>
					<td>
						<?php echo $order->ali_order_no?>
					</td>
					<th>泛远单号</th>
					<td>
						<?php echo $order->far_no?>
					</td>
					<th>客户单号</th>
					<td>
						<?php echo $order->order_no?>
					</td>
					<th>产品</th>
					<td>
					    <?php echo Product::find('product_name = ?',$order->service_code)->getOne()->product_chinese_name?>
					</td>
					<?php if($order->service_code=='EMS-FY' && $order->ems_order_id):?>
					<th>EMS面单ID</th>
					<td>
						<a target="_blank" href="http://134.175.95.217:8089/order/FastRpt/PDF_NEW.aspx?PrintType=A4&order_id=<?php echo $order->ems_order_id ?>"><?php echo $order->ems_order_id?></a>
					</td>
					<?php elseif ($order->service_code=='ePacket-FY' && $order->ems_order_id):?>
					<th>EUB面单ID</th>
					<td>
						<a target="_blank" href="http://134.175.95.217:8089/order/FastRpt/PDF_NEW.aspx?PrintType=lab10_10&order_id=<?php echo $order->ems_order_id ?>"><?php echo $order->ems_order_id?></a>
					</td>
					<?php endif;?>
					<?php if($order->service_code=='EUUS-FY' && $order->ems_order_id):?>
					<th>欧美专线面单ID</th>
					<td>
						<a target="_blank" href="http://tms.mailiancn.com:8086/xms/client/order_online!print.action?userToken=a43fb2769566442291622d8c6e6d8b5c&oid=<?php echo $order->ems_order_id?>&printSelect=3&pageSizeCode=3&showCnoBarcode=0"><?php echo $order->ems_order_id?></a>
					</td>
					<?php endif;?>
					<?php $status=Order::$status?>
			        <th style="text-align: center;font-size:20px;">
			            <?php echo $status[$order->order_status]?>
			        </th>
			        <?php if(Helper_ViewPermission::isAudit()):?>
					<th>
						<?php if($order->order_status=='4'):?>
						<form method="post" action="<?php echo url('/manualout')?>">
						<div style="margin-left:5px;" class="span15">
						  <input type="hidden" name="order_id" value="<?php echo $order->order_id?>">
						  <button class="btn btn-mini btn-primary" style="margin-right: 0px;">手动出库</button>
						</div>
						</form>
						<?php endif;?>
						<?php if($order->order_status=='8'):?>
						<form method="post" id="termination" action="<?php echo url('/termination')?>">
						<div style="margin-left:5px;" class="span15">
						  <input type="hidden" name="order_id" value="<?php echo $order->order_id?>">
						  <a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="Save()" style="margin-right: 0px;">设置为已结束</a>
						</div>
						</form>
						<?php endif;?>
						<?php if($order->order_status=='6'&& $order->service_code=='Express_Standard_Global' && $order->is_send=='0' && $order->channel->network_code<>'UPS'):?>
						<form method="post" action="<?php echo url('/artificialsend')?>">
						<div style="margin-left:5px;" class="span15">
						  <input type="hidden" name="order_id" value="<?php echo $order->order_id?>">
						  <button class="btn btn-mini btn-info" style="margin-right: 0px;">设置为已发送</button>
 						</div>
						</form>
						<?php endif;?>
					</th>
					<?php endif;?>
					<?php if(Helper_ViewPermission::isAudit()):?>
					<td>
					    <?php if((in_array($order->order_status, array('6','7','8')) && MyApp::checkVisible('order-return-paid')) || $order->order_status == '6'):?>
						<form method="post" id="tuipay" action="<?php echo url('/tuipay')?>">
						<div style="margin-left:5px;" class="span15">
						  <input type="hidden" name="order_id" value="<?php echo $order->order_id?>">
						  <!--<a class="btn btn-mini btn-danger" href="javascript:void(0)" onclick="Savepay()" style="margin-right: 0px;">退回已支付</a>-->
						</div>
						</form>
						<?php endif;?>
					</td>
					<?php endif;?>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="easyui-tabs" id="wTab" data-options="tools:'#tab-tools'"  style="min-height: 350px; margin-top:-5px;">
        <div title="订单详情"
			data-options="href:'<?php echo url('order/editdetail',array('order_id'=>$order->order_id))?>'"
			style="padding: 1px 5px 5px 5px"></div>
		<!-- 		订单应收应付权限 -->
		<?php if(MyApp::checkVisible('order-receivable-payment')):?>
		<?php 
			//限制青岛权限
			//$staff_code=MyApp::currentUser('staff_code');
			$department=Department::find('department_id=?',MyApp::currentUser('department_id'))->getOne();
			if($department->status==0){			
		?>
			<div title="应收应付"
				data-options="href:'<?php echo url('order/editbalance',array('order_id'=>$order->order_id))?>'"
				style="padding: 1px 5px 5px 5px"></div>
		<?php }?>
		<?php endif;?>
		<div title="操作日志"
			style="padding: 1px 5px 5px 5px">
			<table class="FarTable" >
			<thead>
			<tr>
				<th>操作人</th>
				<th>时间</th>
				<th>日志</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($order->logs as $log):?>
			<tr>
				<td nowrap="nowrap"><?php echo strlen($log->staff_name)?$log->staff_name:'系统'?></td>
				<td nowrap="nowrap"><?php echo date('Y-m-d H:i:s',$log->create_time)?></td>
				<td><?php echo $log->comment?></td>
			</tr>
			<?php endforeach;?>
			</tbody>
			</table>
		</div>
		<div title="照片信息"
			style="padding: 1px 5px 5px 5px">
			<div class="main-content" id="grid">
                <?php foreach ($order->pictures as $picture):?>
                <?php if(substr($picture->file_path,0,4) == 'http'):?>
                <div class="box"><img class="img-thumbnail pimg" src="<?php echo $picture->file_path?>" style="width:100%;height:200px;"></div>
                <?php else :?>
                <div class="box"><img class="img-thumbnail pimg"  src="<?php echo $_BASE_DIR.strstr($picture->file_path,'public/upload/files')?>" style="width:100%;height:200px;"></div>
                <?php endif;?>
                <?php endforeach;?>
                <?php $kuaishou_picurl = 'http://ia1.oss-cn-hangzhou.aliyuncs.com/kuaishou/'.$order->ali_order_no.'.jpg';?>
                <?php if (@fopen( $kuaishou_picurl, 'r' )):?>
                <div class="box"><img class="img-thumbnail pimg"  src="<?php echo $kuaishou_picurl?>" style="width:100%;height:200px;"></div>
                <?php endif;?>
            </div>
		</div>
	</div>
	<div id="tab-tools">
		<a target="_blank" href="<?php echo url('order/event',array('order_id'=>$order->order_id))?>" class="btn btn-small btn-info">事件</a>
		<a target="_blank" href="<?php echo url('order/trace',array('order_id'=>$order->order_id))?>" class="btn btn-small btn-info">轨迹</a>
	</div>
	<div id="outerdiv"
		style="position: fixed; top: 0; left: 0; background: rgba(0, 0, 0, 0.7); z-index: 100; width: 100%; height: 100%; display: none;">
		<div id="innerdiv" style="position: absolute;">
			<img id="bigimg" style="border: 5px solid #fff;" src="" />
		</div>
	</div>

<?PHP $this->_endblock();?>
<script type="text/javascript">
// function itemWaterfull() {
//     var margin = 0;  //每个item的外边距，因人需求而定
//     var items = $(".box");  //每个item的统一类名
//     var item_width = items[0].offsetWidth + margin; //取区块的实际宽度
//     $("#grid").css("padding", "0");  //容器的起始内边距先设为0，按之后一行item的宽度再来设，保证所有item的居中
//     var container_width = $("#grid")[0].offsetWidth; //获取容器宽度
//     var n = parseInt(container_width / item_width);  //一行所允许放置的item个数
//     var container_padding = (container_width - (n * item_width)) / 2; //一行宽度在容器中所剩余的宽度，设为容器的左右内边距
//     $("#grid").css("padding", "0 " + container_padding + "px");
//     //寻找数组最小高度的下标
//     function findMinIndex(arr) {
//         var len = arr.length, min = 999999, index = -1;
//         for(var i = 0; i < len; i++) {
//             if(min > arr[i]) {
//                 min = arr[i];
//                 index = i;
//             }
//         }
//         return index;
//     }
//     //放置item
//     function putItem() {
//         var items_height = [];  //每个item的高度
//         var len = items.length;  //获取item的个数
//         for(var i = 0; i < len; i++) {
//             var item_height = items[i].offsetHeight;  //获取每个item的高度
//             //放置在第一行的item
//             if(i < n) {
//                 items_height[i] = item_height;  //高度数组更新
//                 items.eq(i).css("top", 0);
//                 items.eq(i).css("left", i * item_width);

//             } else {  //放置在其他行的item  
//                 var final_row_fir = parseInt(len / n) * n; //最后一行第一个item的下标
//                 //处于最后一行
//                 if(final_row_fir <= i) {
//                     var index = i - final_row_fir;  //该item所应该放置的列
//                     items.eq(i).css("top", items_height[index] + margin);
//                     items.eq(i).css("left", index * item_width);
//                     items_height[index] += item_height + margin;
//                 } else {      
//                     var min_index = findMinIndex(items_height);  //寻找最小高度
//                     if(min_index == -1) {
//                         console.log("高度计算出现错误");
//                         return ;
//                     }
//                     items.eq(i).css("top", items_height[min_index] + margin);
//                     items.eq(i).css("left", min_index * item_width);
//                     items_height[min_index] += item_height + margin;  //高度数组更新
//                 }
//             }
//         }
//         var max_height = Math.max.apply(null, items_height);
//         $("#grid").css("height", max_height);   //最后更新容器高度
//     }
//     putItem();
// }
// itemWaterfull();
// window.onresize = function() {itemWaterfull();};
$(function() {
	$(".pimg").click(function() {
		var _this = $(this);//将当前的pimg元素作为_this传入函数  
		imgShow("#outerdiv", "#innerdiv", "#bigimg", _this);
	});
});

function imgShow(outerdiv, innerdiv, bigimg, _this) {
	var src = _this.attr("src");//获取当前点击的pimg元素中的src属性  
	$(bigimg).attr("src", src);//设置#bigimg元素的src属性  

	/*获取当前点击图片的真实大小，并显示弹出层及大图*/
	$("<img/>").attr("src", src).load(function() {
		var windowW = $(window).width();//获取当前窗口宽度  
		var windowH = $(window).height();//获取当前窗口高度  
		var realWidth = this.width;//获取图片真实宽度  
		var realHeight = this.height;//获取图片真实高度  
		var imgWidth, imgHeight;
		var scale = 0.8;//缩放尺寸，当图片真实宽度和高度大于窗口宽度和高度时进行缩放  

		if (realHeight > windowH * scale) {//判断图片高度  
			imgHeight = windowH * scale;//如大于窗口高度，图片高度进行缩放  
			imgWidth = imgHeight
			/realHeight*realWidth;// 等比例缩放宽度
			if (imgWidth > windowW * scale) {//如宽度扔大于窗口宽度  
				imgWidth = windowW * scale;//再对宽度进行缩放  
			}
		} else if (realWidth > windowW * scale) {//如图片高度合适，判断图片宽度  
			imgWidth = windowW * scale;//如大于窗口宽度，图片宽度进行缩放  
			imgHeight = imgWidth/realWidth*realHeight;// 等比例缩放高度
		} else {//如果图片真实高度和宽度都符合要求，高宽不变  
			imgWidth = realWidth;
			imgHeight = realHeight;
		}
		$(bigimg).css("width", imgWidth);//以最终的宽度对图片缩放  

		var w = (windowW - imgWidth)/2;// 计算图片与窗口左边距
		var h = (windowH - imgHeight)/2;// 计算图片与窗口上边距
		$(innerdiv).css({
			"top" : h,
			"left" : w
		});//设置#innerdiv的top和left属性  
		$(outerdiv).fadeIn("fast");//淡入显示#outerdiv及.pimg  
	});

	$(outerdiv).click(function() {//再次点击淡出消失弹出层  
		$(this).fadeOut("fast");
	});
}
function Save(){
	$.messager.confirm('设置为已结束','确定结束？',function(r){
		if(r){
			$("#termination").submit();
		}
	})
}
function Savepay(){
	$.messager.confirm('退回已支付','确定退回？',function(r){
		if(r){
			$("#tuipay").submit();
		}
	})
}
</script>
