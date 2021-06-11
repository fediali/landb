<?php

return [
    [
        'name' => 'Chatings',
        'flag' => 'chating.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'chating.create',
        'parent_flag' => 'chating.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'chating.edit',
        'parent_flag' => 'chating.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'chating.destroy',
        'parent_flag' => 'chating.index',
    ],
];
