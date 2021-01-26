<?php
/**
 * ReturnOrder 封装来自 tb_return_order 数据表的记录及领域逻辑
 */
class ReturnOrder extends QDB_ActiveRecord_Abstract{
	static $status= array(
		'10' => '仓储中',
		'15' => '已确认',
		'20' => '待重发',
		'30' => '待销毁',
		'40' => '待退回',
		'50' => '已打印',
		'60' => '已重发',
		'70' => '已销毁',
		'80' => '已退回'
		
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
            'table_name' => 'tb_return_order',
            	
            // 指定数据表记录字段与对象属性之间的映射关系
            // 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
            'props' => array (
                'return_order_id' => array (
                    'readonly' => true
                ),
                // 1_渠道:1_订单
            	'channel' => array (
            		QDB::BELONGS_TO => 'ReturnChannel','source_key' => 'channel_id','target_key' => 'channel_id','skip_empty' => true
            	),
            	// 订单内产品
            	'product' => array (
            		QDB::HAS_MANY => 'ReturnOrderproduct','target_key' => 'return_order_id',
            		'source_key' => 'return_order_id','skip_empty' => true,'on_delete'=>'skip'
            	),
            	'faroutpackages' => array (
            		QDB::HAS_MANY => 'Returnoutpackage','target_key' => 'return_order_id',
            		'source_key' => 'return_order_id','skip_empty' => true,'on_delete'=>'skip'
            	),
            	// 客户
            	'customer' => array(
            		QDB::HAS_ONE => 'Customer','target_key' => 'customer_id',
            		'source_key' => 'customer_id','skip_empty' => true,'on_delete'=>'skip'
            	),
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
            'attr_protected' => 'return_order_id'
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
}

/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */