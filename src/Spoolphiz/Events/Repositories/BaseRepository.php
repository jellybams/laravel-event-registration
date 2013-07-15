<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\EventRepository;

abstract class BaseRepository
{
		
	/**
	 * allowed operators for filtering data
	 *
	 * @return array
	 */
	public function allowedConditions()
	{
		return array( 'simple' => array('=', '!=', '<', '<=', '>', '>=', 'in', 'not in'), 
						'translate' => array('starts with', 'ends with', 'contains', 'not contains')
					);
	}
	
	
	/**
	 * extracts fields to return from a given model
	 *
	 * @param array		filters
	 *
	 * @return array
	 */
	public function returnFields($filters)
	{
		return (isset($filters['fields'])) ? $filters['fields'] : array('*') ;
	}
	
	
	/**
	 * laravel's Input::get returns key value pairs for filter params but the value
	 * is not json decoded automatically, this function performs that process
	 *
	 * @param array 	filters obtained from Input::get()
	 *
	 * @return array 	
	 */
	public function parseFilters($filters)
	{
		foreach( $filters as &$item )
		{
			if (get_magic_quotes_gpc()) 
			{
				$item = stripslashes($item);
			}
			
			$item = json_decode($item, true);
		}
		
		return $filters;
	}
	
	
	/**
	 * translates non-basic filtering conditions into strings that can be passed
	 * the the query builder using where() or orWhere()
	 *
	 * @param array 	filter field data (contains keys of name, operator, value)
	 *
	 * @return array
	 */
	public function translateCondition($filterField)
	{
		switch( strtolower($filterField['operator']) )
		{
			case 'starts with':
				$filterField['operator'] = 'LIKE';
				$filterField['value'] = $filterField['value'].'%';
				break;
			case 'ends with':
				$filterField['operator'] = 'LIKE';
				$filterField['value'] = $filterField['value'].'%';
				break;
			case 'contains':
				$filterField['operator'] = 'LIKE';
				$filterField['value'] = '%'.$filterField['value'].'%';
				break;
			case 'not contains':
				$filterField['operator'] = 'NOT LIKE';
				$filterField['value'] = '%'.$filterField['value'].'%';
				break;
			/*case 'in':
				$filterField['operator'] = 'IN';
				$filterField['value'] = '('.implode(',', $filterField['value']).')';
				break;*/
		}
		
		return $filterField;
	}
	
	
	/**
	 * applies all filters to a collection object and queries the data
	 *
	 * @param filters 		array - conditions to apply to query
	 * @param collection	Illuminate\Database\Eloquent\Builder instance
	 *
	 * @return Illuminate\Database\Eloquent\Collection instance
	 */
	public function buildFilteredCollection($filters, $collection)
	{	
		$filterFields = (isset($filters['filter']['fields'])) ? $filters['filter']['fields'] : array();
		$allowedConditions = $this->allowedConditions();
		$limit = (isset($filters['limit']) && is_numeric($filters['limit'])) ? $filters['limit'] : 0;
		$page = (isset($filters['page']) && is_numeric($filters['page'])) ? $filters['page'] : 0;

		$lastFilterFieldPos =  (count($filterFields)>0) ? count($filterFields)-1 : 0;
		
		// if filter fields exist...
		if( isset($filterFields[$lastFilterFieldPos]) )
		{
			//if the operator for the where clause is not a simple operator, transate the operator and value fields
			if( in_array($filterFields[$lastFilterFieldPos]['operator'], $allowedConditions['translate'] ) )
			{
				$filterFields[$lastFilterFieldPos] = $this->translateCondition($filterFields[$lastFilterFieldPos]);
			}
			
			$operator = strtoupper($filterFields[$lastFilterFieldPos]['operator']);
			
			//decide what type of WHERE to use with this field
			if( $operator == 'IN' || $operator == 'NOT IN' )
			{
				if( !is_array($filterFields[$lastFilterFieldPos]['value']) )
				{
					App::abort(400, 'The '.$operator.' operator should be accompanied by an array of ids as the value.');
				}
				
				$filterMethod = 'whereIn';
				
				if( $operator == 'NOT IN' )
				{	
					$filterMethod = 'whereNotIn';
				}
				
				$collection = $collection->{$filterMethod}($filterFields[$lastFilterFieldPos]['name'],
															$filterFields[$lastFilterFieldPos]['value']);
			}
			else
			{
				$filterMethod = 'where';

				if( isset($filters['filter']['type']) && strtoupper($filters['filter']['type'] == 'OR' ) )
				{	
					$filterMethod = 'orWhere';
				}

				$collection = $collection->{$filterMethod}($filterFields[$lastFilterFieldPos]['name'], 
															$filterFields[$lastFilterFieldPos]['operator'], 
															$filterFields[$lastFilterFieldPos]['value']);
			}
			
			unset($filterFields[$lastFilterFieldPos]);
			$filters['filter']['fields'] = $filterFields;

			return $this->buildFilteredCollection($filters, $collection);
		}
		//if filter fields don't exist, check if pagination filters exist
		else
		{
			
			//do we want just the total number of records with the critara so far?
			if( isset($filters['total']) && $filters['total'] == 1 )
			{
				return array( "total" => $collection->count() );
			}
			else
			{
				//at this point all filter fields were recursively applied to $collection
				//or there weren't any filter field to begin with... either way lets
				//set up pagination of records
				if( $limit !== 0 )
				{
					$collection = $collection->take($limit);
					$collection = $collection->skip($limit*$page);
				}
			}
			
		}
		
		//set up return fields
		$returnFields = $this->returnFields($filters);

		return $collection->get($returnFields);
	}
	
	
	/**
	 * Reloads requested relationships on the given Eloquent $model
	 *
	 * This function is needed because:
	 * For some off reason when saving related models, the corresponding keys
	 * on the model instance do not automatically update. 
	 *
	 * @param model 		Eloquent model
	 * @param relations		array containing relationship names
	 *
	 * @return Eloquent model
	 */
	public function reloadRelationships( $model, $relations )
	{	
		foreach($relations as $relation)
		{
			unset($model->$relation);
			$model->$relation = $model->{$relation}()->getResults()->toArray();
		}
		
		return $model;
	}
	
	
	
}
