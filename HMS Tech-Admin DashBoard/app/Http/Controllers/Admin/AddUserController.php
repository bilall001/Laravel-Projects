<?php

namespace App\Http\Controllers\Admin;

use App\Models\AddUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AddUserController extends Controller
{
    public function index()
    {
        $users = AddUser::latest()->get();
        return view('admin.pages.users.add_user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:add_users,username',
            'email' => 'required|email|unique:add_users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,developer,client,team manager,business developer,partner',
        ]);

        AddUser::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('add-users.index')->with('success', 'User added!');
    }

    public function update(Request $request, AddUser $addUser)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:add_users,username,' . $addUser->id,
            'email' => 'required|email|unique:add_users,email,' . $addUser->id,
            'role' => 'required|in:admin,developer,client,team manager,business developer,partner',
        ]);

        $addUser->name = $request->name;
        $addUser->username = $request->username;
        $addUser->email = $request->email;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:6|confirmed',
            ]);
            $addUser->password = bcrypt($request->password);
        }

        $addUser->role = $request->role;
        $addUser->save();

        return redirect()->route('add-users.index')->with('success', 'User updated!');
    }

    public function show(AddUser $addUser)
    {
        return view('admin.pages.users.view_user', ['user' => $addUser]);
    }

    public function destroy(AddUser $addUser)
    {
        $addUser->delete();
        return redirect()->route('add-users.index')->with('success', 'User deleted!');
    }
}
