<?php
// new view engine @2015 by firzen

/**
 * 定义 QView_Render_PHP 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: view_render_php.php 2629 2009-07-17 10:28:03Z jerry $
 * @package mvc
 */

/**
 * View 类实现了视图架构的基础
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: view_render_php.php 2629 2009-07-17 10:28:03Z jerry $
 * @package mvc
 */
class View
{
    /**
     * 视图分析类名
     *
     * @var string
     */
    protected $_parser_name = 'QView_Render_PHP_Parser';

    /**
     * 视图文件所在目录
     *
     * @var string
     */
    public $view_dir;

    /**
     * 要输出的头信息
     *
     * @var array
     */
    public $headers;

    /**
     * 视图文件的扩展名
     *
     * @var string
     */
    public $file_extname = 'php';

    /**
     * 模板变量
     *
     * @var array
     */
    public $_vars;

    /**
     * 视图
     *
     * @var string
     */
    protected $_viewname;

    /**
     * 要使用的布局视图
     *
     * @var string
     */
    protected $_view_layouts;

    /**
     * 当前使用的分析器
     *
     * @var View_Parser
     */
    protected $_parser;

    /**
     * 构造函数
     *
     * @param array $config
     */
    function __construct($config = null)
    {
        if (is_array($config))
        {
            foreach ($config as $key => $value)
            {
                $this->{$key} = $value;
            }
        }

        $this->cleanVars();
    }

    /**
     * 设置视图名称
     *
     * @param string $viewname
     *
     * @return View
     */
    function setViewname($viewname)
    {
        $this->_viewname = $viewname;
        return $this;
    }

    /**
     * 指定模板变量
     *
     * @param string|array $key
     * @param mixed $data
     *
     * @return View
     */
    function assign($key, $data = null)
    {
        if (is_array($key))
        {
            $this->_vars = array_merge($this->_vars, $key);
        }
        else
        {
            $this->_vars[$key] = $data;
        }
        return $this;
    }

    /**
     * 获取指定模板变量
     *
     * @param string
     *
     * @return mixed
     */
    function getVar($key, $default = null)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
    }

    /**
     * 获取所有模板变量
     *
     *
     * @return mixed
     */
    function getVars()
    {
        return $this->_vars;
    }

    /**
     * 清除所有模板变量
     *
     * @return View
     */
    function cleanVars()
    {
        ///*
        $context = QContext::instance();
        $this->_vars = array(
            '_ctx'          => $context,
            '_BASE_DIR'     => $context->baseDir(),
            '_BASE_URI'     => $context->baseUri(),
            '_REQUEST_URI'  => $context->requestUri(),
        );
        //*/

        // TODO! 全局变量应该放到 控制器抽象类 _before_render() 中
        //$this->_vars = array();

        return $this;
    }

    /**
     * 渲染视图
     *
     * @param string $viewname
     * @param array $vars
     * @param array $config
     */
    function display($viewname = null,  $vars = null,  $config = null)
    {
        if (empty($viewname))
        {
            $viewname = $this->_viewname;
        }

        if (Q::ini('runtime_response_header'))
        {
            header('Content-Type: text/html; charset=' . Q::ini('i18n_response_charset'));
        }
        header("X-XSS-Protection: 0");

        echo $this->fetch($viewname, $vars, $config);
    }

    /**
     * 执行
     */
    function execute()
    {
        $this->display($this->_viewname);
    }
    
    

    /**
     * 渲染视图并返回渲染结果
     *
     * @param string $viewname
     * @param array $vars
     * @param array $config
     *
     * @return string
     */
    function fetch($viewname,  $vars = null,  $config = null)
    {
        $this->_before_render();
        $view_dir = isset($config['view_dir']) ? $config['view_dir'] : $this->view_dir;
        $extname = isset($config['file_extname']) ? $config['file_extname'] : $this->file_extname;
        //$filename = "{$view_dir}/{$viewname}.{$extname}";
        $filename = getRealFilePath($view_dir,$viewname,$extname);
        if (file_exists($filename))
        {
            if (!is_array($vars))
            {
                $vars = $this->_vars;
            }
            if (is_null($this->_parser))
            {
                $this->_parser = new View_Parser($view_dir);
            }
            $output = $this->_parser->assign($vars)->parse($filename);
        }
        else
        {
            Helper_Filesys::mkdirs(dirname($filename));
            file_put_contents($filename,file_get_contents($view_dir.'/README'));
            chmod($filename, 0777);
            return $this->fetch($viewname,$vars,$config);
//            $output = '';
        }

        $this->_after_render($output);
        return $output;
    }

    /**
     * 渲染之前调用
     *
     * 继承类可以覆盖此方法。
     */
    protected function _before_render()
    {
    }

    /**
     * 渲染之后调用
     *
     * 继承类可以覆盖此方法。
     *
     * @param string $output
     */
    protected function _after_render(& $output)
    {
    }
    static function buildButtonList(){
    		$dir=dirname(__FILE__).DS.'view';
    		foreach (scandir($dir) as $fname){
    			$fullFname=$dir.DS.$fname;
    			if (is_dir($fullFname) && $fname!='.' && $fname!='..'){
    				foreach (scandir($fullFname) as $filename){
    					$fullFilename=$fullFname.DS.$filename;
    					if (is_file($fullFilename)){
    						echo $fullFilename."<br>";
    						dump(View_Parser::filterButtons($fullFilename,$fname.DS.$filename));
    					}
    				}
    			}
    		}
    }
}

