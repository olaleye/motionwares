<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use Google\Cloud\Core\ServiceBuilder;
use GuzzleHttp;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

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
            $edges = $this->getPostAndSentimentsByLimit($result->graphql->hashtag->edge_hashtag_to_media->edges, $limit);

            return response()->json($edges, 200);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return GuzzleHttp\json_decode($e->getResponse()->getBody());
        };
    }

    /**
     * @param array $edges
     * @param int $int
     * @return array
     */
    private function getPostAndSentimentsByLimit(array $edges, int $limit): array
    {
        $result = [];
        $counter = $limit;
        foreach ($edges as $edge) {
            if ($counter == 0) {
                return $result;
            }
            $post = $edge->node->edge_media_to_caption->edges[0]->node;
            $sentiment = $this->getSentiment($post->text);
            array_push($result, $sentiment);
            $counter--;
        }
    }

    /**
     * @param string $text
     * @return array
     */
    private function getSentiment(string $text): array
    {
        $path = File::get(storage_path('keys/key.json'));

        #instantiates a service builder
        $cloud = new ServiceBuilder([
            'projectId' => 'stellar-state-283508',
            'keyFilePath' => storage_path('keys/key.json')
        ]);

        $language = $cloud->language();

        # Detects the sentiment of the text
        $annotation = $language->analyzeSentiment($text);
        $sentiment = $annotation->sentiment();

        return [
            'post' => $text,
            'sentimentScore' => $sentiment['score'],
            'sentimentMagnitude' => $sentiment['magnitude']
        ];
    }
}
