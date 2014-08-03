<?php
namespace Spoolphiz\Events\Interfaces;

interface UserRepository {
	
	/**
	 * get a single user by id
	 *
	 * @param $userId  The id of the user
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\User
	 */
	public function find($id);
	

	/**
	 * get a single user by id but does not throw exception if the user is not found
	 *
	 * @param $userId  The id of the user
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\User
	 */
	public function softFind($id);
	

	/**
	 * get all users
	 *
	 *
	 * @return Collection
	 */
	public function all();


	/**
	 * get users based on filters
	 *
	 * @param user		Spoolphiz\Events\Models\Eloquent\User instance
	 * @param filters	array - conditions for event retrieval 
	 *
	 * @return Collection
	 */
	public function filtered($filters = array());
	

	/**
	 * creates new Spoolphiz/Venues/Models/Eloquent/User
	 *
	 *
	 * @return Spoolphiz/Venues/Models/Eloquent/User
	 */
	public function newUser();
	

	/**
	 * creates new API key
	 *
	 *
	 * @return string
	 */
	public function makeApiKey();
	

	/**
	 * get default role
	 *
	 *
	 * @return string
	 */
	public function defaultRole();


}