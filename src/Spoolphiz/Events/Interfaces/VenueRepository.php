<?php
namespace Spoolphiz\Events\Interfaces;

interface VenueRepository {
	
	/**
	 * get a single venue by id
	 *
	 * @param $venueId  The id of the venue
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Venue
	 */
	public function find($id);
	
	
	/**
	 * get all venues
	 *
	 * @param $venueId  The id of the venue
	 *
	 * @return Collection
	 */
	public function all();


	/**
	 * get venues based on filters
	 *
	 * @param filters	array - conditions for event retrieval 
	 *
	 * @return Collection
	 */
	public function filtered($filters = array() );
	

	/**
	 * creates new Spoolphiz/Venues/Models/Eloquent/Venue
	 *
	 *
	 * @return Spoolphiz/Venues/Models/Eloquent/Venue
	 */
	public function newVenue();
	
} 