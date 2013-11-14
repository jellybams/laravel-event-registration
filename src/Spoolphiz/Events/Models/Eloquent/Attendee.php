<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \Eloquent;
use \Validator;
use \ValidationException;

class Attendee extends Eloquent {

	 /**
	 * A white-list of fillable attributes
	 *
	 * @var array
	 */
	protected $fillable = array('event_id', 'crm_contact_id', 'name', 'email', 'registration_date', 'amount_paid', 'total_amount', 'phone_number', 'seminar_only');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('event_id' => array('numeric', 'required'), 
								'crm_contact_id' => array('numeric'), 
								'name' => array('required', 'max:100'),
								'email' => array('required', 'email', 'max:320'),
								'registration_date' => array('date'), 
								'amount_paid' => array('required', 'numeric'), 
								'total_amount' => array('required','numeric'), 
								'phone_number' => array('max:30'),
								'seminar_only' => array('in:0,1'), 
								);
	
	 /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'attendees';
	
	
	
	/*
	* Relationships
	*/
	public function event()
	{
		return $this->belongsTo('Spoolphiz\Events\Models\Eloquent\Event');
	}
	
	public function comments()
	{
		return $this->hasMany('Spoolphiz\Events\Models\Eloquent\AttendeeComment');
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
	 * deletes an attendee and its associated comment records, updates event capacity
	 *
	 * @return bool
	 */
	public function delete() 
	{
		//delete associated comments
		$comments = $this->comments;
		
		foreach( $comments as $item )
		{
			$item->delete();
		}
		
		//update event capacity
		$event = $this->event;
		$event->capacity = $event->capacity + 1;
		
		if( !$event->save() )
		{
			App::abort('422', 'The attendee was deleted but the event capacity failed to update.');
		}
		
		//delete the attendee record
		return parent::delete();
	}
	
	
	/**
     * Override default toArray function
     */
	public function toArray()
	{
		$this->comment_count  = $this->comments()->count();

		return parent::toArray();
	}

	/**
	 * Round the amount if it has more than
	 * 2 decimals
	 */
	public function setAmountPaidAttribute($value)
    {
        $this->attributes['amount_paid'] = is_int($value) ? $value : round($value, 2);
    }

    /**
	 * Round the amount if it has more than
	 * 2 decimals
	 */
    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_amount'] = is_int($value) ? $value : round($value, 2);
    }
	
}