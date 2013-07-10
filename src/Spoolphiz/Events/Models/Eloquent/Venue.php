<?php
namespace Spoolphiz\Events\Models\Eloquent;
use \App;
use \DB;
use \Eloquent;
use \Validator;
use \ValidationException;
use Spoolphiz\Events\Models\Eloquent\Country;
use RedefineLab\Geocoder\GoogleGeocoder;

class Venue extends Eloquent {

	 /**
	 * A white-list of fillable attributes - not really needed for this model but included for completeness
	 *
	 * @var array
	 */
	protected $fillable = array('name', 'address1', 'city', 'state', 'zip', 'country_id', 'lat', 'long');

	 /**
	 * Validator rules
	 *
	 * @var array
	 */
	protected $validators = array('name' => array('required'), 
								'address1' => array('required'), 
								'city' => array('required'), 
								'state' => array('required'), 
								'zip' => array('required'), 
								'country_id' => array('required', 'numeric', 'exists:countries,id'),
								'lat' => array('numeric'),
								'long' => array('numeric')
								);

	 /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'venues';

	
	/*
	* Relationship
	*/
	public function country()
	{
		return $this->belongsTo('Spoolphiz\Events\Models\Eloquent\Country');
	}
	
	
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
		//don't allow deletion of the 'no venue' venue
		if( !$this->moddable() )
		{
			App::abort(403, 'This venue cannot be modified/deleted');
		}
		
		//get a list of events associated to this venue
		$relatedEventIds = $this->events()->lists('id');
		
		//update the venue id to 1 for the events
		if( !empty($relatedEventIds) )
		{
			$result = DB::table('events')->whereIn('id', $relatedEventIds)->update(array('venue_id' => 1));
		}

		return parent::delete();
	}
	
	
	
	/**
	 * finds latitude and longitude for venue's address and assigns those values
	 * Note that save() must still be run for Eloquent to push the changes to the db
	 *
	 *
	 * @return bool
	 */
	public function geocode()
	{	
		$address = $this->address1;
		
		if( !empty($this->city) ){ $address .= ', '.$this->city; }
		if( !empty($this->state) ){ $address .= ', '.$this->state; }
		if( !empty($this->zip) ){ $address .= ', '.$this->zip; }
		
		$address .= ' '.$this->country->name;
		
		$geocoder = new GoogleGeocoder;
		$geocoder->geocodeAddress($address);
		$lat = $geocoder->getLatitude();
		$long = $geocoder->getLongitude();
		
		if( !empty($lat) && !empty($long) && $lat != '#' && $long != '#' )
		{
			$this->lat = $lat;
			$this->long = $long;
			
			return true;
		}

		return false;
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