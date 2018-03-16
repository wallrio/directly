<?php

namespace directly;

use \directly\FileHandle as FileHandle;
use \directly\Filter as Filter;

class Directly{

	private $dir;
	public $publicDir = '';

	function __construct($appDir = 'app'){
		$this->dir = $appDir.DIRECTORY_SEPARATOR;
		if(!file_exists($this->dir)){
			echo 'Not exist application [ '.$this->dir.' ]';
			exit;	
		}
		
	}

	

	public function run($urlRoute = '/'){
		
		// get request data
		$REQUEST_SCHEME = $_SERVER['REQUEST_SCHEME'];	
		$HTTP_HOST = $_SERVER['HTTP_HOST'];	
		$SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];	
		$SERVER_PROTOCOL = isset($SERVER_PROTOCOL['REDIRECT_URL'])?($SERVER_PROTOCOL['REDIRECT_URL']):null;
		$PHP_SELF = isset($PHP_SELF['REDIRECT_URL'])?($PHP_SELF['REDIRECT_URL']):null;
		$REDIRECT_URL = isset($_SERVER['REDIRECT_URL'])?($_SERVER['REDIRECT_URL']):null;

		

		// set initial variables 
		$localdir = dirname($SCRIPT_NAME);
		$extensionArray = explode('.', $REDIRECT_URL);		
		$extension = false;
		$publicDirReal = '';

		$this->domain = $REQUEST_SCHEME . '://' . str_replace('//', '/', $HTTP_HOST.'/'.$localdir).'/';

		
	
		if(count($extensionArray)>1)			
			$extension = end($extensionArray);
		
		$page = $REDIRECT_URL;
		$page = preg_replace('#'.$localdir.'#i', '', $page, 1);		
		
		if($urlRoute != '/'){
			if(substr($urlRoute, strlen($urlRoute)-1,strlen($urlRoute))=='/')
				 	$urlRoute = substr($urlRoute, 0,strlen($urlRoute)-1);

			if(substr($urlRoute, 0,1)=='/') $urlRoute = substr($urlRoute, 1);
			
				 
		}

		if(substr($page, strlen($page)-1,strlen($page))=='/')
				 	$page = substr($page, 0,strlen($page)-1);


		if($urlRoute!='/'){		
			if($page == ''){						
				header("Location: ".$urlRoute.'/');		
				exit;
			}
			if(substr($page, 0,1)=='/') $page = substr($page, 1);
		}
		
		
		
			
		if($urlRoute==$page){
			$page = $urlRoute.'/home';
		}

		if($page == '') $page = "home";

		$pageArray = explode('/', $page);
		$pageArray = array_filter($pageArray);
		$pageArray = array_values($pageArray);
		$pageNewArray = $pageArray;	
		$urlRouteArray = explode('/', $urlRoute);
		$urlRouteArray = array_filter($urlRouteArray);
		$urlRouteArray = array_values($urlRouteArray);
		
		
		

		$routeStatus = true;
		foreach ($urlRouteArray as $key => $value) {	

		

			if($value === $pageArray[$key]){
				unset($pageNewArray[$key]);
			}else{
				$routeStatus = false;
			}
		}

		

		$pageNewArray = array_filter($pageNewArray);
		$pageArray = array_values($pageNewArray);			
		$page = implode('/', $pageNewArray);


		// set paths
		$global_dir = $this->dir.'global'.DIRECTORY_SEPARATOR;
		$page_app = $this->dir.$this->publicDir.DIRECTORY_SEPARATOR.$page;
		$page_dir = $this->dir.'view'.DIRECTORY_SEPARATOR.$page.DIRECTORY_SEPARATOR;
		$page_dir = str_replace('//', '/', $page_dir);
		$page_error404 = $this->dir.'error/404/'.DIRECTORY_SEPARATOR;



		if($extension !== false){
			$filename = getcwd().DIRECTORY_SEPARATOR.$page_app;
			$filename = str_replace('//', '/', $filename);

			$filenameArray = explode('/',dirname($filename));
			$filenameArray = array_filter($filenameArray);
			$filenameArray = array_values($filenameArray);
			
			$joinPath = '';
			$newArray = [];
			foreach ($filenameArray as $key => $value) {
				$joinPath .= $value  .'/';
				$newArray[] = $joinPath;
			}
			$newArray = array_reverse($newArray);
			foreach ($newArray as $key => $value) {
				if(file_exists('/'.$value)){					
					
					$page_assets = $this->dir.$this->publicDir.DIRECTORY_SEPARATOR;
					$filename = getcwd().DIRECTORY_SEPARATOR.$page_assets;
					break;
				}
				
			}

			// adjust assets to error page -------------------------
			$pageArray = explode('/', $page);
			$pageArray = array_reverse($pageArray);

			$pageJoin = '';
			$pageJoinNewArray = [];
			foreach ($pageArray as $key => $value) {
				 $pageJoin = $value.'/'.$pageJoin;
				 $pageJoin = str_replace('//', '/', $pageJoin);
				 if(substr($pageJoin, strlen($pageJoin)-1,strlen($pageJoin))=='/')
				 	$pageJoin = substr($pageJoin, 0,strlen($pageJoin)-1);
				$pageJoinNewArray[] = $pageJoin;
			}
			$pageJoinNewArray = array_reverse($pageJoinNewArray);
			$preFilename = $filename;
			foreach ($pageJoinNewArray as $key => $value) {
				$filename = $preFilename.$value;
				if(file_exists($filename)){					
					break;
				}
			}




			if(file_exists($filename)){		
				$typeFile = FileHandle::getType($filename);
				// echo $typeFile.'--'.$filename;
				// exit;

				
				header('Content-Type: '.$typeFile.'; charset=UTF-8');
				
				if($typeFile == 'text/php'){
					include $filename;
				}else{
					$contentFile = file_get_contents($filename);				
					echo $contentFile;
				}
			}else{
				header("HTTP/1.0 404 Not Found [".$filename."]");
			}
			return false;
		}

			
			

		// get content of buffer
		
		ob_start();
		include_once $global_dir.'header.php';
		if(file_exists($page_dir.'view.php') && $routeStatus == true){

			include_once $page_dir.'view.php';		
		}else{
			header("HTTP/1.0 404 Not Found [".$page."]");
		    include_once $page_error404.'view.php';				   
		}
		include_once $global_dir.'footer.php';
		$content = ob_get_contents();
		ob_end_clean();


		// filter with shorttag
		$content = Filter::includes($content,$this->dir,$this->domain);
		
		// show de content
		$this->show($content);

	}

	private function show($content = ''){
		echo $content;
	}

	
}