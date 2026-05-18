<?php

namespace App;

function search(array $docs, string $searchQuery): array
{
    if (empty($docs)) {
        return [];
    }

    $preparedSearchQuery = prepareWords($searchQuery)[0] ?? '';
    $results             = [];

    foreach ($docs as $doc) {
        $preparedDocs = prepareWords($doc['text']);

        if (in_array($preparedSearchQuery, $preparedDocs)) {
            $results[] = $doc['id'];
        }
    }

    return $results;
}

function prepareWords(string $words): array
{
    preg_match_all('/\w+/', $words, $matches);

    return collect($matches)->flatten()->toArray();
}
