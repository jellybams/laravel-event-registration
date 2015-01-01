<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \App;
use \DB;
use \Eloquent;
use \Validator;
use \ValidationException;

class Designation extends Eloquent {

	 /**
	 * A white-list of fillable attributes - not really needed for this model but included for completeness
	 *
	 * @var array
	 */
	protected $fillable = array('desc', 'gng_lead_time');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('desc' => array('required'), 
								'gng_lead_time' => array('required', 'numeric'), 
								);

	 /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'designation';
	
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
	 * Checks if this model is allowed to be deleted/modified. As of now only disables
	 * DELETE and PUT operations on the 'no venue' record. 
	 * 
	 * NOTE: This is not an ACL/permissions check.
	 *
	 *
	 * @return bool
	 */
	public function moddable()
	{	
		if( $this->id == 1 )
		{
			return false;
		}
		
		return true;
	}
}