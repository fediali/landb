<?php

return [
    [
        'name' => 'Producttimelines',
        'flag' => 'producttimeline.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'producttimeline.create',
        'parent_flag' => 'producttimeline.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'producttimeline.edit',
        'parent_flag' => 'producttimeline.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'producttimeline.destroy',
        'parent_flag' => 'producttimeline.index',
    ],
];
