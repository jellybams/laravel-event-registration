<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \Eloquent;
use \Validator;
use \ValidationException;
use Spoolphiz\Events\Models\Eloquent\User;
use Spoolphiz\Events\Models\Eloquent\Attendee;

class AttendeeComment extends Eloquent {

	 /**
	 * A white-list of fillable attributes
	 *
	 * @var array
	 */
	protected $fillable = array('attendee_id', 'user_id', 'comment');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('attendee_id' => array('numeric', 'required'), 
								'user_id' => array('numeric', 'required'), 
								'comment' => array('max:500'),
								);
	
	 /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'attendee_comments';
	
	
	
	/*
	* Relationships
	*/
	public function author()
	{
		return $this->belongsTo('Spoolphiz\Events\Models\Eloquent\User', 'user_id');
	}
	
	public function attendee()
	{
		return $this->belongsTo('Attendee');
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