@extends('layouts.admin')
@section('title','Artikel')
@section('page-title','Kelola Artikel')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500 text-sm">{{ $articles->total() }} artikel terdaftar</p>
    <a href="{{ route('admin.articles.create') }}" class="btn-primary">+ Tulis Artikel</a>
</div>

<div class="grid gap-4">
    @forelse($articles as $article)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="{{ match($article->category) {
                        'nutrisi'   => 'badge-green',
                        'lifestyle' => 'badge-orange',
                        'resep'     => 'bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full font-medium',
                        default     => 'bg-blue-100 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium',
                    } }}">{{ ucfirst($article->category) }}</span>
                    @if(!$article->is_published)
                        <span class="badge-red">Draft</span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-800 text-base">{{ $article->title }}</h3>
                @if($article->excerpt)
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $article->excerpt }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">
                    ✍️ {{ $article->author?->name ?? '—' }}
                    · {{ $article->created_at->isoFormat('D MMM Y') }}
                    · {{ $article->read_time }} menit baca
                </p>
            </div>
            <div class="flex gap-3 flex-shrink-0">
                <a href="{{ route('admin.articles.edit', $article) }}" class="text-blue-500 hover:underline text-sm font-medium">Edit</a>
                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                    onsubmit="return confirm('Hapus artikel ini?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:underline text-sm font-medium">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-12 text-gray-400 bg-white rounded-2xl border border-gray-100">
            <p class="text-4xl mb-3">📰</p>
            <p class="font-semibold">Belum ada artikel</p>
            <a href="{{ route('admin.articles.create') }}" class="btn-primary inline-block mt-4 text-sm">Tulis Artikel Pertama</a>
        </div>
    @endforelse
</div>
<div class="mt-4">{{ $articles->links() }}</div>
@endsection