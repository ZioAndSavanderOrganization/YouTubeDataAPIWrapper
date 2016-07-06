<?php

class YouTubeFactory{


    /**
     * Google API key.
     * Change it to yourself, most important thing.
     */
    private $apiKey = "AIzaSyC9hS-ZdTDtLdxUdHptqv_z2EU5EcMflMQ";

    /**
     * Get Google API key, for child-classes
     * @return string
     */
    protected function getKey()
    {
        return $this->apiKey;
    }
    /**
     * Preventing
     */
    protected function __construct(){}
    protected function __clone(){}
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}