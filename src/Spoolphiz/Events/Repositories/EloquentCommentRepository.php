<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\CommentRepository;
use Spoolphiz\Events\Models\Eloquent\AttendeeComment;

class EloquentCommentRepository implements CommentRepository {
	
	/**
	 * get a single comment by id, first checks to make sure the current auth'd user has permission
	 *
	 * @param $id		  	The id of the comment
	 * @param $user  		Spoolphiz\Events\Models\Eloquent\User object
	 * @param $accessType  	string - 'create', 'read', 'update', 'delete'
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Event
	 */
	public function findWithAccess($id, $user, $accessType)
	{	
		$comment = AttendeeComment::with('attendee.event')->where('id', '=', $id)->first();
		
		if(empty($comment))
		{
			App::abort(404, 'Resource not found.');
		}
		
		if( !$comment->attendee->event->allowAccess('update', $user) )
		{
			App::abort(403, 'You do not have access to this resource.');
		}
		
		return $comment;
	}

	
	/**
	 * creates new Spoolphiz/Venues/Models/Eloquent/Venue
	 *
	 *
	 * @return Spoolphiz/Venues/Models/Eloquent/Venue
	 */
	public function newComment()
	{	
		$venue = new AttendeeComment;
		
		return $venue;
	}
	
}
