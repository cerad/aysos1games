<?php
namespace Cerad\Bundle\AppBundle\Controller\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

class ScheduleSearchListController extends MyBaseController
{
    const SESSION_SCHEDULE_SEARCH = 'ScheduleSearchSearch';

    /* =====================================================
     * Wanted to just use GET but the dates mess up
     * Use the session trick for now
     */
    public function listAction(Request $request, $_format = 'html')
    {
        // The search model
        $model = $this->getModel($request);

        // The form stuff
        $searchFormType = $this->get('cerad_tourn.schedule_search.form_type');
        $searchForm = $this->createForm($searchFormType,$model);

        $searchForm->handleRequest($request);

        if ($searchForm->isValid()) // GET Request
        {
            $modelPosted = $searchForm->getData();

            $request->getSession()->set(self::SESSION_SCHEDULE_SEARCH,$modelPosted);

            return $this->redirect('cerad_tourn_schedule_list');
        }

        // Hack in levels for now
        $levelRepo = $this->get('cerad_level.level_repository');
        $levelKeys = $levelRepo->queryKeys($model);
        if (count($levelKeys))
        {
            $model['levels'] = $levelKeys;
        }
        // Query for the games
        $gameRepo = $this->get('cerad_game.game_repository');
        $games = $gameRepo->queryGameSchedule($model);

        // Spreadsheet
        if ($_format == 'xls')
        {
            $export = $this->get('cerad_tourn.schedule_search.export_xls');
            $response = new Response($export->generate($games));

            $outFileName = date('YmdHi') . '_schedule' . '.xls';

            $response->headers->set('Content-Type',       'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }
        // csv processing
        if ($_format == 'csv')
        {
            $export = $this->get('cerad_tourn.schedule_search.export_csv');
            $response = new Response($export->generate($games));

            $outFileName = date('YmdHi') . '_schedule' . '.csv';

            $response->headers->set('Content-Type',       'text/csv;');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }

        // And render
        $tplData = array();
        $tplData['searchForm'] = $searchForm->createView();
        $tplData['games']   = $games;
        $tplData['isAdmin'] = $this->hasRoleAdmin();
        $tplData['project'] = $this->getProject();
        return $this->render('@CeradTourn/Schedule/Search/ScheduleSearchIndex.html.twig',$tplData);
    }
    public function getModel(Request $request)
    {
        $model = array();

        $project = $this->getProject();
        $model['projects'] = array($project->getId());

        //$model['teams' ]  = array();
        $model['fields']  = array();
        $model['dow']  = array();
        $model['dates' ]  = array();
        //$model['league']  = array();
        //$model['allstar']  = array();
        //$model['extra']  = array();
        $model['programs']  = array();
        $model['genders']  = array();
        $model['ages']  = array();

        $model['searches'] = $searches = $project->getSearches();

        foreach($searches as $name => $search)
        {
            $model[$name] = $search['default']; // Array of defaults
        }

        // Merge form session
        $session = $request->getSession();
        if ($session->has(self::SESSION_SCHEDULE_SEARCH))
        {
            $modelSession = $session->get(self::SESSION_SCHEDULE_SEARCH);
            $model = array_merge($model,$modelSession);
        }

        // Done
        return $model;
    }
}
