<?php

namespace App\Http\Traits;

use App\Models\ManualSearch;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use JetBrains\PhpStorm\ArrayShape;

trait ManualSearchTrait
{
    protected int $itemPerPage = 10;
    protected int $minimumWordLength = 2;

    /**
     * @param  Request $request
     * @return array
     */
    #[ArrayShape(['words' => "mixed", 'query' => "array", 'time' => "array"])]
    public function searchWordWeight(Request $request): array
    {
        $startTime = microtime(true);
        $data = $request->all();
        $originalKeyword = $data['keyword'] ?? "";
        $keyword = $this->formatKeyword($originalKeyword);
        if (empty($keyword)) {
            $words = $this->getPaginatedWords();
        } else {
            $phraseIds = $this->searchWholePhrase($keyword);
            $words = explode(" ", $keyword);
            $wordIds = $this->searchEachWords($words);
            $idWeights = array_count_values(array_merge($phraseIds, $wordIds));
            arsort($idWeights);
            $insertDate = [];
            foreach ($idWeights as $id => $weight) {
                $insertDate[] = [
                    'word_id' => $id,
                    'weight' => $weight,
                ];
            }
            ManualSearch::query()->truncate();
            ManualSearch::query()->insert($insertDate);

            /* @var Paginator $words */
            $words = Word::query()
                ->whereIn('words.id', $wordIds)
                ->join('manual_search', 'words.id', '=', 'manual_search.word_id')
                ->orderBy('manual_search.weight', 'desc')
                ->paginate($this->itemPerPage);
            $words = Word::addPaginateDetail($words);
        }
        $query = [
            'keyword' => $keyword,
        ];
        $endTime = microtime(true);

        return ['words' => $words, 'query' => $query, 'time' => $endTime - $startTime];
    }

    /**
     * Format keyword string. Only allow alphabet, digit and space
     *
     * @param  $kw
     * @return string|array
     */
    public function formatKeyword($kw): string|array
    {
        $kw = strip_tags(trim($kw));
        $kw = str_replace(' ', '_', $kw);
        $kw = preg_replace('/[^A-Za-z0-9_]/', '', $kw);
        $kw = preg_replace('/_+/', '_', $kw);
        return str_replace('_', ' ', $kw);
    }

    /**
     * @param  $phrase
     * @return array
     */
    public function searchWholePhrase($phrase): array
    {
        return Word::query()
            ->where('message', 'like', '%' . $phrase . '%')
            ->pluck('id')
            ->toArray();
    }

    /**
     * @param  $words
     * @return array
     */
    public function searchEachWords($words): array
    {
        $wordResult = [];
        foreach ($words as $word) {
            if (strlen($word) >= $this->minimumWordLength) {
                $wordResult = array_merge($wordResult, $this->searchWholePhrase($word));
            }
        }
        return $wordResult;
    }
}
