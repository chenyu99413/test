<?php
// 设置默认的时区
date_default_timezone_set ( "Etc/GMT-8" );
define ( 'INDEX_DIR', dirname ( dirname ( __FILE__ ) ) );
//Myapp根目录
define ( '_MYAPP_DIR_', dirname ( __FILE__ ) );

define('_INDEX_DIR_', INDEX_DIR);
global $g_boot_time;
$g_boot_time = microtime(true);

//强制错误显示
error_reporting(E_ALL | E_STRICT);

//载入QeePHP
require _INDEX_DIR_.'/_library/qee/library/q.php';
Q::import(_INDEX_DIR_.'/_library');


//进入 MVC
$ret = MyApp::instance(_INDEX_DIR_.'/_code/_config')->dispatching($argv);



class MyApp {
	/**
	 * 应用程序的基本设置
	 *
	 * @var array
	 */
	public $_app_config;
	static $static_message;
	/**
	 * 构造函数
	 *
	 * @param array $app_config
	 *        	构造应用程序对象
	 */
	function __construct($app_config_dir) {
		global $g_boot_time;
		QLog::log ( '--- STARTUP TIME --- ' . $g_boot_time, QLog::DEBUG );
		
		// 设置异常处理函数
		set_exception_handler ( array (
			$this,
			'exception_handler' 
		) );
		
		#读取配置文件
		$config = require $app_config_dir . '/init.php';
		$config ['app_config'] ['APP_DIR'] = _MYAPP_DIR_;
		
		// 初始化应用程序设置
		$this->_app_config = $config ['app_config'];
		
		Q::replaceIni ( $config );
		
		// 注册应用程序对象，在程序每个地方使用 Q::get_obj('app') 重新获得本对象的唯一实例
		Q::set_obj ( $this, 'app' );
		
		//添加自动载入路径
		Q::import ( _INDEX_DIR_ . '/_code' );
		Q::import ( _INDEX_DIR_ . '/_code/model' );
		Q::import ( _INDEX_DIR_ . '/_library' );
		
	}
	
	/**
	 * 析构函数
	 */
	function __destruct() {
		global $g_boot_time;
		$shutdown_time = microtime ( true );
		$length = $shutdown_time - $g_boot_time;
		QLog::log ( "--- SHUTDOWN TIME --- {$shutdown_time} ({$length})sec", QLog::DEBUG );
	}
	
	/**
	 * 返回应用程序类的唯一实例
	 *
	 * @param string $app_config_dir
	 *        	配置文件目录
	 *        	
	 * @return MyApp
	 */
	static function instance($app_config_dir = null) {
		static $instance;
		
		if (is_null ( $instance )) {
			if (empty ( $app_config_dir )) {
				die ( 'INVALID CONSTRUCT APP' );
			}
			$instance = new MyApp($app_config_dir);
		}
		return $instance;
	}
	
	/**
	 * 返回应用程序基础配置的内容
	 *
	 * 如果没有提供 $item 参数，则返回所有配置的内容
	 *
	 * @param string $item        	
	 *
	 * @return mixed
	 */
	function config($item = null) {
		if ($item) {
			return isset ( $this->_app_config [$item] ) ? $this->_app_config [$item] : null;
		}
		return $this->_app_config;
	}
	
	/**
	 * 根据运行时上下文对象，调用相应的控制器动作方法
	 *
	 * @param array $args        	
	 *
	 * @return mixed
	 */
	function dispatching(array $args = array()) {
		// 构造运行时上下文对象
		// 获得请求对应的 UDI controller namespace module action
        $udi =array(
        	'controller'=> !empty($args[1])?$args[1]:'default',
        	'action'=>!empty($args[2])?$args[2]:'index',
        	'namespace'=>null,
        	'module'=>null,
        );
        $udiAttr='';
        if(isset($args[3])){
        	$udiAttr.='-'.(strlen($args[3])<=8?$args[3]:substr($args[3],0,8));
        	$udiAttr=str_replace(array('\\','/',':'),'-',$udiAttr);
        }
        //log 文件按时间命名  20091121.log
        Q::changeIni('log_writer_filename' , 'cli.'.$udi['controller'].'-'.$udi['action'].$udiAttr.'-'.Q::ini('app_config/RUN_MODE').'-'.date('Ymd',CURRENT_TIMESTAMP).'.log');

        #IFDEF DEBUG
        QLog::log('REQUEST UDI: ' . implode('/',$args), QLog::DEBUG);
		
		
		# _REQUEST['controller']
		$controller_name = $udi [QContext::UDI_CONTROLLER];
		
		do {
			// 构造控制器对象
			try {
				/* @var $controller Controller_Abstract */
				$class_name = "controller_" . $controller_name;
				$controller = new $class_name ( $this );
			} catch ( Exception $ex ) {
				$response = $this->_on_action_not_defined ();
				break;
			}
			// _REQUEST['action']
			$action_name = $udi [QContext::UDI_ACTION];
			if ($controller->existsAction ( $action_name )) {
				// 如果指定Action存在，则调用
				$response = $controller->execute ( $action_name, $args );
			} else {
				// 如果指定动作不存在，则尝试调用控制器的 _on_action_not_defined() 函数处理错误
				$response = $controller->_on_action_not_defined ( $action_name );
				if (is_null ( $response )) {
					$response = $this->_on_action_not_defined ();
				}
			}
		} while ( false );
		
		if (is_object ( $response ) && method_exists ( $response, 'execute' )) {
			// 如果返回结果是一个对象，并且该对象有 execute() 方法，则调用
			$response = $response->execute ();
		} elseif ($response instanceof QController_Forward) {
			// 如果是一个 QController_Forward 对象，则将请求进行转发
			$response = $this->dispatching ( $response->args );
		}
		
		// 其他情况则返回执行结果
		return $response;
	}
	
