<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail; 

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    protected $table = 'users';
    protected $guarded = array();

    const LEVEL_PATIENT = 'Patient';

    
     public function hasVerifiedEmail()
    {
        if ($this->level !== self::LEVEL_PATIENT) {
            return true;
        }
        return !is_null($this->email_verified_at);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        if ($this->level === self::LEVEL_PATIENT) {
            $this->notify(new CustomVerifyEmail); // ✅ Uses CustomVerifyEmail
        }
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'referring_md');
    }
}
