<?php
declare(strict_types=1);

return [
    'a7picsuggest-give-tags' => [
        'path' => '/a7picsuggest/get-tags',
        'target' => \A7digital\A7picsuggest\Controller\SuggestionAjaxController::class . '::giveAllAvailableTags',
    ],
    'a7picsuggest-suggest' => [
        'path' => '/a7picsuggest/suggest',
        'target' => \A7digital\A7picsuggest\Controller\SuggestionAjaxController::class . '::suggest',
    ],
];