	/**
	 * 访问出现exception错误处理函数
	 */
	protected function _on_access_exception($exception) {
		$message = "";
		require (_MYAPP_DIR_ . '/view/exception.php');
		exit ();
	}
	
	/**
	 * 视图调用未定义的控制器或动作时的错误处理函数
	 */
	protected function _on_action_not_defined() {
		require (_MYAPP_DIR_ . '/view/404.php');
		exit ();
	}
	
	/**
	 * 默认的异常处理
	 */
	function exception_handler( $ex) {
		QLog::log(print_r($ex,true));
		$this->_on_access_exception ( $ex );
		exit ();
	}
	static function currentUser(){
		return null;
	}
	static function getLangeuage() {
		return Q::ini ( 'LANGUAGE', 'cn' );
	}
	static function isShadow(){
		return in_array(self::currentUser('user_account'),array('A000123','F000352','S000001')) || self::$shadow==true;
	}
	static $shadow=false;
}

/**
 * 返回标准地址
 *
 * @param string $udi        	
 * @param mixed $params        	
 * @param string $route_name        	
 * @param array $opts        	
 * @return string
 */
function url_standard($udi, $params = null, $route_name = null, array $opts = null) {
	$opts ['mode'] = QContext::URL_MODE_STANDARD;
	$base = url ( $udi, null, $route_name, $opts );
	//过滤2维元素
	if (is_array ( $params ) && count ( $params )) {
		Helper_Array::removeEmpty ( $params );
		foreach ( $params as $k => $v ) {
			if (is_array ( $v )) {
				//$params[$k]=array_filter($v,'strlen');
			}
		}
		return $base . '&' . http_build_query ( $params );
	}
	return $base;
}
class MyAppException extends QException {}

/**
 * 多语言处理函数
 * 一次全部载入!
 */
function __t($msg) {
	global $language_lines;
	$args = func_get_args ();
	$msg = array_shift ( $args );
	$language = strtolower ( Q::ini ( 'LANGUAGE' ) );
	//BasePackage
	$basefile = Q::ini ( 'app_config/APP_DIR' ) . '/_lang/base.php';
	if (! is_file ( $basefile ) || true) {	//关闭多语言字典学习
		file_put_contents ( $basefile, "<?php\n return " . var_export ( array (), true ) . ';' );
	}
	$basekey = include $basefile;
	if (!is_array($basekey)){
		$basekey=array();
	}
	$basekey [$msg] = $msg;
	
	file_put_contents ( $basefile, "<?php\n return " . var_export ( $basekey, true ) . ';' );
	
	if (! is_array ( $language_lines )) {
		$language_lines = array ();
		$lang_input = @include (Q::ini ( 'app_config/APP_DIR' ) . '/_lang/' . $language . '.php');
		if (is_array ( $lang_input )) $language_lines += $lang_input;
		if (Q::ini ( 'module_name' )) {
			$lang_input = @include (Q::ini ( 'app_config/MODULE_DIR' ) . "/" . Q::ini ( 'module_name' ) . "/_lang/" . $language . '.php');
			if (is_array ( $lang_input )) $language_lines += $lang_input;
		}
	}
	if (isset ( $language_lines [$msg] )) {
		$msg = $language_lines [$msg];
	}
	array_unshift ( $args, $msg );
	return call_user_func_array ( 'sprintf', $args );
}
/**
 * 取得语言
 */
