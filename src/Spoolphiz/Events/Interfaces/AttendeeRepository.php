<?php
namespace Spoolphiz\Events\Interfaces;

interface AttendeeRepository {
	
	/**
	 * create a new Attendee object
	 *
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Attendee
	 */
	public function newAttendee();
	
	
	/**
	 * finds a single attendee record
	 * 
	 * @param $eventId		int
	 * @param $attendeeId	int
	 * @param $currentUser	Spoolphiz\Events\Models\Eloquent\User
	 * @param $accessType	string - 'create', 'read', 'update', 'delete'
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Attendee
	 */
	public function findWithAccess($eventId, $attendeeId, $currentUser, $accessType);
}