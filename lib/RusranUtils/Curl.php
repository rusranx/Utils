<?php
/*
 * Curl
 * @project RusranUtils
 * 
 * @author Stsepanchuk Ruslan
 * @author  Yuri Ashurkov (rusranx)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace RusranUtils;

use RusranUtils\Exception\MethodNotFoundException;

class Curl
{

	var $ch;
	var $httpget = '';
	var $head = '';
	var $isPost = false;
	var $postparams = null;
	var $httpheader = [];
	var $cookie = [];
	var $proxy = '';
	var $proxyUserData = '';
	var $verbose = 0;
	var $referer = '';
	var $autoreferer = 0;
	var $writeheader = '';
	var $agent = 'Mozilla/5.0 (Windows NT 5.1; rv:23.0) Gecko/20100101 Firefox/23.0';
	var $url = '';
	var $followlocation = 1;
	var $returntransfer = 1;
	var $sslVerifypeer = 0;
	var $sslVerifyhost = 2;
	var $sslcert = '';
	var $sslkey = '';
	var $cainfo = '';
	var $cookiefile = '';
	var $timeout = 0;
	var $connectTime = 0;
	var $encoding = 'deflate';
	var $interface = '';

	function __construct()
	{
		$this->ch = curl_init();
		$this->setHttpheader(['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3', 'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7']);
	}

	function get($url)
	{
		$this->url = $url;

		return $this->exec();
	}

	function post($url, $postparams = null)
	{
		$this->url = $url;
		$this->isPost = true;

		$this->postparams = $postparams;

		return $this->exec();
	}

	function setHttpget($httpget)
	{
		$this->httpget = $httpget;
	}

	function setReferer($referer)
	{
		$this->referer = $referer;
	}

	function setAutoreferer($autoreferer)
	{
		$this->autoreferer = $autoreferer;
	}

	function setUseragent($agent)
	{
		$this->agent = $agent;
	}

	function setCookie()
	{
		preg_match_all('/Set-Cookie: (.*?)=(.*?);/i', $this->head, $matches, PREG_SET_ORDER);

		for ($i = 0; $i < count($matches); $i++) {
			if ($matches[ $i ][2] == 'deleted') {
				$this->deleteCookie($matches[ $i ][1]);
			} else {
				$this->cookie[ $matches[ $i ][1] ] = $matches[ $i ][2];
			}
		}
	}

	function addCookie($cookie)
	{
		foreach ($cookie as $name => $value) {
			$this->cookie[ $name ] = $value;
		}
	}

	function deleteCookie($name)
	{
		if (isset($this->cookie[ $name ]))
			unset($this->cookie[ $name ]);
	}

	function getCookie()
	{
		return $this->cookie;
	}

	function clearCookie()
	{
		$this->cookie = [];
	}

	function setHttpheader($httpheader)
	{
		$this->httpheader = $httpheader;
	}

	function clearHttpheader()
	{
		$this->httpheader = [];
	}

	function setHead($head)
	{
		$this->head = $head;
	}

	function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	function setInterface($interface)
	{
		$this->interface = $interface;
	}

	function setWriteheader($writeheader)
	{
		$this->writeheader = $writeheader;
	}

	function setFollowlocation($followlocation)
	{
		$this->followlocation = $followlocation;
	}

	function setReturntransfer($returntransfer)
	{
		$this->returntransfer = $returntransfer;
	}

	function setSslVerifypeer($sslVerifypeer)
	{
		$this->sslVerifypeer = $sslVerifypeer;
	}

	function setSslVerifyhost($sslVerifyhost)
	{
		$this->sslVerifyhost = $sslVerifyhost;
	}

	function setSslcert($sslcert)
	{
		$this->sslcert = $sslcert;
	}

	function setSslkey($sslkey)
	{
		$this->sslkey = $sslkey;
	}

	function setCainfo($cainfo)
	{
		$this->cainfo = $cainfo;
	}

	function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	function setConnectTime($connectTime)
	{
		$this->connectTime = $connectTime;
	}

	function setCookiefile($cookiefile)
	{
		$this->cookiefile = $cookiefile;
	}

	function setProxy($proxy)
	{
		$this->proxy = $proxy;
	}

	function setProxyAuth($proxy_user_data)
	{
		$this->proxyUserData = $proxy_user_data;
	}

	function setVerbose($verbose)
	{
		$this->verbose = $verbose;
	}

	function getError()
	{
		return curl_errno($this->ch);
	}

	function getLocation()
	{
		$result = '';

		if (preg_match("/Location: (.*?)\r\n/is", $this->head, $matches)) {
			$result = end($matches);
		}

		return $result;
	}

	function getHttpCode()
	{
		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
	}

	function getSpeedDownload()
	{
		return curl_getinfo($this->ch, CURLINFO_SPEED_DOWNLOAD);
	}

	function getContentType()
	{
		return curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE);
	}

	function getUrl()
	{
		return curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
	}

	function joinCookie()
	{
		$result = [];
		foreach ($this->cookie as $key => $value)
			$result[] = "$key=$value";

		return join('; ', $result);
	}

	function exec()
	{
		curl_setopt($this->ch, CURLOPT_USERAGENT, $this->agent);
		curl_setopt($this->ch, CURLOPT_AUTOREFERER, $this->autoreferer);
		curl_setopt($this->ch, CURLOPT_ENCODING, $this->encoding);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_POST, $this->isPost);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followlocation);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, $this->returntransfer);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $this->sslVerifyhost);
		curl_setopt($this->ch, CURLOPT_HEADER, 1);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->connectTime);
		curl_setopt($this->ch, CURLOPT_VERBOSE, $this->verbose);

		if ($this->referer)
			curl_setopt($this->ch, CURLOPT_REFERER, $this->referer);

		if ($this->interface)
			curl_setopt($this->ch, CURLOPT_INTERFACE, $this->interface);

		if ($this->httpget)
			curl_setopt($this->ch, CURLOPT_HTTPGET, $this->httpget);

		if ($this->writeheader != '')
			curl_setopt($this->ch, CURLOPT_WRITEHEADER, $this->writeheader);

		if ($this->isPost) {
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postparams);
		}

		if ($this->proxy)
			curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);

		if ($this->proxyUserData)
			curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $this->proxyUserData);

		if ($this->cookie)
			curl_setopt($this->ch, CURLOPT_COOKIE, $this->joinCookie());

		if (count($this->httpheader))
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpheader);

		if ($this->sslcert)
			curl_setopt($this->ch, CURLOPT_SSLCERT, $this->sslcert);

		if ($this->sslkey)
			curl_setopt($this->ch, CURLOPT_SSLKEY, $this->sslkey);

		if ($this->cainfo)
			curl_setopt($this->ch, CURLOPT_CAINFO, $this->cainfo);

		if ($this->cookiefile) {
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookiefile);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookiefile);
		}

		$response = curl_exec($this->ch);
		$this->setHead(substr($response, 0, curl_getinfo($this->ch, CURLINFO_HEADER_SIZE)));
		$response = substr($response, curl_getinfo($this->ch, CURLINFO_HEADER_SIZE));
		$this->setCookie();

		$this->postparams = null;
		$this->isPost = false;

		return $response;
	}

	function __destruct()
	{
		curl_close($this->ch);
	}

	function camelize($input, $separator = '_')
	{
		return str_replace($separator, '', lcfirst(ucwords($input, $separator)));
	}

	function __call($name, $arguments)
	{
		switch (true) {
			case method_exists($this, $name):
				return call_user_func_array([$this, $name], $arguments);
				break;
			case method_exists($this, $newName = $this->camelize($name)):
				return call_user_func_array([$this, $newName], $arguments);
				break;
			default:
				throw new MethodNotFoundException(__CLASS__, $name);
		}
	}
}

?>