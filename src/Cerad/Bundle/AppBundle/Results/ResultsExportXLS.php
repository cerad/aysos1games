<?php
namespace Cerad\Bundle\AppBundle\Results;

/* ============================================
 * Basic referee schedule exporter
 */
class ResultsExportXLS
{
    protected $exportIf = array();

    public function getExports () { return $this->exportIf; }
    public function setExports ($value) { $this->exportIf = $value; }

    protected $counts = array();

    protected $dRec = array(
      'hdr' => "AYSO Section One U10/U12/U14 Playoffs 2014",
      'results' => "POOL PLAY: Saturday November 23, 2013",
      'standings' => 'STANDINGS',
    );

  protected $poFields = array(
    'U10BLeague' => array(
      'sf1' => "Game #149 - Sunday Feb 23 - 9:00 AM - Green 1",
      'sf2' => "Game #150 - Sunday Feb 23 - 9:00 AM - Yellow 2",
      'fin' => "Game #161 - Sunday Feb 23 - 1:00 PM - Green 1",
      'con' => "Game #162 - Sunday Feb 23 - 1:00 PM - Yellow 2",
    ),
    'U10GLeague' => array(
      'sf1' => "Game #155 - Sunday Feb 23 - 11:00 AM - Green 1",
      'sf2' => "Game #156 - Sunday Feb 23 - 11:00 AM - Yellow 2",
      'fin' => "Game #167 - Sunday Feb 23 - 3:00 PM - Green 1",
      'con' => "Game #168 - Sunday Feb 23 - 3:00 PM - Yellow 2",
    ),
    'U12BLeague' => array(
      'sf1' => "Game #153 - Sunday Feb 23 - 11:00 AM - Blue 2",
      'sf2' => "Game #154 - Sunday Feb 23 - 11:00 AM - Blue 3",
      'fin' => "Game #165 - Sunday Feb 23 - 3:00 PM - Blue 2",
      'con' => "Game #166 - Sunday Feb 23 - 3:00 PM - Blue 3",
    ),
    'U12GLeague' => array(
      'sf1' => "Game #147 - Sunday Feb 23 - 9:00 AM - Blue 2",
      'sf2' => "Game #148 - Sunday Feb 23 - 9:00 AM - Blue 3",
      'fin' => "Game #159 - Sunday Feb 23 - 1:00 PM - Blue 2",
      'con' => "Game #160 - Sunday Feb 23 - 1:00 PM - Blue 3",
    ),
    'U14BLeague' => array(
      'sf1' => "Game #145 - Sunday Feb 23 - 9:00 AM - White 2",
      'sf2' => "Game #146 - Sunday Feb 23 - 9:00 AM - Red 2",
      'fin' => "Game #157 - Sunday Feb 23 - 1:00 PM - White 2",
      'con' => "Game #158 - Sunday Feb 23 - 1:00 PM - Red 2",
    ),
    'U14GLeague' => array(
      'sf1' => "Game #151 - Sunday Feb 23 - 11:00 AM - White 2",
      'sf2' => "Game #152 - Sunday Feb 23 - 11:00 AM - Red 2",
      'fin' => "Game #163 - Sunday Feb 23 - 3:00 PM - White 2",
      'con' => "Game #164 - Sunday Feb 23 - 3:00 PM - Red 2",
    ),
    'U10BAllStars' => array(
      'sf1' => "Game #283 - Sunday Mar 2 - 11:00 AM - Green 2",
      'sf2' => "Game #284 - Sunday Mar 2 - 11:00 AM - Yellow 1",
      'fin' => "Game #295 - Sunday Mar 2 - 3:00 PM - Green 2",
      'con' => "Game #296 - Sunday Mar 2 - 3:00 PM - Yellow 1",
    ),
    'U10GAllStars' => array(
      'sf1' => "Game #277 - Sunday Mar 2 - 9:00 AM - Green 2",
      'sf2' => "Game #278 - Sunday Mar 2 - 9:00 AM - Yellow 1",
      'fin' => "Game #289 - Sunday Mar 2 - 1:00 PM - Green 2",
      'con' => "Game #290 - Sunday Mar 2 - 1:00 PM - Yellow 1",
    ),
    'U12BAllStars' => array(
      'sf1' => "Game #275 - Sunday Mar 2 - 9:00 AM - Blue 2",
      'sf2' => "Game #276 - Sunday Mar 2 - 9:00 AM - Blue 3",
      'fin' => "Game #287 - Sunday Mar 2 - 1:00 PM - Blue 2",
      'con' => "Game #288 - Sunday Mar 2 - 1:00 PM - Blue 3",
    ),
    'U12GAllStars' => array(
      'sf1' => "Game #281 - Sunday Mar 2 - 11:00 AM - Blue 2",
      'sf2' => "Game #282 - Sunday Mar 2 - 11:00 AM - Blue 3",
      'fin' => "Game #293 - Sunday Mar 2 - 3:00 PM - Blue 2",
      'con' => "Game #294 - Sunday Mar 2 - 3:00 PM - Blue 3",
    ),
    'U14BAllStars' => array(
      'sf1' => "Game #279 - Sunday Mar 2 - 11:00 AM - White 2",
      'sf2' => "Game #280 - Sunday Mar 2 - 11:00 AM - Red 2",
      'fin' => "Game #291 - Sunday Mar 2 - 3:00 PM - White 2",
      'con' => "Game #292 - Sunday Mar 2 - 3:00 PM - Red 2",
    ),
    'U14GAllStars' => array(
      'sf1' => "Game #273 - Sunday Mar 2 - 9:00 AM - White 2",
      'sf2' => "Game #274 - Sunday Mar 2 - 9:00 AM - Red 2",
      'fin' => "Game #285 - Sunday Mar 2 - 1:00 PM - White 2",
      'con' => "Game #286 - Sunday Mar 2 - 1:00 PM - Red 2",
    ),
    'U10BExtra' => array(
      'sf1' => "Game #311 - Saturday Mar 1 - 11:00 AM - Green 1",
      'sf2' => "Game #312 - Saturday Mar 1 - 11:00 AM - Green 2",
      'fin' => "Game #323 - Saturday Mar 1 - 3:00 PM - Green 1",
      'con' => "Game #324 - Saturday Mar 1 - 3:00 PM - Green 2",
    ),
    'U10GExtra' => array(
      'sf1' => "Game #305 - Saturday Mar 1 - 9:00 AM - Green 1",
      'sf2' => "Game #306 - Saturday Mar 1 - 9:00 AM - Green 2",
      'fin' => "Game #317 - Saturday Mar 1 - 1:00 PM - Green 1",
      'con' => "Game #318 - Saturday Mar 1 - 1:00 PM - Green 2",
    ),
    'U11BExtra' => array(
      'sf1' => "Game #331 - Sunday Mar 2 - 11:00 AM - Blue 4",
      'sf2' => "Game #332 - Sunday Mar 2 - 11:00 AM - Blue 5",
      'fin' => "Game #339 - Sunday Mar 2 - 3:00 PM - Blue 4",
      'con' => "Game #340 - Sunday Mar 2 - 3:00 PM - Blue 5",
    ),
    'U11GExtra' => array(
      'sf1' => "Game #327 - Sunday Mar 2 - 9:00 AM - Blue 4",
      'sf2' => "Game #328 - Sunday Mar 2 - 9:00 AM - Blue 5",
      'fin' => "Game #335 - Sunday Mar 2 - 1:00 PM - Blue 4",
      'con' => "Game #336 - Sunday Mar 2 - 1:00 PM - Blue 5",
    ),
    'U12BExtra' => array(
      'sf1' => "Game #309 - Saturday Mar 1 - 11:00 AM - Blue 6",
      'sf2' => "Game #310 - Saturday Mar 1 - 11:00 AM - Blue 1",
      'fin' => "Game #321 - Saturday Mar 1 - 3:00 PM - Blue 6",
      'con' => "Game #322 - Saturday Mar 1 - 3:00 PM - Blue 1",
    ),
    'U12GExtra' => array(
      'sf1' => "Game #303 - Saturday Mar 1 - 9:00 AM - Blue 6",
      'sf2' => "Game #304 - Saturday Mar 1 - 9:00 AM - Blue 1",
      'fin' => "Game #315 - Saturday Mar 1 - 1:00 PM - Blue 6",
      'con' => "Game #316 - Saturday Mar 1 - 1:00 PM - Blue 1",
    ),
    'U13BExtra' => array(
      'sf1' => "Game #329 - Sunday Mar 2 - 11:00 AM - White 3",
      'sf2' => "Game #330 - Sunday Mar 2 - 11:00 AM - White 4",
      'fin' => "Game #337 - Sunday Mar 2 - 3:00 PM - White 3",
      'con' => "Game #338 - Sunday Mar 2 - 3:00 PM - White 4",
    ),
    'U13GExtra' => array(
      'sf1' => "Game #325 - Sunday Mar 2 - 9:00 AM - White 3",
      'sf2' => "Game #326 - Sunday Mar 2 - 9:00 AM - White 4",
      'fin' => "Game #333 - Sunday Mar 2 - 1:00 PM - White 3",
      'con' => "Game #334 - Sunday Mar 2 - 1:00 PM - White 4",
    ),
    'U14BExtra' => array(
      'sf1' => "Game #307 - Saturday Mar 1 - 11:00 AM - Red 1",
      'sf2' => "Game #308 - Saturday Mar 1 - 11:00 AM - Red 2",
      'fin' => "Game #319 - Saturday Mar 1 - 3:00 PM - Red 1",
      'con' => "Game #320 - Saturday Mar 1 - 3:00 PM - Red 2",
    ),
    'U14GExtra' => array(
      'sf1' => "Game #301 - Saturday Mar 1 - 9:00 AM - Red 1",
      'sf2' => "Game #302 - Saturday Mar 1 - 9:00 AM - Red 2",
      'fin' => "Game #313 - Saturday Mar 1 - 1:00 PM - Red 1",
      'con' => "Game #314 - Saturday Mar 1 - 1:00 PM - Red 2",
    ),
    'U16B' => array(
      'sf1' => "Game #49 - Sunday - Nov. 24th -  8:30am - Ayala 9",
      'sf2' => "Game #50 - Sunday - Nov. 24th -  8:30am - Ayala 10",
      'fin' => "Game #57 - Sunday - Nov. 24th - 12:30pm - Ayala 9",
      'con' => "Game #58 - Sunday - Nov. 24th - 12:30pm - Ayala 10",
    ),
    'U16G' => array(
      'sf1' => "Game #51 - Sunday - Nov. 24th -  8:30am - Ayala 11",
      'sf2' => "Game #52 - Sunday - Nov. 24th -  8:30am - Ayala 12",
      'fin' => "Game #59 - Sunday - Nov. 24th - 12:30pm - Ayala 11",
      'con' => "Game #60 - Sunday - Nov. 24th - 12:30pm - Ayala 12",
    ),
    'U19B' => array(
      'sf1' => "Game #53 - Sunday - Nov. 24th - 10:30am - Ayala 9",
      'sf2' => "Game #54 - Sunday - Nov. 24th - 10:30am - Ayala 10",
      'fin' => "Game #61 - Sunday - Nov. 24th -  2:30pm - Ayala 9",
      'con' => "Game #62 - Sunday - Nov. 24th -  2:30pm - Ayala 10",
    ),
    'U19G' => array(
      'sf1' => "Game #55 - Sunday - Nov. 24th - 10:30am - Ayala 11",
      'sf2' => "Game #56 - Sunday - Nov. 24th - 10:30am - Ayala 12",
      'fin' => "Game #63 - Sunday - Nov. 24th -  2:30pm - Ayala 11",
      'con' => "Game #64 - Sunday - Nov. 24th -  2:30pm - Ayala 12",
    ),
  );

