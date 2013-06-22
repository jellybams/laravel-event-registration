<?php
namespace Spoolphiz\Events\Models\Eloquent;
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
}