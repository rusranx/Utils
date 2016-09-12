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

	private
		$ch,

		$head = '',
		$response = '',
		$cookie = [],

		$url = '',
		$isPost = false,
		$userAgent = 'Mozilla/5.0 (Windows NT 5.1; rv:23.0) Gecko/20100101 Firefox/23.0',
		$encoding = 'deflate',
		$followLocation = 1,
		$returnTransfer = 1,
		$sslVerifyPeer = 0,
		$sslVerifyHost = 2,
		$verbose = 0,
		$autoReferer = 0,
		$timeout = 0,
		$connectTime = 0,

		$postFields = null,
		$httpHeader = [],
		$httpGet = '',
		$proxy = '',
		$proxyUserPwd = '',
		$referer = '',
		$writeHeader = '',
		$sslCert = '',
		$sslKey = '',
		$caInfo = '',
		$cookieFile = '',
		$interface = '';


	function __construct()
	{
		$this->ch = curl_init();
		$this->setHttpHeader(['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3', 'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7']);
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
		$this->postFields = $postparams;

		return $this->exec();
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

	function joinCookie()
	{
		$result = [];
		foreach ($this->cookie as $key => $value)
			$result[] = "$key=$value";

		return join('; ', $result);
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
		$this->setCookie = [];
	}

	function clearHttpheader()
	{
		$this->httpHeader = [];
	}

	function getUrl()
	{
		return curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
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

	function exec()
	{
		curl_setopt_array($this->ch, [
			CURLOPT_USERAGENT      => $this->userAgent,
			CURLOPT_AUTOREFERER    => $this->autoReferer,
			CURLOPT_ENCODING       => $this->encoding,
			CURLOPT_URL            => $this->url,
			CURLOPT_POST           => $this->isPost,
			CURLOPT_FOLLOWLOCATION => $this->followLocation,
			CURLOPT_RETURNTRANSFER => $this->returnTransfer,
			CURLOPT_SSL_VERIFYPEER => $this->sslVerifyPeer,
			CURLOPT_SSL_VERIFYHOST => $this->sslVerifyHost,
			CURLOPT_HEADER         => 1,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_CONNECTTIMEOUT => $this->connectTime,
			CURLOPT_VERBOSE        => $this->verbose
		]);

		if ($this->referer)
			curl_setopt($this->ch, CURLOPT_REFERER, $this->referer);

		if ($this->interface)
			curl_setopt($this->ch, CURLOPT_INTERFACE, $this->interface);

		if ($this->httpGet)
			curl_setopt($this->ch, CURLOPT_HTTPGET, $this->httpGet);

		if ($this->writeHeader != '')
			curl_setopt($this->ch, CURLOPT_WRITEHEADER, $this->writeHeader);

		if ($this->isPost) {
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postFields);
		}

		if ($this->proxy)
			curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);

		if ($this->proxyUserPwd)
			curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $this->proxyUserPwd);

		if ($this->cookie)
			curl_setopt($this->ch, CURLOPT_COOKIE, $this->joinCookie());

		if (count($this->httpHeader))
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpHeader);

		if ($this->sslCert)
			curl_setopt($this->ch, CURLOPT_SSLCERT, $this->sslCert);

		if ($this->sslKey)
			curl_setopt($this->ch, CURLOPT_SSLKEY, $this->sslKey);

		if ($this->caInfo)
			curl_setopt($this->ch, CURLOPT_CAINFO, $this->caInfo);

		if ($this->cookieFile) {
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieFile);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		}

		$response = curl_exec($this->ch);
		$this->head = substr($response, 0, curl_getinfo($this->ch, CURLINFO_HEADER_SIZE));
		$this->response = substr($response, curl_getinfo($this->ch, CURLINFO_HEADER_SIZE));
		$this->setCookie();

		$this->postFields = null;
		$this->isPost = false;

		return $this->response;
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

	/**
	 * @return string
	 */
	public function getHead()
	{
		return $this->head;
	}

	/**
	 * @return string
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @return string
	 */
	public function getInterface()
	{
		return $this->interface;
	}

	/**
	 * @param string $interface
	 */
	public function setInterface($interface)
	{
		$this->interface = $interface;
	}

	/**
	 * @return string
	 */
	public function getCookieFile()
	{
		return $this->cookieFile;
	}

	/**
	 * @param string $cookieFile
	 */
	public function setCookieFile($cookieFile)
	{
		$this->cookieFile = $cookieFile;
	}

	/**
	 * @return string
	 */
	public function getCaInfo()
	{
		return $this->caInfo;
	}

	/**
	 * @param string $caInfo
	 */
	public function setCaInfo($caInfo)
	{
		$this->caInfo = $caInfo;
	}

	/**
	 * @return string
	 */
	public function getSslKey()
	{
		return $this->sslKey;
	}

	/**
	 * @param string $sslKey
	 */
	public function setSslKey($sslKey)
	{
		$this->sslKey = $sslKey;
	}

	/**
	 * @return string
	 */
	public function getSslCert()
	{
		return $this->sslCert;
	}

	/**
	 * @param string $sslCert
	 */
	public function setSslCert($sslCert)
	{
		$this->sslCert = $sslCert;
	}

	/**
	 * @return string
	 */
	public function getWriteHeader()
	{
		return $this->writeHeader;
	}

	/**
	 * @param string $writeHeader
	 */
	public function setWriteHeader($writeHeader)
	{
		$this->writeHeader = $writeHeader;
	}

	/**
	 * @return string
	 */
	public function getReferer()
	{
		return $this->referer;
	}

	/**
	 * @param string $referer
	 */
	public function setReferer($referer)
	{
		$this->referer = $referer;
	}

	/**
	 * @return string
	 */
	public function getProxyUserPwd()
	{
		return $this->proxyUserPwd;
	}

	/**
	 * @param string $proxyUserPwd
	 */
	public function setProxyUserPwd($proxyUserPwd)
	{
		$this->proxyUserPwd = $proxyUserPwd;
	}

	/**
	 * @return string
	 */
	public function getProxy()
	{
		return $this->proxy;
	}

	/**
	 * @param string $proxy
	 */
	public function setProxy($proxy)
	{
		$this->proxy = $proxy;
	}

	/**
	 * @return string
	 */
	public function getHttpGet()
	{
		return $this->httpGet;
	}

	/**
	 * @param string $httpGet
	 */
	public function setHttpGet($httpGet)
	{
		$this->httpGet = $httpGet;
	}

	/**
	 * @return array
	 */
	public function getHttpHeader()
	{
		return $this->httpHeader;
	}

	/**
	 * @param array $httpHeader
	 */
	public function setHttpHeader($httpHeader)
	{
		$this->httpHeader = $httpHeader;
	}

	/**
	 * @return null|array|string[]
	 */
	public function getPostFields()
	{
		return $this->postFields;
	}

	/**
	 * @return int
	 */
	public function getConnectTime()
	{
		return $this->connectTime;
	}

	/**
	 * @param int $connectTime
	 */
	public function setConnectTime($connectTime)
	{
		$this->connectTime = $connectTime;
	}

	/**
	 * @return int
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * @param int $timeout
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * @return int
	 */
	public function getAutoReferer()
	{
		return $this->autoReferer;
	}

	/**
	 * @param int $autoReferer
	 */
	public function setAutoReferer($autoReferer)
	{
		$this->autoReferer = $autoReferer;
	}

	/**
	 * @return int
	 */
	public function getVerbose()
	{
		return $this->verbose;
	}

	/**
	 * @param int $verbose
	 */
	public function setVerbose($verbose)
	{
		$this->verbose = $verbose;
	}

	/**
	 * @return int
	 */
	public function getSslVerifyHost()
	{
		return $this->sslVerifyHost;
	}

	/**
	 * @param int $sslVerifyHost
	 */
	public function setSslVerifyHost($sslVerifyHost)
	{
		$this->sslVerifyHost = $sslVerifyHost;
	}

	/**
	 * @return int
	 */
	public function getSslVerifyPeer()
	{
		return $this->sslVerifyPeer;
	}

	/**
	 * @param int $sslVerifyPeer
	 */
	public function setSslVerifyPeer($sslVerifyPeer)
	{
		$this->sslVerifyPeer = $sslVerifyPeer;
	}

	/**
	 * @return int
	 */
	public function getReturnTransfer()
	{
		return $this->returnTransfer;
	}

	/**
	 * @param int $returnTransfer
	 */
	public function setReturnTransfer($returnTransfer)
	{
		$this->returnTransfer = $returnTransfer;
	}

	/**
	 * @return string
	 */
	public function getEncoding()
	{
		return $this->encoding;
	}

	/**
	 * @param string $encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 * @return int
	 */
	public function getFollowLocation()
	{
		return $this->followLocation;
	}

	/**
	 * @param int $followLocation
	 */
	public function setFollowLocation($followLocation)
	{
		$this->followLocation = $followLocation;
	}

	/**
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->userAgent;
	}

	/**
	 * @param string $userAgent
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = $userAgent;
	}
}

?>