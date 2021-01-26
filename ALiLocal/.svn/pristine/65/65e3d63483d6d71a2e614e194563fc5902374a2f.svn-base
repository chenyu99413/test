<?php //布局设定 ，参考 view/_layouts下面的文件 ?>
<?PHP $this->_extends('_layouts/weui_layout'); ?>
<?PHP $this->_block('title');?><?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<style>
    *{margin: 0;padding: 0;}
    li{list-style-type: none;}
    a,input{outline: none;-webkit-tap-highlight-color:rgba(0,0,0,0);}
    #choose{display: none;}
    canvas{width: 100%;border: 1px solid #000000;}
    
    .touch{background-color: #ddd;}
    .img-list{margin: 10px 5px;}
    .img-list li{position: relative;display: inline-block;width: 100px;height: 100px;margin: 5px 5px 20px 5px;border: 1px solid rgb(100,149,198);background: #fff no-repeat center;background-size: cover;}
    .progress{position: absolute;width: 100%;height: 20px;line-height: 20px;bottom: 0;left: 0;background-color:rgba(100,149,198,.5);}
    .progress span{display: block;width: 0;height: 100%;background-color:rgb(100,149,198);text-align: center;color: #FFF;font-size: 13px;}
    .size{position: absolute;width: 100%;height: 15px;line-height: 15px;bottom: -18px;text-align: center;font-size: 13px;color: #666;}
    .tips{display: block;text-align:center;font-size: 13px;margin: 10px;color: #999;}
    .pic-list{margin: 10px;line-height: 18px;font-size: 13px;}
    .pic-list a{display: block;margin: 10px 0;}
    .pic-list a img{vertical-align: middle;max-width: 30px;max-height: 30px;margin: -4px 0 0 10px;}
  </style>
  <div class="weui-btn-area">
			<a id="button" style="background-color:#07a7c1;width:100%" class="weui-btn weui-btn_primary">提交</a>
		</div>

<div class="page__bd">
	<form action="<?php echo url('wxalipickupdevice/upload')?>" method="post">
		<div class="weui-cells weui-cells_form" style="margin: 0;">
			<div class="weui-cell">
				<div class="weui-cell__hd">
					<label class="weui-label">单号</label>
				</div>
				<div class="weui-cell__bd">
					<input class="weui-input" type="text" name="orderno" id="orderno" value="" />
					<input type="hidden" name="order_id" id="order_id" value="" />
					<input type="hidden" id="wechat_id" name="wechat_id" value="<?php echo request('wechat_id')?>">
				</div>
			</div>
			
			<div class="weui-btn-area">
				<a href="javascript:scan();" style="width:100%" class="weui-btn weui-btn_default">扫一扫</a>
			</div>
		</div>
		<div class="weui-cells__tips" id="error" style="color: red;"></div>
		<ul class="img-list"></ul>
		<input type="file" id="choose" accept="image/*"  capture="camera">
		<div class="weui-btn-area" style="text-align:center">
			<a id="upload" style="width:100%" class="weui-btn  weui-btn_primary">拍照</a>
			<a id="upload2" style="width:100%" class="weui-btn  weui-btn_primary upload">相册</a>
		</div>
		
	</form>
</div>
<script src=http://res.wx.qq.com/open/js/jweixin-1.2.0.js></script>
<script>
	$("#orderno").select();
	$('#orderno').on('blur',function(){
		input_event()
	})
	function input_event(){
		$('.img-list').empty();
		img_files = '';
		if($('#orderno').val().length != ''){
			$.ajax({
	               type: "POST",//规定传输方式
	               url: "<?php echo url('wxalipickupdevice/upload') ?>",//提交URL
	               data: {'orderno':$('#orderno').val()},//提交的数据
	               dataType: 'json',
	               success: function(data){
		               console.log(data)
	                       if(data.code){
	                    	   $('#error').html('');
	                    	   $('#order_id').val(data.msg);
		                   }else{
								$("#orderno").val('').select();
		                    	$('#order_id').val('');
								$('#error').html('单号不存在');
			               }
	                  }
	            });
		}
	}
  var img_files = ''; //用于保存所有图片
  var filechooser = document.getElementById("choose");
  //    用于压缩图片的canvas
  var canvas = document.createElement("canvas");
  var ctx = canvas.getContext('2d');
  //    瓦片canvas
  var tCanvas = document.createElement("canvas");
  var tctx = tCanvas.getContext("2d");
  var maxsize = 100 * 1024;
  //拍照
  $("#upload").on("click", function() {
		if($('#orderno').val().length == 0 || $('#order_id').val().length == 0){
			alert('请先扫描单号');
			return false;
		}
		$('#choose').attr('capture','camera');
        filechooser.click();
      })
  //选择图片
  $("#upload2").on("click", function() {
		if($('#orderno').val().length == 0 || $('#order_id').val().length == 0){
			alert('请先扫描单号');
			return false;
		}
		$('#choose').removeAttr('capture');
        filechooser.click();
      })
      .on("touchstart", function() {
        $(this).addClass("touch")
      })
      .on("touchend", function() {
        $(this).removeClass("touch")
      });
  //选择图片
  filechooser.onchange = function() {
    if (!this.files.length) return;
    var files = Array.prototype.slice.call(this.files);
    //把数据存到全局变量，input标签会覆盖上一次的图片内容，批量上传使用
    if(img_files == ''){
    	img_files = files;
    }else{
    	$.each(files,function(k,v){
        	img_files[img_files.length] = v;
        });
    }
//     console.log(img_files);
//     return false
//     if (files.length > 9) {
//       alert("最多同时只可上传9张图片");
//       return;
//     }
    files.forEach(function(file, i) {
      if (!/\/(?:jpeg|png|gif)/i.test(file.type)) return;
//         console.log(1)
//         return false
      var reader = new FileReader();
      var li = document.createElement("li");
//          获取图片大小
      //var size = file.size / 1024 > 1024 ? (~~(10 * file.size / 1024 / 1024)) / 10 + "MB" : ~~(file.size / 1024) + "KB";
      li.innerHTML = '<div class="progress" style="display:none"><span></span></div><div class="size" onclick="del()">删除</div>';
      $(".img-list").append($(li));
      //回调
      reader.onload = function() {
        var result = this.result;
        var img = new Image();
        img.src = result;
        //给li添加id
        var li_count = $('.img-list li').length-1
        for(li_count; $("#"+li_count).size()>0; li_count++){
        	li_count = li_count;
        }
        $(li).attr('id',li_count);
        $('#'+li_count+' .size').attr('onclick','del('+li_count+')');
        //添加图片
        $(li).css("background-image", "url(" + result + ")"); 
        
      };
      reader.readAsDataURL(file);
    })
  };
  //上传文件
  $('#button').click(function(){
	  if($('#orderno').val().length == 0 || $('#order_id').val().length == 0){
			alert('请先扫描单号');
			return false;
		}
	  if (!img_files) return;
	    //var files = Array.prototype.slice.call(img_files[0]);
	 	 img_files.forEach(function(file, i) {
	    	if (!/\/(?:jpeg|png|gif)/i.test(file.type)) return;
	    	//获取li的id值
	    	var li_id = $(".img-list").find("li").eq(i).attr('id');
	    	//已经上传的跳过
	    	if($('#'+li_id+' .size').html() == '上传成功') return;
	    	var reader = new FileReader();
	  		//回调函数，reader.readAsDataURL执行之后才会运行，不然不运行此代码
	    	reader.onload = function() {
// 		 	 	  console.log(reader);
// 		 	 	  return false
	            var result = this.result;
	            var img = new Image();
	            img.src = result;
	            //如果图片大小小于100kb，则直接上传
	            if (result.length <= maxsize) {
	              img = null;
	              upload(result, file.type, li_id, file.name);
	              return;
	            }
				//	          图片加载完毕之后进行压缩，然后上传
	            if (img.complete) {
	              callback();
	            } else {
	              img.onload = callback;
	            }
	            function callback() {
	              var data = compress(img);
	              upload(data, file.type, li_id, file.name);
	              img = null;
	            }
	          };
	          //执行这个方法之后程序才会调用reader.onload
	          reader.readAsDataURL(file); //读取图像文件++++++++++++++++
	    })
		alert('上传成功')
  })
  //删除图片
  function del(id){
	  if($('#'+id+' .size').html() == '删除'){
		  img_files.splice($('#'+id).index(),1);
		  $('#'+id).remove();
	  }
  }
  //    使用canvas对大图片进行压缩
  function compress(img) {
    var initSize = img.src.length;
    var width = img.width;
    var height = img.height;
    //如果图片大于四百万像素，计算压缩比并将大小压至400万以下
    var ratio;
    if ((ratio = width * height / 4000000) > 1) {
      ratio = Math.sqrt(ratio);
      width /= ratio;
      height /= ratio;
    } else {
      ratio = 1;
    }
    canvas.width = width;
    canvas.height = height;
//        铺底色
    ctx.fillStyle = "#fff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    //如果图片像素大于100万则使用瓦片绘制
    var count;
    if ((count = width * height / 1000000) > 1) {
      count = ~~(Math.sqrt(count) + 1); //计算要分成多少块瓦片
//            计算每块瓦片的宽和高
      var nw = ~~(width / count);
      var nh = ~~(height / count);
      tCanvas.width = nw;
      tCanvas.height = nh;
      for (var i = 0; i < count; i++) {
        for (var j = 0; j < count; j++) {
          tctx.drawImage(img, i * nw * ratio, j * nh * ratio, nw * ratio, nh * ratio, 0, 0, nw, nh);
          ctx.drawImage(tCanvas, i * nw, j * nh, nw, nh);
        }
      }
    } else {
      ctx.drawImage(img, 0, 0, width, height);
    }
    //进行最小压缩
    var ndata = canvas.toDataURL('image/jpeg', 0.1);
    console.log('压缩前：' + initSize);
    console.log('压缩后：' + ndata.length);
    console.log('压缩率：' + ~~(100 * (initSize - ndata.length) / initSize) + "%");
    tCanvas.width = tCanvas.height = canvas.width = canvas.height = 0;
    return ndata;
  }
  //    图片上传，将base64的图片转成二进制对象，塞进formdata上传
  function upload(basestr, type, li_id, name) {
// 	  console.log(basestr);
// 	  console.log(type);
// 	  console.log(i);
// 	  return false
    var text = window.atob(basestr.split(",")[1]);
    var buffer = new Uint8Array(text.length);
    var pecent = 0, loop = null;
    for (var i = 0; i < text.length; i++) {
      buffer[i] = text.charCodeAt(i);
    }
    var blob = getBlob([buffer], type);

    var xhr = new XMLHttpRequest();
    var formdata = getFormData();
//     console.log(buffer);
//     return false
    formdata.append('imagefile', blob);
    xhr.open('post', '<?php echo url("wxalipickupdevice/uploadimg2")?>'+'?order_id='+$('#order_id').val()+'&name='+name+'&wechat_id='+$('#wechat_id').val());  //后台地址
    xhr.onreadystatechange = function() {
        //console.log(xhr)
        //成功后执行
      if (xhr.readyState == 4 && xhr.status == 200) {
        var jsonData = JSON.parse(xhr.response);
        //var imagedata = jsonData[0] || {};
        var text = jsonData.code ? '上传成功' : '上传失败';
        console.log(text + '：' + xhr);
        clearInterval(loop); //上传进度条结束
        //当收到该消息时上传完毕
        $('#'+li_id).find(".progress span").animate({'width': "100%"}, pecent < 95 ? 200 : 0, function() {
        	$('#'+li_id+' .size').html(text);
        });
        //if (!xhr.response.code) return;
        //$(".pic-list").append('<a href="' + imagedata.path + '">' + imagedata.name + '（' + imagedata.size + '）<img src="' + imagedata.path + '" /></a>');
      }
    };
    //数据发送进度，前50%展示该进度
    xhr.upload.addEventListener('progress', function(e) {
      if (loop) return;
      pecent = ~~(100 * e.loaded / e.total) / 2;

      console.log(pecent)
      $('#'+li_id).find(".progress").css('display','block');
      $('#'+li_id).find(".progress span").css('width', pecent + "%");
      if (pecent == 50) {
        mockProgress();
      }
    }, false);
    //数据后50%用模拟进度
    function mockProgress() {
      if (loop) return;
      loop = setInterval(function() {
        pecent++;
        $('#'+li_id).find(".progress span").css('width', pecent + "%");
        if (pecent == 99) {
          clearInterval(loop);
        }
      }, 100)
    }
    xhr.send(formdata);
  }
  /**
   * 获取blob对象的兼容性写法
   * @param buffer
   * @param format
   * @returns {*}
   */
  function getBlob(buffer, format) {
    try {
      return new Blob(buffer, {type: format});
    } catch (e) {
      var bb = new (window.BlobBuilder || window.WebKitBlobBuilder || window.MSBlobBuilder);
      buffer.forEach(function(buf) {
        bb.append(buf);
      });
      return bb.getBlob(format);
    }
  }
  /**
   * 获取formdata
   */
  function getFormData() {
    var isNeedShim = ~navigator.userAgent.indexOf('Android')
        && ~navigator.vendor.indexOf('Google')
        && !~navigator.userAgent.indexOf('Chrome')
        && navigator.userAgent.match(/AppleWebKit\/(\d+)/).pop() <= 534;
    return isNeedShim ? new FormDataShim() : new FormData()
  }
  /**
   * formdata 补丁, 给不支持formdata上传blob的android机打补丁
   * @constructor
   */
  function FormDataShim() {
    console.warn('using formdata shim');
    var o = this,
        parts = [],
        boundary = Array(21).join('-') + (+new Date() * (1e16 * Math.random())).toString(36),
        oldSend = XMLHttpRequest.prototype.send;
    this.append = function(name, value, filename) {
      parts.push('--' + boundary + '\r\nContent-Disposition: form-data; name="' + name + '"');
      if (value instanceof Blob) {
        parts.push('; filename="' + (filename || 'blob') + '"\r\nContent-Type: ' + value.type + '\r\n\r\n');
        parts.push(value);
      }
      else {
        parts.push('\r\n\r\n' + value);
      }
      parts.push('\r\n');
    };
    // Override XHR send()
    XMLHttpRequest.prototype.send = function(val) {
      var fr,
          data,
          oXHR = this;
      if (val === o) {
        // Append the final boundary string
        parts.push('--' + boundary + '--\r\n');
        // Create the blob
        data = getBlob(parts);
        // Set up and read the blob into an array to be sent
        fr = new FileReader();
        fr.onload = function() {
          oldSend.call(oXHR, fr.result);
        };
        fr.onerror = function(err) {
          throw err;
        };
        fr.readAsArrayBuffer(data);
        // Set the multipart content type and boudary
        this.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
        XMLHttpRequest.prototype.send = oldSend;
      }
      else {
        oldSend.call(this, val);
      }
    };
  }
  
//验证
  wx.config(<?php echo Helper_WX::jsConfig(array('scanQRCode'))?>);
  function scan(){
  	wx.scanQRCode({
  	    needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
  	    scanType: ["barCode"], // 可以指定扫二维码还是一维码，默认二者都有
  	    success: function (res) {
  		    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
    		if(result.indexOf(',')==-1){
  			    alert('无法识别单号，请重新扫描');
  			    scan();
  		    }else{
  		    	$('#orderno').val(result.split(',')[1]);
  		    	input_event();
  		    }
  		}
  	});
  }
  wx.ready(function(){
  });
  wx.error(function(res){
	  alert("出错了：" + res.errMsg);
  });
</script>
<?PHP $this->_endblock();?>

