<?php
namespace Spoolphiz\Events\Interfaces;

interface EventRepository {
	
	/**
	 * get a single event by id, first checks to make sure the current auth'd user has permission to view
	 *
	 * @param $eventId  	The id of the event
	 * @param $user  		Spoolphiz\Events\Models\Eloquent\User object
	 * @param $accessType  	string - 'create', 'read', 'update', 'delete'
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Event
	 */
	public function findWithAccess($id, $user, $accessType);
	
	
	/**
	 * Gets all events for a given user
	 *
	 * @param $user  		Spoolphiz\Events\Models\Eloquent\User object
	 *
	 * @return Collection
	 */
	public function all($user);
	
	
	
	/**
	 * Gets events based on supplied filters
	 *
	 * @param $user  		Spoolphiz\Events\Models\Eloquent\User object
	 * @param $filters  	array - contains filtering requirements for event fetching
	 *
	 * @return Collection
	 */
	public function filtered($user, $filters);
	
	
	/**
	 * Create new Event object
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Event
	 */
	public function newEvent();
	
	
	/**
	 * Adds a "instructor_id" attribute to each event in given collection
	 *
	 * @param collection	Illuminate\Database\Eloquent\Collection instance
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function addInsturctorIdsArray( $collection );
	
}