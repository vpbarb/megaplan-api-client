<?php

namespace MegaplanAPIClient;

class Client
{
    /** @var string Хост */
    protected $host;
    /** @var string Идентификатор доступа */
    protected $accessId;
    /** @var string Секретный ключ */
    protected $secretKey;
    /** @var integer Таймаут соединения в секундах */
    protected $timeout = 10;

    protected $httpMethodMapping = array(
       Request::METHOD_GET => \HttpRequest::METH_GET,
       Request::METHOD_POST => \HttpRequest::METH_POST,
       Request::METHOD_PUT => \HttpRequest::METH_PUT,
       Request::METHOD_DELETE => \HttpRequest::METH_DELETE,
    );

    public function __construct( $host, $accessId, $secretKey )
    {
        $this->host = $host;
        $this->accessId = $accessId;
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getAccessId()
    {
        return $this->accessId;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function send( Request $request )
    {
        $httpRequest = new \HttpRequest();
        $httpRequest->setUrl( 'http' . ( $request->getHttps() ? 's' : '' ) . '://' . $this->getHost() . $request->formatUri() );
        $httpRequest->setMethod( $this->httpMethodMapping[$request->getMethod()] );
        $httpRequest->setBody( $request->getData() );

        $httpRequest->setOptions(
            array(
                'timeout' => $this->getTimeout(),
                'connecttimeout' => $this->getTimeout(),
                'useragent' => __CLASS__
            )
        );

        $signature = $this->getSignature( $request );
        var_dump( $signature );

        $headers = array(
            'Accept' => $request->getAccept(),
            'X-Sdf-Date' => $request->getDateAsString(),
            'X-Authorization' => $this->getAccessId() . ':' . $signature
        );

        if ( $request->getContentMD5() ) {
            $headers['Content-MD5'] = $request->getContentMD5();
        }

        $httpRequest->setHeaders( $headers );

//        var_dump( $httpRequest );

        $httpMessage = $httpRequest->send();

        $response = new Response();
        $response->setStatus( $httpMessage->getResponseStatus() );
        $response->setCode( $httpMessage->getResponseCode() );
        $response->setData( $httpMessage->getBody() );

        return $response;
    }

    /**
     * Вычисляет сигнатуру запроса
     * @param Request $request
     * @return string
     */
    private function getSignature( Request $request )
    {
        $dataToHash = $request->getMethod() . "\n" .
            $request->getContentMD5() . "\n" .
            "\n" .
            $request->getDateAsString() . "\n" .
            $this->getHost() . $request->formatUri();

        $signature = base64_encode( $this->hashHmac( 'sha1', $dataToHash, $this->getSecretKey() ) );

        return $signature;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout( $timeout )
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Клон функции hash_hmac
     * @param string $algo алгоритм, по которому производится шифрование
     * @param string $data строка для шифрования
     * @param string $key ключ
     * @param boolean $rawOutput
     * @return string
     */
	public static function hashHmac( $algo, $data, $key, $rawOutput = false )
	{
		if ( function_exists( 'hash_hmac' ) ) {
			return hash_hmac( $algo, $data, $key, $rawOutput );
		}
		$algo = strtolower( $algo );
		$pack = 'H' . strlen( $algo( 'test' ) );
		$size = 64;
		$opad = str_repeat( chr( 0x5C ), $size );
		$ipad = str_repeat( chr( 0x36 ), $size );

		if ( strlen( $key ) > $size ){
			$key = str_pad( pack( $pack, $algo( $key ) ), $size, chr( 0x00 ) );
		} else {
			$key = str_pad( $key, $size, chr( 0x00 ) );
		}

		for ( $i = 0; $i < strlen( $key ) - 1; $i++ ) {
			$opad[$i] = $opad[$i] ^ $key[$i];
			$ipad[$i] = $ipad[$i] ^ $key[$i];
		}

		$output = $algo( $opad.pack( $pack, $algo( $ipad.$data ) ) );

		return ( $rawOutput ) ? pack( $pack, $output ) : $output;
	}


}