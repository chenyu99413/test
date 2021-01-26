<?php

/**
 * Order 封装来自 tb_order 数据表的记录及领域逻辑
 */
class Order extends QDB_ActiveRecord_Abstract {
    /**
     * 渠道分组
     * @return array[]
     */
	static function channelgroup(){
	    $channel_group=Channelgroup::find('channel_group_id in (?)',ChannelGroupDepartment::availablechannelgroupids())->getAll();
	    $result=array();
	    foreach ($channel_group as $temp){
	        $channel=Channel::find('channel_group_id=?',$temp->channel_group_id)->getAll();
	        if(count($channel)>0){
	            $channel_ids=Helper_Array::getCols($channel, 'channel_id');
	            $result[$temp->channel_group_name]=$channel_ids;
	        }
	    }
	    return $result;
	}
	/**
	 * 预报、未入库
	 */
	CONST STATUS_PREPARE=1;
	/**
	 * 已入库
	 */
	CONST STATUS_IN=5;
	/**
	 * 取消
	 */
	CONST STATUS_CANCEL=2;
	/**
	 * 已退货
	 */
	CONST STATUS_RETURN=3;
	/**
	 * 已支付
	 */
	CONST STATUS_PAID=4;
	/**
	 * 已打印
	 */
	CONST STATUS_PRINTED=6;
	/**
	 * 已出库
	 */
	CONST STATUS_OUT=7;
	/**
	 * 待发送
	 */
// 	CONST STATUS_PRESEND=7;
	/**
	 * 已发送
	 */
// 	CONST STATUS_SENT=8;
    /**
     * 已提取
     */
	CONST STATUS_EXTRACTED=8;
	/**
	 * 已签收
	 */
	CONST STATUS_SIGN=9;
	/**
	 * 已查验
	 */
	CONST STATUS_CHECKED=10;
	/**
	 * 待退货
	 */
	CONST STATUS_PRERETURN=11;
	/**
	 * 扣件
	 */
	CONST STATUS_LOCK=12;
	static $status= array(
		'1' => '未入库',
		'2' => '已取消',
		'3' => '已退货',
		'4' => '已支付',
		'5' => '已入库',
		'6' => '已打印',
		'7' => '已出库',
		'8' => '已提取',
		'9' => '已签收',
		'10' => '已核查',
		'11' => '待退货',
		'12' => '已扣件',
	    '13' => '已结束',
	    '14'=>'已分派',
	    '15'=>'已取件',
	    '16'=>'网点已入库',
		'17'=>'其他'
	);
	
