<?php
/**
 * 定义主目录，即index.php入口文件所在目录
 */
define('_INDEX_DIR_', dirname(__FILE__));
global $g_boot_time;
$g_boot_time = microtime(true);

//强制错误显示
error_reporting(E_ALL | E_STRICT);

//载入QeePHP
require _INDEX_DIR_.'/_library/qee/library/q.php';
Q::import(_INDEX_DIR_.'/_library');

//载入应用对象
require _INDEX_DIR_.'/_code/myapp.php';

//自动初始化目录结构
Helper_Filesys::mkdirs(_INDEX_DIR_.DS.'_tmp');
Helper_Filesys::mkdirs(_INDEX_DIR_.DS.'_tmp'.DS.'log');
Helper_Filesys::mkdirs(_INDEX_DIR_.DS.'_tmp'.DS.'upload');
Helper_Filesys::mkdirs(_INDEX_DIR_.DS.'_tmp'.DS.'runtime_cache');


//进入 MVC
$ret = MyApp::instance(_INDEX_DIR_.'/_code/_config')->dispatching();

if (is_string($ret)) echo $ret;

return $ret;
