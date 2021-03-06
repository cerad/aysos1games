<?php
namespace Cerad\Bundle\AppBundle\Controller\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Cerad\Bundle\TournBundle\Controller\BaseController as MyBaseController;

use Cerad\Bundle\TournBundle\FormType\Schedule\My\GameFormType;
use Cerad\Bundle\TournBundle\FormType\Schedule\My\GamesFormType;

/* ===================================================================
 * List games for current user (and family)
 */
class ScheduleMyListController extends MyBaseController
{
    public function listAction(Request $request, $_format = 'html')
    {
        // The search model
        $model = $this->getModel($request);
        if (isset($model['response'])) return $model['repsonse'];

        // Allow user to change slot status
        $form = $this->createModelForm($request,$model);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $model = $form->getData($model);

            $this->processModel($model);

            $route = $request->get('_route');
            return $this->redirect( $route);
        }

        // Hack in levels for now
        $levelRepo = $this->get('cerad_level.level_repository');
        $levelKeys = $levelRepo->queryKeys($model);
        if (count($levelKeys))
        {
            $model['levels'] = $levelKeys;
        }

        // Query for the games
        //$gameRepo = $this->get('cerad_game.game_repository');
        //$games = $gameRepo->queryGameSchedule($model);

        // Spreadsheet
        if ($_format == 'xls')
        {
            $export = $this->get('cerad_tourn.schedule_my.export_xls');
            $export->generate($model['games']);
            $response = new Response($export->getBuffer());

            $outFileName = 'MySched' . date('Ymd-Hi') . '.xlsx';

            $response->headers->set('Content-Type',       'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }
        // csv processing
        if ($_format == 'csv')
        {
            $export = $this->get('cerad_tourn.schedule_my.export_csv');
            $response = new Response($export->generate($model['games']));

            $outFileName = 'MySched' . date('Ymd-Hi') . '.csv';

            $response->headers->set('Content-Type',       'text/csv;');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }

         // And render
        $tplData = array();
        $tplData['form'] = $form->createView();
        $tplData['person'] = $model['person'];
        return $this->render($request->get('_template'),$tplData);
    }
    protected function processModel($model)
    {
        $gameRepo = $this->get('cerad_game.game_repository');
        $gameRepo->commit();
    }
    protected function getModel(Request $request)
    {
        $model = array();

        $project = $this->getProject();
        $person  = $this->getUserPerson();
        if (!$person)
        {
            $model['response'] = $this->redirect('cerad_tourn_welcome');
            return $model;
        }

        $criteria = array();
        $criteria['projects']      = array($project->getId());
        $criteria['officialNames'] = array($person->getName()->full);

        $gameRepo = $this->get('cerad_game.game_repository');
        $games = $gameRepo->queryGameSchedule($criteria);

        $model['project'] = $project;
        $model['person']  = $person;
        $model['games']   = $games;

        return $model;
    }
    protected function createModelForm($request, $model)
    {
        $builder = $this->createFormBuilder($model);

        $route = $request->get('_route');
        $builder->setAction($this->generateUrl($route));
        $builder->setMethod('POST');

        $builder->add('games','collection',array('type' => new GameFormType($model['person'])));

        $builder->add('update', 'submit', array(
            'label' => 'Accept/Decline/Turnback Assignments',
            'attr' => array('class' => 'submit'),
        ));

        return $builder->getForm();
    }
}
