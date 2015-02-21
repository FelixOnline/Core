<?php

use Phinx\Migration\AbstractMigration;

class ContactUsPage extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('UPDATE pages SET content = "<p>You can email us at <?php echo Utility::hideEmail(\'felix@imperial.ac.uk\');?> or use the contact form below: </p><form action=\"\" method=\"post\" id=\"contactform\">  <input type=\"hidden\" id=\"token\" name=\"token\" value=\"__CSRF_TOKEN__\"><input type=\"hidden\" name=\"check\" value=\"generic_page\" id=\"check\"><label for=\"name\">Name: <span>(optional)</span></label><input type=\"text\" id=\"name\" name=\"name\" />    <label for=\"email\">Email: <span>(optional)</span></label><input type=\"text\" id=\"email\" name=\"email\" />  <label for=\"message\">Message: <span>(required)</span></label><textarea id=\"message\" name=\"message\"></textarea>  <div class=\"clear\"></div>   <input type=\"submit\" value=\"Send\" id=\"submit\" name=\"submit\" class=\"button\" /></form><span id=\"sent\" class=\"alert-box\" style=\"display: none;\">Thank you!</span>" WHERE slug = "contact"');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('UPDATE pages SET content = "<p>You can email us at <?php echo Utility::hideEmail(\'felix@imperial.ac.uk\');?> or use the contact form below: </p><form action=\"\" method=\"post\" id=\"contactform\"><input type=\"hidden\" id=\"token\" name=\"token\" value=\"__CSRF_TOKEN__\"> <label for=\"name\">Name: <span>(optional)</span></label><input type=\"text\" id=\"name\" name=\"name\" />  <label for=\"email\">Email: <span>(optional)</span></label><input type=\"text\" id=\"email\" name=\"email\" />  <label for=\"message\">Message: <span>(required)</span></label><textarea id=\"message\" name=\"message\"></textarea>  <label for=\"message\" class=\"error\">Please write a message</label>   <div class=\"clear\"></div>   <input type=\"submit\" value=\"Send\" id=\"submit\" name=\"submit\"/>   <span id=\"sending\" style=\"display: none;\">Sending...</span></form><span id=\"sent\" style=\"display: none;\">Thank you!</span>" WHERE slug = "contact"');
    }
}