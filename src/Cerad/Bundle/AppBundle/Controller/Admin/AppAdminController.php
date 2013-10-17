<?php
namespace Cerad\Bundle\AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

class AppAdminController extends MyBaseController
{
    public function showAction(Request $request)
    {
        if ($this->hasRoleUser() && !$this->hasRoleAdmin()) return $this->redirect('cerad_tourn_home');

        $tplData = array();
        $tplName = $request->get('_template');
        return $this->render($tplName, $tplData);
    }
}