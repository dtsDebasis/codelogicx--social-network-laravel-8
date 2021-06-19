<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFriendshipGroup extends Model
{
    use HasFactory;

        /**
     * @var array
     */
    protected $fillable = ['friendship_id', 'group_id', 'friend_id', 'friend_type'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

}
