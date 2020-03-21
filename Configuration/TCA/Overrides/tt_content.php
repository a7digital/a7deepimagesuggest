<?php

if (empty($GLOBALS['TCA']['tt_content']['ctrl']['container'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['container'] = [];
}

if (empty($GLOBALS['TCA']['tt_content']['ctrl']['container']['inline'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['container']['inline'] = [];
}

if (empty($GLOBALS['TCA']['tt_content']['ctrl']['container']['inline']['fieldWizard'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['container']['inline']['fieldWizard'] = [];
}

$GLOBALS['TCA']['tt_content']['ctrl']['container']['inline']['fieldWizard']['suggestImages'] = [
    'renderType' => 'suggestImages',
];
