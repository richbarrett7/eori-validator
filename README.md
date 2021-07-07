# EORI Number Validator

## Overview
This uses the following resources to validate GB and EU origin EORI numbers.
* https://ec.europa.eu/taxation_customs/dds2/eos/eori_validation.jsp?Lang=en
* https://www.gov.uk/check-eori-number

## Usage

```
<?PHP

try {
  
  echo \eorivalidator\eori::validate('GB981509987000') ? 'Valid' : 'NOT Valid';

} catch (\eorivalidator\serviceNotAvailableException $e) {
  
  die('The required web service was unavailable');
  
} catch (\eorivalidator\serviceNotAvailableException $e) {
  
  die('Error: '.$e->getMessage());
  
} catch (\eorivalidator\malformedEoriNumber $e) {
  
  die('Your EORI number was not formed correctly: '.$e->getMessage());
  
}

?>
```