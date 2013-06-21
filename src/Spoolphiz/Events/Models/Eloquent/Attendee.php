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
	protected $fillable = array('event_id', 'crm_contact_id', 'registration_date', 'amount_paid', 'total_amount', 'phone_number', 'seminar_only');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('event_id' => array('numberic', 'required'), 
								'crm_contact_id' => array('numeric'), 
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
	 
}