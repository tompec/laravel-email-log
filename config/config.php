<?php

return [
    /*
     * This is the name of the table that will be created by the migration.
     */
    'table_name' => 'email_log',

    /*
     * The model that will be attached to the email logs.
     */
    'recipient_model' => \App\User::class,

    /*
     * This is the name of the column that the `recipient_model` uses to store the email address.
     */
    'recipient_email_column' => 'email',
];
