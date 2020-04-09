<?php

namespace Tompec\EmailLog\Models;

use Illuminate\Database\Eloquent\Model;

class EmailEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->table)) {
            $this->setTable(config('email-log.events_table_name'));
        }
        parent::__construct($attributes);
    }

    public function email()
    {
        return $this->belongsTo(EmailLog::class, 'email_log_id');
    }
}
