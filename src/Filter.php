<?php

namespace directly;

class Filter{

	private static  $dir, 
					$domain,
					$contents = array(), 
					$page;	

	/**
	 * [replace to files in global folder]
	 * 
	 */
	public static function filterGlobal($content){
		$content = preg_replace_callback("#\[\=global:(.*?)\=\]#im", function($value){
			
			$source = isset($value[0])?$value[0]:null;
			$key = isset($value[1])?$value[1]:null;
			$value = isset($value[2])?$value[2]:null;

			$page_app =self::$dir.'global'.DIRECTORY_SEPARATOR;	
				
			$filename = $page_app.$value;
			$filename = str_replace('//', '/', $filename);
			
			if(file_exists($filename)){				
				ob_start();
				include $filename;
				$content = ob_get_contents();
				ob_end_clean();

				return $content;
			}
			
			return '';

		}, $content );

		return $content;
	}


	/**
	 * [execute block if condition is true]
	 * 
	 */
	public static function filterCondition($content){
		$content = preg_replace_callback("#\[\=condition:(.*?)\=\]#im", function($value){

			$source = isset($value[0])?$value[0]:null;			
			$value = isset($value[1])?$value[1]:null;

			$valueArray = explode(',',$value);
			$condition = $valueArray[0];
			$conditionTrue = $valueArray[1];
			$conditionFalse = isset($valueArray[2])?$valueArray[2]:'' ;

			$condition = str_replace('{page}', '"'.self::$page.'"', $condition);			

			return eval('if('.$condition.'){return ($conditionTrue); }else {return ($conditionFalse);}');

		}, $content );

		return $content;
	}

	/**
	 * [replace to files in inc folder]
	 * 
	 */
	public static function filterInc($content){
		$content = preg_replace_callback("#\[\=inc:(.*?)\=\]#im", function($value){

			$source = isset($value[0])?$value[0]:null;			
			$value = isset($value[1])?$value[1]:null;

			$page_app =self::$dir.'inc'.DIRECTORY_SEPARATOR;	

			$filename = $page_app.$value;
			$filename = str_replace('//', '/', $filename);

			
			if(file_exists($filename)){				
				ob_start();
				include $filename;
				$content = ob_get_contents();
				ob_end_clean();

				return $content;
			}			
				
			return '';			

		}, $content );

		return $content;
	}

	
	/**
	 * [replace to files in view folder]
	 * 
	 */	
	public static function filterIncRoute($content){

		$content = preg_replace_callback("#\[\=inc-route:(.*?)\=\]#im", function($value){

			$source = isset($value[0])?$value[0]:null;
			$value = isset($value[1])?$value[1]:null;

			$page_app = self::$dir.'view'.DIRECTORY_SEPARATOR.self::$page.DIRECTORY_SEPARATOR;

			if(isset($_GET['sandbox'])){
			
			}

			$filename = $page_app.$value;
			$filename = str_replace('//', '/', $filename);

			if( file_exists($filename) ){				
				ob_start();
				include $filename;
				$content = ob_get_contents();
				ob_end_clean();

				self::$contents[$value] = $content;

				return $content;
			}	

			return '';
		

		}, $content );

		return $content;
	}

	/**
	 * [replace to files in view folder]
	 * 
	 */	
	public static function filterGetContent($content){

		$content = preg_replace_callback("#\[\=getcontent:(.*?):(.*?)\=\]#ims", function($value){
			
			$source = isset($value[0])?$value[0]:null;
			$filename = isset($value[1])?$value[1]:null;
			$method = isset($value[2])?$value[2]:null;

			$page_app = self::$dir.'view'.DIRECTORY_SEPARATOR.self::$page.DIRECTORY_SEPARATOR;

			$filepath = $page_app.$filename;
			$filepath = str_replace('//', '/', $filepath);

			if($method !== null){	
				eval('$methodNew = '.$method.';');		
				if(isset(self::$contents[$filename])){
					$content = self::$contents[$filename];
					$methodResult = $methodNew($content,$filename);
					return $methodResult;
				}
				return '';
			}

			if( file_exists($filepath) ){				
				ob_start();
				include $filepath;
				$content = ob_get_contents();
				ob_end_clean();
				self::$contents[$filename] = $content;
				return $content;
			}	
			return '';
		}, $content );

		return $content;
	}


	/**
	 * [replace string to domain url]
	 * 
	 */
	public static function filterDomain($content){
		$domain = self::$domain;
		if(substr($domain, strlen($domain)-1,strlen($domain)) == '/')
			$domain = substr($domain, 0,strlen($domain)-1);
		$content = preg_replace("#\[\=domain:url\=\]#im", $domain, $content);
		return $content;
	}

	/**
	 * [replace string to page url]
	 * 
	 */
	public static function filterPage($content){
		$content = preg_replace("#\[\=page:url\=\]#im", self::$page, $content);
		return $content;
	}


	/**
	 * [replace general]
	 * 
	 */
	public static function includes($content,$dir,$publicDir,$domain,$page){

		$domain = str_ireplace($dir.$publicDir.'/', '', $domain);
	
		self::$dir = $dir;
		self::$domain = $domain;
		self::$page = $page;

		
		$content = self::filterCondition($content);
		$content = self::filterInc($content);
		$content = self::filterIncRoute($content);
		$content = self::filterGetContent($content);
		$content = self::filterGlobal($content);
		$content = self::filterDomain($content);
		$content = self::filterPage($content);
		

		if (self::checkExistFilter($content) == true) {
			$content = self::includes($content,$dir,$publicDir,self::$domain,$page);
			return $content;
		}

		return $content;
	}

	/**
	 * [check if recursive]
	 * 
	 */
	public static function checkExistFilter($content){
		$list = array(
			'\[\=domain\:url\=\]',
			'\[\=condition\:.*\=\]',
			'\[\=page\:.*\=\]',
			'\[\=global\:.*\=\]',
			'\[\=inc-route\:.*\=\]',
			'\[\=inc\:.*\=\]',
		);

		$test = preg_match_all('#'.implode('|', $list).'#im', $content,$m);
		if($test)return true;
		return false;
	}

}