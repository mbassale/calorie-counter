<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $table = 'meals';

    protected $guarded = [];

    protected $dates = ['date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
