<?php

return [
    [
        'name' => 'Threadvariationsamples',
        'flag' => 'threadvariationsamples.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'threadvariationsamples.create',
        'parent_flag' => 'threadvariationsamples.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'threadvariationsamples.edit',
        'parent_flag' => 'threadvariationsamples.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'threadvariationsamples.destroy',
        'parent_flag' => 'threadvariationsamples.index',
    ],
];
