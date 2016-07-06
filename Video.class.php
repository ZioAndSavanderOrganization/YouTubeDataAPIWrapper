<?php
include_once 'YouTube.class.php';
include_once 'Channel.class.php';

class Video extends YouTubeFactory{

    /**
     * Video.list
     */
    private $VideoID;
    private $isVideoValid = false;

    /**
     * Video.statistics
     */
    private $VStatsViewCount;
    private $VStatsLikeCount;
    private $VStatsDislikeCount;
    private $VStatsFavoriteCount;
    private $VStatsCommentCount;

    /**
     * Video.snippet
     */
    private $VSnippetTitle;
    private $VSnippetDescription;
    private $VSnippetThumbnails = array();
    private $VSnippetTags = array();
    private $VSnippetPublishedAt;


    /**
     * Set VideoID
     * @param $req
     */
    public function __construct($req)
    {
        if($this->isValid($req))
            $this->getInformations();
        else
            return false;
    }

    /**
     * Insert statistics to variables
     */
    private function getInformations()
    {
        $apiRequest = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&fields=items(id%2Csnippet(description%2CpublishedAt%2Ctags%2Cthumbnails(default%2Chigh%2Cmedium)%2Ctitle)%2Cstatistics)%2CpageInfo%2FtotalResults&id=" .$this->VideoID. "&key=" . parent::getKey()) ,true);

        $this->VSnippetDescription	                = 	$apiRequest['items'][0]['snippet']['description'];
        $this->VSnippetTitle    	                = 	$apiRequest['items'][0]['snippet']['title'];
        $this->VSnippetTags     	                = 	$apiRequest['items'][0]['snippet']['tags'];
        $this->VSnippetThumbnails['default'] 	    =  	$apiRequest['items'][0]['snippet']['thumbnails']['default']['url'];
        $this->VSnippetThumbnails['medium']         =  	$apiRequest['items'][0]['snippet']['thumbnails']['medium']['url'];
        $this->VSnippetThumbnails['high']           =  	$apiRequest['items'][0]['snippet']['thumbnails']['high']['url'];
        $this->VSnippetPublishedAt                  =   $apiRequest['items'][0]['snippet']['publishedAt'];

        $this->VStatsCommentCount                   =   $apiRequest['items'][0]['statistics']['commentCount'];
        $this->VStatsViewCount                      =   $apiRequest['items'][0]['statistics']['viewCount'];
        $this->VStatsDislikeCount                   =   $apiRequest['items'][0]['statistics']['dislikeCount'];
        $this->VStatsLikeCount                      =   $apiRequest['items'][0]['statistics']['likeCount'];
        $this->VStatsFavoriteCount                  =   $apiRequest['items'][0]['statistics']['favoriteCount'];
    }

    public function isValid($Request = null)
    {
        if($Request === null)
            return $this->isVideoValid;

        $this->VideoID = $Request;
        $getJson = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=id&fields=pageInfo%2FtotalResults&id=" .$this->VideoID. "&key=" . parent::getKey()) ,true);
        if($getJson['pageInfo']['totalResults'] == 0) {
            $this->isVideoValid = false;
            return false;
        } else {
            $this->isVideoValid = true;
            return true;
        }

    }

    /**
     * Return Video ID
     * @return mixed
     */
    public function getVideoID()
    {
        if($this->isValid())
            return $this->VideoID;
    }

    /**
     * Return Publish date
     * @return mixed
     */
    public function getPublishedAt()
    {
        if($this->isValid())
            return $this->VSnippetPublishedAt;
    }

    /**
     * Return Title
     * @return mixed
     */
    public function getTitle()
    {
        if($this->isValid())
            return $this->VSnippetTitle;
    }

    /**
     * Return Description
     * @return mixed
     */
    public function getDescription()
    {
        if($this->isValid())
            return $this->VSnippetDescription;
    }


    /**
     * Return video tags
     * @return array
     */
    public function getTags()
    {
        if($this->isValid())
            return $this->VSnippetTags;
    }



    public function getViewCount()
    {
        if($this->isValid())
            return $this->VStatsViewCount;
    }

    public function getLikeCount()
    {
        if($this->isValid())
            return $this->videoLikeCount;
    }

    public function getDislikeCount()
    {
        if($this->isValid())
            return $this->videoDislikeCount;
    }

    public function getCommentCount()
    {
        if($this->isValid())
            return $this->VStatsCommentCount;
    }

    public function getFavoriteCount()
    {
        if($this->isValid())
            return $this->VStatsFavoriteCount;
    }

    /**
     * Return thumbnail url
     * @param $type [high, medium, default] or [0,1,2]
     * @return mixed
     */
    public function getThumbnailUrl($type = 'default')
    {
        if($this->isValid())
            switch($type){
                case 'default':
                case 0:
                    return $this->VSnippetThumbnails['default'];
                case 'medium':
                case 1:
                    return $this->VSnippetThumbnails['medium'];
                case 'high':
                case 2:
                    return $this->VSnippetThumbnails['high'];
                default:
                    return $this->VSnippetThumbnails['default'];
            }
    }
}

