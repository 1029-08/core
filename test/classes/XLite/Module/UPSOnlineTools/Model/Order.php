<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

/**
* Class represens an order.
*
* @package Module_UPSOnlineTools
* @access public
* @version $Id$
*/

class XLite_Module_UPSOnlineTools_Model_Order extends XLite_Model_Order
{
	var $_ups_containers = null;

	function constructor($id=null)
	{
		parent::constructor($id);
		$this->fields["ups_containers"] = base64_encode(serialize(array()));
	}

	function assignFirstShippingRate()
	{
		$rates = $this->get("shippingRates");
		$new_rate = array_shift($rates);
		$this->set("shippingMethod", $new_rate->get("shipping"));
	}

    function getCarrier()
	{
        if (!isset($this->_carrier)) {
            $carriers = $this->getCarriers();

			if ($this->get("shipping_id")) {
				$sm = new XLite_Model_Shipping();

				// return NULL if shipping method not available
				if (!$sm->find("shipping_id='".$this->get("shipping_id")."' AND enabled='1'")) {
					$this->assignFirstShippingRate();
					$this->_carrier = null;
					return "";
				}

				// return NULL if shipping class not defined
				if (!func_class_exists("Shipping_".$sm->get("class"))) {
					$this->assignFirstShippingRate();
					$this->_carrier = null;
					return "";
                }
                return $this->_carrier = $sm->get("class");
			}

			$this->_carrier = ((count($carriers) > 1) ? $this->get('shippingMethod.class') : "");
        }
        return $this->_carrier;
    }

    function getCarriers()
    {
        if (!isset($this->_carriers)) {
            $return = array();
            $rates = $this->getShippingRates();
            foreach($rates as $rate) {
                $class = $rate->get('shipping.class');
                if(!isset($return[$class]))
                    $return[$class] = $rate->get('shipping.carrier');
            }
            $this->_carriers = array();
            if (count($return) > 1) {
                $this->_carriers = $return;
            }
        }
        return $this->_carriers;
    }

    function getCarrierRates($carrier = null)
    {
        $rates = $this->getShippingRates();
        if (is_null($carrier)) $carrier = $this->getCarrier();
        if (!$carrier || !is_array($rates)) return $rates;
        foreach($rates as $k=>$rate)
            if ($carrier != $rate->get('shipping.class')) unset($rates[$k]);
        return $rates;
    }

    function calcShippingRates()
    {
		$return = parent::calcShippingRates();
        uasort($return, 'cmp_carrier_array');
        $this->_shippingRates = $return;
        return $this->_shippingRates;
    }

	function set($name, $value)
	{
		if ($name == "ups_containers") {
			$value = base64_encode(serialize((array)$value));
		}

		parent::set($name, $value);
	}

	function get($name)
	{
		$value = parent::get($name);

		if ($name == "ups_containers") {
			$value = unserialize(base64_decode($value));
			if (!is_array($value)) {
				$value = array();
			}
		}

		return $value;
	}

	function getUPSContainersFingerprint()
	{
		$raw = parent::get("ups_containers");
		return md5($raw);
	}

	function ups_online_tools_getItemsFingerprint()
    {
        if ($this->isEmpty()) {
            return false;
        }

        $result = array();
        $items = $this->get("items");
        foreach ($items as $item_idx => $item) {
            $result[] = array
            (
                $item_idx,
                $item->get("key"),
                $item->get("amount")
            );
        }

        return md5(serialize($result));
    }

	function getPackItems()
	{
		$items = array();
		$global_id = 1;
		foreach ((array)$this->get("items") as $item) {
			for ($i = 0; $i < $item->get("amount"); $i++) {
				$obj = $item->get("packItem");
				$obj->set("GlobalId", $global_id);

				$items[] = $obj;
			}

			$global_id++;
		}

		return $items;
	}

