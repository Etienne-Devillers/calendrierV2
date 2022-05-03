<?php
require_once('testapi.php');
// require_once('zoneA.php');
// var_dump($holidaysArray);
// die;

//initialisaion du projet, définition des variables.
$holiday = '';
$actualMonth = false;
$importantDay = '';
$importantDaysArray = [
    '03-08' => 'Journée de la femme',
    '04-13' => 'Pâques',
    '05-01' => 'Fête du travail',
    '05-08' => 'Ascension',
    '06-01' => 'la Pentecôte',
    '07-14' => 'Fête Nationale',
    '15-15' => 'Assomption',
    '11-01' => 'Toussaint',
    '11-11' => 'Armistice de 1918',
    '12-25' => 'Noël',

];



//variable qui contient les douzes mois de l'année.
    $monthsInYear = [
                    '1' => 'Janvier',
                    '2' => 'Février',
                    '3' => 'Mars',
                    '4' => 'Avril',
                    '5' => 'Mai',
                    '6' => 'Juin',
                    '7' => 'Juillet',
                    '8' => 'Août',
                    '9' => 'Septembre',
                    '10' => 'Octobre',
                    '11' => 'Novembre',
                    '12' => 'Décembre'
    ];

//Déclaration du numéro du mois actuel
    $monthNumber = date('m');


//Déclaration de la variable pour avoir notre échantillon d'année.
    $year = date('Y');

    if (!empty($_GET['month']) && !empty($_GET['year'])) {
        $monthChoice = $_GET['month'];
        $chosenYear = $_GET['year'];
    } else {
        $monthChoice = $_POST['month'] ?? date('n');
        $chosenYear = $_POST['year'] ?? date('Y');
    }

    $chosenMonth = $monthsInYear[$monthChoice];
    $lastMonth = ($monthChoice-1);
    $nextMonth = ($monthChoice+1);
    $displayChoice = new DateTime("$chosenYear-$monthChoice");
    $countDay = new DateTime("$chosenYear-$monthChoice");
    $countDay2 = new DateTime("$chosenYear-$monthChoice");
    $countDay3 = new DateTime("$chosenYear-$monthChoice");
    $displayedDay = $countDay -> format('m-d');

    //Création de year-1 et year +1
    $nextYear =  new DateTime("$chosenYear-$monthChoice"); 
    $lastYear =  new DateTime("$chosenYear-$monthChoice"); 
    $interval =  new DateInterval('P1M');
    $nextYear->add(new DateInterval('P1M'));
    $lastYear->sub(new DateInterval('P1M'));
    $previousDisplayMonth = $lastYear->format('n');
    $previousDisplayYear = $lastYear->format('Y');
    $nextDisplayMonth = $nextYear->format('n');
    $nextDisplayYear = $nextYear->format('Y');
    $nextMonthDayCount = 1;

//On trouve le premier jour du mois.
$firstDay = $displayChoice-> format('N');

//vérification si on est sur le mois en cours 
if ($monthChoice == date('n') && $chosenYear == date('Y')) {
    $actualMonth = true;
}

//Identification du jour actuel
$dayToday = date('j');

//Combien de jour dans un mois.
$daysForSpecificMonth = cal_days_in_month(CAL_GREGORIAN, $monthChoice, $chosenYear);
$daysInLastMonth = cal_days_in_month(CAL_GREGORIAN, $previousDisplayMonth, $chosenYear);
$displayLastMonthDays = ($daysInLastMonth - $firstDay)+2 ;
//combien de semaine dans le mois.
function weeksInMonth($dayCount, $daysForSpecificMonth) {
    $weeksInMonth = 1;
    for ($i=1; $i < $daysForSpecificMonth; $i++) { 
        
        if ($dayCount == 7) {
            $dayCount = 0;
            $weeksInMonth++;
        } 
        $dayCount++;
        
    }
    return $weeksInMonth;
    }

// Fonction qui va définir les classes qu'on donnera aux différentes cases de notre tableau
    $weeksInMonth =weeksInMonth($firstDay, $daysForSpecificMonth);
$weeksDisplay = $weeksInMonth*7;

function defineClass($day, $firstDay, $dayToday, $actualMonth, $daysForSpecificMonth) {
    $class='';
    if ((($day-$firstDay)+1 == $dayToday) && ($actualMonth === true)){
        $class .= 'actualDay ';
    }
    if (($day < $firstDay) || ($day >= $daysForSpecificMonth+$firstDay) ) {
        $class .= 'emptyBox ';
    }
    if (($day%7 ==0 ) || ($day+1) %7 == 0) {
        $class .= 'weekend ';
    }
    
return $class;
}

// Fonction qui va définir le contenu à afficher dans les cases.

function defineContent($day, $firstDay, $dayToday, $actualMonth, $daysForSpecificMonth, $nextMonthDayCount, $displayLastMonthDays) {
    $content='';

    if ($day < $firstDay) { 
        $content=$displayLastMonthDays;
        
    }else if ($day >= ($daysForSpecificMonth+$firstDay)){
        $content .= $nextMonthDayCount;
        $nextMonthDayCount ++;
    } else {
        $content .= ($day-$firstDay)+1;
    }
return $content;
}


function isImportantDay($day, $firstDay, $displayedDay, $importantDaysArray, $daysForSpecificMonth,$countDay) {
    $importantDay ='';
    if (($day >= $firstDay) && ($day <= $daysForSpecificMonth )){

        $displayedDay = $countDay -> format('m-d');

        foreach ($importantDaysArray as $key => $value) {
            if ($displayedDay == $key) {
            $importantDay = '<span class="importantDay">'.$value.'</span>';
            }
        }
        $countDay -> add(new DateInterval('P1D'));
    }
    return $importantDay;
}



function defineHolidays($day, $firstDay, $displayedDay, $daysForSpecificMonth,$countDay2, $holidaysArray) {
    $holiday ='';
    if (($day >= $firstDay) && ($day <= $daysForSpecificMonth )){ 

        $timeStampCountDay = $countDay2 ->getTimestamp();
        
        
        foreach ($holidaysArray as $value) {
            if (($timeStampCountDay >= $value->start) && ($timeStampCountDay <= ($value->start + 86400))) {
                $holiday = '<div class="startHolidaysB holidaysB"></div>';
            }  else if ($timeStampCountDay <= $value->end && ($timeStampCountDay+86400 >= ($value->end))) {
                $holiday = '<div class="endHolidaysB holidaysB"></div>';
            } 
            else if (($timeStampCountDay >= $value->start) && ($timeStampCountDay <= $value->end)) {
                $holiday = '<div class="holidaysB fullHolidaysB"></div>';
            } 
           
        }
        $countDay2 -> add(new DateInterval('P1D'));
    } 
    return $holiday;
}
// function defineHolidaysA($day, $firstDay, $displayedDay, $daysForSpecificMonth,$countDay3, $holidaysArrayA) {
    //     $holiday ='';
    //     if (($day >= $firstDay) && ($day <= $daysForSpecificMonth )){ 

    //         $timeStampCountDay = $countDay3 ->getTimestamp();
            
            
    //         foreach ($holidaysArrayA as $value) {
    //             if (($timeStampCountDay >= $value->start) && ($timeStampCountDay <= ($value->start + 86400))) {
    //                 $holiday = '<div class="startHolidaysA holidaysA"></div>';
    //             }  else if ($timeStampCountDay <= $value->end && ($timeStampCountDay+86400 >= ($value->end))) {
    //                 $holiday = '<div class="endHolidaysA holidaysA"></div>';
    //             } 
    //             else if (($timeStampCountDay >= $value->start) && ($timeStampCountDay <= $value->end)) {
    //                 $holiday = '<div class="holidaysA fullHolidaysA"></div>';
    //             } 
            
    //         }
    //         $countDay3 -> add(new DateInterval('P1D'));
    //     } 
    //     return $holiday;
