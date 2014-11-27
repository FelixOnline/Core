<?php
/*
 * New external comment email template
 * 
 * Requires $comment as comment object
 */
?>
<p>A new comment on the post "<?php echo $comment->getArticle()->getTitle(); ?>" is waiting for moderation. <br/>
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

<p>There are <?php echo $comment->getNumCommentsToApprove(); ?> comment(s) waiting to be moderated. View them here: <a href="<?php echo $app->getOption('base_url')."admin/comments/pending"; ?>"><?php echo $app->getOption('base_url')."admin/comments/pending"; ?></a></p>

<p>Felix Online</p>
