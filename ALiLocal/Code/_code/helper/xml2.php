<?php
/**
 * xml解析转换助手
 * @package helper
 */ 
class Helper_xml2{
	/**
	 * 返回多节点样式数组
	 * @param mixed $arr	
	 * @param string $checkFiled	检查下级键名
	 * @return multitype:|unknown|multitype:unknown
	 */
	static function nodesArray($arr,$checkFiled=0){
		if (!is_array($arr) && !($arr instanceof ArrayObject)){
			return Q::normalize($arr);
		}
		if (isset($arr[$checkFiled])){
			return $arr;
		}
		return array($arr);
	}
	/**
	 * DomdocumentNode to Array
	 * @param DOMDocument $node
	 * @return array
	 */
	static function  dom2arr( $node){
		return self::simplexml2a(simplexml_import_dom($node));
	}
	/**
	 * 重载 simplexml_load_string，去掉xml 中的非法字符
	 *
	 * @param string $string
	 * @return SimpleXmlElement
	 */
	static function simplexml_load_string($string){
		$string=preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/',' ',$string);
		return simplexml_load_string($string);
	}
    /**
     * 用 simplexml_load_string 解晰 xml 字符串
     * 变为 数组     
     */
    static function xmlparse($xmlString){
       if($xmlString instanceof SimpleXMLElement ){
          return self::simplexml2XA($xmlString);
       }elseif(is_string($xmlString)){
          return self::simplexml2XA(self::simplexml_load_string($xmlString));
       }
    }
    /**
     * SimpleXmlElement 对象转换为数组
     *	@return array
     */
    static function simplexml2a($o){
        if(is_object($o)){
            settype($o,'Array');
        }
        if(is_array($o)){
           if(count($o)>0){
                foreach($o as $k=>$a){
                    $o[$k]=self::simplexml2a($a);
                }
           }else{
                $o='';
           }
        }
        return $o;
    }
    /**
     *  SimpleXmlElement 转成 xmlArray
     *  @return xmlArray Object     
     */         
    static function simplexml2XA($o){
        if(!($o instanceof  SimpleXMLElement)) return $o;   
           $n =new xmlArray();
           if(count($o->children())>0){ 
    			foreach($o->children() as $k=>$a){
                    if(count($a->attributes())){
                        foreach($a->attributes() as $ak=>$av){
                            $n->setChildrenAttrible($k,$ak,(string)$av );
                        }
                    }
                    if(isset($n[$k])){
                        if(self::isArray($n[$k])&&isset($n[$k][0])){
                            $n[$k][]=self::simplexml2XA($a);
                        }else{
                            $t=$n[$k];
                            $n[$k]=new xmlArray(array($t,self::simplexml2XA($a)));          
                        }
                    }else{
                        $n[$k]=self::simplexml2XA($a);
                    }
    			}
           }else{
                $n=(string)$o;
           }
		return $n;
    }
    /**
     * 从数组生成 xml
     *  @ $arr 是数组与 simplexml 混合 
     *  以3个空格 区分 属性     
     *  $xmlstr=self::simpleArr2xml(
    array(
        //' '后 设置 GetItemTransactionsRequest 的属性,因为 数组键名,让人吃惊的强壮
        'GetItemTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents"'=>array( 
            'IncludeContainingOrder'=>'true',
            'ItemID testK="testV"' =>array( // 设置 ItemId
                $itemid,
            ),
            'ListingEnhancement'=>array( // 转成 Xml 时 , 
                'Border',
                'CustomCode',
                'Featured',
                'Highlight'
            ),
        )
    ));
     */
    static public function simpleArr2xml($arr,$header=1,$cdata=true){
        if($header){
            $str='<?xml version="1.0" encoding="utf-8" ?>'."\r\n";
        }else{
            $str='';
        }
        if(is_array($arr)){
            foreach($arr as $k=>$v){
                $n=$k;
                if(($b=strpos($k,' '))>0){
                    $f=substr($k,0,$b);
                }else{
                    $f=$k;
                }
                if(is_array($v)&&is_numeric(implode('',array_keys($v)))){ 
                // 就是为 Array 为适应 Xml 的可以同时有多个键 所做的 变通
                	foreach($v as $cv){
                		$str.="<$n>".self::simpleArr2xml($cv,0,$cdata)."</$f>\r\n";
                	}
                }elseif ($v instanceof SimpleXMLElement ){
                    $xml = $v->asXML();
                    $xml =preg_replace('/\<\?xml(.*?)\?\>/is','',$xml);
                    $str.=$xml;
                }else{
                    $r="<$n>".self::simpleArr2xml($v,0,$cdata)."</$f>\r\n";
                    if ($r=="<$n></$f>\r\n"){
                    	$str.="<$n />";
                    }else {
                    	$str.=$r;
                    }
                }
            }
        }else{
        	if (preg_match('/^[\w\d\.]+$/', $arr) || is_numeric($arr) || strlen($arr)==0){
        		$str.=$arr;
        	}else {
        		if (strpos($arr, 'CDATA[')){
        			$str.=$arr;
        		}else {
        			if ($cdata || preg_match('/[\&\<\>]/', $arr)){
            			$str.='<![CDATA['.$arr.']]>';
        			}else {
        				$str.=$arr;
        			}
        		}
        	}
        }
        return $str;
    }
    
	/**
	 * 判断是否使用了 xmlArray
	 * 在部分地方 代替 is_array 
	 */
	static function isArray($arr){
		//return is_array($arr)||($arr instanceof xmlArray);
		return ($arr instanceof ArrayObject);
	}
	
