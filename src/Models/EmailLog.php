<?php

namespace Tompec\EmailLog\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $guarded = [];

    protected $dates = [
        'delivered_at',
        'failed_at',
        'opened_at',
        'clicked_at',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->table)) {
            $this->setTable(config('email-log.table_name'));
        }
        parent::__construct($attributes);
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
