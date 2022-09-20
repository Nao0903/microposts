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
}
