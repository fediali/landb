<?php

return [
    [
        'name' => 'Inventory',
        'flag' => 'inventory.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'inventory.create',
        'parent_flag' => 'inventory.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'inventory.edit',
        'parent_flag' => 'inventory.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'inventory.destroy',
        'parent_flag' => 'inventory.index',
    ],
];
