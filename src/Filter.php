<?php

namespace directly;

class Filter{

	private static $dir;
	private static $domain;
	private static $page;

	public static function includes($content,$dir,$domain,$page){

		self::$dir = $dir;
		self::$domain = $domain;
		self::$page = $page;


			
		$content = preg_replace_callback("#\[(.*):(.*)]#i", function($value){
			$source = isset($value[0])?$value[0]:null;
			$key = isset($value[1])?$value[1]:null;
			$value = isset($value[2])?$value[2]:null;

			
			if($key == 'inc' || $key == 'inc-route'){
				if($key == 'inc') $page_app =self::$dir.'inc'.DIRECTORY_SEPARATOR;	
				if($key == 'inc-route') $page_app = self::$dir.'view'.DIRECTORY_SEPARATOR.self::$page.DIRECTORY_SEPARATOR;	

					

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
			$content = self::includes($content,$dir,self::$domain,$page);
		}

		return $content;
	}

}