<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Chat;
use App\Notifications\Message;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $guarded = ['id'] ;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    public function notifayForOneSignal() : array{
        return ['tags'=>['key'=>'user_id','relation'=>'=', 'value'=>(string)($this->id)]];
    }

    public function sendNewMessageNotification(array $data) : void {
        $this->notify(new Message($data));
    }
}