	/**
	 * 返回对象的定义
	 *
	 * @static
	 *
	 * @return array
	 */
	static function __define() {
		return array (
			
			// 用什么数据表保存对象
			'table_name' => 'tb_order',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'order_id' => array (
					'readonly' => true 
				),
				'packages' => array (
					QDB::HAS_MANY => 'Orderpackage','target_key' => 'order_id',
					'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
				),
				'department' => array (
					QDB::HAS_ONE => 'Department','target_key' => 'department_id',
					'source_key' => 'department_id','skip_empty' => true,'on_delete'=>'skip'
				),
			    // 订单内产品
			    'product' => array (
			        QDB::HAS_MANY => 'Orderproduct','target_key' => 'order_id',
			        'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
			    // 产品
			    'service_product' => array(
			        QDB::HAS_ONE => 'Product','target_key' => 'product_name',
			        'source_key' => 'service_code','skip_empty' => true,'on_delete'=>'skip'
			    ),
			    // 客户
			    'customer' => array(
			        QDB::HAS_ONE => 'Customer','target_key' => 'customer_id',
			        'source_key' => 'customer_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
			    'farpackages' => array (
			        QDB::HAS_MANY => 'Farpackage','target_key' => 'order_id',
			        'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
			    'faroutpackages' => array (
			        QDB::HAS_MANY => 'Faroutpackage','target_key' => 'order_id',
			        'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
				'subcodes' => array (
					QDB::HAS_MANY => 'Subcode','target_key' => 'order_id',
					'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
				),
				'fees' => array (
					QDB::HAS_MANY => 'Fee','target_key' => 'order_id',
					'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
				),
				'logs'=>array(
					QDB::HAS_MANY=>'OrderLog','target_key' => 'order_id','on_find_order'=>'create_time desc',
					'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
				),
			    'pictures'=>array(
			        QDB::HAS_MANY=>'File','target_key' => 'order_id',
			        'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
			    'events'=>array(
			        QDB::HAS_MANY=>'Event','target_key' => 'order_id',
			        'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
				// 1_渠道:1_订单
				'channel' => array (
					QDB::BELONGS_TO => 'Channel','source_key' => 'channel_id','target_key' => 'channel_id','skip_empty' => true
				),
			    'customer' => array (
			        QDB::BELONGS_TO => 'customer','source_key' => 'customer_id','target_key' => 'customer_id','skip_empty' => true
			    )
			),
			'validations' => array (),
			'create_autofill' => array (
				
				// 自动填充修改和创建时间
				'create_time' => self::AUTOFILL_TIMESTAMP,
				'update_time' => self::AUTOFILL_TIMESTAMP 
			),
			'update_autofill' => array (
				'update_time' => self::AUTOFILL_TIMESTAMP 
			),
			'attr_protected' => 'order_id' 
		);
	}
	
	/**
	 * 开启一个查询，查找符合条件的对象或对象集合
	 *
	 * @static
	 *
	 * @return QDB_Select
	 */
	static function find() {
		$args = func_get_args ();
		return QDB_ActiveRecord_Meta::instance ( __CLASS__ )->findByArgs ( $args );
	}
	
	/**
	 * 返回当前 ActiveRecord 类的元数据对象
	 *
	 * @static
	 *
	 * @return QDB_ActiveRecord_Meta
	 */
	static function meta() {
		return QDB_ActiveRecord_Meta::instance ( __CLASS__ );
	}
	
	/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */
	function _before_update(){
		$changes=array();
		foreach ($this->changes() as $field){
			if (!in_array($field,array('create_time','update_time'))){
				$desc=$this->getMeta()->table_meta[$field]['desc'];
				if (empty($desc)){
					$desc=$field;
				}
				if (mb_substr($desc,-2,2)=='时间' && is_numeric($this[$field])){
					if ($this->_changed_prop_old_values[$field]){
						$changes[]=$desc.': '.date('Y-m-d H:i:s',$this->_changed_prop_old_values[$field]).' > '.date('Y-m-d H:i:s',$this[$field]);
					}else{
						$changes[]=$desc.': > '.date('Y-m-d H:i:s',$this[$field]);
					}
				}elseif ($field =='order_status'){
					$changes[]='订单状态: '.val(self::$status,$this->_changed_prop_old_values[$field],$this->_changed_prop_old_values[$field]).' > '.val(self::$status,$this[$field],$this[$field]); 
				}else {
					if ($this->_changed_prop_old_values[$field] <> $this[$field]){
						$changes[]=$desc.': '.$this->_changed_prop_old_values[$field].' > '.$this[$field];
					}
				}
				
			}
		}
		if (!empty($changes)){
			$log=new OrderLog(array(
				'order_id'=>$this->order_id,
				'staff_id'=>MyApp::currentUser('staff_id'),
				'staff_name'=>MyApp::currentUser('staff_name'),
				'comment'=>implode(', ',$changes)
			));
			$log->save();
		}
	}
	function getACount(){
		return Abnormalparcel::find('ali_order_no =? and (parcel_flag =1 or parcel_flag =3)',$this->ali_order_no)->getCount();
	}
	function getBCount(){
	    return Abnormalparcel::find('ali_order_no =?',$this->ali_order_no)->getCount();
	}
	function getRCount(){
		return Orderreturn::find('ali_order_no =?',$this->ali_order_no)->getCount();
	}
	
	/*
	 *  @todo 分拆地址1、2、3，现在只有分拆1、2
	 *  增加电子邮件
	 */
	
	static function splitAddress($addr){
	    $addr=str_replace(" ",' ',$addr);
	    $arr=explode(" ",$addr);
	    $ret=array();
	    $line='';
	    foreach ($arr as $word){
	        if (strlen($line.' '.$word)< 74){
	            $line.=' '.$word;
	        }else {
	            $ret[]=trim($line);
	            $line=$word;
	        }
	    }
	    if ($line){
	        $ret[]=$line;
	    }
	    return $ret;
	}
	/*
	 *  @todo 分拆地址1、2，现在只有分拆1、2
	 *  增加电子邮件
	 */
	
	static function splitAddressfedex($addr){
	    $addr=str_replace(" ",' ',$addr);
	    $arr=explode(" ",$addr);
	    $ret=array();
	    $line='';
	    foreach ($arr as $word){
	        if (strlen($line.' '.$word)< 34){
	            $line.=' '.$word;
	        }else {
	            $ret[]=trim($line);
	            $line=$word;
	        }
	    }
	    if ($line){
	        $ret[]=$line;
	    }
	    return $ret;
	}
	
	/*
	 *  @todo 分拆地址1、2，现在只有分拆1、2
	 *  增加电子邮件
	 */
	
	static function splitAddressdhl($addr){
		$addr=str_replace(" ",' ',$addr);
		$arr=explode(" ",$addr);
		$ret=array();
		$line='';
		foreach ($arr as $word){
			if (strlen($line.' '.$word)< 39){
				$line.=' '.$word;
			}else {
				$ret[]=trim($line);
				$line=$word;
			}
		}
		if ($line){
			$ret[]=$line;
		}
		return $ret;
	}
	
}
class OrderException extends QException {}