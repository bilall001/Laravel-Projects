<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KhataEntry extends Model
{
    protected $fillable = [
        'owner_id','khata_account_id','entry_date',
        'ref_type','ref_id','project_id','description',
        'debit','credit','running_balance',
        'payment_method','online_reference','online_proof_path',
        'created_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'running_balance' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(KhataAccount::class, 'khata_account_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(AddUser::class, 'created_by');
    }

    public static function recalcForAccount(int $khataAccountId): void
    {
        $account = KhataAccount::owned()->findOrFail($khataAccountId);

        DB::transaction(function () use ($account) {
            $balance = 0.0;

            $entries = KhataEntry::where('khata_account_id', $account->id)
                ->orderBy('entry_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($entries as $e) {
                $balance += ((float)$e->debit - (float)$e->credit);
                if ((float)$e->running_balance !== $balance) {
                    $e->running_balance = $balance;
                    $e->saveQuietly();
                }
            }
        });
    }
}
