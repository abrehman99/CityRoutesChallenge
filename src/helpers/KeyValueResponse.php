<?php


/**
 * Helper class to generate key/value style responses with format freedom
 * Class KeyValueResponse
 */
class KeyValueResponse
{
    /**
     * @var array associative array that stores the key/value data for the instance
     *
     */
    private $response;

    public function __construct()
    {
        $this->response = array();
    }


    /**
     * @return false|string
     */
    public function getJSONResponse()
    {
        return json_encode($this->response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array
     */
    public function getArrResponse(){
        return $this->response;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getDataByKey($key)
    {
        return $this->response[$key];
    }


    /**
     * Add/Update
     * @param $key
     * @param $value
     */
    public function addData($key, $value)
    {
        $this->response[$key] = $value;
    }
}