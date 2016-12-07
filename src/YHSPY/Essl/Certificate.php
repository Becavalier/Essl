<?php

namespace YHSPY\Essl;

use DateTime;
use YHSPY\Essl\Exception;
use YHSPY\Essl\Parser\SanParser;

class Certificate
{
    protected $rawCert;
    protected $certData;

    /**
     * @var SanParser
     */
    protected $sanParser;

    /**
     * Certificate constructor.
     *
     * @param string $certificate
     * @param SanParser $sanParser
     */
    public function __construct($certificate, $attach = null, SanParser $sanParser = null)
    {
        if ($sanParser === null) {
            $sanParser = new SanParser();
        }

        $this->sanParser = $sanParser;

        $this->rawCert = $certificate;
        $this->certData = $this->extractCertData($certificate);
        $this->sanParser = $sanParser;
        $this->attach = $attach;
    }

    /**
     * @return boolean
     */
    public function validateCert($domain)
    {
        // Validate expired time
        if($this->validTo()->getTimestamp() < time()) 
        {
            return false;
        }

        // Validete valid domains
        if(isset($this->attach->domain) && !empty($this->attach->domain))
        {
            $host = $this->attach->domain;
        }
        else
        {
            $host = $domain;
        }

        if(empty($host)) 
        {
            throw new Exception("Invalid host, please provide a valid host name for validation.", Exception::INVALID_HOST);
        }

        if(!in_array($host, $certificate->sans()))  
        {
            $hosts = explode(".", $host);
            if(count($hosts) > 2)
            {
                $host = "*." . $hosts[count($hosts) - 2] . "." . $hosts[count($hosts) - 1];
            } 
            else
            {
                $host = "*." . $host;
            }

            if(!in_array($host, $certificate->sans()))  
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return DateTime
     */
    public function validFrom()
    {
        $date = new DateTime();
        $date->setTimestamp($this->certData['validFrom_time_t']);
        return $date;
    }

    /**
     * @return DateTime
     */
    public function validTo()
    {
        $date = new DateTime();
        $date->setTimestamp($this->certData['validTo_time_t']);
        return $date;
    }

    /**
     * @return string
     */
    public function certName()
    {
        return $this->certData['name'];
    }

    /**
     * @return array
     */
    public function subject()
    {
        return $this->certData['subject'];
    }

    /**
     * @return array
     */
    public function issuer()
    {
        return $this->certData['issuer'];
    }

    /**
     * @return array
     */
    public function sans()
    {
        return $this->sanParser->parse($this->certData['extensions']['subjectAltName']);
    }

    /**
     * @return string
     */
    public function signatureAlgorithm()
    {
        return $this->certData['signatureTypeSN'];
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->rawCert;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    protected function extractCertData($certificate)
    {
        $parsedData = openssl_x509_parse($certificate);

        if ($parsedData === false) {
            throw new Exception("Unable to extract data from certificate.", Exception::MALFORMED_CERTIFICATE);
        }

        return $parsedData;
    }
}
