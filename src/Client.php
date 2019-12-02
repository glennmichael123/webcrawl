<?php

class Client {

    const USER_HTTP = 'gtorregosa@gmail.com';
    const PASS_HTTP = 'q4froiza';

    private $curl;

    function __construct()
    {
        if($this->curl == null)
        {
            $this->curl = new Curl();
        }
    }

    public function login()
    {   
        // Curl execution to get login page
        $response = $this->curl->execute('http://nabepero.xyz/login');

        // Use simple_html_dom to parse html data
        $dom = new simple_html_dom();
        $dom->load($response);

        // Get token by scraping it from the login html page
        $token = $dom->find('input[name=_token]',0)->value;
        $user  = array(
            "email"    => self::USER_HTTP,
            "password" => self::PASS_HTTP,
            "_token"   => $token,
        );

        // Prepare the curl for the login credentials
        $this->curl->setPost($user);
        $this->curl->setHTTP(self::USER_HTTP, self::PASS_HTTP);

        // Execute curl for logging in
        $this->curl->execute('http://nabepero.xyz/login');

        // Go to the crawler page to scrape the table
        $res = $this->curl->execute('http://nabepero.xyz/crawler');
        $dom->load($res);

        return $this->getDataHtml($dom);
    }

    public function getDataHtml($htmlDom) {
        // Get the html table to be scraped
        $table = $htmlDom->find('#mainTable', 0);

        $tableData = array();
        $headers   = array();

        // Iterate through table header columns
        foreach($table->find('th') as $head) {
            $headers[] = $head->innertext;
        }
        
        // Iterate through table row
        foreach($table->find('tr') as $k => $row) {

            $rowData = array();
            $date = '';

            // Iterate through table date
            foreach($row->find('td') as $key => $cell) {
                if($headers[$key] == 'date') {
                    // Make date as index key so that the dates could be grouped
                    $date = $cell->innertext;
                }

                $ky = $key;
                // Insert table date with table header columns as keys
                $rowData[$headers[$key]] = $cell->innertext;
            }

            // Check if $rowData doesn't have empty data
            $tableData[$date][] = $rowData;
        }

        // Return table data compact with headers
        return compact('tableData', 'headers');
    }

    public function getDataJSON() {
       // Get JSON data from crawl API
       $res = $this->curl->execute('http://nabepero.xyz/crawler/api');

       // Convert JSON to associative array
       $decode    = json_decode($res, true);
       $tableData = array();
       $headers   = array();
       foreach ($decode as $rows) {

           $date = '';
           $rowData = array();
           foreach ($rows as $key => $value) {
                // Make date as index key so that the dates could be grouped, same as above
                if($key == 'date'){
                    $date = $value;
                }
                $rowData[$key] = $value;
            }

            $tableData[$date][] = $rowData;
       }


       // Finally close the session
       $this->curl->closeSession();

       // Only return tableData because headers is just the same
       return $tableData;
    }
}