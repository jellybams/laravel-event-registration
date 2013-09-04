<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\UserRepository;
use Spoolphiz\Events\Models\Eloquent\User;

class EloquentUserRepository extends BaseRepository implements UserRepository {

	protected $repoModel = 'Spoolphiz\Events\Models\Eloquent\User';
	
	/**
	 * get a single user by id
	 *
	 * @param $userId  The id of the user
	 *
	 * @return array
	 */
	public function find($userId) 
	{
		$user = User::where('id', '=', $userId)->first();
		
		if( empty($user) )
		{
			App::abort(404, 'Resource not found');
		}
		
		return $user;
	}
	
	
	/**
	 * get a single user by id but does not throw exception if the user is not found
	 *
	 * @param $userId  The id of the user
	 *
	 * @return array
	 */
	public function softFind($userId) 
	{
		$user = User::where('id', '=', $userId)->first();
		
		return $user;
	}
	
	
	/**
	 * get all users
	 *
	 *
	 * @return array
	 */
	public function all()
	{	
		$users = User::all();
		
		return $users;
	}

	/**
	 * get users based on filters
	 *
	 * @param user		Spoolphiz\Events\Models\Eloquent\User instance
	 * @param filters	array - conditions for event retrieval 
	 *
	 * @return array
	 */
	public function filtered($filters = array() )
	{	
		//filters come in an array containing json strings, parse to all array
		$filters = $this->parseFilters($filters);
				
		$instance = new $this->repoModel;
		$collection = $instance->newQuery();
		
		$collection = $this->buildFilteredCollection($filters, $collection, 'users');
		
		return $collection;
	}
	
	/**
	 * creates new Spoolphiz/Venues/Models/Eloquent/User
	 *
	 *
	 * @return Spoolphiz/Venues/Models/Eloquent/User
	 */
	public function newUser()
	{	
		$user = new User;
		
		return $user;
	}
	
	
	
	public function makeApiKey()
	{
		$key = User::makeApiKey();
		
		return $key;
	}
	
	
	public function defaultRole()
	{
		return User::$userRoleIds['INSTRUCTOR'];
	}
}
