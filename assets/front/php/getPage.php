<?php
/* This file wil return HTML pages to Front end's AJAX requests. */
$HTML_FILE_PATH = '../../../';

$json = file_get_contents('php://input');
$jsonDecoded = json_decode($json, true);
/* The key 'page' will contain the page requested. */
$pageToReturn = $jsonDecoded[page];
readfile($HTML_FILE_PATH . $pageToReturn);
