<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Word\WordCrud;
use App\Http\Traits\ElasticScoutSearchTrait;
use App\Http\Traits\FulltextSearchTrait;
use App\Http\Traits\ManualSearchTrait;
use App\Http\Traits\ScoutSearchTrait;
use App\Models\UserTookWord;
use App\Models\Word;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TakeWordController extends Controller
{
    use WordCrud;
    use ManualSearchTrait;
    use FulltextSearchTrait;
    use ScoutSearchTrait;
    use ElasticScoutSearchTrait;

    protected int $itemPerPage = 10;
    protected string $redirectTo = '/takeword';

    /**
     * User see the list of words and take words that they like.
     *
     * @return Response
     * @throws Exception
     */
    public function index()
    {
        $words = $this->getPaginatedWords();
        $limitTags = Word::getCachedTags();

        return view('takeword', ['words' => $words, 'limitTags' => $limitTags]);
    }

    /**
     * @param  Request $request
     * @return Response
     * @throws Exception
     */
    public function search(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->index();
        }
        $result = $this->searchWordWeight($request);
        $limitTags = Word::getCachedTags();

        return view(
            'takeword',
            [
            'words' => $result['words'],
            'query' => $result['query'],
            'time' => $result['time'],
            'limitTags' => $limitTags
            ]
        );
    }

    /**
     * Load more words via ajax
     *
     * @param  Request $request
     * @return array
     */
    public function loadMore(Request $request): array
    {
        if ($request->ajax()) {
            $result = [];
            if (isset($request['keyword']) && !empty(trim($request['keyword']))) {
                $action = $request['action'] ?? '';
                switch ($action) {
                    case 'search_scout':
                        $search = $this->searchScout($request);
                        break;
                    case 'search_fulltext':
                        $search = $this->searchFulltext($request);
                        break;
                    case 'search_elastic':
                        $search = $this->searchElasticScout($request);
                        break;
                    default:
                        $search = $this->searchWordWeight($request);
                        break;
                }
                $words = $search['words'];
                $result['keyword'] = $search['query']['keyword'];
            } else {
                $words = $this->getPaginatedWords();
            }

            $html = "";
            foreach ($words as $word) {
                $html .= view(
                    'element.takeword.takeword-item',
                    ['data' => $word]
                )->render();
            }
            $result['html'] = $html;
            $result['hasMore'] = $words->hasMorePages();

            return $result;
        }
        return [];
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

    /**
     * @param  Request $request
     * @return Response
     * @throws Exception
     */
    public function takeThisWord(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->index();
        }

        if (Auth::user()) {
            try {
                $data = $request->all();
                $validMan = Validator::make(
                    $data,
                    [
                    'word_id' => 'required|integer',
                    ]
                );
                $validMan->validate();

                $userTookWord = UserTookWord::query()
                    ->createOrFirst(
                        [
                        'user_id' => Auth::user()->id,
                        'word_id' => $data['word_id'],
                        ],
                        ['user_id', 'word_id']
                    );
                if ($request->hasHeader('referer')) {
                    return redirect($request->header('referer'));
                } else {
                    return redirect($this->redirectTo);
                }
            } catch (\Throwable $exception) {
                return redirect($this->redirectTo, 500, ['data' => $data]);
            }
        } else {
            return redirect('login');
        }
    }
}
