<?php
namespace FelixOnline\Core;
/*
 * Page Class
 *
 * Fields:
 *	  id:		 - id of page
 *	  slug:	   - url slug of page
 *	  title:	  - title of page
 *	  content:	- content of page
 */

class Page extends BaseDb {
	public $dbtable = 'pages';

	private $csrf_token;

	/*
	 * Constructor for Page class
	 * If initialised with id then store relevant data in object
	 *
	 * $id - ID of page (optional)
	 *
	 * Returns page object
	 */
	function __construct($id = NULL)
	{
		$fields = array(
			'slug' => new Type\CharField(),
			'title' => new Type\CharField(),
			'content' => new Type\TextField(),
		);

		parent::__construct($fields, $id);
		$this->csrf_token = Utility::generateCSRFToken('generic_page');
	}

	/*
	 * Private: Take string and eval any php
	 * Find any instances of php tags in string and replaces it with the evaluated php
	 */
	private function evalPHP($string) {
		ob_start();
		eval("?>$string<?php ");
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/*
	 * Public: Get page content
	 */
	public function getContent() {
		return str_replace('__CSRF_TOKEN__', $this->csrf_token, $this->evalPHP($this->fields['content']->getValue()));
	}

	/*
	 * Public: Get page content
	 */
	public function getToken() {
		return $this->csrf_token;
	}
}
