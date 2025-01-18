<?php

namespace App\Services;

use App\Models\Article;
use App\Services\NewsServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class GuardianService implements NewsServiceInterface
{
    public function fetchArticles(array $filters = null): array
    {
        $apiKey = config('news.guardian_key');
        $url = 'https://content.guardianapis.com/search';

        $defaultFilters = ['q' => 'sport', 'from-date' => now()->subDays(1)->format('Y-m-d'), 'to-date' => now()->format('Y-m-d'), 'sortBy' => 'popularity'];

        $response = Http::get($url, array_merge($filters ?? $defaultFilters, ['api-key' => $apiKey]));

        if ($response->ok()) {
            $data = json_decode($response->getBody(), true);

            return $data['response']['results'] ?? [];
        }


        throw new \Exception('Failed to fetch articles from NewsAPI: ' . $response->body());
        return [];
    }

    /**
     * Save the fetched articles to the database.
     *
     * @param array $articles
     * @return void
     */
    public function storeArticles(array $articles)
    {
        $articleData = [];

        foreach ($articles as $article) {
            $articleData[] = [
                'api_source' => 'guardian',
                'source_id' => null, // Not provided in this API
                'source_name' => 'The Guardian',
                'author' => null, // Author field not available in this API
                'title' => $article['webTitle'],
                'type' => $article['type'],
                'description' => null, // No description field in this API
                'url' => $article['webUrl'],
                'published_at' => Carbon::parse($article['webPublicationDate']),
                'content' => null, // No content field in this API
            ];
        }

        // Bulk upsert to save the articles
        Article::upsert(
            $articleData,
            ['url'],
            ['source_name', 'title', 'published_at']
        );
    }
}
