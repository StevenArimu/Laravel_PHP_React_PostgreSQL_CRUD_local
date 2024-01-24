<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class Book extends Model
{
    use Notifiable, HasFactory;

    protected $connection = "pgsql";
    protected $table = "books";
    protected $primaryKey = "_id";
    protected $fillable = [
        '_id',
        'title',
        'author',
        'genre',
        'description',
        'ISBN',
        'year',
        'counts',
        'pages',
    ];
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->validateUniqueISBN();
        });
        // static::updating(function ($model) {
        //     $model->validateUniqueISBN();
        // });
        static::saving(function ($model) {
            $model->validateUniqueISBN();
        });
    }

    /**
     * Validate unique ISBN.
     */
    public function validateUniqueISBN()
    {
        $rules = [
            'ISBN' => [
                'required',
                'unique:books,ISBN',
            ],
        ];

        $validator = Validator::make($this->attributes, $rules);

        if ($validator->fails()) {
            abort(422, $validator->errors()->first());
        }
    }
}
