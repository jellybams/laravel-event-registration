<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\GeoRepository;
use Spoolphiz\Events\Models\Eloquent\Country;

class EloquentGeoRepository extends BaseRepository implements GeoRepository {
	
	protected $countryModel = 'Spoolphiz\Events\Models\Eloquent\Country';
	
	/**
	 * get a country list
	 *
	 * @param $filters  Filters to use when querying countries
	 *
	 * @return collection
	 */
	public function getCountries($filters = array()) 
	{
		if( !empty($filters) )
		{
			//filters come in an array containing json strings, parse to all array
			$filters = $this->parseFilters($filters);

			//instantiate a collection object
			$instance = new $this->countryModel;
			$collection = $instance->newQuery();

			$collection = $this->buildFilteredCollection($filters, $collection);
		}
		else
		{
			$instance = new $this->countryModel;
			$collection = $instance->all();
		}
		
		return $collection;
	}
	
	
	/**
	 * get a single country by id
	 *
	 * @param $filters  Filters to use when querying countries
	 *
	 * @return collection
	 */
	public function getCountry($id) 
	{
		$instance = new $this->countryModel;
		$country = $instance->findOrFail($id);
		
		return $country;
	}
}
