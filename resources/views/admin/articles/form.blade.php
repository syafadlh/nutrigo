@extends('layouts.admin')
@section('title', isset($article) ? 'Edit Artikel' : 'Tulis Artikel')
@section('page-title', isset($article) ? 'Edit Artikel' : 'Tulis Artikel Baru')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
        @csrf
        @if(isset($article)) @method('PUT') @endif

        <div class="card space-y-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-1">Judul Artikel *</label>
                <input type="text" name="title" value="{{ old('title', $article->title ?? '') }}"
                    class="input-field" placeholder="Judul yang menarik..." required>
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ringkasan (opsional)</label>
                <textarea name="excerpt" rows="2" class="input-field resize-none"
                        placeholder="Ringkasan singkat artikel...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Kategori *</label>
                    <select name="category" class="input-field" required>
                        @foreach(['nutrisi'=>'Nutrisi','lifestyle'=>'Lifestyle','resep'=>'Resep','kesehatan'=>'Kesehatan'] as $v => $l)
                            <option value="{{ $v }}" {{ old('category', $article->category ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Waktu Baca (menit)</label>
                    <input type="number" name="read_time" value="{{ old('read_time', $article->read_time ?? 3) }}"
                        class="input-field" min="1" max="60">
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-1">Isi Artikel *</label>
                <textarea name="content" id="content" rows="15" class="input-field font-mono text-sm resize-y"
                        placeholder="Tulis artikel di sini..." required>{{ old('content', $article->content ?? '') }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1"
                        {{ old('is_published', $article->is_published ?? true) ? 'checked' : '' }}
                        class="rounded text-ng-green w-5 h-5">
                    <span class="text-sm text-gray-700">Publikasikan sekarang</span>
                </label>
            </div>

            <div class="flex gap-3 justify-end pt-2 border-t border-gray-100">
                <a href="{{ route('admin.articles.index') }}" class="btn-outline">Batal</a>
                <button class="btn-primary">
                    {{ isset($article) ? '💾 Simpan Perubahan' : '🚀 Publikasikan' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection