<?php

return [
    [
        'name' => 'Printdesigns',
        'flag' => 'printdesigns.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'printdesigns.create',
        'parent_flag' => 'printdesigns.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'printdesigns.edit',
        'parent_flag' => 'printdesigns.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'printdesigns.destroy',
        'parent_flag' => 'printdesigns.index',
    ],
];
