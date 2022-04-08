<?php

namespace App\Models;

use app\Models\User;
use Illuminate\Database\Eloquent\Model;

class TrainerComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trainer_id', 'user_id', 'note'
    ];

    /**
     * trainer comment is associated with trainer
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * trainer comment is associated with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
