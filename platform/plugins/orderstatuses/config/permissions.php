<?php

return [
    [
        'name' => 'Orderstatuses',
        'flag' => 'orderstatuses.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'orderstatuses.create',
        'parent_flag' => 'orderstatuses.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'orderstatuses.edit',
        'parent_flag' => 'orderstatuses.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'orderstatuses.destroy',
        'parent_flag' => 'orderstatuses.index',
    ],
];
