<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AddUser;

class ClientController extends Controller
{
    public function index()
    {
        // Clients already stored (with user_id, phone, gender etc)
        $clients = Client::all();

        // Users with role = client to select from
        $users = AddUser::where('role', 'client')->get();
        // dd($users);
        return view('admin.pages.clients.create', compact('clients', 'users'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:add_users,id|unique:clients,user_id',
            'phone'   => 'required|string|max:255',
            'gender'  => 'required|in:male,female,other',
        ]);

        $user = AddUser::findOrFail($request->user_id);
        // dd($user);
        Client::create([
            'user_id' => $request->user_id,   // correct column
    'email'   => $user->email,
    'phone'   => $request->phone,
    'gender'  => $request->gender,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function edit($id)
    {
        $clients = Client::all();
        $users = User::where('role', 'client')->get();

        $editClient = Client::findOrFail($id);

        return view('admin.pages.clients.create', compact('clients', 'users', 'editClient'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:clients,user_id,' . $client->id,
            'phone'   => 'required|string|max:255',
            'gender'  => 'required|in:male,female,other',
        ]);

        $user = User::findOrFail($request->user_id);

        $client->update([
            'user_id' => $request->user_id,
            'email'   => $user->email,
            'phone'   => $request->phone,
            'gender'  => $request->gender,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
