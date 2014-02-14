<?php
namespace Cerad\Bundle\AppBundle\Controller\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

class ScheduleOfficialListController extends MyBaseController
{
    const SESSION_SCHEDULE_OFFICIAL_QUERY_CRITERIA = 'scheduleOfficialQueryCriteria';

    /* =====================================================
     * Wanted to just use GET but the dates mess up
     * Use the session trick for now
     */
    public function listAction(Request $request, $_format = 'html')
    {
        // The search model
        $model = $this->createModel($request);
        $criteria = $model['criteria'];

        // The form stuff
        $searchFormType = $this->get('cerad_tourn.schedule_official_search.form_type');
        $searchForm = $this->createForm($searchFormType,$criteria);

        $searchForm->handleRequest($request);

        if ($searchForm->isValid())
        {
            $criteria = $searchForm->getData();

            $request->getSession()->set(self::SESSION_SCHEDULE_OFFICIAL_QUERY_CRITERIA,$criteria);

            return $this->redirect($model['link']);
        }
        if ($request->getMethod() == 'POST') 
        {
            $errors = $searchForm->getErrors();
            foreach($errors as $error)
            {
                echo implode(',',$error->getMessageParameters());
                echo $error->getMessage() . '<br />';
            }
            die('invalid');
        }
        // Query is done in the model
        $games = $model['games'];
        
        // Spreadsheet
        if ($_format == 'xls')
        {
            $export = $this->get('cerad_tourn.schedule_official.export_xls');
            $export->generate($games);
            $response = new Response($export->getBuffer());

            $outFileName = $this->getFilename() . '.xlsx';

            $response->headers->set('Content-Type',       'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }
        // csv processing
        if ($_format == 'csv')
        {
            $export = $this->get('cerad_tourn.schedule_official.export_csv');
            $response = new Response($export->generate($games));

            $outFileName = $this->getFilename() . '.csv';

            $response->headers->set('Content-Type',       'text/csv;');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }

        // And render
        $tplData = array();
        $tplData['searchForm'] = $searchForm->createView();

        $tplData['isAdmin'] = $this->hasRoleAdmin();  // See if this is still used
        
        $tplData['games'] = $games;
       
        $tplData['link']       = $model['link'];
        $tplData['project']    = $model['project'];
      //$tplData['userPerson'] = $model['userPerson'];
   
        return $this->render($request->get('_template'),$tplData);
    }
    public function createModel(Request $request)
    {
        $model = array();

        $model['link']       = $this->getLink();
        $model['games']      = array();
        $model['project']    = $project = $this->getProject();
        $model['userPerson'] = $this->getUserPerson();
        
        // Game Query Criterisa
        $criteria = array();
        $criteria['projects'] = array($project->getKey());
        $criteria['teams' ]   = array();
        $criteria['fields']   = array();

        // Merge in default values from project
        $criteria['searches'] = $searches = $project->getSearches();

        foreach($searches as $key => $search)
        {
            $criteria[$key] = $search['default']; // Array of defaults
        }
        // Merge form session
        $session = $request->getSession();
        if ($session->has(self::SESSION_SCHEDULE_OFFICIAL_QUERY_CRITERIA))
        {
            $criteriaSession = $session->get(self::SESSION_SCHEDULE_OFFICIAL_QUERY_CRITERIA);
            $criteria = array_merge($criteria,$criteriaSession);
        }
        
        // No need to query if its it a post?
        $model['criteria'] = $criteria;
        if ($request->getMethod() != 'GET') return $model;
        
        // Hack in levels for now
        $levelRepo = $this->get('cerad_level.level_repository');
        $levelKeys = $levelRepo->queryKeys($criteria);
        if (count($levelKeys))
        {
            // This is the most common case since mixing levels and program/age/gender is confusing
            if (!isset($criteria['levels'])) $criteria['levels'] = $levelKeys;
            else
            {
                // More for documentation
                $criteria['levels'] = array_merge($criteria['levels'],$levelKeys);
            }
        }
        // Now query for games
        // Query for the games
        $gameRepo = $this->get('cerad_game.game_repository');
        $games = $gameRepo->queryGameSchedule($criteria);

        // Apply filter
        $gamesFiltered = $this->filterOfficials($games);
       
        // My processing
        $gamesProcessed = $this->processGames($this->getUserPerson(),$gamesFiltered);
        
        // Done
        $model['games'] = $gamesProcessed;
        
        return $model;
    }
    /* =======================================================
     * Move the isAssignable functionality here?
     */
    public function processGames($userPerson,$games)
    {
        foreach($games as $game)
        {
            foreach($game->getOfficials() as $gameOfficial)
            {
                $gameOfficial->isUserUpdateable = $this->isGameOfficialUserUpdateable($userPerson,$game,$gameOfficial);
            }
        }
        return $games;
    }
    protected function isGameOfficialUserUpdateable($userPerson,$game,$gameOfficial)
    {
        if (!$gameOfficial->isAssignableByUser()) return false;
        
        // Open slots or if needed slots can be assigned
        switch($gameOfficial->getAssignState())
        {
            case 'Open': 
            case 'IfNeeded': 
                return true;
            case 'Pending':
                return false;
        }
        // Update your own games but not some elses
        if ($gameOfficial->getPersonGuid() == $userPerson->getGuid()) return true;
        if ($gameOfficial->getPersonGuid()) return false;
        
        // Use the person plan info
        $personPlan = $userPerson->getPlan($game->getProjectKey(),false);
        if (!$personPlan || !$personPlan->getId()) return false;
        
        // Allow match by name?
        if ($gameOfficial->getPersonNameFull() == $personPlan->getPersonName()) return true;
        if ($gameOfficial->getPersonNameFull()) return false;
        
        // Verify willReferee
        if ($personPlan->getWillReferee() != 'Yes') return false;
        
        // Leaving willAttend for now so I come through
        
        return true;
    }
    // Really should be filter games but okay
    public function filterOfficials( array $games )
    {
      return $games;
    }

    public function getLink()
    {
      return 'cerad_tourn_schedule_official_list';
    }

    public function getFilename()
    {
      return 'RefSched' . date('Ymd-Hi');
    }
}
