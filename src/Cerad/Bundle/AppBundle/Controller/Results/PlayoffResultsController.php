<?php
namespace Cerad\Bundle\AppBundle\Controller\Results;

use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

class PlayoffResultsController extends MyBaseController
{
    const SESSION_RESULTS_PLAYOFF_DIV  = 'results_playoff_div';

    public function resultsAction(Request $request)
    {
        // Simple model
        $model = $this->createModel($request);
        if (isset($model['response'])) return $model['response'];

        $tplData = array();

        $tplData['gamesSF'] = $model['gamesSF'];
        $tplData['gamesCM'] = $model['gamesCM'];
        $tplData['gamesFM'] = $model['gamesFM'];
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

        // Need current project
        $project = $this->getProject();
        $model['project'] = $project;

        // Pull the games
        $gameRepo = $this->get('cerad_game.game_repository');
        
        $criteria = array();
        $criteria['projects' ] = $project->getKey();
        $criteria['levels'] = $this->getLevels($request);;

        $criteria['groupTypes'] = 'SF';
        $model['gamesSF'] = $gameRepo->queryGameSchedule($criteria);

        $criteria['groupTypes'] = 'CM';
        $model['gamesCM'] = $gameRepo->queryGameSchedule($criteria);

        $criteria['groupTypes'] = 'FM';
        $model['gamesFM'] = $gameRepo->queryGameSchedule($criteria);

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
