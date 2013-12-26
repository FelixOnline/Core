<?php
	/**
	 * Site constants
	 *
	 * To change constants define them in config.php
	 */

	/* SYSTEM */
	if(!defined('SESSION_LENGTH'))					define('SESSION_LENGTH',7200); // session length
	if(!defined('LOGIN_CHECK_LENGTH'))				define('LOGIN_CHECK_LENGTH',300); // length to allow login check (5mins)
	if(!defined('COOKIE_LENGTH'))					define('COOKIE_LENGTH', 2592000); // cookie length (30 days) (60*60*24*30)
	if(!defined('AUTHENTICATION_SERVER'))			define('AUTHENTICATION_SERVER','dougal.union.ic.ac.uk'); // authentication server
	if(!defined('AUTHENTICATION_PATH'))				define('AUTHENTICATION_PATH','https://dougal.union.ic.ac.uk/media/felix/'); // authentication path
	if(!defined('MOST_POPULAR_INTERVAL'))			define('MOST_POPULAR_INTERVAL',7); // commented - look at comments over previous ... days
	if(!defined('POPULAR_ARTICLES'))				define('POPULAR_ARTICLES',5); // used for commented and viewed
	if(!defined('RECENT_COMMENTS'))					define('RECENT_COMMENTS',5); // number of recent comments to display
	if(!defined('ARTICLES_PER_CAT_PAGE'))			define('ARTICLES_PER_CAT_PAGE',8); // number of articles on the first category page
	if(!defined('ARTICLES_PER_SECOND_CAT_PAGE'))	define('ARTICLES_PER_SECOND_CAT_PAGE',10); // number of articles on the second category page
	if(!defined('ARTICLES_PER_USER_PAGE'))			define('ARTICLES_PER_USER_PAGE',8); // number of articles on user page
	if(!defined('ARTICLES_PER_SECOND_USER_PAGE'))	define('ARTICLES_PER_SECOND_USER_PAGE',10); // number of articles on the second user page
	if(!defined('NUMBER_OF_PAGES_IN_PAGE_LIST'))	define('NUMBER_OF_PAGES_IN_PAGE_LIST',14); // [TODO]
	if(!defined('NUMBER_OF_POPULAR_ARTICLES_USER'))	define('NUMBER_OF_POPULAR_ARTICLES_USER',5); // max number of popular articles on user page
	if(!defined('NUMBER_OF_POPULAR_COMMENTS_USER'))	define('NUMBER_OF_POPULAR_COMMENTS_USER',5); // max number of popular comments on user page
	if(!defined('IMAGE_URL'))						define('IMAGE_URL', 'http://img.felixonline.co.uk/'); // image url
	if(!defined('GALLERY_IMAGE_URL'))				define('GALLERY_IMAGE_URL', 'http://felixonline.co.uk/gallery/'); // image url
	if(!defined('LOCAL'))							define('LOCAL', false); // if true then site is hosted locally - don't use pam_auth etc.
	if(!defined('CACHE'))							define('CACHE', true); // Enable cache
	if(!defined('CACHE_LENGTH'))					define('CACHE_LENGTH', 1800); // Default cache length (20 mins)
	if(!defined('BLOG_POSTS_PER_PAGE'))				define('BLOG_POSTS_PER_PAGE', 10); // number of posts to show on blog page

	/* Media Page */
	if(!defined('NUMBER_OF_ALBUMS_FRONT_PAGE'))		define('NUMBER_OF_ALBUMS_FRONT_PAGE',4); // number of media items on front page
	if(!defined('NUMBER_OF_ALBUMS_PER_FULL_PAGE'))	define('NUMBER_OF_ALBUMS_PER_FULL_PAGE',12); // number of media items on a full page

	/* COMMENTS */
	if(!defined('AKISMET_API_KEY'))					define('AKISMET_API_KEY', 'KEY');

	/* EMAIL */
	if(!defined('EMAIL_ERRORS'))					define('EMAIL_ERRORS', 'jkimbo@gmail.com, philip.kent@me.com'); // people to email on errors
