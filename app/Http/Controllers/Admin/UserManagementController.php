<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller {

    public function index(Request $request) {
        $users = User::where('is_admin', false)
            ->when($request->search, fn($q) => $q->where('name','like',"%{$request->search}%")->orWhere('email','like',"%{$request->search}%"))
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user) {
        $user->load('allergies','medicalNeeds','foodHistories.food');
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user) {
        if ($user->isAdmin()) abort(403, 'Tidak bisa hapus admin.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}