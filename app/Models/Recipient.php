<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email'
    ];

    /**
     * Prevents insertion with id
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function create($request)
    {

        $created_recipient = self::firstOrCreate([
            'name'          => $request->getParam('name'),
            'email'    => $request->getParam('email'),
        ]);

        return $created_recipient;
    }

    public static function findEmail($email)
    {
        return static::where('email', $email)->first();
    }

}