<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 1:1
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // 1:1 (solo si role=company)
    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    // 1:N
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // N:M
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)->withTimestamps();
    }

    // Empresa: 1:N ofertas publicadas
    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class, 'company_user_id');
    }

    // Usuario: 1:N postulaciones
    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    // Mensajes recibidos
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_user_id');
    }
}
