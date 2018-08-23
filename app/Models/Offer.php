<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'discount'
    ];

    /**
     * Prevents insertion with id
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function create($request)
    {

        $created_offer = self::firstOrCreate([
            'name'          => $request->getParam('name'),
            'slug'    => str_slug($request->getParam('name')),
            'discount'      => $request->getParam('discount')

        ]);

        return $created_offer;
    }

}