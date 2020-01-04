<?php

namespace Tompec\EmailLog;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $guarded = [];

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
