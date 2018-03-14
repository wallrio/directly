<?php

namespace directly;

class Filter{

	private static $dir;

	public static function includes($content,$dir){

		self::$dir = $dir;
		
		
		// preg_replace(pattern, replacement, subject)
		$content = preg_replace_callback("#\[(.*):(.*)]#i", function($value){
			$source = isset($value[0])?$value[0]:null;
			$key = isset($value[1])?$value[1]:null;
			$value = isset($value[2])?$value[2]:null;

			
			if($key == 'inc'){
				$page_app =self::$dir.'inc'.DIRECTORY_SEPARATOR;	
				$filename = $page_app.$value;
				$filename = str_replace('//', '/', $filename);
				if(file_exists($filename)){
					$content = file_get_contents($filename);
					return $content;
				}
				return $source;
			}
			return $source;
		}, $content);

		return $content;
	}

}