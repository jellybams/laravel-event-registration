<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\VenueRepository;
use Spoolphiz\Events\Models\Eloquent\Venue;

class EloquentVenueRepository implements VenueRepository {
	
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
