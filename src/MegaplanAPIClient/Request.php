<?php

namespace MegaplanAPIClient;

class Request
{
    const CONTENT_TYPE_XML = 'application/xml';
    const CONTENT_TYPE_JSON = 'application/json';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /** @var int URI запроса */
    protected $uri;
    /** @var string Формат запроса и ответа */
    protected $contentType = self::CONTENT_TYPE_JSON;
    /** @var string Данные запроса в виде строки */
    protected $data;
    /** @var string Метод запроса */
    protected $method = self::METHOD_GET;
    /** @var \DateTime Дата и время запроса */
    protected $date;
    /** @var array Параметры запроса */
    protected $params;
    /** @var boolean Индикатор использования https */
    protected $https = false;
    /** @var string Путь к файлу, который будет записан всё содержимое ответа */
    protected $outputFile = null;

    public function __construct( $uri, $params = array() )
    {
        $this->uri = $uri;
        $this->params = $params;
        $this->date = new \DateTime();
    }

    /**
     * @param string $data
     */
    public function setData( $data )
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $format
     */
    public function setContentType( $format )
    {
        $this->contentType = $format;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $method
     */
    public function setMethod( $method )
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param int $uri
     */
    public function setUri( $uri )
    {
        $this->uri = $uri;
    }

    /**
     * @return int
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate( \DateTime $date )
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDateAsString()
    {
        return $this->date->format( 'r' );
    }

    /**
     * @param boolean $https
     */
    public function setHttps( $https )
    {
        $this->https = $https;
    }

    /**
     * @return boolean
     */
    public function getHttps()
    {
        return $this->https;
    }

    /**
     * @param string $outputFile
     */
    public function setOutputFile( $outputFile )
    {
        $this->outputFile = $outputFile;
    }

    /**
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * Собирает строку запроса из URI и параметров
     * @param string $uri URI
     * @param array $params Параметры запроса (хэш-массив)
     * @return string
     */
	public function formatUri( $uri, array $params = null )
	{
		$part = parse_url( $uri );

		if ( ! preg_match( "/\.[a-z]+$/u", $part['path'] ) ) {
			$part['path'] .= '.' . ( $this->getContentType() == self::CONTENT_TYPE_JSON ? 'api' : 'xml' );
		}

        $uri = $part['path'];

		if ( $params )
		{
			if ( ! empty( $part['query'] ) ) {
				parse_str( $part['query'], $params );
			}
            $uri .= '?'.http_build_query( $params );
		}
		elseif ( ! empty( $part['query'] ) ) {
            $uri .= '?' . $part['query'];
		}

		return $uri;
	}

    public function getContentMD5()
    {
        return $this->getData() ? md5( $this->getData() ) : '';
    }

}