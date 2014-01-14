<?php
namespace FelixOnline\Core;
/*
 * Image class
 *
 * Fields:
 *	  id			  -
 *	  title		   -
 *	  uri			 -
 *	  user			-
 *	  description	 -
 *	  timestamp	   -
 *	  v_offset		-
 *	  h_offset
 *	  caption
 *	  attribution
 *	  attr_link
 *	  width
 *	  height
 */
class Image extends BaseModel {
	/**
	 * Constructor for Image class
	 * If initialised with id then store relevant data in object
	 *
	 * $id - ID of image (optional)
	 *
	 * Returns image object
	 */
	function __construct($id=NULL) {
		$app = App::getInstance();

		if ($id !== NULL) { // if creating an already existing article object
			$sql = $app['safesql']->query(
				"SELECT
					`id`,
					`title`,
					`uri`,
					`user`,
					`description`,
					UNIX_TIMESTAMP(`timestamp`) as timestamp,
					`v_offset`,
					`h_offset`,
					`caption`,
					`attribution`,
					`attr_link`,
					`width`,
					`height`
				FROM `image`
				WHERE id=%i",
				array(
					$id,
				));
			parent::__construct($app['db']->get_row($sql), $id);
		} else {
			// initialise new image
		}
	}

	/**
	 * Public: Get image source url
	 */
	public function getURL($width = '', $height = '') {
		$uri = $this->getName();
		if ($height && $width) {
			return IMAGE_URL.$width.'/'.$height.'/'.$uri;
		} else if ($width) {
			return IMAGE_URL.$width.'/'.$uri;
		} else { // original image
			return IMAGE_URL.'upload/'.$uri;
		}
	}

	/**
	 * Public: Check if image is tall or not
	 */
	public function isTall() {
		if ($this->getWidth() < $this->getHeight()) {
			return true;
		}
		return false;
	}

	/**
	 * Public: Get image name
	 * Get image filename
	 */
	public function getName() {
		return str_replace('img/upload/', '', $this->getUri());
	}
}