	function packOrderItems(&$failed_items)
	{
		$containers = array();

		// build list of all used packaging
		$packaging_ids = array($this->xlite->get("config.UPSOnlineTools.packaging_type"));
		foreach ((array)$this->get("items") as $item) {
			$packaging_ids[] = $item->get("product.ups_packaging");
		}
		$packaging_ids = array_unique($packaging_ids);


		// process order items
		$items = $this->get("packItems");

		$itemsProcess = array();
		$itemsSkip = array();
		$itemsFailed = array();

		$packing_algorithm = $this->xlite->get("config.UPSOnlineTools.packing_algorithm");

		// prevent execution timeout.
		if (count($items) > $this->xlite->get("config.UPSOnlineTools.packing_limit")) {
			$packing_algorithm = BINPACKING_SIMPLE_MAX_SIZE;
		}

		$is_single_container = false;
		if (in_array($packing_algorithm, array(BINPACKING_SIMPLE_FIXED_SIZE, BINPACKING_SIMPLE_MAX_SIZE))) {
			$is_single_container = true;
		}

		// Step #1:
		// try to pack all item in product-defined containers
		foreach ($packaging_ids as $packaging_id) {
			$itemsProcess = array();
			foreach ($items as $item) {
				$packaging = $item->get("packaging");

				if ($packaging == PACKAGING_TYPE_NONE) {
					$packaging = $this->xlite->get("config.UPSOnlineTools.packaging_type");
				}
				if ($packaging == $packaging_id || $is_single_container) {
					$itemsProceed[] = $item;
				} else {
					$itemsSkip[] = $item;
				}
			}

			$items = $itemsSkip;
			$itemsSkip = array();

			if (is_array($itemsProceed) && count($itemsProceed) > 0) {
				$result = $this->_packOrderItems($itemsProceed, $packing_algorithm, $packaging_id);
				$itemsFailed = array_merge($itemsFailed, $itemsProceed);
				if (is_array($result) && count($result) > 0) {
					$containers = array_merge($containers, $result);
				}

				$itemsProceed = array();
			}
		}

		$items = $itemsFailed;
		$itemsFailed = array();

		// Step #2
		// We have unpacked items,
		// try to pack with UPS module params
		if (is_array($items) && count($items) > 0) {
			$result = $this->_packOrderItems($items, null, null);
			if (is_array($result) && count($result) > 0) {
				$containers = array_merge($containers, $result);
			}
		}

		// Step #3
		// We still have items.
		// Try to put them in container with max-size Packing algorithm.
		if (is_array($items) && count($items) > 0) {
			$result = $this->_packOrderItems($items, BINPACKING_SIMPLE_MAX_SIZE, PACKAGING_TYPE_PACKAGE);
			if (is_array($result) && count($result) > 0) {
				$containers = array_merge($containers, $result);
			}
		}

		$ups_containers = "";
		if (count($items) <= 0) {
			// All items packed in containers
			$ups_containers = (array) $this->prepareUpsContainers($containers);
		} else {
			// Failed to pack some items
			$ups_containers = base64_encode(serialize(array()));
			$failed_items = $items;
		}

		$this->set("ups_containers", $ups_containers);

		if (!$this->xlite->get("PromotionEnabled")) {
			$this->update();
		}

		return $containers;
	}

	function prepareUpsContainers($containers)
	{
		$export_data = array();
		foreach ((array)$containers as $container) {
			$export_data[] = $container->export();
		}

		$container_index = 1;
		foreach ((array)$export_data as $conId=>$con) {
			$export_data[$conId]["container_id"] = $container_index++;
		}
		return $export_data;
	}

