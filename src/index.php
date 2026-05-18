<?php

namespace App;

function search(array $docs, string $query): array
{
    if (empty($docs)) {
        return [];
    }

    $results = [];
    foreach ($docs as $doc) {
        if (preg_match("/\b$query\b/i", $doc['text'])) {
            $results[] = $doc['id'];
        }
    }

    return $results;
}
