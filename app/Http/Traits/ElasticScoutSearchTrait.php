<?php

namespace App\Http\Traits;

use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Infrastructure\Scout\Builder;
use JetBrains\PhpStorm\ArrayShape;

trait ElasticScoutSearchTrait
{
    protected int $itemPerPage = 10;

    /**
     * @param  Request $request
     * @return array
     */
    #[ArrayShape(['words' => "mixed", 'query' => "array", 'time' => "array"])]
    public function searchElasticScout(Request $request): array
    {
        $startTime = microtime(true);
        $data = $request->all();
        $originalKeyword = $data['keyword'] ?? "";
        $keyword = $this->formatKeywordElastic($originalKeyword);
        if (empty($keyword)) {
            $words = $this->getPaginatedWords();
        } else {
            /* @var Paginator $words */
            /* @var Builder $search */
            $search = Word::search($keyword);
            $words = $search->must(new Matching('message', $keyword))
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
    private function formatKeywordElastic($kw): string|array
    {
        $kw = strip_tags(trim($kw));
        $kw = str_replace(' ', '_', $kw);
        $kw = preg_replace('/[^A-Za-z0-9_]/', '', $kw);
        $kw = preg_replace('/_+/', '_', $kw);
        return str_replace('_', ' ', $kw);
    }
}
