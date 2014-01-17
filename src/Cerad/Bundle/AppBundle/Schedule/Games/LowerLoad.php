<?php
namespace Cerad\Bundle\AppBundle\Schedule\Games;

use Cerad\Component\Excel\Excel;

/* =======================================================
 * Intended to load a fresh game schedule
 */
class LowerLoad
{
    protected $results;
    protected $projectKey;
    
    protected $gameRepo;
    protected $gameFieldRepo;
    
    protected $pools;
    
    public function __construct($projectKey,$gameRepo,$gameFieldRepo)
    {
        $this->results = new LowerLoadResults();
        
        $this->projectKey    = $projectKey;
        $this->gameRepo      = $gameRepo;
        $this->gameFieldRepo = $gameFieldRepo;
    }
    /* ===============================================================
     * Extract a field
     */
    protected function processField($fieldName)
    {
        $projectKey    = $this->projectKey;
        $gameFieldRepo = $this->gameFieldRepo;
        
        $fieldParts = explode(' ',$fieldName);
        $fieldVenue = $fieldParts[0];
        $fieldSort = (integer)$fieldParts[1];
            
        $gameField = $gameFieldRepo->findOneByProjectName($projectKey,$fieldName);
        if (!$gameField)
        {
            $gameField = $gameFieldRepo->createGameField();
            $gameField->setSort     ($fieldSort);
            $gameField->setName     ($fieldName);
            $gameField->setVenue    ($fieldVenue);
            $gameField->setProjectId($projectKey);
            $gameFieldRepo->save($gameField);
                
            // If we didn't commit then need local cache nonsense
            $gameFieldRepo->commit();
        }
        return $gameField;
    }
    /* ===============================================================
     * Extract a game
     */
    protected function processGame($excel,$row)
    {
        // Need a number
        $num = (int)$row[0];
        if (!$num) return;
        
        $date         = $excel->processDate($row[2]);
        $time         = $excel->processTime($row[5]);
        $field        = $row[ 4];
        $div          = $row[ 6];
        $program      = $row[ 7];
        $type         = $row[12];
        $homeTeamName = $row[ 8];
        $awayTeamName = $row[11];
        
        // AYSO_U14G_Extra
        $levelKey = sprintf('AYSO_%s_%s',$div,$program);
        
        // PP QF SF FM CM
        $gameGroupType = substr($type,0,2);
        if ($gameGroupType != 'PP') 
        {
            $gameGroup     = sprintf('%s %s %s',$program,$div,$type);          // U14G SF1
            $homeTeamGroup = sprintf('%s %s %s',$program,$div,$gameGroupType); // U14G SF
            $awayTeamGroup = sprintf('%s %s %s',$program,$div,$gameGroupType);
        }
        else
        {
            // AYSO_U14G_League R1
            $homeTeamKey = sprintf("AYSO_%s_%s %s",$div,$program,$homeTeamName);
            $awayTeamKey = sprintf("AYSO_%s_%s %s",$div,$program,$awayTeamName);
            
            $homeTeamPool = $this->pools[$homeTeamKey];
            $awayTeamPool = $this->pools[$awayTeamKey];
            
            $gameGroup = sprintf('%s %s PP %d',$program,$div,$homeTeamPool['num']);
            
            $homeTeamGroup = sprintf('%s %s PP %d-%d',$program,$div,$homeTeamPool['num'],$homeTeamPool['index']);
            $awayTeamGroup = sprintf('%s %s PP %d-%d',$program,$div,$awayTeamPool['num'],$awayTeamPool['index']);
        }
        // Build up the game
        $gameRepo = $this->gameRepo;
        $game = $gameRepo->createGame();
        $game->setNum($num);
        $game->setProjectId($this->projectKey);
        $game->setGroup    ($gameGroup);
        $game->setGroupType($gameGroupType);
        $game->setLevelId  ($levelKey);
        
        $game->setField($this->processField($field));
        
        // DateTime
        $dt = $date . ' ' . $time;
            
        $dtBeg = \DateTime::createFromFormat('Y-m-d H:i:s',$dt);
        if (!$dtBeg)
        {
            echo sprintf("*** DT %d '%s'\n",$num,$dt);
            die();
        }
        $dtEnd = clone($dtBeg);
        $dtEnd->add(new \DateInterval('PT80M'));
                
        $game->setDtBeg($dtBeg);
        $game->setDtEnd($dtEnd);
        
        // The teams
        $homeTeam = $game->getHomeTeam();
        $awayTeam = $game->getAwayTeam();
            
        $homeTeam->setName($homeTeamName);
        $awayTeam->setName($awayTeamName);
            
        $homeTeam->setLevelId($levelKey);
        $awayTeam->setLevelId($levelKey);
        
        $homeTeam->setGroup($awayTeamGroup);
        $awayTeam->setGroup($homeTeamGroup);
        
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

        // And save
        $gameRepo->save($game);
        $this->results->countGames++;
    }
    /* ===============================================================
     * Public entry point
     */
    public function process($params)
    {
        // Param stuff
        $this->results->filepath = $params['filepath'];
        $this->results->basename = $params['basename'];
                
        // Open
        if (!is_readable($params['filepath']))
        {
            $this->results->message = '*** Could not open file.';
            return $this->results;
        }
        $excel = new Excel();
        
        $reader = $excel->load($params['filepath']);
        
        // Pools
        $poolWs   = $reader->getSheetByName('Pools');
        $poolRows = $poolWs->toArray();
        $poolHeaders = $poolRows[0];
        $this->pools = array();
        foreach($poolRows as $row)
        {
            $this->processPool($poolHeaders,$row);
        }
        // Games
        $gameWs = $reader->getSheetByName('Schedule');
        $gameRows = $gameWs->toArray();
        foreach($gameRows as $row)
        {
            $this->processGame($excel,$row);
        }
        $this->gameRepo->commit();
        
        return $this->results;
    }
    /* =====================================================
     * Extract a pool
     */
    protected $poolNum;
    protected $poolIndex;
    
    protected function processPool($headers,$row)
    {
        $program = $row[0];
        $poolNum = (int)$row[1];
        if (!$poolNum) return;
        
        if ($poolNum == $this->poolNum) $this->poolIndex++;
        else
        {
            $this->poolNum = $poolNum;
            $this->poolIndex = 1;
        }
        for($i = 2; $i < 8; $i++)
        {
            $div = $headers[$i];
            $area = $row[$i];
            
            $teamKey  = sprintf("AYSO_%s_%s %s",$div,$program,$area);
            
          //echo sprintf("%s %d %d\n",$teamKey,$poolNum,$this->poolIndex);
            
            $this->pools[$teamKey] = array('num' => $poolNum, 'index' => $this->poolIndex);
        }
    }
}
?>