	function _packOrderItems(&$items, $ptype=null, $packaging_type=null, $extra=array())
	{
		include_once "modules/UPSOnlineTools/encoded.php";

		$ups_containers = array();

		if (is_null($ptype)) {
			$ptype = $this->xlite->get("config.UPSOnlineTools.packing_algorithm");
		}

		if (is_null($packaging_type)) {
			$packaging_type = $this->xlite->get("config.UPSOnlineTools.packaging_type");
		}

		$total_weight = 0;

		$is_additional_handling = false;
		$declared_value = 0;

		foreach ($items as $item) {
			$declared_value += $item->get("declaredValue");
			$total_weight += $item->get("weight");
		}

		// process with containers...
		switch ($ptype) {
			case BINPACKING_SIMPLE_FIXED_SIZE:
			case BINPACKING_SIMPLE_MAX_SIZE:
			default:

				if ($ptype == BINPACKING_SIMPLE_MAX_SIZE) {
					// Max size
					$_width = 0;
					$_length = 0;
					$_height = 0;

					foreach ($items as $item) {
						$_width = max($_width, $item->get("width"));
						$_length = max($_length, $item->get("length"));
						$_height = max($_height, $item->get("height"));
					}
				} else {
					// fixed-size container or unknown
					$_width = $this->xlite->get("config.UPSOnlineTools.width");
					$_length = $this->xlite->get("config.UPSOnlineTools.length");
					$_height = $this->xlite->get("config.UPSOnlineTools.height");
				}

				$weight_limit = 150; // lbs

				$container = new XLite_Module_UPSOnlineTools_Model_Container();
				$container->setDimensions($_width, $_length, $_height);
				$container->setWeightLimit($weight_limit);
				$container->setContainerType(PACKAGING_TYPE_PACKAGE); // Package type

				$ups_containers[] = $container;

				// pack items in containers
				for ($iid = 0; $iid < count($items);) {

					$item = $items[$iid];
					$item_weight = $item->get("weight");

					if ($item_weight > $weight_limit)
						return false;

					$continue = false;
					foreach ($ups_containers as $i=>$cont) {
						$c_weight = $cont->getWeight();
						$declared_value = $cont->getDeclaredValue();

						if ($c_weight + $item_weight <= $weight_limit) {
							$ups_containers[$i]->addExtraItemIds($item->get("OrderItemId"));
							$ups_containers[$i]->setWeight($c_weight + $item_weight);
							$ups_containers[$i]->setDeclaredValue($declared_value + $item->get("declaredValue"));

							if ($item->get("additional_handling")) {
								$ups_containers[$i]->setAdditionalHandling(true);
							}

							$iid++;
							$continue = true;

							break;
						}
					}

					// pack next item
					if ($continue)
						continue;

					// add new container
					$c = new XLite_Module_UPSOnlineTools_Model_Container();
					$c->setDimensions($_width, $_length, $_height);
					$c->setWeightLimit($weight_limit);
					$c->setContainerType(PACKAGING_TYPE_PACKAGE); // Package type
					$ups_containers[] = $c;
				}

				$items = array();
			break;
			////////////////////////////////////////////////////////
			case BINPACKING_NORMAL_ALGORITHM:	// pack all items in one package
				$sm = new XLite_Module_UPS_Model_Shipping_Ups();
				$pack = $sm->getUPSContainerDims($packaging_type);

				$const_items = $items;
				$ups_containers = UPSOnlineTools_solve_binpack($pack["width"], $pack["length"], $pack["height"], $pack["weight_limit"], $items);

				// if can't place all items in defined container - fit container size
				if ($ups_containers === false || count($ups_containers) != 1 || count($items) > 0) {
					$summ = 0;
					foreach ($const_items as $item) {
						$summ += $item->get("width");
						$summ += $item->get("length");
						$summ += $item->get("height");
					}

					// calc average container size
					$medium_width = ceil($summ / (max(1, count($const_items)) * 3));
					$inc_width = $medium_width * 0.1;

					// iterate while all items will pack in single container
					$fuse = 35;
					do {
						$items = $const_items;
						$ups_containers = UPSOnlineTools_solve_binpack($medium_width, $medium_width, $medium_width, 0, $items);
						$medium_width += $inc_width;

						// return with error after N=35 tries.
						if ($fuse-- <= 0) {
							return false;
						}

						// increase incremental step on each iteration
						$inc_width += $inc_width * 0.1;
					} while($ups_containers === false || count($ups_containers) > 1 || count($items) > 0);

					$packaging_type = PACKAGING_TYPE_PACKAGE;	// Package type 
				}

				foreach ($ups_containers as $k=>$v) {
					$ups_containers[$k]->setContainerType($packaging_type);
				}
			break;
			////////////////////////////////////////////////////////
			case BINPACKING_OVERSIZE_ALGORITHM:	// pack items in similar containers
				$sm = new XLite_Module_UPS_Model_Shipping_Ups();
				$pack = $sm->getUPSContainerDims($packaging_type);

				$ups_containers = UPSOnlineTools_solve_binpack($pack["width"], $pack["length"], $pack["height"], $pack["weight_limit"], $items);

				if ($ups_containers === false/* || count($items) > 0*/) {
					// return "oversized" items
					return false;
				}

				foreach ($ups_containers as $k=>$v) {
					$ups_containers[$k]->setContainerType($packaging_type);
				}
			break;
		}

		if (!is_array($ups_containers) || count($ups_containers) <= 0) {
			return false;
		}


// TODO: ..............
		// Analyze containers for AdditionalHandling condition(s)
		if ($ptype == BINPACKING_NORMAL_ALGORITHM || $ptype == BINPACKING_OVERSIZE_ALGORITHM) {
			foreach ($ups_containers as $container_id=>$container) {
				$found = false;

				foreach ((array)$container->getLevels() as $level) {
					foreach ((array)$level->getItems() as $item) {
						$item_id = $item->get("item_id");

						$oi = new XLite_Model_OrderItem();
						if ($oi->find("item_id='".addslashes($item_id)."'")) {
							if ($oi->get("product.ups_add_handling")) {
								$ups_containers[$container_id]->setAdditionalHandling(true);
								$found = true;
								break;
							}
						}
					}

					if ($found) {
						break;
					}
				}
			}
		}

		return $ups_containers;
	}
}

function cmp_carrier_array($a, $b)
{
    $class_a = $a->get('shipping.class');
    $class_b = $b->get('shipping.class');
    if ($class_a == 'ups' && $class_b != 'ups') return false;
    if ($class_b == 'ups' && $class_a != 'ups') return true;
    $pos_a = $a->get('shipping.order_by');
    $pos_b = $b->get('shipping.order_by');
    return ($pos_a > $pos_b);
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
