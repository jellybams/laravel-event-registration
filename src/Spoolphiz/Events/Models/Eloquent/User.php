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

	static $userRoleIds = array('ADMIN'=>1,
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
	protected $fillable = array('username', 'email', 'name', 'active', 'api_key');


	/**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('username' => array('required', 'max:32', 'unique:users,username'), 
								'password' => array('required'),
								'email' => array('required', 'email', 'unique:users,email'), 
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
		$dirty = $this->getDirty();
		
		//exclude the current user id from 'unqiue' validators
		if( $this->id > 0 )
		{
			$usernameUnique = 'unique:users,username,'.$this->id;
			$emailUnique = 'unique:users,email,'.$this->id;
			$apiUnique = 'unique:users,api_key,'.$this->id;
			
			//if the password is being changed, check for the password_confirmation field as well
			if( isset($dirty['password']) || isset($dirty['password_confirmation']) )
			{
				$this->validators['password'][] = 'confirmed';
			}
		}
		else
		{
			//this is a new user, no need to exclude any user ids from the validators
			$usernameUnique = 'unique:users,username';
			$emailUnique = 'unique:users,email';
			$apiUnique = 'unique:users,api_key';
			
			//since this is a new user, make sure the password is confirmed
			$this->validators['password'][] = 'confirmed';
		}
		
		
		
		$this->validators['username'] = array('required', 'max:32', $usernameUnique);
		$this->validators['email'] = array('required', 'max:32', $emailUnique);
		$this->validators['api_key'] = array('required', 'max:32', $apiUnique);
		
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
		
		if( isset($this->password) )
		{
			$this->password = Hash::make($this->password);
		}
		
		return parent::save();
	}
	
	/**
	* Relationships
	*/
	public function category()
    {
        return $this->belongsTo('Spoolphiz\Events\Models\Eloquent\Role');
    }


	public static function makeApiKey()
	{
	    /*return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

	      // 32 bits for "time_low"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

	      // 16 bits for "time_mid"
	      mt_rand(0, 0xffff),

	      // 16 bits for "time_hi_and_version",
	      // four most significant bits holds version number 4
	      mt_rand(0, 0x0fff) | 0x4000,

	      // 16 bits, 8 bits for "clk_seq_hi_res",
	      // 8 bits for "clk_seq_low",
	      // two most significant bits holds zero and one for variant DCE1.1
	      mt_rand(0, 0x3fff) | 0x8000,

	      // 48 bits for "node"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	    );*/
	
		return md5(microtime());
	}
	
	
	public function getApiKey()
	{
		return $this->api_key;
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
		if( $this->role_id == static::$userRoleIds['ADMIN'] ){
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
		if( $this->role_id == static::$userRoleIds['SALESREP'] ){
			return true;
		}
		else{
			return false;
		}
	}
}