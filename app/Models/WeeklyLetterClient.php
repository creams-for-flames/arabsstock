<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyLetterClient extends Model
{
    use SoftDeletes;

    protected $guarded = array();
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_SUBMIT = 2;
    protected $appends = ['status_sent'];

    /**
     * Get all of the files for the WeeklyLetterClient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(WeeklyLetterClientFile::class, 'weekly_letter_client_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'weekly_letters_users','weekly_letter_id','user_id');
    }
    public function getStatusSentAttribute()
    {
        $status = $this->status;
        $sent = $this->sent;
            
        switch ($status) {
            case 'pending':
                $status = __('admin.pending');
                break;
            case 'active':
                $status = __('admin.active');
                break;
            case "submit":
                if($sent)
                $status = __('admin.sent');
                else
                $status = __('admin.is_being_sent');

                break;    
            

        }

        return $status;
        
    }
}
