<?php
namespace Cerad\Bundle\AppBundle\Schedule\Games;

use Cerad\Component\Excel\Excel;

/* =======================================================
 * Intended to load a fresh game schedule
 * 13 Feb 2014 - Read the Rick schedule directly
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
    protected function processGame($excel,$item)
    {
        // Need a number
        $num = (int)$item['num'];
        if (!$num) return;
        
        $date         = $excel->processDate($item['date']);
        $time         = $excel->processTime($item['time']);
        $fieldName    = $item['fieldName'];
        $levelKey     = $item['level'];
        $gameGroup    = $item['group'];
        $type         = $item['type'];
        
        $homeTeamGroup = $item['homeTeamGroup'];
        $awayTeamGroup = $item['awayTeamGroup'];
        
        $homeTeamName  = $item['homeTeamName'];
        $awayTeamName  = $item['awayTeamName'];
        
        // PP QF SF FM CM
        $gameGroupType = substr($type,0,2);
        if ($gameGroupType != 'PP') 
        {
            $homeTeamGroup = null;
            $awayTeamGroup = null;
        }
        // Build up the game
        $gameRepo = $this->gameRepo;
        $game = $gameRepo->createGame();
        $game->setNum($num);
        $game->setProjectId($this->projectKey);
        $game->setGroup    ($gameGroup);
        $game->setGroupType($gameGroupType); // Why do I strip the number off here? SF1 vs SF
        $game->setLevelId  ($levelKey);
        
        $game->setField($this->processField($fieldName));
        
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
        
        $homeTeam->setGroup($homeTeamGroup);
        $awayTeam->setGroup($awayTeamGroup);
        
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
echo sprintf("Game %d %s %s %s\n",$gameNum,$gameGroup,$homeTeamGroup,$awayTeamGroup);
die();
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
        
        // Games
        $gameWs = $reader->getSheetByName('Complete Schedule');
        $gameRows = $gameWs->toArray();
        
        $gameHeaders = array_shift($gameRows);
        $gameIndexes = $this->processHeaders($gameHeaders);
      
        foreach($gameRows as $row)
        {
            $item = $gameIndexes;
            foreach($gameIndexes as $key => $index)
            {
                $item[$key] = trim($row[$index]);
            }
            $this->processGame($excel,$item);
        }
        $this->gameRepo->commit();
        
        return $this->results;
    }
    /* ========================================================
     * Returns an array of fields mapped to offset
     * If required fields are not found then return null
     */
    protected function processHeaders($headers)
    {
        $map = array(    
            'Game #' => 'num',
            'Date'   => 'date',
            'Time'   => 'time',
            'Field'  => 'fieldName',
            'Level'  => 'level',
            'Group'  => 'group',
            'GT'     => 'type',
            
            'HT Group' => 'homeTeamGroup',
            'AT Group' => 'awayTeamGroup',
            
            'Home Team'     => 'homeTeamName',
            'Visiting Team' => 'awayTeamName',
        );
        $index = array();
        foreach($map as $key)
        {
            $indexes[$key] = null;
        }
        foreach($headers as $index => $header)
        {
            if (isset($map[$header])) $indexes[$map[$header]] = $index;
        }

        $missing = array();
        foreach(array('program','type') as $key)
        {
            // if ($indexes[$key] === null) $missing = $key;
        }
        if (count($missing))
        {
            print_r($missing);
            die("*** MISSING\n");
            return null;
        }
        return $indexes;
    }
}
?>
