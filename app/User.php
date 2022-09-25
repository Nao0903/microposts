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
     //Userが持つMicropostの数とお気に入りの数をカウントするためのメソッド
     public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
         /*loadCount メソッドの引数に指定しているのはリレーション名です。
         先ほどモデル同士の関係を表すメソッドを定義しましたが、そのメソッド名がリレーション名になります。
         これによりUserのインスタンスに {リレーション名}_count プロパティが追加され、件数を取得できるようになります*/
    }
     
     
     /**
     * このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    
    public function follow($userId)
    {
        // すでにフォローしているか
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうか
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // フォロー済み、または、自分自身の場合は何もしない
            return false;
        } else {
            // 上記以外はフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているか
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうか
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // フォロー済み、かつ、自分自身でない場合はフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 上記以外の場合は何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()//タイムライン用のマイクロポストを取得するためのメソッド
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        //で[]をつけると配列の最後に要素を追加することができます。※上書きされない
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    
    /**
     * このユーザのお気に入り投稿。（ favoritesモデルとの関係を定義）
     */
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($micropostId)
    {
        // すでにフォローしているか
        $exist = $this->is_favorites($micropostId);
        // 対象が自分自身かどうか
        $its_me = $this->id == $micropostId;

        if ($exist || $its_me) {
            // お気に入り済み、または、自分自身の場合は何もしない
            return false;
        } else {
            // 上記以外はお気に入りする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    /**
     * 指定された $micropostIdの投稿をこのユーザがお気に入り中であるか調べる。お気に入り中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_favorites($micropostId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->favorites()->where('favorite_id', $micropostId)->exists();
    }
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_favorites()//タイムライン用のマイクロポストを取得するためのメソッド
    {
        // このユーザがお気に入り中の投稿のidを取得して配列にする
        $micropostIds = $this->favorites()->pluck('favorite.id')->toArray();
        // このユーザのidもその配列に追加
        //で[]をつけると配列の最後に要素を追加することができます。※上書きされない
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('favorite_id', $micropostIds);
    }
    
    
}
