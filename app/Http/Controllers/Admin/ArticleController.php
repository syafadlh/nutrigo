<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller {

    public function index() {
        $articles = Article::with('author')->latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create() { return view('admin.articles.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category'     => 'required|in:nutrisi,lifestyle,resep,kesehatan',
            'read_time'    => 'nullable|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $data['author_id']    = Auth::id();
        $data['is_published'] = $request->boolean('is_published');
        $data['read_time']    = $request->read_time ?? 3;

        Article::create($data);
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil disimpan!');
    }

    public function edit(Article $article) { return view('admin.articles.edit', compact('article')); }

    public function update(Request $request, Article $article) {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category'     => 'required|in:nutrisi,lifestyle,resep,kesehatan',
            'read_time'    => 'nullable|integer|min:1',
            'is_published' => 'boolean',
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $article->update($data);
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy(Article $article) {
        $article->delete();
        return back()->with('success', 'Artikel dihapus.');
    }
}