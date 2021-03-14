<?php

return [
    [
        'name' => 'Categorysizes',
        'flag' => 'categorysizes.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'categorysizes.create',
        'parent_flag' => 'categorysizes.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'categorysizes.edit',
        'parent_flag' => 'categorysizes.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'categorysizes.destroy',
        'parent_flag' => 'categorysizes.index',
    ],
];
