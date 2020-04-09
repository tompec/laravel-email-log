<?php

return [
    /*
     * This is the name of the table that will be created by the migration.
     */
    'table_name' => 'email_log',

    /*
     * This is the name of the table that will be created by the migration.
     */
    'events_table_name' => 'email_log_events',

    /*
     * The model that will be attached to the email logs.
     */
    'recipient_model' => \App\User::class,

    /*
     * This is the name of the column that the `recipient_model` uses to store the email address.
     */
    'recipient_email_column' => 'email',

    /*
     * Whether or not you want to log emails that don't belong to any model
     */
    'log_unknown_recipients' => true,

    /*
     * Whether or not you want to fetch events from Mailgun and store the data
     */
    'log_events' => false,
];
