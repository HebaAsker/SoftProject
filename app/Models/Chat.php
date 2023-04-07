<?php

namespace App\Models;

use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats' ;
    protected $guarded = ['id'] ;

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'chat_id')->latest('updated_at')->firstOrFail();
    }

    public function scopeHasParticipant($query, int $userId){
        return $query->whereHas('participants', function($q) use ($userId){
            $q->where('user_id',$userId);
        });
    }
}
