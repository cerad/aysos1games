<?php
namespace Cerad\Bundle\AppAdminBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\TournBundle\Controller\BaseController as AppBaseController;

class AppAdminController extends AppBaseController
{

  public function showAction(Request $request)
  {
    if ( !$this->hasRoleAdmin() && !$this->hasRoleAssignor() && !$this->hasRoleScoreAdmin() ) return $this->redirect('cerad_tourn_home');

    $tplData = array();
    $tplName = $request->get('_template');
    return $this->render($tplName, $tplData);
  }

}
