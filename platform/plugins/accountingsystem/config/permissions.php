<?php

return [
    [
        'name' => 'Accountingsystems',
        'flag' => 'accountingsystem.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'accountingsystem.create',
        'parent_flag' => 'accountingsystem.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'accountingsystem.edit',
        'parent_flag' => 'accountingsystem.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'accountingsystem.destroy',
        'parent_flag' => 'accountingsystem.index',
    ],
];
