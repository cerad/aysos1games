<?php
namespace Cerad\Bundle\AppAdminBundle\Controller\PersonPlan;

use Cerad\Bundle\AppAdminBundle\Controller\PersonPlan\PersonPlanUpdateController as ParentController;

class UnverifiedPersonPlanUpdateController extends ParentController
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

}
?>
