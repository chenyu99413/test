<?php 

return array(
	'app_config'=>require 'app.config.php',
	'db_dsn_pool' => require 'database.config.php',
	'acl_global'=>require 'acl.config.php',
	
	'runtime_session_start' => '1' ,
	'session_cache_expire' => 3600*3 ,
	'acl_session_key' => 'acl_userdata' ,
	'runtime_cache_backend' => 'QCache_File',
	'runtime_cache_backend' => 'QCache_File',
	'runtime_cache_dir' => _INDEX_DIR_.'/_tmp/runtime_cache',
	'runtime_response_header' => '1' ,
	'log_enabled' => '1' ,
	'acl_default' => array(
		'allow' => 'ACL_EVERYONE'
	) ,
	'db_meta_lifetime' => '60' ,
	'db_meta_cached' => true ,
	'db_meta_cache_backend'=>'QCache_File',
	'log_writer_dir' => _INDEX_DIR_.'/_tmp/log' ,
	'upload_tmp_dir' => _INDEX_DIR_.'/_tmp/upload' ,
	'upload_file_dir' => _INDEX_DIR_.'/public/upload/files' ,
	'log_writer_filename' => 'devel.'.date('Ymd').'.log' ,
	'log_priorities' => 'EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG' ,
	// rewrite 、 pathinfo
 	'dispatcher_url_mode'=>'rewrite',
	'routes'=>array(
			'_default_' => array (
			'pattern' => '/:controller/:action/*',
			'defaults' => array (
				'controller' => 'staff',
				'action' => 'login' 
			) 
		) 
	),
);

