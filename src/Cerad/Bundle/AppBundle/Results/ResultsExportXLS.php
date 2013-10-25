<?php
namespace Cerad\Bundle\AppBundle\Results;

/* ============================================
 * Basic referee schedule exporter
 */
class ResultsExportXLS
{
    protected $counts = array();
    
    protected $widths = array
    (
        'Game' =>  6, 'Status 1' => 10, 'Status 2' => 10,  'PA' =>  4,
        'DOW Time' => 15, 'Field' =>  6, 'Pool' => 12, 'Type' =>  6, 'Team' => 30,
            
        'GS' => 5, 'SP' => 5, 'YC' => 5, 'RC' => 5, 'CE' => 5, 'PE' => 5,
        
        'TPE' => 5, 'GT'  => 5, 'GP'  => 5, 'GW'  => 5,
        'TGS' => 5, 'TGA' => 5, 'TYC' => 5, 'TRC' => 5, 'TCE' => 5, 'TSP' => 5,
        'WPF' => 5, 'SfP' => 5,
    );
    protected $center = array
    (
        'Game','HGS','HPM','HPE','APE','APM','AGS',
        'PE','PM','GP','GW','GS','GA','YC','RC','CD','SD','SP',
    );
    public function __construct($excel)
    {
        $this->excel = $excel;
    }
    protected function setHeaders($ws,$map,$row = 1)
    {
        $col = 0;
        foreach(array_keys($map) as $header)
        {
            $ws->getColumnDimensionByColumn($col)->setWidth($this->widths[$header]);
            $ws->setCellValueByColumnAndRow($col++,$row,$header);

            if (in_array($header,$this->center) == true)
            {
                // Works but not for multiple sheets?
                // $ws->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        return $row;
    }
    // Don't think this is used here
    protected function setRow($ws,$map,$person,&$row)
    {
        $row++;
        $col = 0;
        foreach($map as $propName)
        {
            $ws->setCellValueByColumnAndRow($col++,$row,$person[$propName]);
        }
        return $row;
    }
    protected function pageSetup($ws,$fitToHeight = 0)
    {
        $ws->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $ws->getPageSetup()->setPaperSize  (\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $ws->getPageSetup()->setFitToPage(true);
        $ws->getPageSetup()->setFitToWidth(1);
        $ws->getPageSetup()->setFitToHeight($fitToHeight);
        $ws->setPrintGridLines(true);
        return;
    }
    /* =======================================================
     * Write the list of games to the spreadsheet
     * 
     */
    public function generatePoolGames($ws,$games,&$row)
    {
        $map = array(
            'Game'     => 'game',
            'Status 1' => 'status',
            'Status 2' => 'status',
            'PA'       => 'pointsApplied',
            'DOW Time' => 'date',
            'Field'    => 'field',
            'Pool'     => 'pool',
            'Type'     => true,
            'Team'     => true,
            
            'GS' => true, 'SP' => true, 'YC' => true, 'RC' => true, 'CE' => true, 'PE' => true,
        );
        $row = $this->setHeaders($ws,$map,$row);
        
        foreach($games as $game)
        {
            
            $row++;
            $col = 0;
           
            $gameReport = $game->getReport();
            
            $homeTeam = $game->getHomeTeam();
            $awayTeam = $game->getAwayTeam();
            
            // Page break on pool change
            $poolx = null;
            $pool = $game->getGroup();
            if ($poolx != $pool)
            {
                 //if ($poolx) $ws->setBreak('A' . $row, \PHPExcel_Worksheet::BREAK_ROW);
                $poolx = $pool;
            }
            
            $dt  = $game->getDtBeg();
            $dtg = $dt->format('D h:i A');
          
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getStatus());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$gameReport->getStatus());
            
            // PA stands for Points Applied
            // Special case, probably not used for the s1games
            // Remove later
            $ws->setCellValueByColumnAndRow($col++,$row,'PA');
          //$ws->setCellValueByColumnAndRow($col++,$row,$game->getPointsApplied());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$dtg);
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getField()->getName());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getGroup());
      
            $awayFlag = false;
            foreach(array($homeTeam,$awayTeam) as $team)
            {
                $report = $team->getReport();
                
                if ($awayFlag)
                {
                    $row++;
                    $ws->setCellValueByColumnAndRow(0,$row,$game->getNum());
                    $col = 7;
                }
                if ($awayFlag) $ws->setCellValueByColumnAndRow($col++,$row,'Away');
                else           $ws->setCellValueByColumnAndRow($col++,$row,'Home');
                
                $ws->setCellValueByColumnAndRow($col++,$row,$team->getName());
            
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship());
                
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerWarnings());  // Cautions
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerEjections()); // Sendoffs
                
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getCoachEjections());
                
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());
 
                $awayFlag = true;
            }
        }
        return;
    }
    /* =========================================================================
     * Team standings sorted by the actual standings
     * Tie breaking has been applied so the first team listed should always be the first place team
     * Unless KFTM's are needed
     * 
     * $reports is a list of PoolTeamReport objects
     */
    public function generatePoolTeams($ws,$reports,&$row)
    {
        $map = array
        (
            'Pool' => 'pool',
            'Team' => 'team',
            
            'WPF' => true, 'TPE' => true, 'GT'  => true, 'GP'  => true, 'GW'  => true,
            'TGS' => true, 'TGA' => true, 'TYC' => true, 'TRC' => true, 'TCE' => true,
          //'SfP' => true, // Soccerfest points
            'TSP' => true,
        );
        $row = $this->setHeaders($ws,$map,$row);
        
        foreach($reports as $report)
        {
            $row++;
            $col = 0;
           
            $team = $report->getTeam();    
           
            $ws->setCellValueByColumnAndRow($col++,$row,$team->getGroup());
            $ws->setCellValueByColumnAndRow($col++,$row,$team->getName());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getWinPercent());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesTotal());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesPlayed());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesWon());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsAllowed());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerWarnings());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerEjections());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getCoachEjections());
          //$ws->setCellValueByColumnAndRow($col++,$row,$team->getSfSP());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship());
        }
        return;
    }
    /* ===================================================================
     * Starts up everything and generates individual pool play sheetd
     * 
     * Note: In the current version, $pools contains a list of PoolTeamReports with a key of 'teams'
     * In the older version it actually had a list of pool teams but we no lone have pool team entities
     * just pool team reports.
     * 
     * So some of the code talks about teams when it really should talk about pool team reports
     * Clean it up later
     */
    public function generatePoolPlay($project,$pools)
    {   
        // Spreadsheet
        $this->ss = $ss = $this->excel->newSpreadSheet();
        $sheetIndex = 0;
        
        // Individual sheets for division
        $keyx2 = null;
        foreach($pools as $key => $pool)
        {
            if (count($pool['teams'])) {
                
            // key = U19B PP Bracket
            if (substr($keyx2,0,7) == substr($key,0,7))
            {           
                $gameRow += 2;              
                $teamRow += 3;           
            }
            else
            {
                $gameWS = $ss->createSheet($sheetIndex++);
                $this->pageSetup($gameWS);
                $gameWS->setTitle('Games ' . substr($key,0,7));
                $gameRow = 1;
                
                $teamWS = $ss->createSheet($sheetIndex++);
                $this->pageSetup($teamWS,1);
                $teamWS->setTitle('Teams ' . substr($key,0,7));
                $teamRow = 1;
            }
            // Gets called for once for A B C D etc
            
            if ($gameRow != 1) $gameWS->setBreak('A' . ($gameRow - 1), \PHPExcel_Worksheet::BREAK_ROW);
             
            $this->generatePoolGames($gameWS,$pool['games'],$gameRow);
            $this->generatePoolTeams($teamWS,$pool['teams'],$teamRow);
            
            $keyx2 = $key;
        }}
        
        // Return the spreadsheet
        $ss->setActiveSheetIndex(0);
        return $ss;
        
    }
    /* =======================================================
     * Called by controller to get the content
     */
    protected $ss;
    
    public function getBuffer($ss = null)
    {
        if (!$ss) $ss = $this->ss;
        if (!$ss) return null;
        
        $objWriter = $this->excel->newWriter($ss); // \PHPExcel_IOFactory::createWriter($ss, 'Excel5');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name
        
        return ob_get_clean();        
    }
}
?>
