<?php

namespace App;

function search(array $docs, string $search): array
{
    if (empty($docs)) {
        return [];
    }

    $handledSearchText = handleText($search);
    $docIds = fuzzySearch($docs, $handledSearchText);

    return (array) collect($handledSearchText)
        ->reduce(function ($acc, $word) use ($docs, $docIds) {
            foreach ($docIds as $docId) {
                $docText = collect($docs)->where('id', $docId)->first()['text'];
                $handledDocText = handleText($docText);

                if (in_array($word, $handledDocText)) {
                    $acc[$word][] = $docId;
                }
            }

            return $acc;
        }, []);
}

function fuzzySearch(array $docs, array $handledSearchText): array
{
    return collect($docs)
        ->map(function ($doc) use ($handledSearchText) {
            $handledDocText = handleText($doc['text']);
            $relevantWords  = relevantWords($handledDocText, $handledSearchText);
            $doc['score']   = count($relevantWords);
            $doc['score']   += inputsCount($handledDocText, $relevantWords);

            return $doc;
        })
        ->filter(fn($doc) => $doc['score'] > 0)
        ->sortByDesc(fn($doc) => $doc['score'])
        ->pluck('id')
        ->values()
        ->toArray();
}

function handleText(string $words): array
{
    preg_match_all('/\w+/', $words, $matches);

    return collect($matches)->flatten()->toArray();
}

function relevantWords(array $docText, array $searchText): array
{
    return collect($docText)
        ->filter(fn($word) => in_array($word, $searchText))
        ->toArray();
}

function inputsCount(array $docText, array $searchText): int
{
    return (int) collect($docText)
        ->reduce(function ($acc, $word) use ($searchText) {
            if (in_array($word, $searchText)) {
                $acc++;
            }

            return $acc;
        }, 0);
}
