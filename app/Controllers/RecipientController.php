<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Recipient;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class RecipientController extends Controller
{

    public function index(Request $request, Response $response)
    {
        $recipients = Recipient::all();

        return $response->withStatus(200)->withJson([
            'success' => true,
            'count'    => count($recipients->toArray()),
            'data'     => $recipients->toArray()
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function store(Request $request, Response $response, $args)
    {
        // checks to ensure we have valid inputs
        $validator = $this->c->validator->validate($request, [
            'name' => Validator::alnum()->notBlank(),
            'email' => Validator::email()->notBlank(),
        ]);

        if ($validator->isValid()) {
            $recipient_model = new Recipient();

            // Create new recipient
            $created_recipient = $recipient_model->create($request);

            if ($created_recipient) {

                return $response->withStatus(201)->withJson([
                    'success' => true,
                    'data'     => $created_recipient
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

}