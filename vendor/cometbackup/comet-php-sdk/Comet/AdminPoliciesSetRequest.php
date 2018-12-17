<?php

/**
 * Copyright (c) 2018 Comet Licensing Ltd.
 * Please see the LICENSE file for usage information.
 * 
 * SPDX-License-Identifier: MIT
 */

namespace Comet;

/** 
 * Comet Server AdminPoliciesSet API 
 * Update an existing policy object
 * An optional hash may be used, to ensure the modification was atomic.
 * This API can also be used to create a new policy object with a specific hash.
 * 
 * You must supply administrator authentication credentials to use this API.
 * This API requires the Auth Role to be enabled.
 */
class AdminPoliciesSetRequest implements \Comet\NetworkRequest {
	
	/**
	 * The policy ID to update or create
	 *
	 * @var string
	 */
	protected $PolicyID = null;
	
	/**
	 * The policy data
	 *
	 * @var \Comet\GroupPolicy
	 */
	protected $Policy = null;
	
	/**
	 * An atomic verification hash as supplied by the AdminPoliciesGet API (optional)
	 *
	 * @var string|null
	 */
	protected $CheckPolicyHash = null;
	
	/**
	 * Construct a new AdminPoliciesSetRequest instance.
	 *
	 * @param string $PolicyID The policy ID to update or create
	 * @param \Comet\GroupPolicy $Policy The policy data
	 * @param string $CheckPolicyHash An atomic verification hash as supplied by the AdminPoliciesGet API (optional)
	 */
	public function __construct($PolicyID, GroupPolicy $Policy, $CheckPolicyHash = null)
	{
		$this->PolicyID = $PolicyID;
		$this->Policy = $Policy;
		$this->CheckPolicyHash = $CheckPolicyHash;
	}
	
	/**
	 * Get the URL where this POST request should be submitted to.
	 *
	 * @return string
	 */
	public function Endpoint()
	{
		return '/api/v1/admin/policies/set';
	}
	
	/**
	 * Get the POST parameters for this request.
	 *
	 * @return string[]
	 */
	public function Parameters()
	{
		$ret = [];
		$ret["PolicyID"] = (string)($this->PolicyID);
		$ret["Policy"] = $this->Policy->toJSON();
		if ($this->CheckPolicyHash !== null) {
			$ret["CheckPolicyHash"] = (string)($this->CheckPolicyHash);
		}
		return $ret;
	}
	
	/**
	 * Decode types used in a response to this request.
	 * Use any network library to make the request.
	 *
	 * @param int $responseCode HTTP response code
	 * @param string $body HTTP response body
	 * @return \Comet\APIResponseMessage 
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
		
		// Parse as CometAPIResponseMessage
		$ret = \Comet\APIResponseMessage::createFromStdclass($decoded);
		
		return $ret;
	}
	
}
