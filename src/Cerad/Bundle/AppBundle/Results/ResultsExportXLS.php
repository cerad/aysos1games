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
        'Pool' => 21,
        'Team' => 21,
        'Game' =>  8,

        'Score' => 8, 'Send Off' => 8, 'Points' => 13, 'Sports Points' => 13,
        'Total Points' => 13, 'Send Off Total' => 13, 'Goals Against' => 13, 'Total Sports' => 13,
        'Status 1' => 10, 'Status 2' => 10,  'PA' =>  4,
        'DOW Time' => 15, 'Field' =>  6, 'Pool' => 12, 'Type' =>  6,

        'GS' => 8, 'Goals For' => 13, 'SP' => 8, 'YC' => 8, 'RC' => 8, 'CE' => 8, 'PE' => 8,

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
            'Total Points' => true, 'Send Off Total' => true, 'Goals Against' => true, 'Total Sports' => true
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
                $report = $team->getReport();
                $row++;

                $col = 1;

                $ws->setCellValueByColumnAndRow($col++,$row,$team->getName()); //Team name
                $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());  // Game #
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored()); // Score
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerEjections() + $report->getCoachEjections() + $report->getBenchEjections() ); // Sendoffs
                $ws->setCellValueByColumnAndRow($col++,$row,max($report->getPointsEarned(),0) ); // Total Points
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship()); //Sports Points
             }
        }

        foreach($reports as $report)
        {
            $topRow++;
            $col = 7;

            $ws->setCellValueByColumnAndRow($col++,$topRow,max( $report->getPointsEarned(),0) );
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getPlayerEjections() + $report->getCoachEjections() + $report->getBenchEjections() + $report->getSpecEjections() );
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getGoalsAllowed());
            $ws->setCellValueByColumnAndRow($col++,$topRow,$report->getSportsmanship());

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

            'Goals For' => true, 'Goals Against' => true,
            'Send Off Total' => true, 'Total Points' => true, 'Total Sports' => true,

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
            if ( empty($report->getGamesPlayed()) ) {
              $gp = 0;
            }
            else {
              $gp = $report->getGamesPlayed();
            }
            $ws->setCellValueByColumnAndRow($col++,$row,$gp);
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsAllowed());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPlayerEjections());
            $ws->setCellValueByColumnAndRow($col++,$row,max($report->getPointsEarned(),0));
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
        $dRec = array(
          'hdr' => "AYSO - Section 1 Tournament - 2013",
          'title' => "POOL PLAY  Saturday, November 23. 2013",
        );

        $dFields = array(
          'sf1' => "Game #55 - Sunday - Nov. 24th - 10:30am - Ayala 11",
          'sf2' => "Game #50 - Sunday - Nov. 24th - 8:30am - Ayala 10",
          'fin' => "Game #57 - Sunday - Nov. 24th -12:30pm - Ayala 9",
          'con' => "Game #58 - Sunday - Nov. 24th - 12:30pm - Ayala 10",
        );

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
                $gameRow += 3;
                $teamRow += 3;
            }
            else
            {
                $gameWS = $ss->createSheet($sheetIndex++);
                $this->pageSetup($gameWS);
                $gameWS->setTitle('Games ' . substr($key,0,7));
                $gameRow = 4;

                $teamWS = $ss->createSheet($sheetIndex++);
                $this->pageSetup($teamWS,1);
                $teamWS->setTitle('Teams ' . substr($key,0,7));
                $teamRow = 3;
            }
            // Gets called for once for A B C D etc

            //if ($gameRow != 1) $gameWS->setBreak('A' . ($gameRow - 1), \PHPExcel_Worksheet::BREAK_ROW);

            if (substr($key,3,1) == 'B')
            {
              $divTitle = substr($key,0,3). ' Boys';
            }
            else
            {
              $divTitle = substr($key,0,3). ' Girls';
            }
            $gameWS->setCellValueByColumnAndRow(2, 1, $divTitle);
            $gameWS->setCellValueByColumnAndRow(10, 1, $dRec['hdr']);
            $gameWS->setCellValueByColumnAndRow(4, 2,  $dRec['title']);
            $this->generatePoolGames($gameWS,$pool['games'],$pool['teams'],$gameRow);
            $this->FormatPlayoffSummary($gameWS);
            $gameWS->setShowGridlines(false);
            $gameWS->getPageSetup()->setPrintArea('A1:S38');
            $gameWS->getPageSetup()->setFitToHeight(1);
            $gameWS->getPageSetup()->setHorizontalCentered(true);

            $this->generatePoolTeams($teamWS,$pool['teams'],$teamRow);

            $styleArray = array(
             'alignment' => array(
               'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              )
            );
            $highestColumm = $teamWS->getHighestColumn();
            $highestRow = $teamWS->getHighestRow();
            $rng = "A1:Z100";

            $teamWS->getStyle($rng)->applyFromArray($styleArray);
            $teamWS->setSelectedCell("A1");
            $teamWS->getPageSetup()->setHorizontalCentered(true);

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

    /* =======================================================
     * Called by controller to format the Games export
     */

    protected function FormatPlayoffHeader($ws,$dRec)
    {
      $styleArray = array(
          'font' => array(
            'bold' => true,
            'size' => 24,
          ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $ws->getStyle('C1')->applyFromArray($styleArray);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 18,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $ws->getStyle('K1')->applyFromArray($styleArray);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 12,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $ws->getStyle('E2')->applyFromArray($styleArray);

    }

    protected function FormatPlayoffResults($ws, $r, $r2, $c3, $c4)
    {
      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 12,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'=>true,
         )
      );

      $ws->getStyle($r)->applyFromArray($styleArray);

      $styleArray = array(
        'font' => array(
          'bold' => false,
          'size' => 10,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'=>true,
        )
      );

      $ws->getStyle($r2)->applyFromArray($styleArray);

      $ws->getStyle($c3)->getBorders()->getInside()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
      $ws->getStyle($c3)->getBorders()->getOutline()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);

      $ws->getStyle($c4)->getBorders()->getInside()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
      $ws->getStyle($c4)->getBorders()->getOutline()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);

    }

    protected function pFormatTitle($ws, $rng1, $title1, $rng2 = '', $title2 = '')
    {
      $ws->setCellValue($rng1, $title1);

      $styleArray = array(
        'font' => array(
          'bold' => true,
          'size' => 12,
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
            'size' => 12,
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
          'size' => 12,
          'underline'=>true,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
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
          'size' => 10,
        ),
        'alignment' => array(
          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )
      );

      $ws->getStyle($rng)->applyFromArray($styleArray);

    }

    protected function AddPlayoffSummaries($ws,$dFields)
    {

      $this->pFormatTitle($ws,"P4","Semi Finals");
      $this->pFormatWP($ws,"N5","Winner Pool 1");
      $this->pFormatWP($ws,"R5","Winner Pool 2");
      $this->pFormatEntry ($ws,"M9:O9");
      $this->pFormatEntry ($ws,"Q9:S9");
      $this->pFormatField ($ws,"P11", $dFields['sf1']);

      $this->pFormatTitle($ws,"P13","Semi Finals");
      $this->pFormatWP($ws,"N14","Winner Pool 3");
      $this->pFormatWP($ws,"R14","Winner Pool 4");
      $this->pFormatEntry ($ws,"M18:O18");
      $this->pFormatEntry ($ws,"Q18:S18");
      $this->pFormatField ($ws,"P20", $dFields['sf2']);

      $this->pFormatTitle ($ws,"P22", "Finals", "P23", "Championship Game");
      $this->pFormatWP ($ws,"N24","Winner Game #49");
      $this->pFormatWP ($ws,"R24","Winner Game #50");
      $this->pFormatEntry ($ws,"M28:O28");
      $this->pFormatEntry ($ws,"Q28:S28");
      $this->pFormatField ($ws,"P30", $dFields['fin']);

      $this->pFormatTitle ($ws,"P31", "Consolation Game");
      $this->pFormatWP ($ws,"N32","Winner Game #49");
      $this->pFormatWP ($ws,"R32","Winner Game #50");
      $this->pFormatEntry ($ws,"M36:O36");
      $this->pFormatEntry ($ws,"Q36:S36");
      $this->pFormatField ($ws,"P38", $dFields['con']);

    }

    protected function FormatPlayoffSummary($ws)
    {
      $dRec = array(

        'hdr' => "AYSO - Section 1 Tournament - 2013",
        'title' => "POOL PLAY  Saturday, November 23. 2013",
      );

      $dFields = array(
        'sf1' => "Game #55 - Sunday - Nov. 24th - 10:30am - Ayala 11",
        'sf2' => "Game #50 - Sunday - Nov. 24th - 8:30am - Ayala 10",
        'fin' => "Game #57 - Sunday - Nov. 24th -12:30pm - Ayala 9",
        'con' => "Game #58 - Sunday - Nov. 24th - 12:30pm - Ayala 10",
      );

      $this->FormatPlayoffHeader ($ws,$dRec);
      $this->FormatPlayoffResults ($ws,"B4:K4","B5:K10","B4:G10","H4:K7"); //ws.Range("B4:K10")
      $this->FormatPlayoffResults ($ws,"B13:K13","B14:K19","B13:G19","H13:K16"); //ws.Range("B13:K19")
      $this->FormatPlayoffResults ($ws,"B22:K22","B23:K28","B22:G28","H22:K25");  //ws.Range("B22:K28")
      $this->FormatPlayoffResults ($ws,"B31:K31","B32:K37","B31:G37","H31:K34"); //ws.Range("B31:K37")
      $this->AddPlayoffSummaries ($ws,$dFields);
    }

}

?>
