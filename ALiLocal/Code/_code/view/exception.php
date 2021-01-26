<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>出错啦</title>
    <style type="text/css">
        body { background-color: #fff; color: #666; font-family: arial, sans-serif; }
        div.dialog {
            width: 25em;
            padding: 0 4em;
            margin: 4em auto 0 auto;
            border: 1px solid #ccc;
            border-right-color: #999;
            border-bottom-color: #999;
            text-align: left;
        }
        h1 { font-size: 100%; color: #f00; line-height: 1.5em; }
        p.tip { font-size: 12px; color: #aaa; }
        div.detail {
        	padding: 8px 35px 8px 14px;
			  margin-bottom: 20px;
			  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
			  background-color: #fcf8e3;
			  border: 1px solid #fbeed5;
			  -webkit-border-radius: 4px;
			  -moz-border-radius: 4px;
			  border-radius: 4px;
        }
        div.detail h4{
        	color:#c09853;
        }
    </style>
</head>

<body>
  <div class="dialog">
    <h1>系统出现错误</h1>
    <p>
      请联系技术人员报告错误。
    </p>
    <p class="tip">
      <?php echo h($exception->getMessage()); ?>
    </p>
    
  </div>
  <div class="detail" style="margin-top: 100px">
  	<h4>下面是技术需要的错误内容</h4>
  	<hr />
    <pre><?php echo str_replace(INDEX_DIR, '', print_r($exception,true));?></pre>
    </div>
</body>
</html>
