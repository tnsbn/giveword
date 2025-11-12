<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Pagination\Paginator;

class TagController extends Controller
{
    protected array $itemPerPage = [
        'ipp5' => 5,
        'ipp10' => 10,
        'ipp15' => 15,
        'ipp20' => 20,
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index($tagName)
    {
        $ipp = 'ipp' . request('ipp');
        $ipp = ($this->itemPerPage[$ipp] ?? null) ? $ipp : 'ipp5';

        /* @var Paginator $words */
        $words = Word::query()
            ->where('tags', 'like', "%," . $tagName . ",%")
            ->orderBy('created_at', 'desc')
            ->paginate($this->itemPerPage[$ipp]);
        $words = Word::addPaginateDetail($words);

        $limitTags = Word::getCachedTags();

        return view(
            'tag',
            [
            'words' => $words,
            'tagName' => $tagName,
            'limitTags' => $limitTags
            ]
        );
    }
}
