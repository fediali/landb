<?php

return [
    [
        'name' => 'Washes',
        'flag' => 'wash.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'wash.create',
        'parent_flag' => 'wash.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'wash.edit',
        'parent_flag' => 'wash.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'wash.destroy',
        'parent_flag' => 'wash.index',
    ],
];
