<?php

namespace App\Services;

interface NewsServiceInterface
{
    /**
     * Fetch articles from the news API.
     *
     * @param array $filters
     * @return array
     */
    public function fetchArticles(array $filters): array;

    /**
     * Save the fetched articles to the database.
     *
     * @param array $articles
     * @return void
     */
    public function storeArticles(array $articles);
}
