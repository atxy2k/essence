<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:26
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeEmailRequest extends Model
{
    use SoftDeletes;

    protected $table = 'change_email_requests';
    protected $fillable = ['user_id', 'token_confirmation_change', 'token_confirmation_email', 'before_email', 'next_email', 'confirmated', 'email_confirmed' ];
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTokenConfirmationAttribute()
    {
        return encrypt(sprintf('%s_____%s', $this->before_email, $this->token_confirmation_change) );
    }

    public function getTokenConfirmationMailAttribute()
    {
        return encrypt(sprintf('%s_____%s', $this->before_email, $this->token_confirmation_email) );
    }

}
