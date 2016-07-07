<?php
include_once 'YouTube.class.php';
class Channel extends YouTubeFactory{

    /**
     * Channel.list
     */
    private $ChannelID;
    private $isChannelValid = false;
    /**
     * Channel.list.snippet
     */
    private $CSnippetTitle;
    private $CSnippetDescription;
    private $CSnippetCustomUrl = false;
    private $CSnippetPublishedAt;
    private $CSnippetAvatars = array();
    private $CSnippetBanner;
    private $CSnippetCountry = false;
    private $CSnippetKeywords = false;

    /**
     * Channel.list.statistics
     */
    private $CStatsViewCount;
    private $CStatsCommentCount;
    private $CStatsSubsCount;
    private $CStatsSubsHidden;
    private $CStatsVideoCount;


    public function __construct($req)
    {
        if($this->isValid($req)) {
            $this->getInformations();
        }
        else
            return false;
    }

    /**
     * Insert statistics to variables
     */
    private function getInformations()
    {

        $apiRequest = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/channels?part=statistics%2Csnippet%2CbrandingSettings&fields=items(brandingSettings(channel%2Fkeywords%2Cimage)%2Cid%2Csnippet(country%2CcustomUrl%2Cdescription%2CpublishedAt%2Cthumbnails(default%2Chigh%2Cmedium)%2Ctitle)%2Cstatistics)%2CpageInfo%2FtotalResults&id=" .$this->ChannelID. "&key=" . parent::getKey()) ,true);

        $this->CSnippetTitle			        = 	$apiRequest['items'][0]['snippet']['title'];
        $this->CSnippetDescription 		        = 	$apiRequest['items'][0]['snippet']['description'];
        $this->CSnippetPublishedAt 		        = 	strtotime($apiRequest['items'][0]['snippet']['publishedAt']);
        $this->CSnippetAvatars['default'] 	    =  	$apiRequest['items'][0]['snippet']['thumbnails']['default']['url'];
        $this->CSnippetAvatars['medium']        =  	$apiRequest['items'][0]['snippet']['thumbnails']['medium']['url'];
        $this->CSnippetAvatars['high']          =  	$apiRequest['items'][0]['snippet']['thumbnails']['high']['url'];
        $this->CSnippetBanner                   =  	$apiRequest['items'][0]['brandingSettings']['image']['bannerImageUrl'];
        if(isset($apiRequest['items'][0]['brandingSettings']['keywords']))
            $this->CSnippetKeywords             =  	$apiRequest['items'][0]['brandingSettings']['keywords'];
        $this->CStatsSubsCount 	                = 	$apiRequest['items'][0]['statistics']['subscriberCount'];
        $this->CStatsSubsHidden 	            = 	$apiRequest['items'][0]['statistics']['hiddenSubscriberCount'];
        $this->CStatsViewCount   		        = 	$apiRequest['items'][0]['statistics']['viewCount'];
        $this->CStatsVideoCount 		        = 	$apiRequest['items'][0]['statistics']['videoCount'];
        $this->CStatsCommentCount 		        =	$apiRequest['items'][0]['statistics']['commentCount'];
        if(isset($apiRequest['items'][0]['snippet']['country']))
            $this->CSnippetCountry              =   $apiRequest['items'][0]['snippet']['country'];
        if(isset($apiRequest['items'][0]['snippet']['customUrl']))
            $this->CSnippetCustomUrl            =   $apiRequest['items'][0]['snippet']['customUrl'];
    }

    /**
     * Check if channel even exists.
     * @param null $Request
     * @return bool
     */
    public function isValid($Request = null)
    {
        if($Request === null)
            return $this->isChannelValid;

        if (preg_match("/^UC/", $Request)) {
            $this->ChannelID = $Request;
            $getJson = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/channels?part=id&id=" . $Request . "&key=" . parent::getKey()), true);

            if($getJson['pageInfo']['totalResults'] == 0){
                $this->isChannelValid = false;
                return false;
            }
            else{
                $this->isChannelValid = true;
                return true;
            }
        } else {
            $getJson = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/channels?part=id&forUsername=" . $Request . "&key=" . parent::getKey()), true);

            if($getJson['pageInfo']['totalResults'] == 0){
                $this->isChannelValid = false;
                return false;
            }
            else{
                $this->ChannelID = $getJson['items'][0]['id'];
                $this->isChannelValid = true;
                return true;
            }
        }
    }

    /**
     * Return ChannelID
     * @return mixed
     */
    public function getChannelID()
    {
        return $this->ChannelID;
    }

    /**
     * Return view count
     * @return mixed
     */
    public function getViewCount()
    {
        if($this->isValid())
            return $this->CStatsViewCount;
    }

    /**
     * Return subscriber count
     * @return mixed
     */
    public function getSubsCount()
    {
        if($this->isValid())
            return $this->CStatsSubsCount;
    }

    /**
     * Return video count
     * @return mixed
     */
    public function getVideoCount()
    {
        if($this->isValid())
            return $this->CStatsVideoCount;
    }

    /**
     * Check if subs are hidden
     * @return bool
     */
    public function isSubsHidden()
    {
        if($this->isValid())
            if($this->CStatsSubsHidden)
                return true;
            return false;
    }

    /**
     * Get comment count
     * @return mixed
     */
    public function getCommentCount()
    {
        if($this->isValid())
            return $this->CStatsCommentCount;
    }


    /**
     * Return Channel Title
     * @return mixed
     */
    public function getTitle()
    {
        if($this->isValid())
            return $this->CSnippetTitle;
    }

    /**
     * Return description
     * @return mixed
     */
    public function getDescription()
    {
        if($this->isValid())
            return $this->getDescription();
    }

    /**
     * Return CustomURL or false if not exists.
     * @return bool
     */
    public function getCustomUrl()
    {
        if($this->isValid())
            if($this->CSnippetCustomUrl)
                return $this->CSnippetCustomUrl;
            return false;
    }

    /**
     * Return country or false if not exists.
     * @return bool
     */
    public function getCountry()
    {
        if($this->isValid())
            if($this->CSnippetCountry)
                return $this->CSnippetCountry;
            else
                return false;
    }

    /**
     * Return date of account registration
     * @return mixed
     */
    public function getPublishedAt()
    {
        if($this->isValid())
            return $this->CSnippetPublishedAt;
    }

    /**
     * Return avatar url
     * @param $type [high, medium, default] or [0,1,2]
     */
    public function getAvatarUrl($type = 'default')
    {
        if($this->isValid())
            switch($type){
                case 'default':
                case 0:
                    return $this->CSnippetAvatars['default'];
                case 'medium':
                case 1:
                    return $this->CSnippetAvatars['medium'];
                case 'high':
                case 2:
                    return $this->CSnippetAvatars['high'];
                default:
                    return $this->CSnippetAvatars['default'];
            }
    }

    /**
     * Return banner url
     * @return mixed
     */
    public function getBannerUrl()
    {
        if($this->isValid())
            return $this->CSnippetBanner;
    }

    /**
     * Return keywords
     * @return mixed
     */
    public function getKeywords()
    {
        if($this->isValid())
            if($this->CSnippetKeywords)
                return true;
            return false;
    }


    /**
     * Return ID of latest video.
     * @return mixed
     */
    public function getLatestVideoId()
    {
        if($this->isValid())
            if($this->getVideoCount() > 0) {
                $videoId = file_get_contents("https://www.googleapis.com/youtube/v3/search?order=date&part=id&fields=items(id)&maxResults=1&channelId=" . $this->ChannelID . "&key=" . parent::getKey());
                return json_decode($videoId, true)['items'][0]['id']['videoId'];
            }
    }
}



