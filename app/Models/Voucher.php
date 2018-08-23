<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'offer_id',
        'recipient_id',
        'expires_at',
        'used_at'
    ];

    /**
     * Prevents insertion with id
     *
     * @var array
     */
    protected $guarded = ['id'];


    public function create(array $inputs)
    {

        $offer_id = $inputs['offer_id'];
        $recipient_id = $inputs['recipient_id'];
        $expires_at = $inputs['expires_at'];

        $created_voucher = self::firstOrCreate([
            'code'          => strtoupper(substr(md5(rand()), 0, 8)),
            'offer_id'    => $offer_id,
            'recipient_id' => $recipient_id,
            'expires_at' => $expires_at
        ]);

        return $created_voucher;

    }


    // Assertain that the voucher code belongs to the user and has not expired/not yet used
    public function validateVoucher($voucher, $recipient_id)
    {
        $voucher_details = self::leftjoin('recipients', 'vouchers.recipient_id', '=', 'recipients.id')
            ->leftjoin('offers', 'vouchers.offer_id', '=', 'offers.id')
            ->select('vouchers.code', 'recipients.id as recipient_id', 'recipients.email', 'vouchers.expires_at','offers.name as offer_name','offers.discount as percentage_discount')
            ->where([
                ['vouchers.code', $voucher],
                ['vouchers.recipient_id', $recipient_id],
                ['vouchers.is_used', 0],
                ['vouchers.expires_at', '>', \Carbon\Carbon::now()],
            ])
            ->get();

        return ($voucher_details == null ? [] : $voucher_details);
    }

    // activate voucher code, set is_used and date_used fields
    public function activateVoucher($voucher, $recipient_id)
    {
        $activate_voucher = self::where([
            ['code', $voucher],
            ['recipient_id', $recipient_id],
        ])
            ->update(array('is_used' => 1, 'used_at' => \Carbon\Carbon::now() ));

        return $activate_voucher;

    }

    public function fetchRecipientVouchers($recipient_id)
    {
        $voucher_details = self::leftjoin('recipients', 'vouchers.recipient_id', '=', 'recipients.id')
            ->leftjoin('offers', 'vouchers.offer_id', '=', 'offers.id')
            ->select('vouchers.code','recipients.id as recipient_id', 'recipients.email', 'vouchers.expires_at','offers.name as offer_name','offers.discount as percentage_discount')

            ->where([
                ['vouchers.recipient_id', $recipient_id],
                ['vouchers.is_used', 0],
                ['vouchers.expires_at', '>',  \Carbon\Carbon::now()],
            ])
            ->get();

        return ($voucher_details == null ? [] : $voucher_details);

    }


}