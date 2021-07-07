<?PHP

namespace eorivalidator;

class eori {
  
  const eu_wsdl = 'https://ec.europa.eu/taxation_customs/dds2/eos/validation/services/validation?wsdl';
  const gov_uk_url = 'https://www.tax.service.gov.uk/check-eori-number/result';
  
  static function validate(string $eori) {
    
    $eori = strtoupper($eori);
    $sanitised = preg_replace("/[^A-Z0-9]/",'', $eori);
    
    if($eori != $sanitised) {
      throw new malformedEoriNumber('The EORI should contain letters and numbers only, no spaces');
    }
    
    if(!is_numeric(substr($sanitised,2))) {
      throw new malformedEoriNumber('The EORI should start with two letters followed by numbers');
    }
    
    if(!ctype_alpha(substr($sanitised,0,2))) {
      throw new malformedEoriNumber('The EORI should start with two letters');
    }
    
    if(substr($sanitised,0,2) == 'GB') {
      return self::GB($sanitised);
    } else {
      return self::EU($sanitised);
    }
    
  }
  
  static function EU(string $eori) {
    
    $params = array(
      "eori" => $eori
    );
    
    try {
      
      $client = new \SoapClient(self::eu_wsdl);
      $response = $client->__soapCall("validateEORI", array($params));
      
    } catch (\SoapFault $e) {
      
      throw new serviceNotAvailableException('The EU web service was unavailable: '.$e->getMessage());
      
    }
    
    return $response->return->result->statusDescr == 'Valid' ? true : false;
        
  }
  
  static function GB(string $eori) {
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::gov_uk_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('eori' => $eori)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,5);
    
    $response_body = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
        
    if($response_code != 200) {
      throw new serviceNotAvailableException('The GB web service was unavailable: Response code '.$response_code);
    }
    
    if(stripos($response_body, 'Invalid EORI number')) return false;
    
    return true;
    
  }
    
}

class serviceNotAvailableException extends \Exception {}
class malformedEoriNumber extends \Exception {}
  
?>