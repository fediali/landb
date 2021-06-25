<?php

return [
    [
        'name' => 'Sourcings',
        'flag' => 'sourcing.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'sourcing.create',
        'parent_flag' => 'sourcing.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'sourcing.edit',
        'parent_flag' => 'sourcing.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'sourcing.destroy',
        'parent_flag' => 'sourcing.index',
    ],
];
