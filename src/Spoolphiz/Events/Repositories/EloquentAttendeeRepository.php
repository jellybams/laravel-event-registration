<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use \Auth;
use Spoolphiz\Events\Interfaces\AttendeeRepository;
use Spoolphiz\Events\Models\Eloquent\Attendee;

class EloquentAttendeeRepository implements AttendeeRepository {

	
	/**
	 * create a new Spoolphiz\Events\Models\Eloquent\Attendee object
	 *
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Attendee
	 */
	public function newAttendee()
	{
		$attendee = new Attendee;
		return $attendee;
	}
	
	/**
	 * finds a single attendee record
	 * 
	 * @param $eventId		int
	 * @param $attendeeId	int
	 * @param $accessType	string - 'create', 'read', 'update', 'delete'
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Attendee
	 */
	public function findWithAccess($eventId, $attendeeId, $accessType = 'read')
	{
		$currentUser = Auth::user();
		$attendee = Attendee::with('event')->where('id', '=', $attendeeId)->first();
		
		//dd($attendee->toArray());
		
		if( empty($attendee) || empty($attendee->event) )
		{
			App::abort(404, 'Resource not found.');
		}
		elseif( !$attendee->event->allowAccess($accessType, $currentUser) )
		{
			App::abort(401, 'You are not allowed to access this resource.');
		}
		elseif( $attendee->event->id != $eventId )
		{
			App::abort(404, 'The attendee you requested is not registered to the event specified.');
		}
		
		return $attendee;
	}
}
