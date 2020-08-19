<?php
namespace App\Providers;

class Youtubeapi
{

    /**
     * @var string
     */
    protected $youtube_key; // from the config file

    /**
     * @var array
     */
    public $API_URL = 'https://www.googleapis.com/youtube/v3/search';

    /**
     * Constructor
     * $youtube = new Youtube(['key' => 'KEY HERE'])
     *
     * @param string $key
     * @throws \Exception
     */
    public function __construct($key, $config = [])
    {
        if (is_string($key) && !empty($key)) {
            $this->youtube_key = $key;
        } else {
            throw new \Exception('Google API key is Required, please visit https://console.developers.google.com/');
        }
    }

    /**
     * Search video based on params
     *
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function searchVideos($params)
    {
        if (empty($params) || !isset($params['q'])) {
            throw new \InvalidArgumentException('Params or search query are missing');
        }

        $apiData = $this->getResults($params);
        if (isset($apiData->error)) {
            throw new \Exception($apiData->error->message);
        } else {
            $itemsArray = $apiData->items;
            if (!is_array($itemsArray) || count($itemsArray) == 0) {
                return array();
            }

            $results = array();
            $i = 0;
            foreach ($itemsArray as $item) {
                $results[$i]['id'] = $item->id->videoId;
                $results[$i]['title'] = html_entity_decode($item->snippet->title);
                $results[$i]['img'] = $item->snippet->thumbnails->medium->url;
                $i++;
            }

            return $results;
        }
    }

    /**
     * Get response from CURL 
     *
     * @param $params
     * @return object
     * @throws \Exception
     */
    public function getResults($params)
    {
        $apikey = $this->youtube_key;
        $googleApiUrl = $this->API_URL . '?part=snippet&q=' . $params['q'] . '&maxResults=' . $params['maxResults'] . '&key=' . $apikey;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Curl Error : ' . curl_error($ch));
        }

        curl_close($ch);
        $decoded_response = json_decode($response);

        return $decoded_response;
    }
}
