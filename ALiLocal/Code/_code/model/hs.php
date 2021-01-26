<?php
/**
 * HS编码
 */
class Hs extends QDB_ActiveRecord_Abstract
{
	
    static function __define()
    {
        return array
        (
            // 指定该 ActiveRecord 要使用的行为插件
            'behaviors' => '',
        	'table_config' => array (
        		'dsn_name' => 'far800deploy'
        	),
            // 指定行为插件的配置
            'behaviors_settings' => array
            (
            ),

            // 用什么数据表保存对象
            'table_name' => 'hscode',

            'props' => array
            (
               
            ),

            'create_autofill' => array
            (
                
            ),
            'update_autofill' => array
            (
               
            ),
        );
    }


/* ------------------ 以下是自动生成的代码，不能修改 ------------------ */

    /**
     * 开启一个查询，查找符合条件的对象或对象集合
     *
     * @static
     *
     * @return QDB_Select
     */
    static function find()
    {
        $args = func_get_args();
        return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);
    }

    /**
     * 返回当前 ActiveRecord 类的元数据对象
     *
     * @static
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function meta()
    {
        return QDB_ActiveRecord_Meta::instance(__CLASS__);
    }


/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */
    
}

