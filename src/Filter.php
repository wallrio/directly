<?php

namespace directly;

class Filter{

	private static $dir;
	private static $domain;

	public static function includes($content,$dir,$domain){

		self::$dir = $dir;
		self::$domain = $domain;
			
		$content = preg_replace_callback("#\[(.*):(.*)]#i", function($value){
			$source = isset($value[0])?$value[0]:null;
			$key = isset($value[1])?$value[1]:null;
			$value = isset($value[2])?$value[2]:null;

			
			if($key == 'inc'){
				$page_app =self::$dir.'inc'.DIRECTORY_SEPARATOR;	
				$filename = $page_app.$value;
				$filename = str_replace('//', '/', $filename);
				if(file_exists($filename)){				
					ob_start();
					include $filename;
					$content = ob_get_contents();
					ob_end_clean();

					return $content;
				}else{				
					$content = '';
					return $content;
				}
				return $source;
			}

			if($key == 'url'){							
				return self::$domain;
			}

			return $source;
		}, $content);

		preg_match_all("#\[(.*):(.*)]#i", $content, $matches);

		if(count($matches[0]) > 0){		
			$content = self::includes($content,$dir,self::$domain);
		}

		return $content;
	}

}