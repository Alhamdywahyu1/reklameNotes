<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
        'last_otp_sent_at',
        'name',
        'email',
        'password',
        'role_id',
        'nik',
        'address',
        'phone',
        'is_active',
        'last_login_at',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'last_otp_sent_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permohonanReklame(): HasMany
    {
        return $this->hasMany(PermohonanReklame::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ApprovalWorkflow::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function hasRole(string $roleName): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role?->slug === $roleName;
    }

    public function hasAnyRole(array $roleNames): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return in_array($this->role?->slug, $roleNames);
    }
}
