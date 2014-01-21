<?php
namespace Cerad\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Cerad\Component\Excel\Excel;

class ImportS1GamesCommand extends ContainerAwareCommand
{
    protected $commandName = 'command';
    protected $commandDesc = 'Command Description';

    protected function configure()
    {
        $this
            ->setName       ('cerad:import:s1games')
            ->setDescription('Schedule Import')
            ->addArgument   ('importFile', InputArgument::REQUIRED, 'Import File');
          //->addArgument   ('truncate',   InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService  ($id)   { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Project is fixed
        $projectId  = 'AYSOS1GamesLowerSpring2014';

        // The repos
        $gameRepo      = $this->getService('cerad_game.game_repository');
        $gameFieldRepo = $this->getService('cerad_game.game_field_repository');

        // Just hard code for now
        $inputFileName = $input->getArgument('importFile');

        $excel = new Excel();

        $reader = $excel->load($inputFileName);

        $ws = $reader->getSheetByName('Schedule');

        $rows = $ws->toArray();

        $header = array_shift($rows);

        // Process
        foreach($rows as $row)
        {
            $rowx = array
            (
                'num'       => (integer)$row[0],
                'date'      => $excel->processDate($row[ 1]),
                'time'      => $excel->processTime($row[ 4]),
                'field'     => $row[ 3],
                'div'       => $row[ 5],
                'home_name' => $row[ 7],
                'away_name' => $row[10],
                'home_pool' => $row[14],
                'away_pool' => $row[16],
            );
            // ===================================================
            // Fields
            $fieldName  = $rowx['field'];

            $fieldParts = explode(' ',$fieldName);
            $fieldVenue = $fieldParts[0];
            $fieldSort = (integer)$fieldParts[1];

            $gameField = $gameFieldRepo->findOneByProjectName($projectId,$fieldName);
            if (!$gameField)
            {
                $gameField = $gameFieldRepo->createGameField();
                $gameField->setSort     ($fieldSort);
                $gameField->setName     ($fieldName);
                $gameField->setVenue    ($fieldVenue);
                $gameField->setProjectId($projectId);
                $gameFieldRepo->save($gameField);

                // If we didn't commit then need local cache nonsense
                $gameFieldRepo->commit();
            }
            // ===================================================
            // Games
            $num = $rowx['num'];
            $game = $gameRepo->findOneByProjectNum($projectId,$num);
            if (!$game)
            {
                $game = $gameRepo->createGame();
                $game->setNum($num);
                $game->setProjectId($projectId);
            }
            $game->setField($gameField);

            // Level id
            $div = $rowx['div'];
            $levelId = 'AYSO_' . $div . '_Core';
            $game->setLevelId($levelId);

            // DateTimes
            $dt = $rowx['date'] . ' ' . $rowx['time'];

            $dtBeg = \DateTime::createFromFormat('Y-m-d H:i:s',$dt);
            $dtEnd = clone($dtBeg);
            $dtEnd->add(new \DateInterval('PT80M'));

            $game->setDtBeg($dtBeg);
            $game->setDtEnd($dtEnd);

            $homeTeam = $game->getHomeTeam();
            $awayTeam = $game->getAwayTeam();

            $homeTeam->setName($rowx['home_name']);
            $awayTeam->setName($rowx['away_name']);

            $homeTeam->setLevelId($levelId);
            $awayTeam->setLevelId($levelId);

            // Group nonsense
            $pool = $rowx['home_pool'];
            if ($pool)
            {
                $gameGroup = sprintf('%s PP %d',$div,substr($pool,0,1));
                $game->setGroup    ($gameGroup);
                $game->setGroupType('PP');

                $homeTeamGroup = sprintf('%s PP %s',$div,$rowx['home_pool']);
                $awayTeamGroup = sprintf('%s PP %s',$div,$rowx['away_pool']);

                $homeTeam->setGroup($homeTeamGroup);
                $awayTeam->setGroup($awayTeamGroup);
            }
            else
            {
                switch(substr($rowx['home_name'],0,4))
                {
                    case 'Semi': $type = 'SF'; break;
                    case 'Cham': $type = 'FM'; break;
                    case 'Cons': $type = 'CM'; break;
                    default: $type = '??';
                }
                $gameGroup = sprintf('%s %s',$div,$type);
                $game->setGroup    ($gameGroup);
                $game->setGroupType($type);

                $homeTeamGroup = sprintf('%s %s',$div,$type);
                $awayTeamGroup = sprintf('%s %s',$div,$type);

                $homeTeam->setGroup($homeTeamGroup);
                $awayTeam->setGroup($awayTeamGroup);
            }
            // Allocate three referees
            $officials = array(1 => 'CR', 2 => 'AR 1', 3 => 'AR 2');
            foreach($officials as $slot => $role)
            {
                $official = $game->getOfficialForSlot($slot);
                if (!$official)
                {
                    $official = $game->createGameOfficial();
                    $official->setSlot($slot);
                    $official->setRole($role);
                    $game->addOfficial($official);
                }
            }
            // $pool = $row['pool'];
            $gameRepo->save($game);

            // For debugging
            /*
            echo sprintf("Game %2d %s %s %-8s %s %-4s v %-4s %-28s %-28s\n",
                $rowx['num'],$rowx['date'],$rowx['time'],
                $rowx['field'],$rowx['div'],
                $rowx['home_pool'],$rowx['away_pool'],
                $rowx['home_name'],$rowx['away_name']
            );*/
        }
        $gameRepo->commit();

      //print_r($header);
        return;
    }
}
?>
