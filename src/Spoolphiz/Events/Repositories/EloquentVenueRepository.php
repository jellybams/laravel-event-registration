<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\VenueRepository;
use Spoolphiz\Events\Models\Eloquent\Venue;

class EloquentVenueRepository extends BaseRepository implements VenueRepository {

	protected $repoModel = 'Spoolphiz\Events\Models\Eloquent\Venue';
	
	/**
	 * get a single venue by id
	 *
	 * @param $venueId  The id of the venue
	 *
	 * @return array
	 */
	public function find($venueId) 
	{	
		//$venue = Venue::findOrFail($venueId);
		//$venue = Venue::find($venueId);
		$venue = Venue::with('country')->where('id', '=', $venueId)->first();
		
		if( empty($venue) )
		{
			App::abort(404, 'Resource not found');
		}
		
		return $venue;
	}
	
	
	/**
	 * get all venues
	 *
	 * @param $venueId  The id of the venue
	 *
	 * @return array
	 */
	public function all()
	{	
		$venues = Venue::all();
		
		return $venues;
	}
	
	
	/**
	 * get venues based on filters
	 *
	 * @param filters	array - conditions for event retrieval 
	 *
	 * @return array
	 */
	public function filtered( $filters = array() )
	{	
		//filters come in an array containing json strings, parse to all array
		$filters = $this->parseFilters($filters);
				
		//instantiate a collection object
		$instance = new $this->repoModel;
		$collection = $instance->newQuery();
		
		$collection = $this->buildFilteredCollection($filters, $collection, 'venues');
		
		return $collection;
	}
	
	
	/**
	 * creates new Spoolphiz/Venues/Models/Eloquent/Venue
	 *
	 *
	 * @return Spoolphiz/Venues/Models/Eloquent/Venue
	 */
	public function newVenue()
	{	
		$venue = new Venue;
		
		return $venue;
	}
	
}
