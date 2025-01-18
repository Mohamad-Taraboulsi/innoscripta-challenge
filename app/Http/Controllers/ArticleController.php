<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{

    public function search(Request $request)
    {
        $query = Article::query();

        // Apply filters
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('content', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        if ($request->has('category')) {
            $query->where('source_name', $request->category)
            ->orWhere('type', $request->category);
        }

        if ($request->has('author')) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }

        if ($request->has('source')) {
            $query->where('api_source', $request->source);
        }

        // Pagination
        $articles = $query->paginate(10);

        return response()->json($articles);
    }
}
