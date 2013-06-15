<?php
namespace Spoolphiz\Events\Interfaces;

interface CrmRepository {
	
	public function getContact($billingInfo, $shippingInfo);
	
}