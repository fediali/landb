<?php

return [
    [
        'name' => 'Vendororders',
        'flag' => 'vendororder.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'vendororder.create',
        'parent_flag' => 'vendororder.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'vendororder.edit',
        'parent_flag' => 'vendororder.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'vendororder.destroy',
        'parent_flag' => 'vendororder.index',
    ],
];
