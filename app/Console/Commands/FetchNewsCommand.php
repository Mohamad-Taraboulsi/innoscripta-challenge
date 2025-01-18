<?php

namespace App\Console\Commands;

use App\Services\GuardianService;
use App\Services\NewsApiOrgService;
use App\Services\NytService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from all configured services';

    /**
     * Array of news service implementations.
     *
     * @var array
     */
    protected $newsServices;

    /**
     * Create a new command instance.
     *
     * @param  array  $newsServices
     */
    public function __construct() //array $newsServices
    {
        parent::__construct();

        $this->newsServices = $this->newsServices = [
            new NewsApiOrgService(),
            new NytService(),
            new GuardianService(),
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->newsServices as $service) {
            try {

                $articles = $service->fetchArticles(null);// $filters 

                if($articles){
                    $service->storeArticles($articles);
                }

            } catch (\Exception $e) {
                Log::error('Error fetching articles from ' . get_class($service) . ': ' . $e->getMessage());
                $this->error('Failed to fetch news articles from ' . get_class($service));
            }
        }

        return 0;
    }
}