	/**
     * XML Reader instance.
     *
     * @var XMLReader
     */
    protected $reader;

    /**
     * Directory of the file to process.
     *
     * @var string
     */
    protected $directory;

    /**
     * Nodes to handle as plain text.
     *
     * @var array
     */
    protected $textNodes = array(
        XMLReader::TEXT,
        XMLReader::CDATA,
        XMLReader::WHITESPACE,
        XMLReader::SIGNIFICANT_WHITESPACE
    );

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromFile()
     * @param  string $filename
     * @return array
     * @throws Exception\RuntimeException
     */
    public function fromFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception(sprintf(
                "File '%s' doesn't exist or not readable",
                $filename
            ));
        }
        $this->reader = new XMLReader();
        $this->reader->open($filename, null, LIBXML_XINCLUDE);

        $this->directory = dirname($filename);

        set_error_handler(
            function ($error, $message = '') use ($filename) {
                throw new Exception(
                    sprintf('Error reading XML file "%s": %s', $filename, $message),
                    $error
                );
            },
            E_WARNING
        );
        $return = $this->process();
        restore_error_handler();
        $this->reader->close();

        return $return;
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param  string $string
     * @return array|bool
     * @throws Exception\RuntimeException
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return array();
        }
        $this->reader = new XMLReader();

        $this->reader->xml($string, 'UTF-8', LIBXML_XINCLUDE);

        $this->directory = null;

        set_error_handler(
            function ($error, $message = '') {
                throw new Exception(
                    sprintf('Error reading XML string: %s', $message),
                    $error
                );
            },
            E_WARNING
        );
        $return = $this->process();
        restore_error_handler();
        $this->reader->close();

        return $return;
    }

    /**
     * Process data from the created XMLReader.
     *
     * @return array
     */
    protected function process()
    {
        return $this->processNextElement();
    }

    /**
     * Process the next inner element.
     *
     * @return mixed
     */
    protected function processNextElement()
    {
        $children = array();
        $text     = '';

        while ($this->reader->read()) {
        	
            if ($this->reader->nodeType === XMLReader::ELEMENT) {
                if ($this->reader->depth === 0) {
                    return $this->processNextElement();
                }
                $attributes = $this->getAttributes();
                $name       = $this->reader->name;
               
                if ($this->reader->isEmptyElement) {
                    $child = array();
                } else {
                    $child = $this->processNextElement();
                }
              
                if ($attributes) {
                    if (is_string($child)) {
                        $child = array('_' => $child);
                    }

                    if (! is_array($child)) {
                        $child = array();
                    }

                    $child = array_merge($child, $attributes);
                }

                if (isset($children[$name])) {
                    if (!is_array($children[$name]) || !array_key_exists(0, $children[$name])) {
                        $children[$name] = array($children[$name]);
                    }

                    $children[$name][] = $child;
                } else {
                    $children[$name] = $child;
                }
            } elseif ($this->reader->nodeType === XMLReader::END_ELEMENT) {
                break;
            } elseif (in_array($this->reader->nodeType, $this->textNodes)) {
                $text .= $this->reader->value;
            }
        }

        return $children ?: $text;
    }

    /**
     * Get all attributes on the current node.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attributes = array();
       
        if ($this->reader->hasAttributes) { 
            while ($this->reader->moveToNextAttribute()) {
            	if ($this->reader->localName=='xmlns'){
            		continue;
            	}
                $attributes[$this->reader->localName] = $this->reader->value;
            }

            $this->reader->moveToElement();
        }

        return $attributes;
    }
}


/***
 *  数组对象 用于解析xml 
 */
class xmlArray extends ArrayObject
{
    public $_attribles;  // 
    /**
     * 从数组转
     * @param array  $array
     */
    public function __construct( $array = array())
    {
        foreach ($array as &$value){
            is_array($value) && $value = new self($value);
        }
        parent::__construct($array);
    }
 
    public function __get($index)
    {
        return @$this->offsetGet($index);
    }
 
    public function __set($index, $value)
    {
        @$this->offsetSet($index, $value);
    }
 
    public function __isset($index)
    {
        return @$this->offsetExists($index);
    }
 
    public function __unset($index)
    {
        $this->offsetUnset($index);
    }
 
    /**
     * 将数据信息转换为数组形式
     *
     * @return array
     */
    public function toArray()
    {
        $array = $this->getArrayCopy();
        foreach ($array as &$value)
            ($value instanceof self) && $value = $value->toArray();
        return $array;
    }
 
    /**
     * 将数据组转换为字符串形式
     *
     * @return array
     */
    public function __toString()
    {
    	$ret=$this->toArray();
    	if (is_array($ret)){
    		return 'Array';
    	}
    	return $ret;
    }
    /**
     *  设置属性
     */
    public function setChildrenAttrible($n,$k,$v=null){
        $this->_attribles[$n][$k]=$v;
    }
    /**
     * 取属性
     */
    public function getChildrenAttrible($n=null,$k=null){
        if($n){
			if(isset($this->_attribles[$n])){
	        	if($k){
            		return $this->_attribles[$n][$k];
	        	}
          		return $this->_attribles[$n]; 
			}
			return null;
        }
        return $this->_attribles;
    }
    static function attrible(xmlArray $obj,$k1,$k2){
    	return $obj->getChildrenAttrible($k1,$k2);
    }
}