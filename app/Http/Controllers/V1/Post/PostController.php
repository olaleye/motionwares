<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use Illuminate\Http\Request;
use GuzzleHttp;
use GuzzleHttp\Client;


class PostController extends Controller
{
    private $headers;
    private $guzzle;

    public function __construct()
    {
        $this->headers = [
            'Accept' => 'application/json',
        ];
        $this->guzzle = new Client([
            'base_uri' => "https://www.instagram.com",
            'headers' => $this->headers,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]
        ]);
    }

    /**
     * @param int $limit
     */
    public function show(int $limit = 20)
    {
        try {
            $data = $this->guzzle->get('/explore/tags/nodejs/?__a=1');
            $result = GuzzleHttp\json_decode($data->getBody());
            $edges = $this->getEdgesByLimit($result->graphql->hashtag->edge_hashtag_to_media->edges, $limit);
            return response()->json($edges, 200);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return GuzzleHttp\json_decode($e->getResponse()->getBody());
        };
    }

    /**
     * @param array $edges
     * @param int $int
     */
    private function getEdgesByLimit(array $edges, int $limit):array
    {
        $result = [];
        $counter = $limit;
        foreach ($edges as $edge) {
            if($counter == 0){
                return $result;
            }
            array_push($result, $edge);
            $counter--;
        }
    }
}
