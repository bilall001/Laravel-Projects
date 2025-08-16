<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AddUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * Admin: Show all partners
     */
    public function index()
    {
        $partners = Partner::with('investments', 'user')->get();
        $partnerUsers = AddUser::where('role', 'partner')->get();
        // dd($partnerUsers);
        return view('admin.pages.partner', compact('partners', 'partnerUsers'));
    }

    /**
     * Admin: Store a new partner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:add_users,id',
            'image' => 'nullable|image|max:2048',
            'investments.*.contribution' => 'required|numeric',
            'investments.*.contribution_date' => 'required|date',
            'investments.*.payment_method' => 'required|string',
            'investments.*.payment_receipt' => 'nullable|file|max:2048',
        ]);

        $partnerData = [
            'user_id' => $request->user_id,
        ];

        if ($request->hasFile('image')) {
            $partnerData['image'] = $request->file('image')->store('partners', 'public');
        }

        $partner = Partner::create($partnerData);

        // Save investments
        foreach ($request->investments as $inv) {
            $investment = [
                'contribution' => $inv['contribution'],
                'contribution_date' => $inv['contribution_date'],
                'payment_method' => $inv['payment_method'],
                'is_received' => isset($inv['is_received']) ? 1 : 0,
            ];

            if (isset($inv['payment_receipt']) && $inv['payment_receipt'] instanceof \Illuminate\Http\UploadedFile) {
                $investment['payment_receipt'] = $inv['payment_receipt']->store('receipts', 'public');
            }

            $partner->investments()->create($investment);
        }

        return redirect()->route('admin.partners.index')->with('success', 'Partner added successfully.');
    }

    /**
     * Admin: Update an existing partner
     */
    public function update(Request $request, Partner $partner)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:add_users,id',
        'image' => 'nullable|image|max:2048',
        'investments.*.contribution' => 'required|numeric',
        'investments.*.contribution_date' => 'required|date',
        'investments.*.payment_method' => 'required|string',
        'investments.*.payment_receipt' => 'nullable|file|max:2048',
    ]);

    $partnerData = [
        'user_id' => $request->user_id,
    ];

    if ($request->hasFile('image')) {
        $partnerData['image'] = $request->file('image')->store('partners', 'public');
    }

    $partner->update($partnerData);

    // 🔹 Update investments
    $partner->investments()->delete(); // simple approach: delete old & insert new
    foreach ($request->investments as $inv) {
        $investment = [
            'contribution' => $inv['contribution'],
            'contribution_date' => $inv['contribution_date'],
            'payment_method' => $inv['payment_method'],
            'is_received' => isset($inv['is_received']) ? 1 : 0,
        ];

        if (isset($inv['payment_receipt']) && $inv['payment_receipt'] instanceof \Illuminate\Http\UploadedFile) {
            $investment['payment_receipt'] = $inv['payment_receipt']->store('receipts', 'public');
        }

        $partner->investments()->create($investment);
    }

    return redirect()->route('admin.partners.index')->with('success', 'Partner updated successfully.');
}

    /**
     * Admin: Delete a partner
     */
    public function destroy(Partner $partner)
    {
        if ($partner->image && Storage::disk('public')->exists($partner->image)) {
            Storage::disk('public')->delete($partner->image);
        }

        foreach ($partner->investments as $investment) {
            if ($investment->payment_receipt && Storage::disk('public')->exists($investment->payment_receipt)) {
                Storage::disk('public')->delete($investment->payment_receipt);
            }
        }

        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partner deleted successfully.');
    }

    /**
     * Partner: Show login form
     */
    public function showLoginForm()
    {
        return view('partner.login'); // Ensure this view exists
    }

    /**
     * Partner: Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('partner')->attempt($credentials)) {
            return redirect()->intended(route('partner.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Partner: Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('partner.login');
    }

    /**
     * Partner: Dashboard after login
     */
    public function dashboard()
    {
        $partner = Auth::guard('partner')->user();
        return view('partner.dashboard', compact('partner'));
    }
}
