<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookRepository;
use App\Eloquent\Book;
use App\Filter\BookFilters;
use App\Eloquent\BookUser;
use App\Eloquent\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class BookRepositoryEloquent extends AbstractRepositoryEloquent implements BookRepository
{
    public function model()
    {
        return new \App\Eloquent\Book;
    }

    public function getDataInHomepage($with = [], $dataSelect = ['*'])
    {
        $limit = config('paginate.book_home_limit');

        return [
            [
                'key' => config('model.filter_books.latest.key'),
                'title' => translate('title_key.' . config('model.filter_books.latest.key')),
                'data' => $this->getLatestBooks($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => config('model.filter_books.view.key'),
                'title' => translate('title_key.' . config('model.filter_books.view.key')),
                'data' => $this->getBooksByCountView($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => config('model.filter_books.rating.key'),
                'title' => translate('title_key.' . config('model.filter_books.rating.key')),
                'data' => $this->getBooksByRating($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => config('model.filter_books.waiting.key'),
                'title' => translate('title_key.' . config('model.filter_books.waiting.key')),
                'data' => $this->getBooksByWaiting($with, $dataSelect, $limit)->items(),
            ],
        ];
    }

    public function getDataSearch(array $attribute, $with = [], $dataSelect = ['*'])
    {
        $sortField = 'created_at';
        $sortType = 'desc';

        if (isset($attribute['sort']['field']) && $attribute['sort']['field']) {
            $sortField = config('model.sort_field')[$attribute['sort']['field']];
        }

        if (isset($attribute['sort']['order_by']) && $attribute['sort']['order_by']) {
            $sortType = $attribute['sort']['order_by'];
        }

        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->where(function ($query) use ($attribute) {
                if (isset($attribute['conditions']) && $attribute['conditions']) {
                    foreach ($attribute['conditions'] as $conditions) {
                        foreach ($conditions as $type => $typeIds) {
                            if (in_array($type, config('model.filter_type')) && count($typeIds)) {
                                $query->whereIn($type . '_id', $typeIds);
                            }
                        }
                    }
                }
                if (isset($attribute['search']['keyword']) && $attribute['search']['keyword']) {
                    $query->where(function ($query) use($attribute) {
                        if (isset($attribute['search']['field']) && $attribute['search']['field']) {
                            $query->where($attribute['search']['field'], 'LIKE', '%' . $attribute['search']['keyword'] . '%');
                        } else {
                            foreach (config('model.book.fields') as $field) {
                                $query->where($field, 'LIKE', '%' . $attribute['search']['keyword'] . '%');
                            }
                        }
                    });
                }
            })
            ->orderBy($sortField, $sortType)
            ->paginate(config('paginate.default'));
    }

    protected function getLatestBooks($with = [], $dataSelect = ['*'], $limit = '')
    {
        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData('created_at')
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByCountView($with = [], $dataSelect = ['*'], $limit = '')
    {
        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData('count_view')
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByRating($with = [], $dataSelect = ['*'], $limit = '')
    {
        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData('avg_star')
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByWaiting($with = [], $dataSelect = ['*'], $limit = '')
    {
        $numberOfUserWaitingBook = \DB::table('books')
            ->join('book_user', 'books.id', '=', 'book_user.book_id')
            ->select('book_user.book_id', \DB::raw('count(book_user.user_id) as count_waiting'))
            ->where('book_user.status', Book::STATUS['waiting'])
            ->groupBy('book_user.book_id')
            ->orderBy('count_waiting', 'DESC')
            ->limit($limit ?: config('paginate.default'))
            ->get();

        $books = $this->model()
            ->select($dataSelect)
            ->with($with)
            ->whereIn('id', $numberOfUserWaitingBook->pluck('book_id')->toArray())
            ->paginate($limit ?: config('paginate.default'));

        foreach ($books->items() as $book) {
            $book->count_waiting = $numberOfUserWaitingBook->where('book_id', $book->id)->first()->count_waiting;
        }

        return $books;
    }

    public function getBooksByFields($with = [], $dataSelect = ['*'], $field)
    {
        switch ($field) {
            case 'view':
                return $this->getBooksByCountView($with, $dataSelect);

            case 'latest':
                return $this->getLatestBooks($with, $dataSelect);

            case 'rating':
                return $this->getBooksByRating($with, $dataSelect);

            case 'waiting':
                return $this->getBooksByWaiting($with, $dataSelect);
        }
    }

    public function booking(Book $book, array $attributes)
    {
        $bookUpdate = array_only($attributes['item'], app(BookUser::class)->getFillable());
        $checkUser = $book->users()->find($this->user->id);

        if ($checkUser && $checkUser->pivot->status == config('model.book_user.status.reading')) {
            $book->update(['status' => config('model.book.status.available')]);
            $book->userReadingBook()->updateExistingPivot($this->user->id, ['status' => config('model.book_user.status.done')]);
        } else {
            $userWaiting = $book->users()
                ->where('user_id', '<>', $this->user->id)
                ->count();

            if ($userWaiting) {
                if (!$checkUser) {
                    $book->users()->attach($this->user->id, [
                        'user_id' => $this->user->id,
                        'book_id' => $bookUpdate['book_id'],
                        'status' => config('model.book_user.status.waiting')
                    ]);
                } else {
                    $book->users()->updateExistingPivot($this->user->id, ['status' => config('model.book_user.status.waiting')]);
                }
            } else {
                $book->users()->updateExistingPivot($this->user->id, ['book_user.status' => config('model.book_user.status.reading')]);
                $book->where('id', $bookUpdate['book_id'])->update(['status' => config('model.book.status.unavailable')]);
            }
        }
    }

}
