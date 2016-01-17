<?php

use Phinx\Migration\AbstractMigration;

class MoreSettings extends AbstractMigration
{
    public function up()
    {
        echo "The settings will be pre-populated with the values hardcoded into Felix Online in versions prior to this one. You may wish to update them after.\n";
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("site_name", "Site name", "Felix Online");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("site_tagline", "Site tagline", "The student voice of Imperial College London");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("site_keywords", "Site keywords (search engines)", "felix, student news, student newspaper, felix online, imperial college union, imperial college, felixonline");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("site_description", "Site description", "Felix Online is the online companion to Felix, the student newspaper of Imperial College London.");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_google", "Google site verification ID", "V5LPwqv0BzMHvfMOIZvSjjJ-8tJc4Mi1A-L2AEbby50");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_fb", "Facebook app ID", "200482590030408");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_ga", "Google Analytics ID", "UA-12220150-1");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_gs", "GoSquared ID", "GSN-410478-T");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_api", "API URL", "http://www.felixonline.co.uk/api");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_admin", "Admin URL (affects frontend)", "https://union.ic.ac.uk/media/felix/admin");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("app_twitter", "Main site twitter name (excluding @)", "felixonline");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("contact_email", "Email URL (if not a page, prefix with mailto:", "mailto:felix@imperial.ac.uk");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("contact_fb", "Facebook URL", "http://www.fb.me/FelixImperial");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("contact_twitter", "Twitter URL", "http://www.twitter.com/FelixImperial");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("contact_address", "Postal address", "Felix, Beit Quad, Prince Consort Road, London, SW7 2BB. Tel: <a href=\"tel:02075948072\">020 7594 8072</a>.");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("contact_copyright", "Copyright", "Copyright &copy; Felix Imperial. Registered newspaper ISSN 1040-0711.");');

    }

    public function down()
    {
        $elements = array('site_name', 'site_tagline', 'site_keywords', 'site_description', 'app_google', 'app_fb', 'app_ga', 'app_gs', 'app_api', 'app_admin', 'app_twitter', 'contact_email', 'contact_fb', 'contact_twitter', 'contact_address', 'contact_copyright');

        foreach($elements as $element) {
            $this->execute('DELETE FROM settings WHERE setting = "'.$element.'"');
        }
    }
}
