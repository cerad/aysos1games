<?php
namespace Cerad\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResetAssignmentsCommand extends ContainerAwareCommand
{
    protected $commandName = 'command';
    protected $commandDesc = 'Command Description';

    protected function configure()
    {
        $this->setName       ('s1games:reset:assignments');
        $this->setDescription('Reset Official Assignments');
        
          //->addArgument   ('path', InputArgument::REQUIRED, 'path');
          //->addArgument   ('truncate',   InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService  ($id)   { return $this->getContainer()->get($id); }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getService('cerad_project.project_current');
        
        $gameRepo = $this->getService('cerad_game.game_repository');
        
        $games = $gameRepo->queryGameSchedule(array('projects' => $project->getKey()));
        foreach($games as $game)
        {
            $dow = $game->getDtBeg()->format('D');
            $assignRole = ($dow == 'Sat') ? 'ROLE_USER' : 'ROLE_ASSIGNOR';
            
            $gameOfficials = $game->getOfficials();
            foreach($gameOfficials as $gameOfficial)
            {
                $gameOfficial->setAssignRole($assignRole);
                $gameOfficial->setAssignState('Open');
                $gameOfficial->setPersonGuid    (null);
                $gameOfficial->setPersonNameFull(null);
            }
            
        }
        $gameRepo->commit();
        echo sprintf("Game count %d\n",count($games));
    }
}
?>