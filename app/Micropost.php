<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content'];
    
    
    /**
     * この投稿を所有するユーザ。（ Userモデルとの関係を定義）
     */
     
     
     public function user()
     {
        return $this->belongsTo(User::class); 
        // Micropostのインスタンスが所属している唯一のUser（投稿者の情報）を
        //$micropost->user()->first() もしくは $micropost->user という簡単な記述で取得できるようになります。
     }
     
     
     //多対多の関係を正確に記述するため
     public function favorite_users()
     {
        //return $this->hasMany(User::class);
        return $this->belongsToMany(User::class, 'users', 'micropost_id', 'user_id')->withTimestamps();
         // UserのインスタンスからそのUserが持つお気に入りMicropostsを
         //$microposts->favorite_users()->get() もしくは $microposts->favorite_users()
         //という簡単な記述で取得できるようになります。
     }
}
