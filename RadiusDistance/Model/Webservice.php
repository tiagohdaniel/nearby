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
class ThdSolution_RadiusDistance_Model_Webservice extends Mage_Core_Model_Abstract
{

    /**
     * @const       string
     */
    const APPLICATION_TYPE = 'application/json';

    /**
     * @var       array
     */
    protected $_contentTypes = array(
        'json'  => 'application/json',
        'xml'   => 'application/xml',
        'form'  => 'application/x-www-form-urlencoded'
    );

    /**
     * @const       string
     */
    const RETURN_TYPES = 'array/json/xml';

    /**
     * send()
     * Method to send informations to webservice.
     *
     * @example
     *            $webservices = Mage::getSingleton('smartservices/webservice');
     *            $webservices->request = '/cesta/estimativafrete';
     *            $var = $webservices->send();
     *            $var = $webservices->send('array');
     *            $var = $webservices->send('json');
     *
     * @param     string
     * @param     string     *
     * @return    mixed|string
     *
     * @throws    Exception
     * @access    public
     */
    public function send($url, $returnFormat = 'json')
    {
        if(!$url) {
            Mage::throwException('ERROR: No URL for requested webservice.');
        }

        try {
            if (!in_array($returnFormat, $this->_getReturnTypes())) {
                Mage::throwException('ERROR: Invalid format passed to send() function in SmartServices Module.');
            }

            $requestType = $this->getRequestType() == 'POST' ? 'POST' : 'GET';

            $client = new Zend_Http_Client($url);

            $return = new Varien_Object();

            switch ($returnFormat) {
                case 'json':
                    if ($requestType == 'POST') {

                        if($this->_contentType == 'application/x-www-form-urlencoded') {
                            $response = $client->setParameterPost($this->getParams())
                                ->setHeaders('Accept', self::APPLICATION_TYPE)
                                ->setHeaders('Content-Type', $this->_contentType)
                                ->request($requestType);
                        } else {
                            $data = Mage::helper('core')->jsonEncode( $this->getParams() );
                            $response = $client->setRawData($data, self::APPLICATION_TYPE)
                                ->setHeaders('Accept', self::APPLICATION_TYPE)
                                ->setHeaders('Content-Type', $this->_contentType)
                                ->request($requestType);
                        }
                    } else {
                        $response = $client->setEncType(self::APPLICATION_TYPE)
                            ->setHeaders('Accept', self::APPLICATION_TYPE)
                            ->setHeaders('Content-Type', $this->_contentType)
                            ->request($requestType);
                    }


                    $return->setStatus( $response->getStatus() );
                    $return->setBody( $response->getBody() );
                    $return->setMessage( $response->getMessage() );

                    return $return;
                    break;
                //@TODO Make validations and encapsulate infos for bellow methods
                case 'array':
                case 'xml':
                default:
                    $response = $client->request($requestType);
                    return Mage::helper('core')->jsonDecode($response->getBody());
                    break;
            }
        } catch (Zend_Http_Client_Exception $e) {
            Mage::log('[Error Request] - ' . $requestType, null, self::LOG_FILE, true);
            return false;
        }
    }

    /**
     * getReturnTypes()
     *
     * @return    array
     * @access    private
     */
    protected function _getReturnTypes()
    {
        return explode('/', self::RETURN_TYPES);
    }

}