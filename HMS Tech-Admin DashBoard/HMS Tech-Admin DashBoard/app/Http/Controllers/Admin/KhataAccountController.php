<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhataAccount;
use App\Models\KhataEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class KhataAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // add role check if needed
    }

    public function index(Request $request)
    {
        $accounts = KhataAccount::owned()
            ->orderBy('status')->orderBy('name')
            ->get();

        // Build party options arrays (id,label) for dynamic selects in the modal
        $partyOptions = [
            'clients'             => $this->simpleList(\App\Models\Client::class),
            'partners'            => $this->simpleList(\App\Models\Partner::class),
            'developers'          => $this->simpleList(\App\Models\Developer::class),
            'team_managers'       => $this->simpleList(\App\Models\TeamManager::class),
            'business_developers' => $this->simpleList(\App\Models\BusinessDeveloper::class),
        ];

        return view('admin.pages.khata.index', [
            'accounts'     => $accounts,
            'partyOptions' => $partyOptions ?? [
                'clients' => [],
                'partners' => [],
                'developers' => [],
                'team_managers' => [],
                'business_developers' => [],
            ],
        ]);
    }

    private function simpleList(string $class): array
    {
        $model = new $class;
        $table = $model->getTable(); // e.g. 'clients', 'partners', ...

        // Detect a FK to the user table
        $fk = null;
        foreach (['user_id', 'add_user_id'] as $col) {
            if (Schema::hasColumn($table, $col)) { $fk = $col; break; }
        }

        // Which user table do we have?
        $userTable = Schema::hasTable('add_users') ? 'add_users' : (Schema::hasTable('users') ? 'users' : null);

        $q = $class::query()->from($table.' as t')->select('t.id');

        if ($fk && $userTable) {
            $q->leftJoin($userTable.' as u', 'u.id', '=', 't.'.$fk);

            // Build a smart label from whatever the user table actually has
            $pieces = [];
            if (Schema::hasColumn($userTable, 'name'))        $pieces[] = 'u.name';
            if (Schema::hasColumn($userTable, 'full_name'))   $pieces[] = 'u.full_name';
            if (Schema::hasColumn($userTable, 'first_name') && Schema::hasColumn($userTable, 'last_name')) {
                $pieces[] = "CONCAT(u.first_name, ' ', u.last_name)";
            }
            if (Schema::hasColumn($userTable, 'company_name')) $pieces[] = 'u.company_name';
            if (Schema::hasColumn($userTable, 'email'))        $pieces[] = 'u.email';
            if (Schema::hasColumn($userTable, 'phone'))        $pieces[] = 'u.phone';

            $labelSql = $pieces
                ? ('COALESCE('.implode(',', $pieces).', CAST(t.id AS CHAR))')
                : 'CAST(t.id AS CHAR)';

            $q->selectRaw("$labelSql AS label");
        } else {
            // Fallback: try local columns or just ID
            $candidates = ['name','client_name','full_name','title'];
            $label = null;
            foreach ($candidates as $c) if (Schema::hasColumn($table, $c)) { $label = "t.$c"; break; }
            if (!$label && Schema::hasColumn($table, 'first_name') && Schema::hasColumn($table, 'last_name')) {
                $label = "CONCAT(t.first_name,' ',t.last_name)";
            }
            $q->selectRaw(($label ? "COALESCE($label, CAST(t.id AS CHAR))" : 'CAST(t.id AS CHAR)').' AS label');
        }

        return $q->orderBy('label')
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'label' => $r->label])
            ->toArray();
    }

    public function store(Request $request)
    {
        $isManual = in_array($request->input('party_type'), ['manual','other'], true);

        // If not manual, nuke manual fields so they don't interfere with validation
        if (!$isManual) {
            $request->merge([
                'name' => null, 'phone' => null, 'email' => null, 'cnic' => null, 'address' => null,
            ]);
        }

        $rules = [
            'party_type'      => 'required', Rule::in(['clients','partners','developers','team_managers','business_developers','manual','other']),
            // required unless manual/other:
            'party_id'        => 'nullable|integer|required_unless:party_type,manual,other',
            'opening_balance' => 'nullable|numeric',
            'currency'        => 'nullable|string|size:3',
            'status'          => 'nullable|in:active,archived',
            'notes'           => 'nullable|string',
        ];

        if ($isManual) {
            $rules += [
                'name'    => 'required|string|max:255',
                'phone'   => 'nullable|string|max:30',
                'email'   => 'nullable|email|max:120',
                'cnic'    => 'nullable|string|max:30',
                'address' => 'nullable|string|max:255',
            ];
        }

        // Only enforce uniqueness when it’s a linked party (not manual/other)
    if (!in_array($request->party_type, ['manual','other'])) {
        $rules['party_id'] = [
            'required','integer',
            Rule::unique('khata_accounts')
                ->where(fn($q) => $q->where('owner_id', auth()->id())
                                    ->where('party_type', $request->party_type))
        ];
    }

    $messages = [
        'party_id.unique' => 'This party already has a Khata account,Open the existing Khata or Delete the old Khata.',
    ];

        $validated = $request->validate($rules);

        $account = KhataAccount::create([
            'owner_id'        => auth()->id(),
            'party_type'      => $validated['party_type'],
            'party_id'        => $isManual ? null : (int)$validated['party_id'],
            'name'            => $validated['name'] ?? '',
            'phone'           => $validated['phone'] ?? null,
            'email'           => $validated['email'] ?? null,
            'cnic'            => $validated['cnic'] ?? null,
            'address'         => $validated['address'] ?? null,
            'opening_balance' => (float)($validated['opening_balance'] ?? 0),
            'currency'        => $validated['currency'] ?? 'PKR',
            'status'          => $validated['status'] ?? 'active',
            'notes'           => $validated['notes'] ?? null,
        ]);

        // Optional explicit opening entry
        if (!empty($validated['opening_balance']) && (float)$validated['opening_balance'] != 0.0) {
            DB::transaction(function () use ($account) {
                KhataEntry::create([
                    'owner_id'         => auth()->id(),
                    'khata_account_id' => $account->id,
                    'entry_date'       => now()->toDateString(),
                    'ref_type'         => 'opening',
                    'description'      => 'Opening balance',
                    'debit'            => max(0, (float)$account->opening_balance),
                    'credit'           => max(0, (float)-$account->opening_balance),
                    'payment_method'   => 'none',
                    'created_by'       => auth()->id(),
                ]);
                KhataEntry::recalcForAccount($account->id);
            });
        }

        return redirect()->route('khata.accounts.index')->with('success', 'Khata account created.');
    }

   public function showModal(Request $request, int $id)
{
    $account = KhataAccount::owned()->with('party')->findOrFail($id);

    $entries = KhataEntry::where('khata_account_id', $account->id)
        ->where('owner_id', auth()->id())
        ->orderBy('entry_date', 'desc')
        ->orderBy('id', 'desc')
        ->limit(100)               // show latest 100 lines inside modal
        ->get();

    // returns just the modal body (a partial)
    return view('admin.pages.khata._show_modal', compact('account','entries'));
}

    public function update(Request $request, int $id)
    {
        $account = KhataAccount::owned()->findOrFail($id);

        $isManual = in_array($account->party_type, ['manual','other'], true);

        // Only allow editing manual contact fields if the account is manual/other.
        $rules = [
            'status' => 'nullable|in:active,archived',
            'notes'  => 'nullable|string',
        ];

        if ($isManual) {
            $rules += [
                'name'    => 'required|string|max:255',
                'phone'   => 'nullable|string|max:30',
                'email'   => 'nullable|email|max:120',
                'cnic'    => 'nullable|string|max:30',
                'address' => 'nullable|string|max:255',
            ];
        } else {
            // ignore manual fields entirely if not manual
            $request->merge([
                'name' => null, 'phone' => null, 'email' => null, 'cnic' => null, 'address' => null,
            ]);
        }

        $validated = $request->validate($rules);

        // Build list of fields we actually want to update
        $fields = ['status','notes'];
        if ($isManual) {
            $fields = array_merge($fields, ['name','phone','email','cnic','address']);
        }

        $account->fill($request->only($fields))->save();

        return redirect()->route('khata.accounts.index')->with('success', 'Khata account updated.');
    }

    public function destroy(int $id)
    {
        $account = KhataAccount::owned()->findOrFail($id);
        $account->delete();

        return redirect()->route('khata.accounts.index')->with('success', 'Khata account deleted.');
    }
}