  protected $regionsXLS = array(
    ##League, AllStars, Extra, U16U19
    'League' => array(
      ##pool number
      1 => array(
        'Results' => array("B4:G16","B4:G4","B5:G16"),
        'Standings' => array("I4:N8","I4:N4","I5:N8"),
      ),
      2 => array(
        'Results' => array("B19:G31","B19:G19","B20:G31"),
        'Standings' => array("I19:N23","I19:N19","I20:N23"),
      ),
      3 => array(
        'Results' => array("B34:G46","B34:G34","B35:G46"),
        'Standings' => array("I34:N38","I34:N34","I34:N38"),
      ),
      4 => array(
        'Results' =>array("B49:G61","B49:G49","B50:G61"),
        'Standings' => array("I49:N53","I49:N49","I50:N53"),
      ),
    ),
    'Extra' => array(
      ##pool number
      1 => array(
        'Results' => array("B4:G16","B4:G4","B5:G16"),
        'Standings' => array("I4:N8","I4:N4","I5:N8"),
      ),
      2 => array(
        'Results' => array("B19:G31","B19:G19","B20:G31"),
        'Standings' => array("I19:N23","I19:N19","I20:N23"),
      ),
      //3 => array(
      //  'Results' => array("B34:G46","B34:G34","B35:G46"),
      //  'Standings' => array("I34:N38","I34:N34","I34:N38"),
      //),
      //4 => array(
      //  'Results' =>array("B49:G61","B49:G49","B50:G61"),
      //  'Standings' => array("I49:N53","I49:N49","I50:N53"),
      //),
    ),
    'AllStars' => array(
      ##pool number
      1 => array(
        'Results' => array("B4:G16","B4:G4","B5:G16"),
        'Standings' => array("I4:N8","I4:N4","I5:N8"),
      ),
      2 => array(
        'Results' => array("B19:G31","B19:G19","B20:G31"),
        'Standings' => array("I19:N23","I19:N19","I20:N23"),
      ),
      //3 => array(
      //  'Results' => array("B34:G46","B34:G34","B35:G46"),
      //  'Standings' => array("I34:N38","I34:N34","I34:N38"),
      //),
      //4 => array(
      //  'Results' =>array("B49:G61","B49:G49","B50:G61"),
      //  'Standings' => array("I49:N53","I49:N49","I50:N53"),
      //),
    ),
    'U16U19' => array(
      1 => array(
        'Results' => array("B4:G10","B4:G4","B5:G10"),
        'Standings' => array("I4:N7","I4:N4","I5:N10"),
      ),
      2 => array(
        'Results' => array("B13:G19","B13:G13","B14:G19"),
        'Standings' => array("I13:N16","I13:N13","I14:N16"),
      ),
      3 => array(
        'Results' => array("B22:G28","B22:G22","B23:G28"),
        'Standings' => array("I22:N25","I22:N22","I23:N25"),
      ),
      4 => array(
        'Results' =>array("B31:G37","B31:G31","B32:G37"),
        'Standings' => array("I31:N34","I31:N31","I32:N34"),
      ),
    ),
  );

