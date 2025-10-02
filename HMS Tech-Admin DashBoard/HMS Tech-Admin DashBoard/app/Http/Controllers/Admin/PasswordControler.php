<?php

namespace App\Http\Controllers\Admin;

use App\Models\Password;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PasswordControler extends Controller
{
    // INDEX: Show all passwords
    public function index()
    {
        $passwords = Password::latest()->get();
        return view('admin.pages.passwords.index', compact('passwords'));
    }

    // CREATE: Show create form
    public function create()
    {
        return view('admin.pages.passwords.create');
    }

    // STORE: Save new password
    public function store(Request $request)
    {
        $request->validate([
            'site' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'description' => 'nullable|string',

        ]);

        $password = new Password();
        $password->data = [
            'site' => $request->site,
            'username' => $request->username,
            'password' => encrypt($request->password),
                    'description' => $request->description,

        ];
        $password->save();

        return redirect()->route('passwords.index')
            ->with('success', 'Password saved successfully!');
    }

    // SHOW: View single password
    public function show($id)
    {
        $password = Password::findOrFail($id);
        $data = $password->data;
        $data['password'] = decrypt($data['password']);

        return view('admin.pages.passwords.show', compact('data'));
    }

    // EDIT: Show edit form
    public function edit($id)
    {
        $password = Password::findOrFail($id);
        return view('admin.pages.passwords.update', compact('password'));
    }

    // UPDATE: Save changes
    public function update(Request $request, $id)
    {
        $request->validate([
            'site' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
                        'description' => 'nullable|string',

        ]);

        $password = Password::findOrFail($id);
        $password->data = [
            'site' => $request->site,
            'username' => $request->username,
            'password' => encrypt($request->password),
                                'description' => $request->description,

        ];
        $password->save();

        return redirect()->route('passwords.index')
            ->with('success', 'Password updated successfully!');
    }

    // DESTROY: Delete password
    public function destroy($id)
    {
        $password = Password::findOrFail($id);
        $password->delete();

        return redirect()->route('passwords.index')
            ->with('success', 'Password deleted successfully!');
    }
}