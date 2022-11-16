<?php

namespace IS\PazarYeri\Trendyol\Helper;

Class Format
{

	/**
	 *
	 * Sınıf ayarlamalarını yapar.
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 *
	 */
	public static function initialize($query, $data)
	{
		
		if ($data === true) {
			
			$data = array();
		}

		if ($query === true) {
			$query = $data;
		}
			
		$responseList = array();
		
		foreach ($query as $key => $value) 
		{
			if (!isset($data[$key])) {
				continue;
			}

			if (isset($value['required']) && !in_array($data[$key], $value['required'] )) {
				continue;
			}

			if (isset($value['format'])) {
				$formatName = $value['format'];
				$data[$key] = self::$formatName($data[$key]);
			}
			
			is_array($data[$key]) === true 
			? $responseList[$key] = $data[$key] 
			: $responseList[$key] = self::trim($data[$key]);
			 
		}
		return $responseList;
	}

	/**
	 *
	 * Url içerisindeki özel parametleri ayıklar
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @param string $apiUrl
	 * @return array 
	 *
	 */
	public static function getUrlSpecialParameters($apiUrl)
	{
		if(preg_match_all('@\{(.*?)\}@si', $apiUrl, $output)) 
		{
			return $output[1];
		}
		return array();
	}

	/**
	 *
	 * UnixTime değerini milisaniye cinsine çevririr.
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @param int $timestamp
	 *
	 */
	public static function unixTime($timestamp)
	{
		return $timestamp * 1000;
	}

	/**
	 *
	 * Metnin başındaki ve sonundaki boşlukları siler.
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @param int $timestamp
	 *
	 */
	public static function trim($text)
	{
		
		return trim($text);
	}

}