<?php

return [
    [
        'name' => 'Threads',
        'flag' => 'thread.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'thread.create',
        'parent_flag' => 'thread.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'thread.edit',
        'parent_flag' => 'thread.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'thread.destroy',
        'parent_flag' => 'thread.index',
    ],
];
