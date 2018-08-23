<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Offer;
use App\Models\Recipient;
use App\Models\Voucher;
use Carbon\Carbon;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class VoucherController extends Controller
{

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function generateVouchers(Request $request, Response $response, $args)
    {
        // checks to ensure we have valid inputs
        $validator = $this->c->validator->validate($request, [
            'offer_id' => Validator::intVal()->notBlank(),
            'expiry_date' => Validator::date()->notBlank()
        ]);

        if ($validator->isValid()) {
            $offer = Offer::find($request->getParam('offer_id'));

            if($offer){

                $recipients = Recipient::all();

                foreach ($recipients as $recipient){

                    $voucher_model = new Voucher();

                    $input = array();
                    $input['recipient_id'] = $recipient->id;
                    $input['offer_id'] = $offer->id;

                    $expiry = $request->getParam('expiry_date');
                    $input['expires_at'] = is_string($expiry) ? Carbon::parse($expiry) : $expiry;


                    $voucher_model->create($input);

                }

                $vouchers = Voucher::all();

                return $response->withStatus(201)->withJson([
                    'success' => true,
                    'count'  => count($vouchers->toArray()),
                    'data'     => $vouchers->toArray()
                ]);


            }else{

                return $response->withStatus(400)->withJson([
                    'success' => false,
                    'message' => 'Invalid Offer'
                ]);
            }


        } else {
            // return an error on failed validation, with a statusCode of 400
            return $response->withStatus(400)->withJson([
                'success' => false,
                'message' => $validator->getErrors()
            ]);
        }
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function validateVoucher(Request $request, Response $response, $args)
    {
        $validator = $this->c->validator->validate($request, [
            'code' => Validator::alnum()->notBlank(),
            'email' => Validator::email()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {

            $voucher    = $request->getParam('code');
            $email      = $request->getParam('email');

            $voucher_model    =   new Voucher();
            $user_model       =   new Recipient();

            // check if recipient exist
            $recipient_details     =   $user_model->findEmail($email);

            if ($recipient_details) {
                // Assertain that the voucher code belongs to the user and has not expired/not yet used
                $validate_voucher =   $voucher_model->validateVoucher($voucher, $recipient_details->id);

                if (!$validate_voucher->isEmpty()) {
                    // activate and set date voucher was used
                    $activate_voucher   =   $voucher_model->activateVoucher($voucher, $recipient_details->id);
                    // return voucher details
                    return $response->withStatus(200)->withJson([
                        'success'    => true,
                        'data'      => $validate_voucher
                    ]);
                } else {
                    // return failure message if voucher does not exist
                    return $response->withStatus(403)->withJson([
                        'success' => false,
                        'message' => 'Voucher code is invalid'
                    ]);
                }
            } else {
                // return failure message if user does not exist
                return $response->withStatus(400)->withJson([
                    'success' => false,
                    'message' => 'Recipient does not exist'
                ]);
            }
        } else {
            // return failure message if validation fails
            return $response->withStatus(400)->withJson([
                'success' => false,
                'message' => $validator->getErrors()
            ]);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function getRecipientVouchers(Request $request, Response $response, $args)
    {
        $validator = $this->c->validator->validate($request, [
            'email' => Validator::email()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {

            $email = $request->getQueryParam('email');

            $voucher_model    =   new Voucher();
            $recipient_model       =   new Recipient();

            //check if user exist
            $recipient     =   $recipient_model->findEmail($email);

            if ($recipient) {

                //Fetch all valid user voucher codes
                $recipient_vouchers =   $voucher_model->fetchRecipientVouchers($recipient->id);

                //return voucher details
                return $response->withStatus(200)->withJson([
                    'success' => true,
                    'count'     => count($recipient_vouchers),
                    'data'     => $recipient_vouchers
                ]);

            } else {
                //return failure message if user does not exist
                return $response->withStatus(400)->withJson([
                    'success' => false,
                    'message' => 'User does not exist'
                ]);
            }
        } else {
            return $response->withStatus(400)->withJson([
                'success' => false,
                'message' => $validator->getErrors()
            ]);
        }
    }


}