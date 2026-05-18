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

    usort($results, function ($a, $b) use ($docs, $preparedSearchQuery) {
        $aWords = collect($docs)->where('id', $a)->first()['text'];
        $bWords = collect($docs)->where('id', $b)->first()['text'];

        return inputsCount($bWords, $preparedSearchQuery) <=> inputsCount($aWords, $preparedSearchQuery);
    });

    return $results;
}

function prepareWords(string $words): array
{
    preg_match_all('/\w+/', $words, $matches);

    return collect($matches)->flatten()->toArray();
}

function inputsCount(string $words, string $searchedWord): int
{
    $count    = 0;
    $wordsArr = prepareWords($words);

    foreach ($wordsArr as $word) {
        if ($word === $searchedWord) {
            $count++;
        }
    }

    return $count;
}
