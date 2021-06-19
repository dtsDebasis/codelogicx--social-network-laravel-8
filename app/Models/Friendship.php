<?php

namespace App\Models;

use App\Models\UserFriendshipGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Friendship extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function sender()
    {
        return $this->morphTo('sender');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function recipient()
    {
        return $this->morphTo('recipient');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function groups() {
        return $this->hasMany(UserFriendshipGroup::class, 'friendship_id');
    }

    /**
     * @param Model $recipient
     * @return $this
     */
    public function fillRecipient($recipient)
    {
        return $this->fill([
            'recipient_id' => $recipient->getKey(),
            'recipient_type' => $recipient->getMorphClass()
        ]);
    }

    /**
     * @param $query
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRecipient($query, $model)
    {
        return $query->where('recipient_id', $model->getKey())
            ->where('recipient_type', $model->getMorphClass());
    }

    /**
     * @param $query
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSender($query, $model)
    {
        return $query->where('sender_id', $model->getKey())
            ->where('sender_type', $model->getMorphClass());
    }

    /**
     * @param $query
     * @param Model $model
     * @param string $groupSlug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereGroup($query, $model, $groupSlug)
    {

        $groupsTable   = 'user_friendship_groups';
        $friendsTable  = 'friendships';
        $groupsAvailable = config('friendships.groups', []);

        if ('' !== $groupSlug && isset($groupsAvailable[$groupSlug])) {

            $groupId = $groupsAvailable[$groupSlug];

            $query->join($groupsTable, function ($join) use ($groupsTable, $friendsTable, $groupId, $model) {
                $join->on($groupsTable . '.friendship_id', '=', $friendsTable . '.id')
                    ->where($groupsTable . '.group_id', '=', $groupId)
                    ->where(function ($query) use ($groupsTable, $friendsTable, $model) {
                        $query->where($groupsTable . '.friend_id', '!=', $model->getKey())
                            ->where($groupsTable . '.friend_type', '=', $model->getMorphClass());
                    })
                    ->orWhere($groupsTable . '.friend_type', '!=', $model->getMorphClass());
            });

        }

        return $query;

    }

    /**
     * @param $query
     * @param Model $sender
     * @param Model $recipient
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenModels($query, $sender, $recipient)
    {
        $query->where(function ($queryIn) use ($sender, $recipient){
            $queryIn->where(function ($q) use ($sender, $recipient) {
                $q->whereSender($sender)->whereRecipient($recipient);
            })->orWhere(function ($q) use ($sender, $recipient) {
                $q->whereSender($recipient)->whereRecipient($sender);
            });
        });
    }
}
