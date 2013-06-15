<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Models\Eloquent\Attendee;

class EloquentAttendeeRepository implements AttendeeRepository {

	
	
		
	/**
	 * get a single event by id
	 *
	 * @param $eventId  The id of the event
	 *
	 * @return array
	 */
	public function find($eventId) 
	{

	}
	
	
	/**
	 * get all events
	 *
	 *
	 * @return array
	 */
	public function all()
	{	
	}
	
	
	/**
	 * delete a single event by id
	 * also deletes all associated attendees
	 *
	 * @param $eventId  The id of the event
	 *
	 * @return bool
	 */
	public function delete($eventId)
	{
	}
	
	
	/**
	 * get IFS contact id by email address
	 *
	 * @param $email  email address of contact
	 *
	 * @return bool
	 */
	public function getIfsContact($attendeeData)
	{
		
	}
	
}
