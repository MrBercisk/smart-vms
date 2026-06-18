<?php
namespace App\Providers;

use App\Repositories\Eloquent\EloquentDepartmentRepository;
use App\Repositories\Eloquent\EloquentEmployeeRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\VisitorRepositoryInterface;
use App\Repositories\Eloquent\EloquentVisitorRepository;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VisitorRepositoryInterface::class, EloquentVisitorRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, EloquentDepartmentRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EloquentEmployeeRepository::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}