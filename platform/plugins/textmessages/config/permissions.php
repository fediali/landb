<?php

return [
    [
        'name' => 'Textmessages',
        'flag' => 'textmessages.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'textmessages.create',
        'parent_flag' => 'textmessages.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'textmessages.edit',
        'parent_flag' => 'textmessages.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'textmessages.destroy',
        'parent_flag' => 'textmessages.index',
    ],
];
