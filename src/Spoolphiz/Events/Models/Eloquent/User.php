<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \Eloquent;
use \Hash;
use \Validator;
use \ValidationException;
use Spoolphiz\Events\Models\Eloquent\Event as Event;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	protected $userRoleIds = array('ADMIN'=>1,
									'SALESREP'=>2,
									'INSTRUCTOR'=>3);

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'api_key');
	
	
	/**
	 * A white-list of fillable attributes
	 *
	 * @var array
	 */
	protected $fillable = array('username', 'email', 'name', 'role_id', 'active', 'api_key');


	/**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('username' => array('required', 'max:32', 'unique:users'), 
								'password' => array('required', 'confirmed'),
								'email' => array('required', 'email', 'unique:users'), 
								'name' => array('required', 'max:100'), 
								'role_id' => array('required', 'numeric'), 
								'active' => array('required', 'numeric'), 
								'api_key' => array('required', 'unique:users,api_key')
								);
								

	 /**
	 * Relationships
	 */
	public function events()
	{
		return $this->belongsToMany('Spoolphiz\Events\Models\Eloquent\Event', 'event_instructor')->with('instructors');
	}


	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate() 
	{
		$val = Validator::make($this->attributes, $this->validators);

		if ($val->fails())
		{
			throw new ValidationException($val);
		}
	}
	
	
	/**
	 * hashes password before saving user
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array()) 
	{
		if( isset($this->password_confirmation) )
		{
			unset($this->password_confirmation);
		}
		
		$this->password = Hash::make($this->password);
		
		return parent::save();
	}
	

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}


	/**
	 * Figures out if the user is a (super) administrator
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		if( $this->role_id == $this->userRoleIds['ADMIN'] ){
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Figures out if the user is a (super) administrator
	 *
	 * @return bool
	 */
	public function isSalesRep()
	{
		if( $this->role_id == $this->userRoleIds['SALESREP'] ){
			return true;
		}
		else{
			return false;
		}
	}
}