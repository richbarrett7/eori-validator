<?PHP

include_once('src/eori.php');

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