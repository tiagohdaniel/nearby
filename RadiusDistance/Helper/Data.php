<?php
/**
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category  ThdSolution
 * @package   ThdSolution_RadiusDistance
 *
 * @author    Tiago Daniel <tiago.daniel@gmail.com.br>
 */
class ThdSolution_RadiusDistance_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return nearest places based on geo localization
     *
     * @param $lat
     * @param $lng
     * @return string
     */
    public function getHaversineQuery($lat, $lng)
    {
        $query = sprintf('SELECT name, id,  ( 6371 * acos( cos( radians(%s) ) * cos( radians( lat ) )
                  * cos( radians( lng ) - radians(%s) ) + sin( radians(%s) )
                  * sin(radians(lat)) ) ) AS distance
                  FROM markers
                  HAVING distance < 25
                  ORDER BY distance
                  LIMIT 0 , 20', $lat, $lng, $lat);

        return $query;
    }

    /**
     * Set Nearby places data in cookie
     *
     * @param $places
     */
    public function setAddresstoCookie($places)
    {
        Mage::getModel('core/cookie')->set('places', serialize($places));
    }

    /**
     * Return Data from cookie by ZIP-Code
     *
     * @param $address
     * @return bool|string
     */
    public function getAddressCookie($address)
    {
        $cookieData = unserialize(Mage::getModel('core/cookie')->get('places'));
        return $cookieData[$address] ? json_encode($cookieData) : false;
    }

}