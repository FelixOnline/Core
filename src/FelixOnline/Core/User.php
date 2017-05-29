<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Base\AbstractUser;
use FelixOnline\Exceptions\InternalException;

/*
 * User class
 *
 * Fields:
 *	  user		-
 *	  name		-
 *	  visits	  -
 *	  ip		  -
 *	  timestamp   -
 *	  description -
 *	  email	   -
 *	  facebook	-
 *	  twitter	 -
 *	  websitename -
 *	  websiteurl  -
 *	  img		 -
 */
class User extends AbstractUser
{
    private $articles;
    private $count;
    private $popArticles = array();
    private $comments = array();
    private $likes;
    private $dislikes;
    private $image;
    public $dbtable = 'user';

    public function __construct($uname = null)
    {
        $fields = array(
            'user' => new Type\CharField(array('primary' => true)),
            'name' => new Type\CharField(),
            'visits' => new Type\IntegerField(array('dont_log' => true)),
            'ip' => new Type\CharField(array('dont_log' => true)),
            'timestamp' => new Type\DateTimeField(array('dont_log' => true)),
            'info' => new Type\TextField(),
            'description' => new Type\TextField(array(
                'transformers' => array(
                    Type\BaseType::TRANSFORMER_NO_HTML
                )
            )),
            'email' => new Type\CharField(array(
                'transformers' => array(
                    Type\BaseType::TRANSFORMER_NO_HTML
                )
            )),
            'facebook' => new Type\CharField(array(
                'transformers' => array(
                    Type\BaseType::TRANSFORMER_NO_HTML
                )
            )),
            'twitter' => new Type\CharField(array(
                'transformers' => array(
                    Type\BaseType::TRANSFORMER_NO_HTML
                )
            )),
            'websitename' => new Type\CharField(array(
                'transformers' => array(
                    Type\BaseType::TRANSFORMER_NO_HTML
                )
            )),
            'websiteurl' => new Type\CharField(array(
                'transformers' => array(
                    Type\BaseType::TRANSFORMER_NO_HTML
                )
            )),
            'image' => new Type\ForeignKey('FelixOnline\Core\Image'),
            'show_email' => new Type\BooleanField(),
            'show_ldap' => new Type\BooleanField(),
        );

        parent::__construct($fields, $uname);
    }

    public function getUsername() {
        return $this->getUser();
    }

    public function getPasswordHash() {
        return null;
    }

    public function setUsername($username) {
        $this->setUser($username);
    }

    public function setPasswordHash($hash) {
        return null;
    }

    /*
     * Public: Get url for user
     *
     * $page - page to link to
     */
    public function getURL($pagenum = null)
    {
        $app = App::getInstance();

        $output = $app->getOption('base_url') . 'user/'.$this->getUser().'/';
        if ($pagenum != null) {
            $output .= $pagenum.'/';
        }
        return $output;
    }

    /*
     * Public: Get user comment popularity
     *
     * Returns percentage
     */
    public function getCommentPopularity()
    {
        $total = $this->getLikes() + $this->getDislikes();
        if ($total) {
            $popularity = 100 * ($this->getLikes()
                            / ($this->getLikes() + $this->getDislikes()));
            return round($popularity);
        } else {
            return 0;
        }
    }

    /*
     * Public: Get likes
     * Get number of likes on comments by user
     */
    public function getLikes()
    {
        $app = App::getInstance();

        if (!$this->likes) {
            $sql = $app['safesql']->query(
                "SELECT
    SUM(likes)
    FROM `comment`
    WHERE user='%s'
    AND `active`=1
    AND deleted=0",
                array(
                    $this->getUser(),
                ));
            $this->likes = $app['db']->get_var($sql);
        }
        return $this->likes;
    }

    /*
     * Public: Get dislikes
     * Get number of dislikes on comments by user
     */
    public function getDislikes()
    {
        $app = App::getInstance();

        if (!$this->dislikes) {
            $sql = $app['safesql']->query(
                "SELECT
    SUM(dislikes)
    FROM `comment`
    WHERE user='%s'
    AND `active`=1
    AND deleted = 0",
                array(
                    $this->getUser(),
                ));
            $this->dislikes = $app['db']->get_var($sql);
        }
        return $this->dislikes;
    }

