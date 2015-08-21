<?php
	/**
	 * Site constants
	 *
	 * To change constants define them in config.php
	 */

	/* SYSTEM */
	if(!defined('SESSION_NAME'))					define('SESSION_NAME','felix');
	if(!defined('COOKIE_NAME'))						define('SESSION_NAME','felixonline');
	if(!defined('SESSION_LENGTH'))					define('SESSION_LENGTH',7200); // session length
	if(!defined('LOGIN_CHECK_LENGTH'))				define('LOGIN_CHECK_LENGTH',300); // length to allow login check (5mins)
	if(!defined('COOKIE_LENGTH'))					define('COOKIE_LENGTH', 2592000); // cookie length (30 days) (60*60*24*30)
	if(!defined('ARTICLES_PER_SEARCH_PAGE'))		define('ARTICLES_PER_SEARCH_PAGE',8); // number of articles on the first category page
	if(!defined('IMAGE_URL'))						define('IMAGE_URL', 'http://img.felixonline.co.uk/'); // image url
	if(!defined('LOCAL'))							define('LOCAL', false); // if true then site is hosted locally - don't use pam_auth etc.

	/* COMMENTS */
	if(!defined('AKISMET_API_KEY'))					define('AKISMET_API_KEY', 'KEY');

	/* EMAIL */
	if(!defined('EMAIL_EXTCOMMENT_NOTIFYADDR'))		define('EMAIL_EXTCOMMENT_NOTIFYADDR', 'felix@imperial.ac.uk'); // comma-separated list of addresses to notify when a new external comment needs approval
	if(!defined('EMAIL_REPLYTO_ADDR'))				define('EMAIL_REPLYTO_ADDR','no-reply@imperial.ac.uk');
	if(!defined('EMAIL_REPLYTO_NAME'))				define('EMAIL_REPLYTO_NAME','Felix Online');