/**
 * View_Parser 类实现了视图的分析
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: view_render_php.php 2629 2009-07-17 10:28:03Z jerry $
 * @package mvc
 */
class View_Parser
{
    /**
     * 视图文件扩展名
     * 
     * @var string
     */
    protected $_extname;

    /**
     * 视图堆栈
     *
     * @var array
     */
    private $_stacks = array();

    /**
     * 当前处理的视图
     *
     * @var int
     */
    private $_current;

    /**
     * 视图变量
     *
     * @var array
     */
    protected $_vars;

    /**
     * 视图文件所在目录
     *
     * @var string
     */
    private $_view_dir;

    static function filterButtons($FullFilename,$IDprefix){
    		$html=file_get_contents($FullFilename);
    		$html=preg_replace("/<\?php.*?\?>/is", '[PHP]', $html);
    		$blist1=array();
    		preg_match_all("/<input[^<>]*?btn[^<>]*?value=[\"'](.*?)[\"'].*?>/is", $html,$blist1,PREG_SET_ORDER);
    		$blist2=array();
    		preg_match_all("/<input[^<>]*?value=[\"']([^<>]*?)[\"'][^<>]*?btn.*?>/is", $html,$blist2,PREG_SET_ORDER);
    		$blist3=array();
    		preg_match_all("/<a[^<>]*?btn[^<>]*?>([^<>]*?)<\/a>/is", $html,$blist3,PREG_SET_ORDER);
    		$blist4=array();
    		preg_match_all("/<button[^>]*?btn[^<>]*?>(.*?)<\/button>/is", $html,$blist4,PREG_SET_ORDER);
    		$blist=array();
    		if (count($blist1)){
    			$blist=array_merge($blist1);
    		}
    		if (count($blist2)){
    			$blist=array_merge($blist2);
    		}
    		if (count($blist3)){
    			$blist=array_merge($blist3);
    		}
    		if (count($blist4)){
    			$blist=array_merge($blist4);
    		}
    		foreach ($blist as $i=> &$itm){
    			if (!empty($itm)){
    				$itm[1]=str_replace(array("\n","\t"), '', trim(strip_tags($itm[1])));
    				$itm[2]=$IDprefix.'-'.$i;
    				$itm[3]=$IDprefix.'-'.md5($itm[0]);
    			}
    		}
    		$a=htmlentities(print_r($blist,true));
    		echo '<pre>'.$a.'</pre>';
    }
    /**
     * 构造函数
     */
    function __construct($view_dir)
    {
        $this->_view_dir = $view_dir;
    }

    /**
     * 设置分析器已经指定的变量
     *
     * @param array $vars
     *
     * @return View_Parser
     */
    function assign($vars)
    {
        $this->_vars = $vars;
        return $this;
    }

