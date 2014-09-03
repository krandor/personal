<?php
class HttpRequest
{
  // Request mode, 0 - use CURL, 1 - use SOCKETS
  var $mode = 0;
  var $timeout = -1;
  function HttpRequest($mode = 0, $timeout = -1)
  {
    $this->mode = ($mode == 0 && function_exists('curl_init') ? 0 : 1);
    $this->timeout = $timeout;
  }

  function _connect($host, $port)
  {
    $errno = null;
    $errstr = null;
    //print "Connecting... ({$this->timeout})<br>";
    $hf = fsockopen($host, $port ? $port : 80, $errno, $errstr, $this->timeout);
    return $hf;
  }
  
  function _disconnect($hs)
  {
    fclose($hs);
  }
  
  function request($url, $post_data = false)
  {
    switch ($this->mode)
    {
    case 0:
      return $this->_requestCurl($url, $post_data);
    case 1:
      return $this->_requestSock($url, $post_data);
    default:
      return false;
    };
  }
  
  function _requestCurl($url, $post_data)
  {
    $hc = curl_init($url);
    if ($post_data)
      curl_setopt($hc, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($hc, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($hc, CURLOPT_CONNECTTIMEOUT, $this->timeout);
    $res = curl_exec($hc);
    $this->httpStatus = curl_getinfo($hc, CURLINFO_HTTP_CODE);
    curl_close($hc);
    return $res;
  }
  
  function _requestSock($url, $post_data)
  {
    $info = parse_url($url);
    $httpHostStr = $info['host'];
    if ($info['port'])
      $httpHostStr .= ':' . $info['port'];
    if (!empty($post_data))
    {
      $rtype = 'POST';
      $post = array();
      foreach ($post_data as $key => $val)
      {
        $post[] = urlencode($key) . '=' . urlencode($val);
      };
      $post = implode('&', $post);
      $contentLength = strlen($post);
      $contentType = "Content-Type: application/x-www-form-urlencoded\n";
    }
    else
    {
      $rtype = 'GET';
      $post = '';
      $contentLength = 0;
      $contentType = '';
    };
    $req = <<<EOR
{$rtype} {$url} HTTP/1.0
Host: {$httpHostStr}
User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.10) Gecko/20071115 Firefox/2.0.0.10
Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8
Accept-Language: en
{$contentType}Content-Length: {$contentLength}

$post
EOR;
    //print "SENDING REQUEST: *** \n $req \n***\n";
    $hc = $this->_connect($info['host'], $info['port']);
    if (!$hc)
    {
      trigger_error("Failed to connect to [{$info['host']}]", E_USER_WARNING);
      return false;
    };
    //socket_write($hc, $req, strlen($req));
    fwrite($hc, $req);
    $res = '';
    while (!feof($hc))
      $res .= fread($hc, 8192);
    $this->_disconnect($hc);
    if (preg_match('/^HTTP\/[^\s]+\s+(\d+)/', $res, $match))
    {
      $this->httpStatus = $match[1];
    }
    else
    {
      $this->httpStatus = 666;
      return false;
    };
    if (preg_match('/^.+?(?:\r\n\r\n|\n\n)(.*)$/ms', $res, $match))
    {
      //print_r($match);
      return $match[1];
    };
    $this->httpStatus = 666;
    return false;
  }
};

/*
$req = new HttpRequest(0);
print $req->request('http://www.local.net/test2.php?g1=get1',Array('p1'=>'post1', 'p2' => 'post2'));
print "\n\nRequest finished with status: {$req->httpStatus}\n";
*/ 
?>