<?php

return [
    [
        'name' => 'Rises',
        'flag' => 'rises.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'rises.create',
        'parent_flag' => 'rises.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'rises.edit',
        'parent_flag' => 'rises.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'rises.destroy',
        'parent_flag' => 'rises.index',
    ],
];
