<?php 
namespace Spoolphiz\Events;

use Illuminate\Support\ServiceProvider;

class EventsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('Spoolphiz/events');
		
		//include __DIR__.'/../../errors.php';
		//include __DIR__.'/../../routes.php';
		//include __DIR__.'/../../start.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Spoolphiz\Events\Interfaces\VenueRepository', 'Spoolphiz\Events\Repositories\EloquentVenueRepository');
		$this->app->bind('Spoolphiz\Events\Interfaces\EventRepository', 'Spoolphiz\Events\Repositories\EloquentEventRepository');
		$this->app->bind('Spoolphiz\Events\Interfaces\AttendeeRepository', 'Spoolphiz\Events\Repositories\EloquentAttendeeRepository');
		$this->app->bind('Spoolphiz\Events\Interfaces\UserRepository', 'Spoolphiz\Events\Repositories\EloquentUserRepository');
		$this->app->bind('Spoolphiz\Events\Interfaces\CategoryRepository', 'Spoolphiz\Events\Repositories\EloquentCategoryRepository');
		$this->app->bind('Spoolphiz\Events\Interfaces\CommentRepository', 'Spoolphiz\Events\Repositories\EloquentCommentRepository');
		$this->app->bind('Spoolphiz\Events\Interfaces\GeoRepository', 'Spoolphiz\Events\Repositories\EloquentGeoRepository');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}