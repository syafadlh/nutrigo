@extends('layouts.app')
@section('title','Notifikasi')
@section('page-title','Notifikasi')

@section('content')
<div class="py-4 max-w-2xl">
    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-gray-500">{{ $notifications->total() }} notifikasi</p>
        <form method="POST" action="{{ route('user.notifications.read') }}">
            @csrf
            <button class="text-sm text-ng-orange hover:underline">Tandai semua dibaca</button>
        </form>
    </div>
    <div class="space-y-3">
        @forelse($notifications as $notif)
            <div class="card flex items-start gap-4 {{ !$notif->is_read ? 'border-l-4 border-ng-orange' : '' }}">
                <div class="text-2xl mt-1">
                    {{ match($notif->type) { 'reminder'=>'⏰', 'warning'=>'⚠️', default=>'💬' } }}
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800 text-sm">{{ $notif->title }}</p>
                    <p class="text-gray-600 text-sm mt-1">{{ $notif->message }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->is_read)
                    <div class="w-2.5 h-2.5 rounded-full bg-ng-orange flex-shrink-0 mt-2"></div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-3">🔔</p>
                <p class="font-semibold">Belum ada notifikasi</p>
            </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection