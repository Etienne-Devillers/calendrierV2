<?php 
$curl = curl_init('https://data.education.gouv.fr/api/records/1.0/search/?dataset=fr-en-calendrier-scolaire&q=&facet=description&facet=population&facet=start_date&facet=end_date&facet=location&facet=zones&facet=annee_scolaire&refine.location=Besan%C3%A7on&refine.annee_scolaire=2021-2022');
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //à ne pas faire en prod. dangereux.
curl_setopt_array($curl, [
    CURLOPT_CAINFO =>'C:\laragon\www\Calendrier\CalendrierV2\certifAPI.cer',
    CURLOPT_RETURNTRANSFER => true,

]);

$data = curl_exec($curl);
if ($data === false) {
    var_dump(curl_error($curl));
} else {
$data = json_decode($data, true);

$holidaysArrayA =  array();
$springHolidaysB =array();


function createObjectHolidaysA($holidaysNameGroup, $holidaysArrayA, $value) {

    $startTime = new DateTime($value['fields']['start_date']);
    $startTimeStamp = $startTime -> getTimestamp();
    $endTime = new DateTime($value['fields']['end_date']);
    $endTimeStamp = $endTime -> getTimestamp();

    $holidaysNameGroup = (object) [   'start' => $startTimeStamp,
                                    'end' => $endTimeStamp,
                                    'zone' => 'B',
                                    'name' =>  $value['fields']['description']
                                ];       
                                // var_dump($holidaysNameGroup);
    return $holidaysNameGroup;
    }

    $duplicateArray = [
        'spring' => 0,
        'summer' => 0,
        'christmas' => 0,
        'winter' => 0,
        'saints' => 0
    ];

foreach ($data['records'] as $key => $value) {
    $tempObject= array();

    if ($value['fields']['description'] == 'Vacances de Printemps' && $duplicateArray['spring'] != 1) {
        $tempObject = createObjectHolidays($tempObject, $holidaysArrayA, $value);
        array_push($holidaysArrayA, $tempObject);
        $duplicateArray['spring'] = 1 ;
    }

    if ($value['fields']['description'] == 'Vacances d\'Été' && $duplicateArray['summer'] != 1) {
        $tempObject = createObjectHolidays($tempObject, $holidaysArrayA, $value);
        array_push($holidaysArrayA, $tempObject);
        $duplicateArray['summer'] = 1 ;
    }

    if ($value['fields']['description'] == 'Vacances de Noël' && $duplicateArray['christmas'] != 1) {
        $tempObject = createObjectHolidays($tempObject, $holidaysArrayA, $value);
        array_push($holidaysArrayA, $tempObject);
        $duplicateArray['christmas'] = 1 ;
    }

    if ($value['fields']['description'] == 'Vacances d\'Hiver' && $duplicateArray['winter'] != 1) {
        $tempObject = createObjectHolidays($tempObject, $holidaysArrayA, $value);
        array_push($holidaysArrayA, $tempObject);
        $duplicateArray['winter'] = 1 ;
    }
    if ($value['fields']['description'] == 'Vacances de la Toussaint' && $duplicateArray['saints'] != 1) {
        $tempObject = createObjectHolidays($tempObject, $holidaysArrayA, $value);
        array_push($holidaysArrayA, $tempObject);
        $duplicateArray['saints'] = 1 ;
    }
}

// var_dump($holidaysArray);
// var_dump($data['records']['0']['fields']['start_date']);

}

curl_close($curl);

