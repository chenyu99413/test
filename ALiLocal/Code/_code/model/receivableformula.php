<?php

/**
 * Receivableformula 封装来自 tb_receivable_formula 数据表的记录及领域逻辑
 */
class Receivableformula extends QDB_ActiveRecord_Abstract
{
    
    /**
     * 返回对象的定义
     *
     * @static
     *
     * @return array
     */
    static function __define()
    {
        return array(
            
            // 用什么数据表保存对象
            'table_name' => 'tb_receivable_formula',
            
            // 指定数据表记录字段与对象属性之间的映射关系
            // 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
            'props' => array(
                'receivable_formula_id' => array(
                    'readonly' => true
                )
            ),
            'validations' => array(),
            'create_autofill' => array(
                
                // 自动填充修改和创建时间
                'create_time' => self::AUTOFILL_TIMESTAMP,
                'update_time' => self::AUTOFILL_TIMESTAMP
            ),
            'update_autofill' => array(
                'update_time' => self::AUTOFILL_TIMESTAMP
            ),
            
            // 不允许通过构造函数给 file_id 属性赋值
            'attr_protected' => 'receivable_formula_id'
        );
    }
    
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
    //公式费用名称列表
    static function getFeename($type,$formula_id=NULL,$customs_code){
        //查询已添加过的费用名称
        $fee_names=self::find('package_type=?',$type);
        if($formula_id){
            $fee_names->where('receivable_formula_id!=?',$formula_id);
        }
        if($customs_code){
        	$fee_names->where('customs_code!=?',$customs_code);
        }
        if ($customs_code=='ALCN'){
        	$fee_item_name_array=array('运费(ALL IN)');
        }else{
        	$fee_item_name_array=array('基础运费','取件费','防疫附加费','非正式报关费','燃油附加费','偏远地区附加费','税');
        }
        $fee_names=Helper_Array::getCols($fee_names->getAll(), 'fee_name');
        foreach ( FeeItem::find ('item_code is not null and customs_code=?',$customs_code)->getAll () as $value ) {
            if(!in_array($value->item_name, $fee_item_name_array)){
                $result [] = array (
                    "id" => $value->item_name,"text" => $value->item_name
                );
            }
        }
        return $result;
    }
}

class ReceivableformulaException extends QException{
}