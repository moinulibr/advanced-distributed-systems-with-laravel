<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'city',
        'bio',
        'created_at',
        'updated_at'
    ];

    /**
     * User relationship with UserProfile (BelongsTo)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->setConnection($this->getConnectionName());
    }
}
