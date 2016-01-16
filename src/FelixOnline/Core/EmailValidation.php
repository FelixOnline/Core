<?php
namespace FelixOnline\Core;
/**
 * Category class
 */
class EmailValidation extends BaseDB
{
	public $dbtable = 'email_validation';

	function __construct($id = NULL)
	{
		$fields = array(
			'email' => new Type\CharField(),
			'code' => new Type\CharField(),
			'confirmed' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id, null, true);
	}

	public static function create($email) {
		$manager = BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation');
		$manager->filter('email = "%s"', array($email));

		$count = $manager->count();

		if($count != 0) {
			return false;
		}

		$class = new EmailValidation();
		$class->setEmail($email);
		$class->setConfirmed(0);

		// Generate a code
		$code = uniqid();
		$class->setCode($code);
		$class->save();

		// Return the code for use elsewhere
		return $code;
	}

	public static function isEmailValidated($email) {
		$manager = BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation');
		$manager->filter('email = "%s"', array($email));

		$count = $manager->count();

		if($count > 1) {
			throw new FelixOnline\Exceptions\InternalException('Too many email validation records found');
		}

		if($count == 0) {
			return false;
		}

		$record = $manager->one();

		return $record->getConfirmed();
	}
}
