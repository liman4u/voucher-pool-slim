<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Offer;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class OfferController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function index(Request $request, Response $response)
    {
        $offers = Offer::all();

        return $response->withStatus(200)->withJson([
            'success' => true,
            'count'   => count($offers->toArray()),
            'data'    => $offers->toArray()
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
            'discount' => Validator::intVal()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {
            $offer_model = new Offer();

            // Create new offer
            $created_offer = $offer_model->create($request);

            if ($created_offer) {

                return $response->withStatus(201)->withJson([
                    'success' => true,
                    'data'     => $created_offer
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