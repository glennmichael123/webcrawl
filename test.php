<?php

class Client {

const USER_HTTP = 'fritzgeralddumdum7@gmail.com';
const PASS_HTTP = 'y1lix2w6';

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
$res = $this->curl->execute('http://nabepero.xyz/login');
$this->curl->closeSession();
$dom = new simple_html_dom();
$dom->load($res);
$token = $dom->find('input[name=_token]', 0)->value;

$this->curl = new Curl();
$this->curl->setPost(array(
'email' => Client::USER_HTTP,
'password' => Client::PASS_HTTP,
'_token' => $token
));
$this->curl->setHTTP(Client::USER_HTTP, Client::PASS_HTTP);
$res = $this->curl->execute('http://nabepero.xyz/login');
$this->curl->closeSession();

$this->curl = new Curl();
$res = $this->curl->execute('http://nabepero.xyz/crawler');
$dom->load($res);

return $this->getDataHtml($dom);
}

public function getDataHtml($htmlDom) {

/// TODO: Implement getDataHtml() method.
$table = $htmlDom->find('table[id=mainTable]', 0);

// initialize empty array to store the data array from each row
$theData = array();
$headers = array();
foreach($table->find('th') as $head) {
$headers[] = $head->innertext;
}

// loop over rows
foreach($table->find('tr') as $k => $row) {

// initialize array to store the cell data from each row
$rowData = array();
foreach($row->find('td') as $key => $cell) {

// push the cell's text to the array
$rowData[$headers[$key]]= $cell->innertext;
}

// push the row's data array to the 'big' array
$theData[] = $rowData;
}

$data = array_shift($theData);
return $data;

}

public function getDataJSON() {
// TODO: Implement getDataJSON() method.
}
}