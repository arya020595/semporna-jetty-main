<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Manifest extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "manifest";

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_APPROVED_PROGRESS = 2;
    const STATUS_AMEND = 3;
    const STATUS_REJECTED = -1;

    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_FAILED = -1;
    const PAYMENT_STATUS_PENDING = 0;

    const TYPE_BY_COMPANY = 1;
    const TYPE_RENTAL = 2;

    const ARR_STATUS = [
        -1 => "Rejected",
        0 => "Pending",
        1 => "Approved",
        2 => "In Progress",
        3 => "Amend"
    ];

    const ARR_PAYMENT_STATUS = [
        -1 => "Failed",
        0 => "Pending",
        1 => "Paid"
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function boatman(): BelongsTo
    {
        return $this->belongsTo(Boatman::class);
    }

    public function assistance(): BelongsTo
    {
        return $this->belongsTo(Boatman::class, "assistance_id");
    }

    public function boatmanOther(): BelongsToMany
    {
        return $this->belongsToMany(Boatman::class, "manifest_boatman", "manifest_id", "boatman_id")
            ->withPivot("name", "ic_no", "type");
    }

    public function manifestBoatman(): HasMany
    {
        return $this->hasMany(ManifestBoatman::class);
    }

    public function departure(): BelongsTo
    {
        return $this->belongsTo(RefDestination::class, "departure_id");
    }

    public function destination(): BelongsToMany
    {
        return $this->belongsToMany(RefDestination::class, "manifest_destination", "manifest_id", "ref_destination_id")
            ->withPivot('ref_destination_name');
    }

    public function manifestDestination(): HasMany
    {
        return $this->hasMany(ManifestDestination::class);
    }

    public function guest(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function manifestFee(): HasMany
    {
        return $this->hasMany(ManifestFee::class);
    }

    public function approvement(): HasMany
    {
        return $this->hasMany(Approvement::class);
    }

    public function latestApprovementByRole()
    {
        return $this->approvement()->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('approvement')
                ->whereColumn('manifest_id', 'approvement.manifest_id')
                ->groupBy('role_id');
        });
    }

    public function jettyApprovalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, "jetty_approval_user_id");
    }

    public function getStatusTextAttribute()
    {
        return self::ARR_STATUS[$this->status] ?? self::ARR_STATUS[0];
    }

    public function getJettyApprovalStatusTextAttribute()
    {
        return self::ARR_STATUS[$this->jetty_approval_status] ?? self::ARR_STATUS[0];
    }

    public function getPaymentStatusTextAttribute()
    {
        // Seafest Jetty - manual payment (if bypass enabled)
        if (
            config('features.bypass_seafest_payment') &&
            $this->departure &&
            $this->departure->code == RefDestination::CODE_SEAFEST
        ) {
            return '-';
        }

        return self::ARR_PAYMENT_STATUS[$this->payment_status] ?? self::ARR_PAYMENT_STATUS[0];
    }

    /**
     * Determine if this manifest includes a destination that requires Sabah Parks approval
     *
     * @return bool
     */
    public function isSabahParks(): bool
    {
        $sabahParksDestNames = [
            'Bohey Dulang',
            'Sibuan',
            'Mantabuan',
            'Maiga'
        ];

        $sabahParksDestIds = RefDestination::query()
            ->where('type', RefDestination::TYPE_DESTINATION)
            ->whereIn('title', $sabahParksDestNames)
            ->pluck('id')
            ->toArray();

        $manifestDestIds = $this->manifestDestination->pluck('ref_destination_id')->toArray();

        return !empty(array_intersect($manifestDestIds, $sabahParksDestIds));
    }
}