    /**
     * 返回分析器使用的视图文件的扩展名
     *
     * @return string
     */
    function extname()
    {
        return $this->_extname;
    }

    /**
     * 分析一个视图文件并返回结果
     *
     * @param string $filename
     * @param string $view_id
     * @param array $inherited_stack
     *
     * @return string
     */
    function parse($filename, $view_id = null,  $inherited_stack = null)
    {
        
        if (!$view_id) $view_id = mt_rand();
        $this->_include($filename);
        if (isset($this->_stacks[$this->last_block_name])) {
        	echo $this->_stacks[$this->last_block_name];
        }
    }

    /**
     * 视图的继承
     *
     * @param string $tplname
     *
     * @access public
     */
    protected function _extends($tplname)
    {
    	$this->in_layout=true;
    	$filename = getRealFilePath($this->_view_dir,$tplname,$this->_extname);
    	extract($this->_vars);
    	include $filename;
    	$this->in_layout=false;
    	$this->_stacks[$this->last_block_name]=ob_get_clean();
    }

    public $in_layout=false;
    public $last_block_name='';
    /**
     * 开始定义一个区块
     *
     * @param string $block_name
     * @param mixed $config
     *
     * @access public
     */
    protected function _block($block_name, $config = null)
    {
    	if ($this->in_layout){
    		if ($this->last_block_name){
    			$this->_stacks[$this->last_block_name]=ob_get_clean();
    		}else {
    			@ob_end_flush();
    		}
    		$this->last_block_name=$block_name;
    	}else {
    		if (!isset($this->_stacks[$block_name])){
    			ob_start();
    			return ;
    		}
    		foreach ($this->_stacks as $bname =>$btext){
    			if ($bname==$block_name || $bname==$this->last_block_name){
    				break;
    			}
    			echo $btext;
    			unset($this->_stacks[$bname]);
    		}
    		@ob_end_flush();
    	}
    }

    /**
     * 结束一个区块
     *
     * @access public
     */
    protected function _endblock()
    {
    	if ($this->in_layout){
    		ob_start();
    	}else {
    		@ob_end_flush();
    	}
    }

    /**
     * 构造一个控件
     *
     * @param string $control_type
     * @param string $id
     * @param array $args
     *
     *
     * @access public
     */
    protected function _control($control_type, $id = null, $args = array())
    {
        Q::control($control_type, $id, $args)->display();
        // TODO! display($this) 避免多次构造视图解析器实例
        // 由于视图解析器实例的继承问题，所以暂时无法利用
    }

    /**
     * 载入一个视图片段
     *
     * @param string $element_name
     * @param array $vars
     *
     * @access public
     */
    protected function _element($element_name,  $vars = null)
    {
        //$filename = "{$this->_view_dir}/_elements/{$element_name}_element.{$this->_extname}";
        $filename = getRealFilePath($this->_view_dir,'/_elements/'.$element_name.'_element',$this->_extname);
        $this->_include($filename, $vars);
    }

    /**
     * 载入视图文件
     */
    protected function _include($___filename,  $___vars = null)
    {
        $this->_extname = pathinfo($___filename, PATHINFO_EXTENSION);
        extract($this->_vars);
        if (is_array($___vars)) extract($___vars);
        include $___filename;
    }
    /**
     * html连接文件
     * @param string $file *.js *.css
     */
    static function _link($file) {
        if (strpos ( strtolower ( $file ), '.css' ) !== false) {
            echo '<link rel="stylesheet" type="text/css" href="' . QContext::instance ()->baseDir () . 'link/css/' . $file . '" />' . "\n";
        } elseif (strpos ( strtolower ( $file ), '.js' ) !== false) {
            echo '<script type="text/javascript" src="' . QContext::instance ()->baseDir () . 'link/js/' . $file . '" /></script>' . "\n";
        }
    }
    
    /**
     * 公開載入外部視圖
     */         
    public function includeView($viewpath,$___vars=null)
    {
        extract($this->_vars);
        if (is_array($___vars)) extract($___vars);
        include $viewpath;
    }
}
function getRealFilePath($view_dir,$viewname,$extname)
{
    $filename = "{$view_dir}/{$viewname}.{$extname}";
    return $filename;
}