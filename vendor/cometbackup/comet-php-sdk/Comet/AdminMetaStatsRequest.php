<?php

/**
 * Copyright (c) 2018 Comet Licensing Ltd.
 * Please see the LICENSE file for usage information.
 * 
 * SPDX-License-Identifier: MIT
 */

namespace Comet;

/** 
 * Comet Server AdminMetaStats API 
 * Get Comet Server historical statistics
 * The returned key-value map is not necessarily ordered. Client-side code should sort the result before display.
 * 
 * You must supply administrator authentication credentials to use this API.
 */
class AdminMetaStatsRequest implements \Comet\NetworkRequest {
	
	/**
	 * Remove redundant statistics
	 *
	 * @var boolean
	 */
	protected $Simple = null;
	
	/**
	 * Construct a new AdminMetaStatsRequest instance.
	 *
	 * @param boolean $Simple Remove redundant statistics
	 */
	public function __construct($Simple)
	{
		$this->Simple = $Simple;
	}
	
	/**
	 * Get the URL where this POST request should be submitted to.
	 *
	 * @return string
	 */
	public function Endpoint()
	{
		return '/api/v1/admin/meta/stats';
	}
	
	/**
	 * Get the POST parameters for this request.
	 *
	 * @return string[]
	 */
	public function Parameters()
	{
		$ret = [];
		$ret["Simple"] = (string)($this->Simple);
		return $ret;
	}
	
	/**
	 * Decode types used in a response to this request.
	 * Use any network library to make the request.
	 *
	 * @param int $responseCode HTTP response code
	 * @param string $body HTTP response body
	 * @return \Comet\StatResult[] An array with int keys. 
	 * @throws \Exception
	 */
	public static function ProcessResponse($responseCode, $body)
	{
		// Require expected HTTP 200 response
		if ($responseCode !== 200) {
			throw new \Exception("Unexpected HTTP " . intval($responseCode) . " response");
		}
		
		// Decode JSON
		$decoded = \json_decode($body); // as stdClass
		if (\json_last_error() != \JSON_ERROR_NONE) {
			throw new \Exception("JSON decode failed: " . \json_last_error_msg());
		}
		
		// Try to parse as error format
		$isCARMDerivedType = (($decoded instanceof \stdClass) && property_exists($decoded, 'Status') && property_exists($decoded, 'Message'));
		if ($isCARMDerivedType) {
			$carm = \Comet\APIResponseMessage::createFromStdclass($decoded);
			if ($carm->Status !== 200) {
				throw new \Exception("Error " . $carm->Status . ": " . $carm->Message);
			}
		}
		
		// Parse as map[int64]StatResult
		$val_0 = [];
		foreach($decoded as $k_0 => $v_0) {
			$phpk_0 = (int)($k_0);
			$phpv_0 = \Comet\StatResult::createFromStdclass($v_0);
			$val_0[$phpk_0] = $phpv_0;
		}
		$ret = $val_0;
		
		return $ret;
	}
	
}

