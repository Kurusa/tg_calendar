<?php

namespace App\Models;

use App\Services\Enums\EventStatus;
use App\Services\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model {

    protected $table = 'users';

    protected $fillable = [
        'user_name',
        'first_name',
        'chat_id',
        'status',
        'role',
    ];

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by', 'id');
    }

    public function draftEvent()
    {
        return $this->events()
            ->where('created_by', $this->id)
            ->where('status', EventStatus::DRAFT)
            ->first();
    }

    public function editingEvent()
    {
        return $this->events()
            ->where('status', EventStatus::EDITING)
            ->first();
    }

}
