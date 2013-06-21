<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \Eloquent;
use \Validator;

class Attendee extends Eloquent {

	 /**
	 * A white-list of fillable attributes - not really needed for this model but included for completeness
	 *
	 * @var array
	 */
	//protected $fillable = array('name', 'address1', 'city', 'state', 'zip', 'country_id', 'lat', 'long');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	/*protected $validators = array('name' => array('required'), 
								'address1' => array('required'), 
								'city' => array('required'), 
								'state' => array('required'), 
								'zip' => array('required'), 
								'country_id' => array('required'), 
								);
	*/
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
		//TODO: do the damn thang
	}
	 
}