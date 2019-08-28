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
Class ThdSolution_RadiusDistance_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $request         = Mage::app()->getRequest()->getParams();
        $alreadySearched = Mage::helper('thdsolution_radiusdistance')->getAddressCookie($request['address']);

        /**
         * Return data from api
         */
        if (!$alreadySearched) {
            $places = Mage::getModel('thdsolution_radiusdistance/radius')->getApiInformation($request);
            echo 'API';
            echo $places;
        }

        /**
         * Return data from Cookie
         */
        echo 'COOKIE';
        echo $alreadySearched;
    }

}
