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
];
