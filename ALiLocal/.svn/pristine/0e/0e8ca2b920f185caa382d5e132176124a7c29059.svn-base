<?php
/**
 * @author firzen 2014-11-23 
 * 序列助手，通过mysql的function，生成序列，获取序列值
 * @package helper  
 */ 
class Helper_Seq{
	/**
	 * @var QDB_Adapter_Abstract
	 */
	static $conn;
	static $inited=false;
	/**
	 * 序列下一个值
	 * @param string $seq_name 序列名
	 * @return int
	 */
    static function nextVal($seq_name){
    	self::init();
    	$r=self::$conn->execute("select nextval('{$seq_name}')")->fetchOne();
    	return $r;
    }
    /**
     * 初始化序列值
     * @param string $seq_name 序列名
     * @param int $num
     * @return int
     */
    static function setVal($seq_name,$num){
    	self::init();
    	$r=self::$conn->execute("select setval('{$seq_name}','{$num}')")->fetchOne();
    	return $r;
    }
    /**
     * 序列目前值
     * @param string $seq_name 序列名
     * @return int
     */
    static function currentVal($seq_name){
    	self::init();
    	$r=self::$conn->execute("select currval('{$seq_name}')")->fetchOne();
    	return $r;
    }
    /**
     * 增加序列
     * @param string $seq_name 序列名
     * @param init $base_num
     * @param init $step
     * @param init $min_value
     * @param init $max_value
     * @return boolean
     */
    static function addSeq($seq_name,$base_num=1,$step=1,$min_value=1,$max_value=999999999){
    	self::init();
    	self::$conn->execute("INSERT INTO `sequence` (`name`, `current_value`, `increment`, `min_value`, `max_value`) VALUES ('{$seq_name}', '{$base_num}', '{$step}', '{$min_value}', '{$max_value}');");
    	return true;
    }
    static function init(){
    	//check init
    	self::$conn=QDB::getConn();
    	if (self::$inited==false){
	    	self::$inited=true;
	    	$r=self::$conn->execute('show tables like "sequence"')->fetchOne();
	    	if (is_null($r)){
	    		$sqls=explode('###', self::$sql);
	    		foreach ($sqls as $sql){
	    			self::$conn->execute($sql);
	    		}
	    	}
    	}
    }
    
    
    
    
    
    static $sql="

#创建序列表
DROP TABLE IF EXISTS `sequence`;
###
CREATE  TABLE `sequence` (
  `name` VARCHAR(50) NOT NULL COMMENT '序列名称' ,
  `current_value` BIGINT NOT NULL COMMENT '当前值' ,
  `increment` INT NOT NULL DEFAULT 1 COMMENT '增加值' ,
  PRIMARY KEY (`name`) )
COMMENT = '序列信息';

###
ALTER TABLE `sequence` ADD COLUMN `min_value` BIGINT NULL COMMENT '最小值'  AFTER `increment` , ADD COLUMN `max_value` BIGINT NULL COMMENT '最大值'  AFTER `min_value` ;
###
#序列方法-currval
DROP function IF EXISTS `currval`;
###
CREATE FUNCTION `currval` (seq_name VARCHAR(50))
RETURNS BIGINT
BEGIN

DECLARE value BIGINT;  
SET value = 0;  
SELECT current_value INTO value  
FROM sequence  
WHERE name = seq_name;  
RETURN value; 

END
###
#序列方法-nextval
DROP function IF EXISTS `nextval`;
###
CREATE FUNCTION `nextval` (seq_name VARCHAR(50))
RETURNS BIGINT
BEGIN

UPDATE sequence  
SET current_value = if(current_value=max_value,min_value,current_value+increment)  
WHERE name = seq_name;  
RETURN currval(seq_name); 

END
###
#序列方法-setval
DROP function IF EXISTS `setval`;
###
CREATE FUNCTION `setval` (seq_name VARCHAR(50), value BIGINT)
RETURNS BIGINT
BEGIN

UPDATE sequence  
SET current_value = value  
WHERE name = seq_name;  
RETURN currval(seq_name);  

END
###
# 初始化
#INSERT INTO `sequence` (`name`, `current_value`, `increment`, `min_value`, `max_value`) VALUES ('object_seq', '100', '1', '100', '999999999999');

#设置当前的序列值
#select setval('object_seq', 100);
#获取下一个的序列值
#select nextval('object_seq');
#获取当前的序列值
#select currval('object_seq');";
}