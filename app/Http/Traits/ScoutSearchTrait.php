<?php

namespace App\Http\Traits;

use App\Models\Word;
use App\Models\WordScoutDb;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use JetBrains\PhpStorm\ArrayShape;

trait ScoutSearchTrait
{
    protected int $itemPerPage = 10;

    /**
     * @param  Request $request
     * @return array
     */
    #[ArrayShape(['words' => "mixed", 'query' => "array", 'time' => "array"])]
    public function searchScout(Request $request): array
    {
        $startTime = microtime(true);
        $data = $request->all();
        $originalKeyword = $data['keyword'] ?? "";
        $keyword = $this->formatKeywordScout($originalKeyword);
        if (empty($keyword)) {
            $words = $this->getPaginatedWords();
        } else {
            /* @var Paginator $words */
            $words = WordScoutDb::search($keyword)
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
    private function formatKeywordScout($kw): string|array
    {
        $kw = strip_tags(trim($kw));
        $kw = str_replace(' ', '_', $kw);
        $kw = preg_replace('/[^A-Za-z0-9_]/', '', $kw);
        $kw = preg_replace('/_+/', '_', $kw);
        return str_replace('_', ' ', $kw);
    }
}
