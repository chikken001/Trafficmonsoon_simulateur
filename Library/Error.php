<?php

namespace Library;

class Error extends ApplicationComponent
{
	function shut(){
	
		$error = error_get_last();
	
		if($error && ($error['type'] & E_FATAL)){
			$this->handler($error['type'], $error['message'], $error['file'], $error['line']);
		}
	
	}
	
	function handler( $errno, $errstr, $errfile, $errline ) {
	
		switch ($errno){
	
			case E_ERROR: // 1 //
				$typestr = 'E_ERROR'; break;
			case E_WARNING: // 2 //
				$typestr = 'E_WARNING'; break;
			case E_PARSE: // 4 //
				$typestr = 'E_PARSE'; break;
			case E_NOTICE: // 8 //
				$typestr = 'E_NOTICE'; break;
			case E_CORE_ERROR: // 16 //
				$typestr = 'E_CORE_ERROR'; break;
			case E_CORE_WARNING: // 32 //
				$typestr = 'E_CORE_WARNING'; break;
			case E_COMPILE_ERROR: // 64 //
				$typestr = 'E_COMPILE_ERROR'; break;
			case E_CORE_WARNING: // 128 //
				$typestr = 'E_COMPILE_WARNING'; break;
			case E_USER_ERROR: // 256 //
				$typestr = 'E_USER_ERROR'; break;
			case E_USER_WARNING: // 512 //
				$typestr = 'E_USER_WARNING'; break;
			case E_USER_NOTICE: // 1024 //
				$typestr = 'E_USER_NOTICE'; break;
			case E_STRICT: // 2048 //
				$typestr = 'E_STRICT'; break;
			case E_RECOVERABLE_ERROR: // 4096 //
				$typestr = 'E_RECOVERABLE_ERROR'; break;
			case E_DEPRECATED: // 8192 //
				$typestr = 'E_DEPRECATED'; break;
			case E_USER_DEPRECATED: // 16384 //
				$typestr = 'E_USER_DEPRECATED'; break;
	
		}
	
		$message = '<b>'.$typestr.': </b>'.$errstr.' dans <b>'.$errfile.'</b> a la ligne <b>'.$errline.'</b><br/>';
	
		if(($errno & E_FATAL) && ENV === 'production'){
			$url = $this->app->config()->getGlobal('site') ;
			$url = $url.'/500' ;
			header($url);
		}
	
		if(!($errno & ERROR_REPORTING))
			return;
	
		if(DISPLAY_ERRORS  && ENV === 'development')
			printf('%s', $message);
	
		if(LOG_ERRORS)
			error_log(strip_tags($message), 0);
	}
}