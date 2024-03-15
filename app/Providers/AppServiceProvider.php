<?php

namespace App\Providers;

use App\Jobs\BadmintonCrawler;
use App\Jobs\HandleCountryImg;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Facades\Octane;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerSqlDebug();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //$service = new BadmintonCrawler();
        //Octane::tick('badminton-crawler', fn () => $service->test('Ticking...'))->seconds(60*60*24)->immediate();
        //Octane::tick('handleCountryImg', fn () => HandleCountryImg::dispatch())->seconds(60*60*24)->immediate();
    }

    protected function registerSqlDebug()
    {
        if (config('logging.enable_log_sql', false)) {
            $print = false;
            if ($this->app->environment('local') && env('IS_UNIT')) {
                $print = true;
            }

            DB::listen(function ($query) use ($print) {
                $sql = $query->sql;
                foreach ($query->bindings as $binding) {
                    $value = is_numeric($binding) ? $binding : "'{$binding}'";
                    $sql   = preg_replace('/\?/', (string) $value, $sql, 1);
                }
                $sql              = sprintf('【%s】 %s', $this->format_duration($query->time / 1000), $sql);
                Log::channel('sql')->debug($sql);
                if ($print) {
                    dump($sql);
                }
            });

        }
    }

    private function format_duration($seconds): string
    {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        } elseif ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }

        return round($seconds, 2) . 's';
    }
}
