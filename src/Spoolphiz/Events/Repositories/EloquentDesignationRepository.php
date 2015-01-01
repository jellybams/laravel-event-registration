<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\DesignationRepository;
use Spoolphiz\Events\Models\Eloquent\Designation;

class EloquentDesignationRepository extends BaseRepository implements DesignationRepository {

	protected $repoModel = 'Spoolphiz\Events\Models\Eloquent\Designation';
	
	/**
	 * get a single designation by id
	 *
	 * @param $desId  The id of the designation
	 *
	 * @return array
	 */
	public function find($desId) 
	{	
		//$designation = Designation::findOrFail($desId);
		$designation = Designation::find($desId);
		
		if( empty($designation) )
		{
			App::abort(404, 'Resource not found');
		}
		
		return $designation;
	}
	
	
	/**
	 * get all designations
	 *
	 * @return array
	 */
	public function all()
	{	
		$designations = Designation::all();
		
		return $designations;
	}
	
	
	/**
	 * get designations based on filters
	 *
	 * @param filters	array - conditions for event retrieval 
	 *
	 * @return array
	 */
	public function filtered( $filters = array() )
	{	
		//instantiate a collection object
		$instance = new $this->repoModel;
		$collection = $instance->newQuery();
		
		$collection = $this->buildFilteredCollection($filters, $collection, 'designation');
		
		return $collection;
	}
}
