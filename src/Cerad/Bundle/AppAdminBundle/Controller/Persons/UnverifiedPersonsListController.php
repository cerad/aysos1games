<?php
namespace Cerad\Bundle\AppAdminBundle\Controller\Persons;

use Cerad\Bundle\AppAdminBundle\Controller\Persons\PersonsListController as ParentController;

class UnverifiedPersonsListController extends ParentController
{
  public function filterPersons( array $persons )
  {
    $unapproved = array();

    $project = $this->getProject();

    foreach ( $persons as $person )
    {
      $personFed = $person->getFed($project->getFedRole());
      if ( $personFed->getfedKeyVerified() == 'No'  )
      {
        $unapproved[] = $person;
      }
    }

    return $unapproved;
  }

  public function getLink()
  {
    return 'cerad_tourn_admin_unapproved_persons_list';
  }

  public function getOutFilename()
    {
      return 'UnverifiedPersons' . date('Ymd-Hi') . '.xls';
    }

}
?>
