<?php

namespace App\Services;

use App\Models\Article;
use App\Services\NewsServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class NewsApiOrgService implements NewsServiceInterface
{
    public function fetchArticles(array $filters = null): array
    {
        $apiKey = config('news.newsapi_key');
        $url = 'https://newsapi.org/v2/everything';

        $defaultFilters = ['q' => 'bitcoin', 'from' => now()->subDays(1)->format('Y-m-d'), 'to' => now()->format('Y-m-d'), 'sortBy' => 'popularity'];

        $response = Http::get($url, array_merge($filters ?? $defaultFilters, ['apiKey' => $apiKey]));
        if ($response->ok()) {

            $data = json_decode($response->getBody(), true);

            return $data['articles'];
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
                'api_source' => 'newsapiorg',
                'source_id' => $article['source']['id'] ?? null,
                'source_name' => $article['source']['name'] ?? null,
                'author' => $article['author'],
                'title' => $article['title'],
                'description' => $article['description'],
                'url' => $article['url'],
                'published_at' => Carbon::parse($article['publishedAt']),
                'content' => $article['content'],
            ];
        }

        // Use bulk upsert to insert or update the records in one query
        Article::upsert(
            $articleData,
            ['url'],
            ['source_id', 'source_name', 'author', 'title', 'description', 'published_at', 'content']
        );
    }
}
