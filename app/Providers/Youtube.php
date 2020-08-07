<?php
namespace App\Providers;

class Youtube
{

    /**
     * @var string
     */
    protected $youtube_key; // from the config file

    /**
     * @var array
     */
    public $APIs = [
        'search.list' => 'https://www.googleapis.com/youtube/v3/search',
    ];

    /**
     * @var array
     */
    public $page_info = [];

    /**
     * @var array
     */
    protected $config = [];

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
        $this->config['use-http-host'] = isset($config['use-http-host']) ? $config['use-http-host'] : false;
    }

    /**
     * @param $setting
     * @return Youtube
     */
    public function useHttpHost($setting)
    {
        $this->config['use-http-host'] = !!$setting;

        return $this;
    }

    /**
     * @param $key
     * @return Youtube
     */
    public function setApiKey($key)
    {
        $this->youtube_key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->youtube_key;
    }

    /**
     * Generic Search interface, use any parameters specified in
     * the API reference
     *
     * @param $params
     * @param $pageInfo
     * @return array
     * @throws \Exception
     */
    public function searchAdvanced($params)
    {
        $API_URL = $this->getApi('search.list');

        if (empty($params) || (!isset($params['q']) && !isset($params['channelId']) && !isset($params['videoCategoryId']))) {
            throw new \InvalidArgumentException('at least the Search query or Channel ID or videoCategoryId must be supplied');
        }

        $apiData = $this->api_get($API_URL, $params);

        return $this->decodeList($apiData);
    }
    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */

    /**
     * @param $name
     * @return mixed
     */
    public function getApi($name)
    {
        return $this->APIs[$name];
    }

    /**
     * Decode the response from youtube, extract the list of resource objects
     *
     * @param  string $apiData response string from youtube
     * @throws \Exception
     * @return array Array of StdClass objects
     */
    public function decodeList(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        } else {
            $this->page_info = [
                'resultsPerPage' => $resObj->pageInfo->resultsPerPage,
                'totalResults' => $resObj->pageInfo->totalResults,
                'kind' => $resObj->kind,
                'etag' => $resObj->etag,
                'prevPageToken' => null,
                'nextPageToken' => null,
            ];

            if (isset($resObj->prevPageToken)) {
                $this->page_info['prevPageToken'] = $resObj->prevPageToken;
            }

            if (isset($resObj->nextPageToken)) {
                $this->page_info['nextPageToken'] = $resObj->nextPageToken;
            }

            $itemsArray = $resObj->items;
            if (!is_array($itemsArray) || count($itemsArray) == 0) {
                return false;
            } else {
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
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function api_get($url, $params)
    {
        //set the youtube key
        $params['key'] = $this->youtube_key;

        //boilerplates for CURL
        $tuCurl = curl_init();

        if (isset($_SERVER['HTTP_HOST']) && $this->config['use-http-host']) {
            curl_setopt($tuCurl, CURLOPT_HEADER, array('Referer' => $_SERVER['HTTP_HOST']));
        }

        curl_setopt($tuCurl, CURLOPT_URL, $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params));
        if (strpos($url, 'https') === false) {
            curl_setopt($tuCurl, CURLOPT_PORT, 80);
        } else {
            curl_setopt($tuCurl, CURLOPT_PORT, 443);
        }

        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) {
            throw new \Exception('Curl Error : ' . curl_error($tuCurl));
        }

        return $tuData;
    }
}
