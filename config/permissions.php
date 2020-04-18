<?php

return [
    [
        'name'        => 'Logs list',
        'flag'        => 'logs.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'logs.destroy',
        'parent_flag' => 'logs.index',
    ],
];