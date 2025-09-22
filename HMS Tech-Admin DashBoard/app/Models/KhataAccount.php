<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KhataAccount extends Model
{
    protected $fillable = [
        'owner_id','name','party_type','party_id','phone','email','cnic','address',
        'opening_balance','currency','status','notes'
    ];

    /* Relationships */

    // Polymorphic link to party (by table family), driven by morphMap (see AppServiceProvider)
    public function party()
    {
        return $this->morphTo(__FUNCTION__, type: 'party_type', id: 'party_id');
    }

    public function entries()
    {
        return $this->hasMany(KhataEntry::class);
    }

    /* Scopes */

    public function scopeOwned(Builder $query): Builder
    {
        return $query->where('owner_id', auth()->id());
    }

    /* Accessors */

    public function getCurrentBalanceAttribute(): float
    {
        static $cached = [];
        if (isset($cached[$this->id])) return $cached[$this->id];

        $sum = $this->entries()->selectRaw('COALESCE(SUM(debit - credit),0) as net')->value('net') ?? 0;
         return $cached[$this->id] = (float) $sum;
    }

  protected function pickLabel($m): ?string
    {
        if (!$m) return null;

        // direct simple columns
        foreach (['name','full_name','company_name','title'] as $col) {
            if (isset($m->$col) && $m->$col) return (string) $m->$col;
        }

        // first + last
        if (isset($m->first_name) || isset($m->last_name)) {
            $fn = trim((string)($m->first_name ?? ''));
            $ln = trim((string)($m->last_name ?? ''));
            $full = trim("$fn $ln");
            if ($full !== '') return $full;
        }

        // fallback to unique but readable fields
        foreach (['email','phone','username'] as $col) {
            if (isset($m->$col) && $m->$col) return (string) $m->$col;
        }

        return null;
    }

    /** Human label for the party shown in index & elsewhere. */
   public function getPartyLabelAttribute(): string
    {
        $type  = $this->party_type;

        // Manual/other: stored on this row
        if (in_array($type, ['manual','other'])) {
            $candidates = [
                trim((string)($this->name ?? '')),
                trim((string)($this->phone ?? '')),
                trim((string)($this->email ?? '')),
            ];
            foreach ($candidates as $v) { if ($v !== '') return $v; }
            return ucfirst(str_replace('_',' ', $type));
        }

        // Linked types -> load party and prefer add_users.name
        $party = $this->resolvePartyModel();
        if ($party) {
            // 1) use user.name if available
            $name = optional($party->user)->name;
            if ($name && trim($name) !== '') return trim($name);

            // 2) otherwise fall back to party’s own best fields
            foreach (['name','full_name','company_name','contact_person','email','phone'] as $f) {
                $v = trim((string)($party->{$f} ?? ''));
                if ($v !== '') return $v;
            }
            return ucfirst(str_replace('_',' ', $type)).' #'.$this->party_id;
        }

        return ucfirst(str_replace('_',' ', $type));
    }

    protected function resolvePartyModel()
    {
        // Load the correct linked record; keep it simple
        switch ($this->party_type) {
            case 'clients':              return \App\Models\Client::with('user')->find($this->party_id);
            case 'developers':           return \App\Models\Developer::with('user')->find($this->party_id);
            case 'partners':             return \App\Models\Partner::with('user')->find($this->party_id);
            case 'team_managers':        return \App\Models\TeamManager::with('user')->find($this->party_id);
            case 'business_developers':  return \App\Models\BusinessDeveloper::with('user')->find($this->party_id);
            default: return null;
        }
    }
}
