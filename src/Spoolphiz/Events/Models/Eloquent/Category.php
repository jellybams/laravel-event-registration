<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \App;
use \DB;
use \Eloquent;
use \Validator;
use \ValidationException;

class Category extends Eloquent {

	public $timestamps = false;

	 /**
	 * A white-list of fillable attributes - not really needed for this model but included for completeness
	 *
	 * @var array
	 */
	protected $fillable = array('label', 'link');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('label' => array('required', 'max:40'), 'link' => array('required', 'url'));

	 /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'categories';

	
	/*
	* Relationships
	*/	
	public function events()
	{
		return $this->hasMany('Spoolphiz\Events\Models\Eloquent\Event');
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
	 * Delete the given model. When a venue is deleted, all events that were associated to
	 * it will be instead transferred to the 'no venue' record (venue_id = 1)
	 *
	 * @return bool
	 */
	public function delete() 
	{
		//don't allow deletion of the 'bootcamp' category
		if( !$this->allowModify() )
		{
			App::abort(403, 'This category cannot be modified/deleted.');
		}
		
		//get a list of events associated to this category
		$relatedEventIds = $this->events()->lists('id');
		
		//update the venue id to 1 for the events
		if( !empty($relatedEventIds) )
		{
			$result = DB::table('events')->whereIn('id', $relatedEventIds)->update(array('category' => 1));
		}
		
		return parent::delete();
	}
	
	
	
	/**
	 * Checks if this model is allowed to be deleted/modified. As of now only disables
	 * DELETE and PUT operations on the 'Bootcamp' record. 
	 * 
	 * NOTE: This is not an ACL/permissions check.
	 *
	 *
	 * @return bool
	 */
	public function allowModify()
	{	
		if( $this->id == 1 )
		{
			return false;
		}
		
		return true;
	}
}