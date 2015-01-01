<?php
namespace Spoolphiz\Events\Interfaces;

interface CommentRepository {
	
	/**
	 * get a single comment by id, first checks to make sure the current auth'd user has permission
	 *
	 * @param $id		  	The id of the comment
	 * @param $user  		Spoolphiz\Events\Models\Eloquent\User object
	 * @param $accessType  	string - 'create', 'read', 'update', 'delete'
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Event
	 */
	public function findWithAccess($id, $user, $accessType);
	
	
	/**
	 * creates new Spoolphiz/Venues/Models/Eloquent/Comment
	 *
	 *
	 * @return Spoolphiz/Venues/Models/Eloquent/Comment
	 */
	public function newComment();
	
}