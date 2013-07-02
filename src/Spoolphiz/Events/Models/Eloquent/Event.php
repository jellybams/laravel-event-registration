<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \Eloquent;
use \Validator;
use Spoolphiz\Events\Models\Eloquent\Attendee;
use Spoolphiz\Events\Models\Eloquent\Venue;

class Event extends Eloquent {
	
	protected $userRoleIds = array('INSTRUCTOR'=>3);
	
	protected $defaultAttribs = array('event_type_id' => 1, 
									'status' => 0, 
									'create_seminaronly' => 0, 
									'create_fullevent' => 1);

	 /**
	 * A white-list of fillable attributes - not really needed for this model but included for completeness
	 *
	 * @var array
	 */
	protected $fillable = array('event_type_id', 'venue_id', 'start_date', 'end_date', 'title', 'contact_phone', 'seminar_price', 'full_price', 'capacity', 'status', 'create_seminaronly', 'create_fullevent');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('event_type_id' => array('required', 'numeric'),
								'venue_id' => array('numeric'), 
								'start_date' => array('date'), 
								'end_date' => array('date'), 
								'title' => array('max:100'),
								'contact_phone' => array('max:30'), 
								'seminar_price' => array('numeric'),
								'full_price' => array('numeric'), 
								'capacity' => array('numeric'),
								'status' => array('required', 'numeric', 'max:3'),
								'create_seminaronly' => array('in:0,1'),
								'create_fullevent' => array('in:0,1')
								);
	
	 /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'events';
	
	 
	 /**
	 * Relationships
	 */
	public function attendees()
    {
        return $this->hasMany('Spoolphiz\Events\Models\Eloquent\Attendee');
    }

	public function instructors()
    {
        return $this->belongsToMany('Spoolphiz\Events\Models\Eloquent\User', 'event_instructor', 'event_id', 'user_id');
    }

	public function venue()
	{
		return $this->belongsTo('Spoolphiz\Events\Models\Eloquent\Venue');
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
			throw new \ValidationException($val);
		}
	}
	
	
	/**
	 * Makes sure required values are present and fills in defaults if not.
	 * This function is to be run after fill() and before validate() to minimize risk of returning error to user
	 *
	 * @return void
	 */
	public function checkDefaults() 
	{
		foreach( $this->defaultAttribs as $key => $value )
		{
			if( empty($this->$key) )
			{
				$this->$key = $value;
			}
		}
	}
	
	
	/**
	 * deletes a event and its associated attendee records
	 *
	 * @return bool
	 */
	public function delete() 
	{
		$attendees = $this->attendees;
		
		foreach( $attendees as $attendee )
		{
			$attendee->delete();
		}
		
		return parent::delete();
	}
	
	
	/**
	 * decides if a user is allowed CRUD access to this resource - only happens if
	 * the user is admin, sales rep or listed as an instructor on the event
	 *
	 * @param $type		string - 'create', 'read', 'update', 'delete'
	 * @param $user		Spoolphiz\Events\Models\Eloquent\User
	 *
	 * @return bool
	 */
	public function allowAccess( $type, $user ) 
	{	
		switch( $type )
		{
			case 'create':
			case 'update':
			case 'delete':
				if( $user->isAdmin() )
				{
					return true;
				}
				else
				{
					return false;
				}
			case 'read':
				if( $user->isAdmin() || $user->isSalesRep() )
				{
					return true;
				}
				else
				{
					foreach( $this->instructors as $instructor )
					{
						if( $instructor->id == $user->id )
						{
							return true;
						}
					}
				}
			default:
				return false;
		}
		
		
		
		return false;
	}
}