<?php

namespace App\Models;

use App\Traits\Friendable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'avatar_url',
    ];

    //Dynamically create username
    public function setUsernameAttribute($value)
    {
        $username = head(explode(' ', trim($value)));

        $i = 0;
        while(User::whereUsername($username)->exists())
        {
            $i++;
            $username = $username . $i;
        }

        $this->attributes['username'] = strtolower($username);
    }

    //Custom attribute for user profile avatar image
    public function getAvatarUrlAttribute()
    {
        return "https://www.gravatar.com/avatar/".md5($this->email)."?d=mp&s=40";
    }
}
