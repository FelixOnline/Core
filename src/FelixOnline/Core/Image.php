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
		if ($id !== NULL) { // if creating an already existing article object
			$sql = App::query(
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
			parent::__construct(App::$db->get_row($sql), $id);
		} else {
			// initialise new image
		}
	}

	/**
	 * Public: Get image source url
	 */
	public function getURL($width = '', $height = '') {
		if($this->getUri()) {
			$uri = $this->getName();
			if($height) {
				return IMAGE_URL.$width.'/'.$height.'/'.$uri;
			} else if($width) {
				return IMAGE_URL.$width.'/'.$uri;
			} else {
				return IMAGE_URL.'upload/'.$uri;
			}
		} else {
			return IMAGE_URL.DEFAULT_IMG_URI;
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
