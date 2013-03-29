<?php

/**
 * Define namespace
 */
namespace Bazinga;

/**
 * Bazinga PHP serverside
 * 
 * This class is ad php tidy wrapper to check your html code.
 * NOTE: Be sure to have php tidy installed! Check http://www.php.net/manual/en/book.tidy.php
 * for more information on php tidy.
 *
 * @author Florian Binder <fb@sideshow-systems.de>
 */
class Bazinga {
	
	/**
	 * Hold tidy object
	 */
	private $tidy = null;
	
	/**
	 * Url to validate
	 */
	private $url2validate = null;
	
	/**
	 * Constructor
	 */
	public function __construct($url2validate = null) {
		if(!extension_loaded('tidy')) {
			throw new \Exception('php tidy is not installed!');
		}
		
		if(!empty($url2validate)) {
			$this->url2validate = $url2validate;
		}
		
		$this->tidy = new \tidy();
	}
	
	/**
	 * Setter method to set the url you want to validate
	 * 
	 * @param string $url2validate
	 * @return void
	 */
	public function setUrlToValidate($url2validate) {
		$this->url2validate = $url2validate;
	}
	
	/**
	 * Validate the data
	 * 
	 * @return array The validation result
	 */
	public function validate() {
		$result = array();
		
		// Get source
		$source2validate = $this->getUrlContent();
		
		// Check it
		$this->tidy->parseString($source2validate);
		$this->tidy->diagnose();
		
		// Get info
		$info = $this->tidy->errorBuffer;
		$info_exp = \explode("\n", $info);
		$info_lines = array();
		if(!empty($info_exp)) {
			foreach($info_exp as $line) {
				$info_lines[] = htmlentities($line);
			}
		}
		$result['info'] = $info_lines;
		
		// Count errors
		$errorCnt = tidy_error_count($this->tidy);
		$result['cnt_error'] = $errorCnt;
		
		// Count warnings
		$warningCnt = tidy_warning_count($this->tidy);
		$result['cnt_warning'] = $warningCnt;
		
		// Count access
		$accesCnt = tidy_access_count($this->tidy);
		$result['cnt_access'] = $accesCnt;
		
		// Get tidy status -- Returns 0 if no error/warning was raised, 1 for warnings or accessibility errors, or 2 for errors
		$tidyStatus = $this->tidy->getStatus();
		$result['status'] = $tidyStatus;
		
//		\sleep(2);
		
		return $result;
	}
	
	/**
	 * Get the source of the url to validate
	 * 
	 * @return string The source of the url to validate
	 */
	private function getUrlContent() {
		$result = file_get_contents($this->url2validate);
		if(empty($result) || !$result) {
			throw new \Exception('Could not get source from url');
		}
		return $result;
	}
}

?>
