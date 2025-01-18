<?php

namespace App\Services;

use App\Models\Article;
use App\Services\NewsServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class NytService implements NewsServiceInterface
{
    public function fetchArticles(array $filters = null): array
    {
        $apiKey = config('news.nyt_key');
        $url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';

        $defaultFilters = ['q' => 'bitcoin', 'begin_date' => now()->subDays(1)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d'), 'sort' => 'relevance'];

        $response = Http::get($url, array_merge($filters ?? $defaultFilters, ['api-key' => $apiKey]));

        if ($response->ok()) {
            $data = json_decode($response->getBody(), true);

            return $data['response']['docs'] ?? [];
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
                'api_source' => 'nyt',
                'source_id' => $article['_id'],
                'source_name' => $article['source'] ?? 'The New York Times',
                'author' => $article['byline']['original'] ?? null,
                'title' => $article['headline']['main'] ?? null,
                'description' => $article['abstract'] ?? null,
                'type' => $article['type_of_material'] ?? null,
                'url' => $article['web_url'],
                'published_at' => Carbon::parse($article['pub_date']),
                'content' => $article['lead_paragraph'] ?? null,
            ];
        }

        // Bulk upsert to save the articles
        Article::upsert(
            $articleData,
            ['source_id'],
            ['source_name', 'author', 'title', 'description', 'published_at', 'content']
        );
    }
}