    /*
     * Public: Get first name of user
     *
     * Returns string
     */
    public function getFirstName()
    {
        $name = explode(' ', $this->getName());
        return $name[0];
    }

    /*
     * Public: Get last name of user
     *
     * Returns string
     */
    public function getLastName()
    {
        $name = explode(' ', $this->getName());
        return $name[1];
    }

    /*
     * Public: Get explicit roles
     */
    public function getExplicitRoles()
    {
        $manager = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\UserRole', 'user_roles', 'id');
        $manager->filter('user = "%s"', array($this->getUser()));

        $values = $manager->values();
        if (!$values) {
            return null;
        }

        $roles = array();
        foreach ($values as $value) {
            $roles[] = $value->getRole();
        }

        return $roles;
    }

    /*
     * Public: Get all including inherited roles
     */
    public function getRoles()
    {
        $roles = $this->getExplicitRoles();

        if (!$roles) {
            return null;
        }

        $finalRoles = array();
        foreach ($roles as $role) {
            $childRoles = $role->getChildRoles();

            foreach ($childRoles as $childRole) {
                if (!in_array($childRole, $finalRoles)) {
                    $finalRoles[] = $childRole;
                }
            }

            if (!in_array($role, $finalRoles)) {
                $finalRoles[] = $role;
            }
        }

        return $finalRoles;
    }

    /*
     * Public: Get user info
     * Decode json array of info
     *
     * Returns array
     */
    public function getInfo()
    {
        return self::jsonDecode($this->fields['info']->getValue());
    }

    /**
    * Decode JSON string and throw error if fails
    *
    * @param string $string - JSON string to decode
    *
    * @static
    *
    * @return mixed - associative array
    * @throws \Exception if json decode fails with message about why
    */
    public static function jsonDecode($string)
    {
        if ($string) {
            $json = json_decode($string, true);
            // if json_decode failed
            if ($json === null) {
                self::jsonLastError();
            }
            return $json;
        } else {
            return false;
        }
    }
    /**
    * Encode as JSON and throw error if fails
    *
    * @param mixed $data - data to encode
    *
    * @static
    *
    * @return string - json string
    * @throws \Exception if json decode fails with message about why
    */
    public static function jsonEncode($data)
    {
        $json = json_encode($data);
        // if json_encode failed
        if ($json === false) {
            self::jsonLastError();
        }
        return $json;
    }

