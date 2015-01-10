<?php
/*
 * New external comment email template
 * 
 * Requires $comment as comment object
 */
?>
<p>A comment on the post "<?php echo $comment->getArticle()->getTitle(); ?>" <b>has been reported as being abusive or inappropriate</b>. Please tend to this at the nearest possible opportunity. <br/>
<a href="<?php echo $comment->getArticle()->getURL(); ?>"/><?php echo $comment->getArticle()->getURL(); ?></a>
</p>

<p>
	Author: <?php echo $comment->getName(); ?> (IP: <?php echo $comment->getIp();?>)<br/>
	Whois: <a href="http://ip-whois-lookup.com/lookup.php?ip=<?php echo $comment->getIp(); ?>"/>http://ip-whois-lookup.com/lookup.php?ip=<?php echo $comment->getIp(); ?></a>
</p>

<p>
	Comment:<br/>
	"<?php echo $comment->getContent(); ?>"
</p>

<p>Please reject the comment, or approve it on the admin page: <a href="<?php echo $app->getOption('base_url')."admin/comments/pending"; ?>"><?php echo $app->getOption('base_url')."admin/comments/pending"; ?></a>. The comment will remain online unless it is explicitly taken down.</p>

<p>Felix Online</p>
