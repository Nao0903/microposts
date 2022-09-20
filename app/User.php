<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    //$fillableでユーザーが使用できるものを指定する
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    //$hidden で暗号化した状態でDBに保存する。
    //認証の際は暗号化したものでチェックする。
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
     
     
     /*
     Userモデルファイルにも一対多の表現を記述しておきます。
     Userが持つMicropostは複数存在するため、 function microposts() のように複数形micropostsで
     メソッドを定義します。
     */
     
     
     //Userモデルファイルにも一対多の表現を記述しておきます。 Userが持つMicropostは複数存在するため、
     //function microposts() のように複数形micropostsでメソッドを定義します。
     public function microposts()
     {
         return $this->hasMany(Micropost::class);
         //Micropostのときと同様に記述することで、 UserのインスタンスからそのUserが持つMicropostsを
         //$user->microposts()->get() もしくは $user->microposts という簡単な記述で取得できるようになります。
     }
    
    
     /**
     * このユーザに関係するモデルの件数をロードする。
     */
     //Userが持つMicropostの数をカウントするためのメソッド
     public function loadRelationshipCounts()
    {
        $this->loadCount('microposts');
         /*loadCount メソッドの引数に指定しているのはリレーション名です。
         先ほどモデル同士の関係を表すメソッドを定義しましたが、そのメソッド名がリレーション名になります。
         これによりUserのインスタンスに {リレーション名}_count プロパティが追加され、件数を取得できるようになります*/
     }
    
    
}