// }
?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>calendrier php</title>
</head>

<body>
    <header>
        <h1>Calendrier -
            <?= (($chosenYear) && ($monthChoice))? $chosenMonth.' '.$chosenYear : 'à définir' ;?></h1>
        <div class="displayChoices">

        <!----------------------Formulaire----------------------->
            <form action="./index.php" method="post">

                <!-- Création du select pour les mois de l'année. -->
                <label class="label" for="month">Mois : </label>
                <select name="month">
                    <?php foreach ($monthsInYear as $key => $value) {
                        $isSelected = ( $key == $monthNumber) ? 'selected' : ''; ?>
                    <option value="<?=$key?>" <?=$isSelected?>><?=$value?></option>
                    <?php
                    }?>
                </select>


                <!-- Création du select pour les années. -->
                <label class="label" for="year">Année : </label>
                <select name="year">
                    <?php for ($i = $year-10; $i  <= $year+10 ; $i ++) {
                        $isSelected = ($i==$year) ? 'selected' : ''; ?>

                    <option value="<?=$i?>" <?=$isSelected?>><?=$i?></option>
                    <?php
                    }
                    ?>
                </select>
                <button type="submit">Modifier</button>
            </form>
        </div>
    </header> 

    <div class="monthsMenu">
        <div class="changeDisplayButton">
        <a href="./index.php?month=<?=$previousDisplayMonth?>&year=<?=$previousDisplayYear?>"><i class="arrow left"></i> <?=$monthsInYear[$lastMonth??''] ?? 'Année précédente' .'</a>'?></a>
        </div>
        <div class="changeDisplayButton">
        <a href="./index.php?month=<?=$nextDisplayMonth?>&year=<?=$nextDisplayYear?>"> <?=$monthsInYear[$nextMonth??''] ?? 'Année suivante'?> <i class="arrow right"></i></a>
        </div>
    </div>

<!--------------------Tableau--------------------->
    <section class="tableau">
<table>


<tr>
    <th class=" weekDays"><span class="large">Lundi</span> <span class="short">Lun</span></th>
    <th class=" weekDays"><span class="large">Mardi</span> <span class="short">Mar</span></th>
    <th class="weekDays"><span class="large ">Mercredi</span> <span class="short ">Mer</span></th>
    <th class=" weekDays"><span class="large">Jeudi</span> <span class="short">Jeu</span></th>
    <th class=" weekDays"><span class="large">Vendredi</span> <span class="short">Ven</span></th>
    <th class="weekDays"><span class="large">Samedi</span> <span class="short">Sam</span></th>
    <th class=" weekDays"><span class="large">Dimanche</span> <span class="short">Dim</span></th>
</tr>
<tr>


<?php


    for ($day=1; $day <= $weeksDisplay; $day++) { 
        $class = defineClass($day, $firstDay, $dayToday, $actualMonth, $daysForSpecificMonth);
        $content = defineContent($day, $firstDay, $dayToday, $actualMonth, $daysForSpecificMonth, $nextMonthDayCount, $displayLastMonthDays);

        $importantDay = isImportantDay($day, $firstDay, $displayedDay, $importantDaysArray, $daysForSpecificMonth, $countDay);
        $holiday =  defineHolidays($day, $firstDay, $displayedDay, $daysForSpecificMonth,$countDay2, $holidaysArray);
        // $holidayA =  defineHolidaysA($day, $firstDay, $displayedDay, $daysForSpecificMonth,$countDay2, $holidaysArrayA);
        // $holiday .= $holidayA;
        $tr = ($day%7 == 0) ? '</tr><tr>' : '';
    echo '<td><span class="'.$class.'">'.$content.'</span>'.$importantDay.''.$holiday.'</td>'.$tr;
    $displayLastMonthDays++;
    if ($day >= ($daysForSpecificMonth+$firstDay)){
        $nextMonthDayCount ++;
    }

}

?>
</tr>
</table>


    </section>




</body>

</html>