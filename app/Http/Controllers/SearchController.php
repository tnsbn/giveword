<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Word\WordCrud;
use App\Http\Traits\ElasticScoutSearchTrait;
use App\Http\Traits\FulltextSearchTrait;
use App\Http\Traits\ScoutSearchTrait;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class SearchController extends Controller
{
    use WordCrud;
    use FulltextSearchTrait;
    use ScoutSearchTrait;
    use ElasticScoutSearchTrait;

    protected int $itemPerPage = 10;
    protected string $redirectTo = '/takeword';

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function indexFulltext(Request $request)
    {
        if ($request->isMethod('get')) {
            $words = $this->getPaginatedWords();
            $limitTags = Word::getCachedTags();

            return view('takeword', [
                'words' => $words,
                'limitTags' => $limitTags,
                'action' => 'search_fulltext',
            ]);
        } elseif ($request->isMethod('post')) {
            $result = $this->searchFulltext($request);
            $limitTags = Word::getCachedTags();

            return view(
                'takeword',
                [
                    'words' => $result['words'],
                    'query' => $result['query'],
                    'time' => $result['time'],
                    'limitTags' => $limitTags,
                    'action' => 'search_fulltext',
                    'searchName' => 'Fulltext search',
                ]
            );
        }
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function indexScout(Request $request)
    {
        if ($request->isMethod('get')) {
            $words = $this->getPaginatedWords();
            $limitTags = Word::getCachedTags();

            return view('takeword', [
                'words' => $words,
                'limitTags' => $limitTags,
                'action' => 'search_scout',
            ]);
        } elseif ($request->isMethod('post')) {
            $result = $this->searchScout($request);
            $limitTags = Word::getCachedTags();

            return view(
                'takeword',
                [
                    'words' => $result['words'],
                    'query' => $result['query'],
                    'time' => $result['time'],
                    'limitTags' => $limitTags,
                    'action' => 'search_scout',
                    'searchName' => 'Scout with database',
                ]
            );
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function indexElastic(Request $request)
    {
        if ($request->isMethod('get')) {
            $words = $this->getPaginatedWords();
            $limitTags = Word::getCachedTags();

            return view('takeword', [
                'words' => $words,
                'limitTags' => $limitTags,
                'action' => 'search_elastic',
            ]);
        } elseif ($request->isMethod('post')) {
            $result = $this->searchElasticScout($request);
            $limitTags = Word::getCachedTags();

            return view(
                'takeword',
                [
                    'words' => $result['words'],
                    'query' => $result['query'],
                    'time' => $result['time'],
                    'limitTags' => $limitTags,
                    'action' => 'search_elastic',
                    'searchName' => 'Elastic search',
                ]
            );
        }
    }

    /**
     * @return mixed
     */
    public function getPaginatedWords(): mixed
    {
        /* @var Paginator $words */
        $words = Word::query()
            ->with('userTookWord')
            ->orderBy('created_at', 'desc')
            ->paginate($this->itemPerPage);
        return Word::addPaginateDetail($words);
    }
}
