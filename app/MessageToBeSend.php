<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageToBeSend extends Model
{
    protected $fillable = [
        'message_type',
        'subject',
        'message_body',
        'email',
        'sent ',
    ];
    protected $primaryKey = 'message_to_be_send_id';
    protected $table = 'message_to_be_sends';
}
