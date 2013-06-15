<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \Eloquent;
use Spoolphiz\Events\Models\Eloquent\Event as Event;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	protected $userRoleIds = array('INSTRUCTOR'=>3);

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
	protected $hidden = array('password');


	 /**
	 * Relationships
	 */
	public function events()
	{
		return $this->belongsToMany('Spoolphiz\Events\Models\Eloquent\Event', 'event_instructor');
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
		if( $this->role_id == 1 ){
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
		if( $this->role_id == 2 ){
			return true;
		}
		else{
			return false;
		}
	}
}