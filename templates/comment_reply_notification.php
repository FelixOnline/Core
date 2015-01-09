<?php
/*
 * Comment reply notification template
 * 
 * Requires $comment as comment object and $reply as the comment object of the comment that is replying to $comment
 */
?>
<p>
	Hi <?php echo $reply->getUser()->getFirstName(); ?>
</p>

<p>
<?php echo $comment->getName();?>

 has replied to your comment on "<a href="<?php echo $reply->getArticle()->getURL().'#comment'.$reply->getId(); ?>"><?php echo $reply->getArticle()->getTitle(); ?></a>" with: 
</p>

<p>
	"<?php echo $comment->getContent(); ?>"
</p>

<p>
	<a href="<?php echo $comment->getArticle()->getURL().'#comment'.$comment->getId(); ?>">View Comment</a>
</p>

<p>Lots of love,<br/>
Felix</p>
