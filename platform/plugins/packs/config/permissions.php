<?php

return [
    [
        'name' => 'Packs',
        'flag' => 'packs.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'packs.create',
        'parent_flag' => 'packs.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'packs.edit',
        'parent_flag' => 'packs.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'packs.destroy',
        'parent_flag' => 'packs.index',
    ],
];
