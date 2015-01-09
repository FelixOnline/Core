<?php
/*
 * New comment notification (to authors) email template
 * 
 * Requires $comment as comment object
 * and $user as username of person the email is being sent to
 */
?>
<p>Hi <?php echo $user->getFirstName(); ?></p>
<p>
<?php echo $comment->getName(); ?>
 has posted a comment on your article, "<a href="<?php echo $comment->getArticle()->getURL().'#'.$comment->getId(); ?>"><?php echo $comment->getArticle()->getTitle();?></a>" with:
</p>
<p>
	"<?php echo $comment->getContent(); ?>"
</p>

<p>
	<a href="<?php echo $comment->getArticle()->getURL().'#comment'.$comment->getId(); ?>">View Comment</a>
</p>

<p>Lots of love,
<br/>
Felix</p>
