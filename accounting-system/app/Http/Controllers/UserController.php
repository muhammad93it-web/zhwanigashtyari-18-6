<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', ['modules' => User::MODULES]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6',
            'is_admin'      => 'nullable|boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(User::MODULES)),
        ]);

        $isAdmin = $request->boolean('is_admin');

        User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'is_admin'    => $isAdmin,
            'permissions' => $isAdmin ? [] : ($data['permissions'] ?? []),
        ]);

        return redirect()->route('users.index')->with('success', 'بەکارهێنەری نوێ زیادکرا.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'modules' => User::MODULES]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin'      => 'nullable|boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(User::MODULES)),
        ]);

        $isAdmin = $request->boolean('is_admin');

        if (! $isAdmin && $user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['is_admin' => 'ناتوانیت دەسەڵاتی بەڕێوەبەر لە تاکە بەڕێوەبەر بسڕیتەوە.'])->withInput();
        }

        $user->update([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'is_admin'    => $isAdmin,
            'permissions' => $isAdmin ? [] : ($data['permissions'] ?? []),
        ]);

        return redirect()->route('users.index')->with('success', 'بەکارهێنەر نوێکرایەوە.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'ناتوانیت هەژماری خۆت بسڕیتەوە.']);
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['delete' => 'ناتوانیت تاکە بەڕێوەبەر بسڕیتەوە.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'بەکارهێنەر سڕایەوە.');
    }

    public function editPassword(User $user)
    {
        return view('admin.users.password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->route('users.index')->with('success', 'وشەی نهێنی گۆڕدرا بۆ «' . $user->name . '».');
    }
}
