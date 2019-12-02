<?php
/*
 * Curl Class
 */
class Curl {

    /**
     * @var $ch
     */
    private $ch;

    /**
     * @const
     */
    const COOKIE_PATH = '/tmp/cookie';

    /**
     * The contents of the "User-Agent: " header to be used in a HTTP request.
     * @var string
     */
    private $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6';

    /**
     * Curl constructor.
     */
    function __construct()
    {
        if($this->ch == null)
        {
            $this->ch = curl_init();
        }
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setPost($params = array())
    {
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);

        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setHTTP($username = '', $password = '')
    {
        curl_setopt($this->ch,CURLOPT_HTTPAUTH, constant('CURLAUTH_ANY'));
        curl_setopt($this->ch,CURLOPT_USERPWD, $username . ':' . $password);

        return $this;
    }

    /**
     * Execute the request & return the results
     * @param $url
     * @return mixed
     * @throws Exception
     */
    public function execute($url)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION,TRUE);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Uncomment this code so that cookie will be saved in a txt file
        $cookieFile = dirname(__FILE__).self::COOKIE_PATH;
        curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $cookieFile);

        $response = curl_exec ($this->ch);
        if ($response === FALSE)
        {
            curl_close($this->ch);
            throw new \Exception("Opps, An error has occurred while trying scrape data!",500);
        }
        return $response;
    }

    /**
     * close a session
     */
    public function closeSession()
    {
        curl_close($this->ch);
    }
} 