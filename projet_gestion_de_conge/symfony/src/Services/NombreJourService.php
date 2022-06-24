<?php

namespace App\Services;

use DateTime;
use DatePeriod;
use DateInterval;

class NombreJourService {

    public function getNombreJourConge (DateTime $dateDebut, string $periodeDebut, DateTime $dateFin = null, string $periodeFin = null) : float {

        $result = 0;
        $weekend = 0;

        if(!$dateFin) {
            if($periodeDebut == 'Toute la journée') {
                $result = 1;
            } else {
                $result = 0.5;
            }
        } else {
            $period = new DatePeriod($dateDebut, new DateInterval('P1D'), $dateFin);
            foreach($period as $dt) {
                $jour = $dt->format('D');
                if($jour == 'Sat' || $jour == 'Sun') {
                    $weekend++;
                }
            }
            $result = $dateDebut->diff($dateFin)->format('%a');
            if(($periodeDebut == 'Toute la journée' && ($periodeFin == 'Toute la journée'))) {
                $result += 1;
            } else if(($periodeDebut == 'Toute la journée') && ($periodeFin == 'Matin')) {
                $result += 0.5;
            } else if(($periodeDebut == 'Après-midi') && ($periodeFin == 'Toute la journée')) {
                $result += 0.5;
            }
            
        }

        return $result - $weekend;
        
    }

}            