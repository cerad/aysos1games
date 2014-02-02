<?php
namespace Cerad\Bundle\AppBundle\Controller\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Cerad\Bundle\AppBundle\Controller\Schedule\ScheduleOfficialListController as ParentController;

class UnassignedMatchesListController extends ParentController
{
    public function filterOfficials( array $games )
    {
      $unassigned = array();

      foreach ( $games as $game )
      {
        $isAssigned = true;
        foreach ( $game->getOfficials() as $gameOfficial )
        {
          $officialName = $gameOfficial->getPersonNameFull();
          if ( $officialName == null )
            $isAssigned = false;
        }

      if ( $isAssigned == false )
        $unassigned[] = $game;
      }

      return $unassigned;
    }

    public function getLink()
    {
      return 'cerad_tourn_unassigned_matches_list';
    }

    public function getFilename()
    {
      return 'Unassigned.' . date('YmdHi');
    }
}
