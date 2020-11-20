<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;


    /**
     * Renvoie l'utilisateur propriétaire du board (celui qui l'a créé)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    } 


    /**
     * Renvoie tous les utilisateurs qui sont asssociés au board
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User')
                    ->using("App\Models\BoardUser")
                    ->withPivot("id")
                    ->withTimestamps();
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

}
