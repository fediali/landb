<?php

return [
    [
        'name' => 'Timelines',
        'flag' => 'timeline.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'timeline.create',
        'parent_flag' => 'timeline.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'timeline.edit',
        'parent_flag' => 'timeline.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'timeline.destroy',
        'parent_flag' => 'timeline.index',
    ],
];
