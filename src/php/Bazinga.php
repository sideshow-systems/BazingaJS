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
	 * Parsed html data
	 */
	private $parsedHtmlData = array();

	/**
	 * Constructor
	 */
	public function __construct($url2validate = null) {
		if (!extension_loaded('tidy')) {
			throw new \Exception('php tidy is not installed!');
		}

		if (!empty($url2validate)) {
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

		// Get root from tidy
		$root = $this->tidy->root();
//		print_r($root);
		$this->generateParsedHtmlData($root, 1);
//		echo "<pre>";
//		print_r($this->parsedHtmlData);
//		echo "</pre>";
		// Get info
		$info = $this->tidy->errorBuffer;
		$info_exp = \explode("\n", $info);
		$info_lines = array();
		if (!empty($info_exp)) {
			foreach ($info_exp as $line) {

				// Try to get line number and column from $line
				$pattern = '/^line\ (?P<line>\d+) column\ (?P<column>\d+)/';
				$matches = array();
				preg_match($pattern, $line, $matches);
				$lin = null;
				$col = null;
				if (!empty($matches)) {
					$lin = (!empty($matches['line'])) ? $matches['line'] : null;
					$col = (!empty($matches['column'])) ? $matches['column'] : null;
				}
				$info_lines[] = array(
					'line'	=> htmlentities($line),
					'lin'	=> $lin,
					'col'	=> $col
				);
//				print_r($line);
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
		if (empty($result) || !$result) {
			throw new \Exception('Could not get source from url');
		}
		return $result;
	}

	/**
	 * Generate parsed html data
	 * 
	 * @param tidyNode $node
	 * @param int $indent
	 */
	private function generateParsedHtmlData($node, $indent) {
		$pData = array();

		if ($node->hasChildren()) {
			foreach ($node->child as $child) {
				$pData = array(
					'value' => $child->value,
					'name' => $child->name,
					'type' => $child->type,
					'line' => $child->line,
					'column' => $child->column,
					'proprietary' => $child->proprietary,
					'id' => $child->id,
					'attribute' => $child->attribute
				);
				$this->parsedHtmlData[] = $pData;

				$this->generateParsedHtmlData($child, $indent + 1);
			}
		}
	}

}

?>
