<?php

return [
    [
        'name' => 'Seasons',
        'flag' => 'seasons.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'seasons.create',
        'parent_flag' => 'seasons.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'seasons.edit',
        'parent_flag' => 'seasons.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'seasons.destroy',
        'parent_flag' => 'seasons.index',
    ],
];
