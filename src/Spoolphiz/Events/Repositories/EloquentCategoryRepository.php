<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\CategoryRepository;
use Spoolphiz\Events\Models\Eloquent\Category;

class EloquentCategoryRepository implements CategoryRepository {
	
	/**
	 * get a single category by id
	 *
	 * @param $categoryId  The id of the category
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Category
	 */
	public function find($categoryId) 
	{	
		$category = Category::where('id', '=', $categoryId)->first();
		
		if( empty($category) )
		{
			App::abort(404, 'Resource not found');
		}
		
		return $category;
	}
	
	
	/**
	 * get all categories
	 *
	 * @param $categoryId  The id of the category
	 *
	 * @return Collection
	 */
	public function all()
	{	
		$categorys = Category::all();
		
		return $categorys;
	}
	
	/**
	 * creates new Spoolphiz/Events/Models/Eloquent/Category
	 *
	 *
	 * @return Spoolphiz/Events/Models/Eloquent/Category
	 */
	public function newCategory()
	{	
		$category = new Category;
		
		return $category;
	}
	
}
