<?php
namespace Cerad\Bundle\AppBundle\Controller\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

class UnregisteredOfficialsListController extends MyBaseController
{

    /* =====================================================
     * Wanted to just use GET but the dates mess up
     * Use the session trick for now
     */
    public function listAction(Request $request, $_format = 'xls')
    {
        $project = $this->getProject();

        // The search model
        $model = $this->getModel($request);

        // Query for the games
        $gameRepo = $this->get('cerad_game.game_repository');
        $games = $gameRepo->queryGameSchedule($model);

        $games = $this->filterOfficials($games);

        // Spreadsheet
        $export = $this->get('cerad_tourn.unregistered_official.export_xls');

        $response = new Response($export->generate($games));

        $outFileName = $this->getFilename() . '.xls';

        $response->headers->set('Content-Type',       'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
        return $response;
    }

    public function getModel(Request $request)
    {
        $model = array();

        $project = $this->getProject();
        $model['projects'] = array($project->getId());

        // Done
        return $model;
    }

    public function filterOfficials( array $games )
    {
      return $games;
    }

    public function getFilename()
    {
      return 'UnregisteredReferees' . date('YmdHi');
    }
}
