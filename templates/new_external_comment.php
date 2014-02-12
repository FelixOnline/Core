<?php
/*
 * New external comment email template
 * 
 * Requires $comment as comment object
 */
?>
<p>A new comment on the post "<?php echo $comment->getArticle()->getTitle(); ?>" is waiting for your approval. </br>
<a href="<?php echo $comment->getArticle()->getURL(); ?>"/><?php echo $comment->getArticle()->getURL(); ?></a>
</p>

<p>
	Author: <?php echo $comment->getName(); ?> (IP: <?php echo $comment->getIp();?>)</br>
	Whois: <a href="http://ip-whois-lookup.com/lookup.php?ip=<?php echo $comment->getIp(); ?>"/>http://ip-whois-lookup.com/lookup.php?ip=<?php echo $comment->getIp(); ?></a>
</p>

<p>
	Comment:</br>
	"<?php echo $comment->getContent(); ?>"
</p>

<p>
	Approve it: <a href="<?php echo $app->getOption('base_url')."engine/?page=comment&action=approve&c=".$comment->getId(); ?>"><?php echo $app->getOption('base_url')."engine/?page=comment&action=approve&c=".$comment->getId(); ?></a></br>
	Trash it: <a href="<?php echo $app->getOption('base_url')."engine/?page=comment&action=trash&c=".$comment->getId(); ?>"><?php echo $app->getOption('base_url')."engine/?page=comment&action=trash&c=".$comment->getId(); ?></a></br>
	Spam it: <a href="<?php echo $app->getOption('base_url')."engine/?page=comment&action=spam&c=".$comment->getId();?>"><?php echo $app->getOption('base_url')."engine/?page=comment&action=spam&c=".$comment->getId();?></a>

</p>

<p>There are <?php echo $comment->getNumCommentsToApprove(); ?> comment(s) waiting to be approved. View them here: <a href="<?php echo $app->getOption('base_url')."engine/?page=comment"; ?>"><?php echo $app->getOption('base_url')."engine/?page=comment"; ?></a></p>

<p>Felix Online</p>
