<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use \Auth;
use Spoolphiz\Events\Interfaces\EventRepository;
use Spoolphiz\Events\Models\Eloquent\Event;

class EloquentEventRepository implements EventRepository {
		
	/**
	 * get a single event by id, first checks to make sure the current auth'd user has permission to view
	 *
	 * @param $eventId  The id of the event
	 * @param $user  Spoolphiz\Events\Models\Eloquent\User object
	 *
	 * @return array
	 */
	public function findWithAccess($eventId, $user) 
	{
		$data = '';
		
		//$event = Event::find($eventId);
		$events = Event::with('instructors')->where('id', '=', $eventId)->get();
		
		if( $events->isEmpty() )
		{
			App::abort(404, 'Resource not found.');
		}
		else
		{
			$event = $events->first();
			
			if( !$event->allowAccess('read', $user) )
			{
				App::abort(401, 'You are not allowed to access this event.');
			}
		}
		
		return $event;
	}
	
	
	
	
	/**
	 * returns a new Event object
	 *
	 * @param $eventId  The id of the event
	 *
	 * @return array
	 */
	public function newEvent() 
	{	
		$event = new Event;
		
		return $event;
	}
	
	
	
	/**
	 * get all events or all of an instructor's events
	 *
	 * @param user  User
	 *
	 * @return array
	 */
	public function all( $user )
	{	
		if( $user->isAdmin() || $user->isSalesRep() )
		{
			$events = Event::all();
		}
		else
		{
			//get only this instructor's events
			$events = $user->events;
		}
		
		return $events;
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
		$event = Event::find($eventId);
		
		if( empty($event) )
		{
			App::abort(404, 'Resource not found');
		}
		
		if( $event->delete() )
		{
			return true;
		}
		
		return false;
	}
	
}