    protected $widths = array
    (
        'Pool' => 21,
        'Team' => 21, 'Team ' => 13,
        'Game' =>  12,
        'Blank' => 3,

        'Score' => 12, 'Send Off' => 12, 'Points' => 13, 'Sports Points' => 12,
        'Player Send Off Total' => 13,'Substitute Send Off Total' => 13,'Coach Ejections Total' => 13,'Spectator Ejections Total' => 13,
        'Total Points' => 13, 'Send Off Total' => 13, 'Goals Against' => 13,
        'Status 1' => 10, 'Status 2' => 10,  'PA' =>  4,
        'DOW Time' => 15, 'Field' =>  6, 'Pool' => 12, 'Type' =>  6,

        'GS' => 8, 'SP' => 8, 'YC' => 8, 'RC' => 8, 'CE' => 8, 'PE' => 8,
        'Goals Scored' => 12, 'Goals Allowed' => 12, 'Total Sports' => 13,'3 Goal Differential' => 16,

        'TPE' => 8, 'GT'  => 8, 'GP'  => 8, 'Games Played'  => 13,  'GW'  => 8,
        'TGS' => 8, 'TGA' => 8, 'TYC' => 8, 'TRC' => 8, 'TCE' => 8, 'TSP' => 8,
        'WPF' => 8, 'SfP' => 8,
    );
    protected $center = array
    (
        'Game','HGS','HPM','HPE','APE','APM','AGS',
        'PE','PM','GP','GW','GS','GA','YC','RC','CD','SD','SP',
    );
    public function __construct($excel)
    {
        $this->excel = $excel;
        $this->setExports(array('League'));
    }
    protected function setHeaders($ws,$map,$row = 0)
    {
        $col = 1;
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
    //protected function setRow($ws,$map,$person,&$row)
    //{
    //    $row++;
    //    $col = 2;
    //    foreach($map as $propName)
    //    {
    //        $ws->setCellValueByColumnAndRow($col++,$row,$person[$propName]);
    //    }
    //    return $row;
    //}
    protected function pageSetup($ws,$printArea,$fitToHeight = 0)
    {
      $ws->getPageSetup()->setPrintArea($printArea);
      $ws->getPageMargins()->setTop(0.5);
      $ws->getPageMargins()->setRight(0.25);
      $ws->getPageMargins()->setLeft(0.25);
      $ws->getPageMargins()->setBottom(0.25);

        $ws->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $ws->getPageSetup()->setPaperSize  (\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $ws->getPageSetup()->setFitToPage(true);
        $ws->getPageSetup()->setFitToWidth(1);
        $ws->getPageSetup()->setFitToHeight($fitToHeight);
        $ws->setPrintGridLines(true);

      $ws->getPageSetup()->setHorizontalCentered(true);
      $ws->getPageSetup()->setVerticalCentered(true);
      $ws->setSelectedCell("A1");

      $footerText = '&R&I&10' . 'Printed on ' . date('d-M-Y').' at '. date('H:i:s T');
      $ws->getHeaderFooter()->setOddFooter($footerText);
      $ws->getHeaderFooter()->setEvenFooter($footerText);

        return;
    }
    /* =======================================================
     * Write the list of games to the spreadsheet
     *
     */
    public function generatePoolGames($ws,$games,$reports,&$row)
    {
        $map = array(
            'Pool'     => 'pool',
            'Game'     => 'game',
          //  'Status 1' => 'status',
          //  'Status 2' => 'status',
          //  'PA'       => 'pointsApplied',
          //  'DOW Time' => 'date',
          //  'Field'    => 'field',
          //  'Pool'     => 'pool',
          //  'Type'     => true,

            'Score' => true, 'Send Off' => true, 'Points' => true, 'Sports Points' => true,
            'Blank' => 'blank',
            'Team ' => true,'Total Points' => true, 'Send Off Total' => true, 'Goals Against' => true, '3 Goal Differential' => true, 'Total Sports' => true
        );
        $row = $this->setHeaders($ws,$map,$row);
        $topRow = $row;

        foreach($games as $game)
        {
           $col = 1;
            if ( $row == $topRow )
            {
              $ws->setCellValueByColumnAndRow($col,$row,'POOL ' . substr($game->getGroup(), -1) );
            }

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
/*
            $dt  = $game->getDtBeg();
            $dtg = $dt->format('D h:i A');

            //$ws->setCellValueByColumnAndRow($col++,$row,$game->getGroup());

             $ws->setCellValueByColumnAndRow($col++,$row,$game->getStatus());

            $ws->setCellValueByColumnAndRow($col++,$row,$gameReport->getStatus());

            // PA stands for Points Applied
            // Special case, probably not used for the s1games
            // Remove later
            $ws->setCellValueByColumnAndRow($col++,$row,'PA');
          //$ws->setCellValueByColumnAndRow($col++,$row,$game->getPointsApplied());

            $ws->setCellValueByColumnAndRow($col++,$row,$dtg);
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getField()->getName());
*/
            foreach(array($homeTeam,$awayTeam) as $team)
            {
                $row++;
                $col = 1;

                $report = $team->getReport();

                $ws->setCellValueByColumnAndRow($col++,$row,$team->getName()); //Team name
                $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());  // Game #
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored()); // Score
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getTotalEjections() ); // Sendoffs
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned() ); // Total Points
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship()); //Sports Points
             }
       }

        //clear separator column header cell
        $ws->setCellValueByColumnAndRow(7,$topRow,'');

        foreach($reports as $report)
        {
            $topRow++;
            $col = 8;

            $team = $report->getTeam();

            $ws->setCellValueByColumnAndRow($col++,$topRow,$team->getName() );
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getPointsEarned() );
            $r = $report->getTotalEjections();
            if ( !$r )
              $r = 0;
            $ws->setCellValueByColumnAndRow($col++,$topRow, $r);
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getGoalsAllowed() );
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getGoalDifferential() );
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getSportsmanship() );
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
            'Games Played'  => true,

            'Goals Scored' => true, 'Goals Allowed' => true, '3 Goal Differential' => true,
            'Player Send Off Total' => true,'Substitute Send Off Total' => true,'Coach Ejections Total' => true,'Spectator Ejections Total' => true,
            'Total Points' => true, 'Total Sports' => true,

          //  'GW'  => true,
          //  'TGS' => true, 'TGA' => true, 'TYC' => true, 'TRC' => true, 'TCE' => true,
          ////'SfP' => true, // Soccerfest points
          //  'TPE' => true, 'TSP' => true,
        );
        $row = $this->setHeaders($ws,$map,$row);

        foreach($reports as $report)
        {
            $row++;
            $col = 1;

            $team = $report->getTeam();

            $ws->setCellValueByColumnAndRow($col++,$row,$team->getGroup());
            $ws->setCellValueByColumnAndRow($col++,$row,$team->getName());

            // Using empty confuses php 5.3 and 5.4
            $gp = $report->getGamesPlayed();
            if (!$gp) $gp = 0;

            $ws->setCellValueByColumnAndRow($col++,$row,$gp);
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsAllowed());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalDifferential());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerEjections());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getBenchEjections());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getCoachEjections());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getSpecEjections());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship());

            //$ws->setCellValueByColumnAndRow($col++,$row,$report->getWinPercent());
            //$ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesTotal());
            //$ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());

          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesWon());
          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
          //
          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerWarnings());
          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerEjections());
          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getCoachEjections());
          ////$ws->setCellValueByColumnAndRow($col++,$row,$team->getSfSP());
          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());
          //  $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship());
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
      try {
      // Spreadsheet
      $this->ss = $ss = $this->excel->newSpreadSheet();
      $sheetIndex = 0;

      // Individual sheets for division
      $keyx2 = "x x x x";
      foreach($pools as $key => $pool)
      {
        $Values = explode(' ',$key);
        $Valuesx2 = explode(' ',$keyx2);
        $Keys = array('program','div','pool','team');
        $Key = array_combine($Keys, $Values);
        $Keyx2 = array_combine($Keys, $Valuesx2);

//$varDump=array('program'=>$Key['program'], 'exportIf'=>$this->exportIf);
//var_dump($varDump);
//print_r("<br>****************<br>");
        if (count($pool['teams']) AND in_array($Key['program'], $this->exportIf, true) ) {
          // key = U19B PP Bracket
          if ($Keyx2['div'] . $Keyx2['program'] == $Key['div'] . $Keyx2['program'])
          {
              $gameRow += 3;
              $teamRow += 3;
          }
          else
          {
              $gameWS = $ss->createSheet($sheetIndex++);
              //$this->pageSetup($gameWS);
              $gameWS->setTitle('Games ' . $Key['div'] . ' ' . $Key['program']);

              $gameRow = 4;

              $teamWS = $ss->createSheet($sheetIndex++);
              //$this->pageSetup($teamWS,1);
              $teamWS->setTitle('Teams ' . $Key['div'] . ' ' . $Key['program']);
              $teamRow = 3;
          }
          // Gets called for once for A B C D etc

          //if ($gameRow != 1) $gameWS->setBreak('A' . ($gameRow - 1), \PHPExcel_Worksheet::BREAK_ROW);

          if (substr($Key['div'],-1) == 'B')
          {
            $divTitle = substr($Key['div'],0,-1) . ' Boys ' . $Key['program'];
          }
          else
          {
            $divTitle = substr($Key['div'],0,-1) . ' Girls ' . $Key['program'];
          }

          $gameWS->setCellValueByColumnAndRow(1, 1, $divTitle);
          $gameWS->setCellValueByColumnAndRow(10, 1, $this->dRec['hdr']);
          $gameWS->setCellValueByColumnAndRow(1, 2,  $this->dRec['results']);
          $gameWS->setCellValueByColumnAndRow(8, 2,  $this->dRec['standings']);

          $this->generatePoolGames($gameWS,$pool['games'],$pool['teams'],$gameRow);

          try
          {
            $this->FormatResults($gameWS, $Key['program'], $Key['div']);
          }
          catch (Exception $e)
          {
            throw new Exception('Unrecognized key in ResultsExportXLS:' . $key);
          }

          $gameWS->setShowGridlines(false);
          $gameWS->getColumnDimension('H')->setWidth(3);
          $pageMargins = $teamWS->getPageMargins();

          // margin is set in inches
          $margin = 0;

          $pageMargins->setTop($margin);
          $pageMargins->setBottom($margin+0.25);
          $pageMargins->setLeft($margin);
          $pageMargins->setRight($margin);
          //##U16U19
          //$this->pageSetup($gameWS,"B1:V38",1);
          ##U10-U14
          $this->pageSetup($gameWS,"B1:V61",1);

          $this->generatePoolTeams($teamWS,$pool['teams'],$teamRow);

          $styleArray = array(
           'alignment' => array(
             'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
             'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
             'wrap' => true,
            )
          );
          $highestColumn = $teamWS->getHighestColumn();
          $highestRow = $teamWS->getHighestRow();
          $rng = "A1:Z100";

          $teamWS->getStyle($rng)->applyFromArray($styleArray);
          $pageMargins = $teamWS->getPageMargins();

          // margin is set in inches
          $margin = 0;

          $pageMargins->setTop($margin);
          $pageMargins->setBottom($margin+0.25);
          $pageMargins->setLeft($margin);
          $pageMargins->setRight($margin);
          $this->pageSetup($teamWS, "b3:m28", 1);

          $keyx2 = $key;
        }
      }

      // Return the spreadsheet
      $ss->setActiveSheetIndex(0);
      return $ss;
      }
      catch (Exception $e){
        echo "Caught exception processing {$Key['program']}:{$Key['div']}:{$Key['pool']}: ",  $e->getMessage(), "<br>";
      }
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

    /* =======================================================
     * Called by controller to format the Games export
     */

    protected function FormatPlayoffHeader($ws,$dRec)
    {
      $styleArray = array(
          'font' => array(
            'bold' => true,
            'italic' => true,
            'size' => 36,
          ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          'vertical'  => \PHPExcel_Style_Alignment::VERTICAL_TOP,
        )
      );

      $ws->getStyle('B1')->applyFromArray($styleArray);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 24,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical'  => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
      );

      $ws->getStyle('K1')->applyFromArray($styleArray);

      $ws->getRowDimension('1')->setRowHeight(50);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 16,
          'italic' => true,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $ws->getStyle('B2')->applyFromArray($styleArray);
      $ws->mergeCells('B2:G2');

      $ws->getStyle('I2')->applyFromArray($styleArray);
      $ws->mergeCells('I2:N2');

    }

    protected function FormatPlayoffResults($ws, $p)
    {
      list($r, $h, $d) = $p;

      //format the header range $h
      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 16,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'=>true,
         )
      );

      $ws->getStyle($h)->applyFromArray($styleArray);

      //format the data range $d
      $styleArray = array(
        'font' => array(
          'bold' => false,
          'size' => 14,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'=>true,
        )
      );

      $ws->getStyle($d)->applyFromArray($styleArray);

      //format the table range $r
      $ws->getStyle($r)->getBorders()->getInside()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
      $ws->getStyle($r)->getBorders()->getOutline()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);

    }

    protected function pFormatTitle($ws, $rng1, $title1, $rng2 = '', $title2 = '')
    {
      $ws->setCellValue($rng1, $title1);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 16,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $ws->getStyle($rng1)->applyFromArray($styleArray);

      if ($title2 != '')
      {
        $ws->setCellValue($rng2, $title2);

        $styleArray = array(
            'font' => array(
            'bold' => true,
            'size' => 16,
          ),
          'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          )
        );

        $ws->getStyle($rng2)->applyFromArray($styleArray);
      }
    }

    private function pFormatWP($ws, $rng,$title)
    {
      $ws->setCellValue($rng, $title);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 16,
          'underline'=>true,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
      );

      $ws->getStyle($rng)->applyFromArray($styleArray);

    }

    protected function pFormatEntry($ws,$rng)
    {
      $ws->mergeCells($rng);

      $ws->getStyle($rng)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
    }

    protected function pFormatField($ws, $rng, $title)
    {
      $ws->setCellValue($rng, $title);
        $styleArray = array(
          'font' => array(
          'bold' => false,
          'size' => 14,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )
      );

      $ws->getStyle($rng)->applyFromArray($styleArray);

    }

    protected function FormatResults($ws, $prog, $div)
    {
      $this->FormatPlayoffHeader ($ws,$this->dRec);

      foreach ($this->regionsXLS[$prog] as $pool){
        $this->FormatPlayoffResults ($ws,$pool['Results']); //Pool Results
        $this->FormatPlayoffResults ($ws,$pool['Standings']);  //Pool Standings
      }
      $this->AddPlayoffSummaries ($ws,$this->poFields[$div.$prog]);
    }

    protected function AddPlayoffSummaries($ws,$dFields)
    {
      $sf1 = explode('-', $dFields['sf1']);
      $sf2 = explode('-', $dFields['sf2']);

      $this->pFormatTitle($ws,"s3","Semi Finals");
      $this->pFormatWP($ws,"q4","Winner Pool 1");
      $this->pFormatWP($ws,"u4","Winner Pool 2");
      $this->pFormatEntry ($ws,"p7:r7");
      $this->pFormatEntry ($ws,"t7:v7");
      $this->pFormatField ($ws,"s9", $dFields['sf1']);

      $this->pFormatTitle($ws,"s12","Semi Finals");
      $this->pFormatWP($ws,"q13","Winner Pool 3");
      $this->pFormatWP($ws,"u13","Winner Pool 4");
      $this->pFormatEntry ($ws,"p16:r16");
      $this->pFormatEntry ($ws,"t16:v16");
      $this->pFormatField ($ws,"s18", $dFields['sf2']);

      $this->pFormatTitle ($ws,"s21", "Championship Match");
      $this->pFormatWP ($ws,"q22","Winner ".trim($sf1[0]));
      $this->pFormatWP ($ws,"u22","Winner ".trim($sf2[0]));
      $this->pFormatEntry ($ws,"p25:r25");
      $this->pFormatEntry ($ws,"t25:v25");
      $this->pFormatField ($ws,"s27", $dFields['fin']);

      $this->pFormatTitle ($ws,"s30", "Consolation Match");
      $this->pFormatWP ($ws,"q31","Loser ".trim($sf1[0]));
      $this->pFormatWP ($ws,"u31","Loser ".trim($sf2[0]));
      $this->pFormatEntry ($ws,"p34:r34");
      $this->pFormatEntry ($ws,"t34:v34");
      $this->pFormatField ($ws,"s36", $dFields['con']);

    }
}
?>
