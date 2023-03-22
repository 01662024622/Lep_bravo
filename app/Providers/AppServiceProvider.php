<?php

namespace App\Providers;

use App\Models\HT00\Category;
use App\Models\HT20\Apartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $apartment_user = Apartment::select('id')->where('status', 0)->where('user_id', Auth::id())->get()->pluck('id')->toArray();
                $view->with('apartment_user', $apartment_user);
                $user = Auth::user();
                $categories = Category::where('status', 0)->where('type', '>=', 5)->orderBy('sort')
                ->get(['id', 'title', 'slug', 'type', 'sort', 'icon', 'header']);
                $nav = '';
                foreach ($categories as $category) {
                    $sub = Category::where('status', 0)->where('parent_id', $category->id)->orderBy('sort')
                    ->get(['id', 'title', 'parent_id', 'slug', 'url', 'sort', 'icon', 'header']);
                    if (count($sub) > 0) {
                        $nav = $nav . '<hr class="sidebar-divider">
                        <li class="nav-item" id="' . $category->slug . '">
                        <a class="nav-link" aria-expanded="true" href="#" data-toggle="collapse" data-target="#collapsePages' . $category->slug . '"
                        aria-controls="collapsePages' . $category->slug . '">
                        <i class="' . $category->icon . '"> </i> <span> ' . $category->title . '
                        </a>
                        <div id="collapsePages' . $category->slug . '" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">';
                        foreach ($sub as $element) {
                            if (!$element->header == "") {
                                $nav = $nav . '<h6 class="collapse-header">' . $element->header . '</h6>';
                            }
                            $nav = $nav . '<a id="' . $element->slug . '" class="collapse-item"  href="' . $element->url . '">
                            <i class="' . $element->icon . '"> </i> <span> ' . $element->title . '</span>
                            </a>';
                        }
                        $nav = $nav . '</div>
                        </div>
                        </li>';
                        
                    }
                }
                $view->with('nav', $nav);
            }
        });
    }
}
