<?php

use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfig as I;

return [
    I::FIELD__REACTION => [
        I::FIELD__PROJECTS => [],
        I::FIELD__IN_WORK => [
            I::FIELD__FROM => 'To Do',
            I::FIELD__TO => 'In progress'
        ],
        I::FIELD__RESOLVED => [
            I::FIELD__FROM => 'Done',
            I::FIELD__TO => 'Approved'
        ]
    ]
];
