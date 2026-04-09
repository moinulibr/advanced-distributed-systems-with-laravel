<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['id', 'name', 'email', 'phone', 'password', 'shard_key', 'created_at'];

    public function profile()
    {
        /** * Important: In a sharded environment, the profile MUST be on the same shard.
         * We ensure the child query uses the same connection as the parent.
         */
        $instance = new UserProfile();
        $instance->setConnection($this->getConnectionName());

        return $this->hasOne(UserProfile::class, 'user_id', 'id')
            ->setQuery($instance->newQuery());
    }
    
    // relation with user profile
   /*  public function profile()
    {
        //sence the distributed system, we're sure to use the same connection
        return $this->hasOne(UserProfile::class, 'user_id', 'id')->onWriteConnection();
    } */
}
