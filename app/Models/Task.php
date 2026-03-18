<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
  protected $fillable = [
    'user_id',
    'title',
    'description', 
    'status',
    'priority',
    'due_date',
    'project_id',
    'assigned_to',  // ✅ yeh hona chahiye
];
    public function user(){
    return $this->belongsTo(User::class);
}
public function project(){
    return $this->belongsTo(Project::class);
}
}



