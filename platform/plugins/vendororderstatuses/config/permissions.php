<?php

return [
    [
        'name' => 'Vendororderstatuses',
        'flag' => 'vendororderstatuses.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'vendororderstatuses.create',
        'parent_flag' => 'vendororderstatuses.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'vendororderstatuses.edit',
        'parent_flag' => 'vendororderstatuses.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'vendororderstatuses.destroy',
        'parent_flag' => 'vendororderstatuses.index',
    ],
];
