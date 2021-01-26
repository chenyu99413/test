<?php

namespace extended;

if (! defined ( 'EXTENDED_ROOT' )) {
	define ( 'EXTENDED_ROOT', __DIR__ );
}

spl_autoload_register ( function ($className) {
	$classFile = $className . '.php';
	$classFilePath = str_replace ( '\\', '/', $classFile );
	$classFileFullPath = EXTENDED_ROOT . DIRECTORY_SEPARATOR . $classFilePath;
// 	echo $classFileFullPath;
	if (file_exists ( $classFileFullPath )) {
		require_once $classFileFullPath;
	}
} );