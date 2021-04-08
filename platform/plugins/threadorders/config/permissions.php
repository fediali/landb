<?php

return [
    [
        'name' => 'Threadorders',
        'flag' => 'threadorders.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'threadorders.create',
        'parent_flag' => 'threadorders.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'threadorders.edit',
        'parent_flag' => 'threadorders.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'threadorders.destroy',
        'parent_flag' => 'threadorders.index',
    ],
    [
        'name'        => 'Detail',
        'flag'        => 'threadorders.details',
        'parent_flag' => 'threadorders.index',
    ],
    [
        'name'        => 'Status',
        'flag'        => 'threadorders.status',
        'parent_flag' => 'threadorders.index',
    ],
    [
        'name'        => 'Push to Ecommerce',
        'flag'        => 'threadorders.pushEcommerce',
        'parent_flag' => 'threadorders.index',
    ], [
        'name'        => 'Thread Order',
        'flag'        => 'threadorders.order',
        'parent_flag' => 'threadorders.index',
    ],
];
