<?php

return [

    /*
     * Select the columns that should be displayed.
     */
    'display_columns' => [
        'name' => true,
        'laravel' => true,
        'type' => true,
        'length' => true,
        'nullable' => true,
        'auto_increment' => true,
        'default' => true,
        'comment' => true,
    ],

    /*
     * Define a custom database connection, default will be used if empty
     */
    'connection' => null,

    'emojis' => true,

    'colors' => true,

    /*
     * Specify the style that should be used.
     * Available: default, borderless, compact, symfony-style-guide, box, box-double
     */
    'style' => 'box',
];
