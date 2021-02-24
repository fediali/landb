<?php

return [
    [
        'name' => 'Fabrics',
        'flag' => 'fabrics.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'fabrics.create',
        'parent_flag' => 'fabrics.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'fabrics.edit',
        'parent_flag' => 'fabrics.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'fabrics.destroy',
        'parent_flag' => 'fabrics.index',
    ],
];
