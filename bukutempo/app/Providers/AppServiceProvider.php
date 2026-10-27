<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        // Pemakaian di Blade: @rupiah($angka)
        Blade::directive('rupiah', fn ($expression) => '<?php echo \App\Support\Rupiah::format('.$expression.'); ?>');
    }
}
