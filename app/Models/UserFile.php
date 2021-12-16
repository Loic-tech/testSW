<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class UserFile extends Model {
    protected $table = 'file_for_user';
    protected $fillable = ['path','name_of_file','user_id'];


    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}