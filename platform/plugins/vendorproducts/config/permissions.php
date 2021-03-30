<?php

return [
    [
        'name' => 'Vendorproducts',
        'flag' => 'vendorproducts.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'vendorproducts.create',
        'parent_flag' => 'vendorproducts.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'vendorproducts.edit',
        'parent_flag' => 'vendorproducts.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'vendorproducts.destroy',
        'parent_flag' => 'vendorproducts.index',
    ],
];
