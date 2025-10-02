<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhataAccount;
use App\Models\KhataEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KhataEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, int $khataAccountId)
    {
        $account = KhataAccount::owned()->findOrFail($khataAccountId);

        $request->validate([
            'entry_date'     => 'required|date',
            'ref_type'       => ['required', Rule::in(['invoice', 'payment', 'expense', 'salary', 'investment', 'adjustment', 'opening', 'other'])],
            'ref_id'         => 'nullable|integer',
            'project_id'     => 'nullable|integer',
            'description'    => 'nullable|string|max:2000',
            'debit'          => 'numeric|min:0',
            'credit'         => 'numeric|min:0',
            'payment_method' => ['required', Rule::in(['none', 'cash', 'online'])],
            'online_reference'  => 'nullable|string|max:120',
            'online_proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:4096',
        ]);

        $debit  = (float) $request->debit;
        $credit = (float) $request->credit;

        if (!(($debit > 0 && $credit == 0) || ($credit > 0 && $debit == 0))) {
            return back()->withErrors(['amount' => 'Provide either a debit OR a credit amount.']);
        }
        if ($request->payment_method === 'online' && empty($request->online_reference)) {
            return back()->withErrors(['online_reference' => 'Online reference is required for online payments.']);
        }

        // Save uploaded proof (optional)
        $proofPath = null;
        if ($request->hasFile('online_proof_file')) {
            $stored = $request->file('online_proof_file')->store('khata/proofs', 'public'); // storage/app/public/khata/proofs
            $proofPath = 'storage/' . $stored; // web path (needs storage:link)
        }
        DB::transaction(function () use ($request, $account, $debit, $credit,$proofPath) {
            KhataEntry::create([
                'owner_id'         => auth()->id(),
                'khata_account_id' => $account->id,
                'entry_date'       => $request->date('entry_date')->toDateString(),
                'ref_type'         => $request->ref_type,
                'ref_id'           => $request->ref_id,
                'project_id'       => $request->project_id,
                'description'      => $request->description,
                'debit'            => $debit,
                'credit'           => $credit,
                'payment_method'   => $request->payment_method,
                'online_reference' => $request->online_reference,
                'online_proof_path' => $proofPath,
                'created_by'       => auth()->id(),
            ]);

            KhataEntry::recalcForAccount($account->id);
        });

        return back()->with('success', 'Entry added.');
    }

    public function update(Request $request, int $entryId)
    {
        $entry = KhataEntry::where('owner_id', auth()->id())->findOrFail($entryId);
        $accountId = $entry->khata_account_id;

        $request->validate([
            'entry_date'     => 'required|date',
            'ref_type'       => ['required', Rule::in(['invoice', 'payment', 'expense', 'salary', 'investment', 'adjustment', 'opening', 'other'])],
            'ref_id'         => 'nullable|integer',
            'project_id'     => 'nullable|integer',
            'description'    => 'nullable|string|max:2000',
            'debit'          => 'numeric|min:0',
            'credit'         => 'numeric|min:0',
            'payment_method' => ['required', Rule::in(['none', 'cash', 'online'])],
            'online_reference'  => 'nullable|string|max:120',
            'online_proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:4096'
        ]);

        $debit  = (float) $request->debit;
        $credit = (float) $request->credit;

        if (!(($debit > 0 && $credit == 0) || ($credit > 0 && $debit == 0))) {
            return back()->withErrors(['amount' => 'Provide either a debit OR a credit amount.']);
        }
        if ($request->payment_method === 'online' && empty($request->online_reference)) {
            return back()->withErrors(['online_reference' => 'Online reference is required for online payments.']);
        }

        $proofPath = $entry->online_proof_path;
        if ($request->hasFile('online_proof_file')) {
            // delete old (only if it lives on public disk)
            if ($proofPath && substr($proofPath, 0, 8) === 'storage/') {
                $prev = substr($proofPath, 8); // remove 'storage/' prefix
                Storage::disk('public')->delete($prev);
            }
            $stored = $request->file('online_proof_file')->store('khata/proofs', 'public');
            $proofPath = 'storage/' . $stored;
        }
        DB::transaction(function () use ($entry, $request, $debit, $credit, $accountId,$proofPath) {
            $entry->update([
                'entry_date'       => $request->date('entry_date')->toDateString(),
                'ref_type'         => $request->ref_type,
                'ref_id'           => $request->ref_id,
                'project_id'       => $request->project_id,
                'description'      => $request->description,
                'debit'            => $debit,
                'credit'           => $credit,
                'payment_method'   => $request->payment_method,
                'online_reference' => $request->online_reference,
                'online_proof_path' => $proofPath,
            ]);

            KhataEntry::recalcForAccount($accountId);
        });

        return back()->with('success', 'Entry updated.');
    }

    public function destroy(int $entryId)
    {
        $entry = KhataEntry::where('owner_id', auth()->id())->findOrFail($entryId);
        $accountId = $entry->khata_account_id;

        DB::transaction(function () use ($entry, $accountId) {
            $entry->delete();
            KhataEntry::recalcForAccount($accountId);
        });

        return back()->with('success', 'Entry deleted.');
    }
}