    public static function jsonLastError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new InternalException('Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new InternalException('Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new InternalException('Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                throw new InternalException('Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                throw new InternalException('Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
            default:
                throw new InternalException('Unknown error');
                break;
        }
    }

    public function getFirstLogin()
    {
        $app = App::getInstance();

        $sql = $app['safesql']->query(
            "SELECT
    UNIX_TIMESTAMP(timestamp) as timestamp
    FROM `login`
    WHERE user='%s'
    AND deleted=0
    ORDER BY timestamp ASC
    LIMIT 1",
            array(
                $this->getUser()
            ));
        $login = $app['db']->get_var($sql);
        if ($login) {
            return $login;
        } else {
            return 1262304000; // 1st of January 2010
        }
    }

    public function getLastLogin()
    {
        $app = App::getInstance();

        $sql = $app['safesql']->query(
            "SELECT
    UNIX_TIMESTAMP(timestamp) as timestamp
    FROM `login`
    WHERE user='%s'
    AND deleted=0
    ORDER BY timestamp DESC
    LIMIT 1",
            array(
                $this->getUser()
            ));
        $login = $app['db']->get_var($sql);
        if ($login) {
            return $login;
        } else {
            return 1262304000; // 1st of January 2010
        }
    }

    /*
     * Public: Has personal info
     * Check to see whether user has personal info
     */
    public function hasPersonalInfo()
    {
        if ($this->getDescription()
            || $this->getFacebook()
            || $this->getTwitter()
            || $this->getEmail()
            || $this->getWebsiteurl()
        ) {
            return true;
        }
        return false;
    }

    /*
     * Public: has Articles Hidden from Robots
     */
    public function hasArticlesHiddenFromRobots()
    {
        $app = App::getInstance();

        $sql = $app['safesql']->query(
            "SELECT
    COUNT(article_author.article)
    FROM `article_author`
    INNER JOIN `article`
    ON article_author.article = article.id
    WHERE article_author.`author` = '%s'
    AND article.deleted = 0
    AND article_author.deleted = 0
    AND searchable = 0",
            array(
                $this->getUser())
            );
        $result = $app['db']->get_var($sql);
        return ($result > 0 ? true: false);
    }

    /*
     * Create new user
     */
    public static function createUser($username)
    {
        $user = new User();
        $user->setUser($username);
        $user->setName($username);
        $user->setEmail($username."@imperial.ac.uk");
        $user->setInfo(json_encode(array()));
        $user->setVisits(0);
        $user->setIp(0);
        $user->setImage(676); // FIXME - Move to const
        $user->setShowEmail(true);
        $user->setShowLdap(true);

        $user->updateName();
        $user->updateEmail();
        $user->updateInfo();
        $user->save();

        return $user;
    }

    /**
     * Sync details from LDAP
     */
    public function syncLdap()
    {
        $this->updateName();
        $this->updateEmail();
        $this->updateInfo();
        $this->save();
    }

    /**
     * Update user's name from ldap
     */
    private function updateName()
    {
        if (!LOCAL) {
            $ds = ldap_connect("addressbook.ic.ac.uk");
            $r = ldap_bind($ds);
            $justthese = array("displayname");
            $sr = ldap_search(
                $ds,
                "ou=People,ou=shibboleth,dc=ic,dc=ac,dc=uk",
                "uid=".$this->getUser(),
                $justthese
            );
            $info = ldap_get_entries($ds, $sr);
            if ($info["count"] > 0) {
                $this->setName($info[0]['displayname'][0]);
                return ($info[0]['displayname'][0]);
            } else {
                return false;
            }
        } else {
            $name = $this->getName();
            return $name;
        }
    }

    /**
     * Update user's email address from ldap
     */
    private function updateEmail()
    {
        if (!LOCAL) {
            $ds = ldap_connect("addressbook.ic.ac.uk");
            $r = ldap_bind($ds);
            $justthese = array("mail");
            $sr = ldap_search(
                $ds,
                "ou=People,ou=shibboleth,dc=ic,dc=ac,dc=uk",
                "uid=".$this->getUser(),
                $justthese
            );
            $info = ldap_get_entries($ds, $sr);
            if ($info["count"] > 0) {
                $this->setEmail($info[0]['mail'][0]);
                return ($info[0]['mail'][0]);
            } else {
                return false;
            }
        } else {
            $email = $this->getEmail();
            return $email;
        }
    }

    /*
     * Update user's info from ldap
     *
     * Returns json encoded array
     */
    private function updateInfo()
    {
        $info = '';
        if (!LOCAL) { // if on union server
            $ds = ldap_connect("addressbook.ic.ac.uk");
            $r = ldap_bind($ds);
            $justthese = array("o");
            $sr = ldap_search(
                $ds,
                "ou=People,ou=shibboleth,dc=ic,dc=ac,dc=uk",
                "uid=".$this->getUser(),
                $justthese
            );
            $info = ldap_get_entries($ds, $sr);
            if ($info["count"] > 0) {
                $info = json_encode(explode('|', $info[0]['o'][0]));
                $this->setInfo($info);
            } else {
                return false;
            }
        }
        return $info;
    }

    /**
     * Public: Get categories this user edits
     *
     * Returns array of category objects
     */
    public function getCategories()
    {
        $editors = BaseManager::build('FelixOnline\Core\Category', 'category_author', 'category')
            ->filter("user = '%s'", array($this->getUser()))
            ->values();

        return $editors;
    }
}
