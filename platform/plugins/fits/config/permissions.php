<?php

return [
    [
        'name' => 'Fits',
        'flag' => 'fits.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'fits.create',
        'parent_flag' => 'fits.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'fits.edit',
        'parent_flag' => 'fits.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'fits.destroy',
        'parent_flag' => 'fits.index',
    ],
];
