<?php
namespace Cerad\Bundle\AppBundle\Controller\Results;

use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

/* ===========================================================
 * The main difference bewteen this and the TournBundle is that
 * this one does not use the session to store query information
 * I think because wordpress is used
 * 
 * 08 Feb 2014 - Add program/gender/age search functionality
 */
class PoolPlayResultsController extends MyBaseController
{
    public function resultsAction(Request $request)
    {
        // Simple model
        $model = $this->createModel($request);
        if ($model['response']) return $model['response'];

        $tplData = array();

        $tplData['pools']   = $model['pools'];
        $tplData['project'] = $model['project'];

        return $this->render($request->get('_template'), $tplData);
    }
    /* ===============================================
     * Assorted report objects
     */
    protected function createModel(Request $request)
    {
        // Back and forth on this
        $model = array();
        $model['response'] = null;

        // Need current project
        $project = $this->getProject();
        $model['project'] = $project;

        // Levels come from div (aka level, program,gender,age
        $levels = $this->getLevels($request);

        // Pull the games
        $gameRepo = $this->get('cerad_game.game_repository');
        $criteria = array();
        $criteria['projects' ]  = $project->getId();
        $criteria['levels'   ]  = $levels;
        $criteria['groupTypes'] = 'PP';

        $games = $gameRepo->queryGameSchedule($criteria);

        $resultsServiceId = sprintf('cerad_tourn.%s_results',$project->getResults());
        $results = $this->get($resultsServiceId);

        $pools = $results->getPools($games);

        $model['pools'] = $pools;

        return $model;
    }
    protected function getLevels($request)
    {   
        // Deal with program,age,gender
        $age     = $request->get('age');
        $gender  = $request->get('gender');
        $program = $request->get('program');
        
        $criteria = array
        (
            'ages'     => $age,
            'genders'  => $gender,
            'programs' => $program,
        );
        $levelRepo = $this->get('cerad_level.level_repository');
        $levelKeys = $levelRepo->queryKeys($criteria);
        
        // Add in individual levels (previously called div)
        // TODO: Make this part of queryKeys
        $level = $request->get('level');
        if ($level) array_merge($levelKeys,explode(',',$level));
         
        return $levelKeys;
    }
}
