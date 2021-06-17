<?php

return [
    [
        'name' => 'Threadsamples',
        'flag' => 'threadsample.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'threadsample.create',
        'parent_flag' => 'threadsample.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'threadsample.edit',
        'parent_flag' => 'threadsample.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'threadsample.destroy',
        'parent_flag' => 'threadsample.index',
    ],
];
