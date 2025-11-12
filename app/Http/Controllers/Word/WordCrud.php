<?php

namespace App\Http\Controllers\Word;

use App\Jobs\RemoveElasticWordJob;
use App\Models\Word;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait WordCrud
{
    /**
     * Get a validator for an incoming posting request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
            'message' => 'required|string|max:2550',
            'price' => 'required|integer',
            'tags' => 'nullable|string|max:255',
            ]
        );
    }

    /**
     * @param  $id
     * @param  Request $request
     * @return array
     * @throws \Exception
     */
    #[ArrayShape(['count' => "mixed"])]
    public function ajaxDeleteItem($id, Request $request): array
    {
        if ($id == null || !$request->ajax()) {
            throw new NotFoundHttpException();
        }
        $word = Word::query()->where(
            [
            'user_id' => Auth::user()->id,
            'id' => $id
            ]
        )->first();
        $deletedCount = $word->delete();
        if ($deletedCount) {
            if (env('ELASTICSEARCH_ENABLED')) {
                dispatch(new RemoveElasticWordJob($word));
            }
            Word::cacheTags();
        }

        return [
            'count' => $deletedCount
        ];
    }

    /**
     * @param  $id
     * @param  Request $request
     * @return array
     */
    #[ArrayShape(['html' => "string"])]
    public function ajaxGetItem($id, Request $request): array
    {
        if ($id == null || !$request->ajax()) {
            throw new NotFoundHttpException();
        }
        $word = Word::query()->where(
            [
            'user_id' => Auth::user()->id,
            'id' => $id
            ]
        )
            ->first();

        $word['tags'] = tagsToViewString($word['tags']);
        $form = view('element.handbook.edit-item', ['data' => $word])->render();

        return [
            'html' => $form
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function ajaxUpdateItem(Request $request): array
    {
        if (!$request->ajax()) {
            throw new NotFoundHttpException();
        }

        $queryData = $request->all();
        $data = [
            'user_id' => Auth::user()->id,
            'id' => $queryData['id'] ?? "",
            'message' => strip_tags($queryData['message'] ?? "", '<br>'),
            'price' => $queryData['price'] ?? 1,
            'tags' => tagsToDbString($queryData['tags'] ?? null),
        ];

        $this->validator($data)->validate();

        /* @var Word $word */
        $word = Word::query()
            ->where(
                [
                'user_id' => Auth::user()->id,
                'id' => $data['id']
                ]
            )
            ->first();

        if (empty($word)) {
            return [
                'error' => 'Error! Item not found.'
            ];
        }

        if ($word->update($data)) {
            $user = User::query()->where('id', '=', $word['user_id'])->first();
            $word['tags'] = tagsToArray($word['tags']);
            $word['short_date'] = date_create($word['created_at'])->format('M, d Y');
            $word['username'] = $user['name'] ?? "";
            $viewNames = [
                'takeword' => 'element.takeword.takeword-item',
                'handbook' => 'element.handbook.item',
                'search' => 'element.search.search-item',
            ];
            Word::cacheTags();
            return [
                'html' => view(
                    $viewNames[$queryData['from'] ?? 'takeword'],
                    ['data' => $word]
                )->render()
            ];
        }

        return [
            'error' => "There is error when update this word."
        ];
    }
}
