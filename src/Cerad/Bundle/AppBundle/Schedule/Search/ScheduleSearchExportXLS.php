<?php
namespace Cerad\Bundle\AppBundle\Schedule\Search;

use Cerad\Component\Excel\Export as BaseExport;

/* ============================================
 * Basic referee schedule exporter
 * TODO: All of these exports should be in one file or at least extend a base
 */
class ScheduleSearchExportXLS extends BaseExport
{
    protected $counts = array();

    protected $widths = array
    (
        'Game' =>  6, 'Game#' =>  6,

        'DOW' =>  5, 'Date' =>  12, 'Time' => 10,

        'Venue' =>  8, 'Field' =>  6, 'GT' => 4, 'Group' => 20,

        'Home Team' => 12, 'Away Team' => 12,

    );
    protected $center = array
    (
        'Game',
    );
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
    public function generateGames($ws,$games)
    {
        // Only the keys are currently being used
        $map = array(
            'Game'     => 'game',
            'Date'     => 'date',
            'DOW'      => 'dow',
            'Time'     => 'time',
            'Venue'    => 'venue',
            'Field'    => 'field',
            'Group'    => 'group',
            'GT'       => 'GT',

            'Home Team' => 'homeTeam',
            'Away Team' => 'awayTeam',

            'Game#'   => 'game',
        );
        $ws->setTitle('Games');

        $row = $this->setHeaders($ws,$map);

        $timex = null;

        foreach($games as $game)
        {
            $row++;
            $col = 0;

            // Date/Time
            $dt   = $game->getDtBeg();
            $dow  = $dt->format('D');
            $date = $dt->format('M d y');
            $time = ' ' . $dt->format('H:i A'); //('g:i A');

            // Skip on time changes
            if ($timex != $time)
            {
                if ($timex) $row++;
                $timex = $time;
            }
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());
            $ws->setCellValueByColumnAndRow($col++,$row,$date);
            $ws->setCellValueByColumnAndRow($col++,$row,$dow);
            $ws->setCellValueByColumnAndRow($col++,$row,$time);
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getField()->getVenue());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getField()->getName ());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getGroup());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getGroupType());

            $ws->setCellValueByColumnAndRow($col++,$row,$game->getHomeTeam()->getName());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getAwayTeam()->getName());

            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());
        }
        return;
    }
    public function generate($games)
    {
        // Spreadsheet
        $this->ss = $ss = $this->createSpreadSheet();
        $ws = $ss->getSheet(0);

        $ws->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $ws->getPageSetup()->setPaperSize  (\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $ws->getPageSetup()->setFitToPage(true);
        $ws->getPageSetup()->setFitToWidth(1);
        $ws->getPageSetup()->setFitToHeight(0);
        $ws->setPrintGridLines(true);

        $this->generateGames($ws,$games);

        // Done
        $ss->setActiveSheetIndex(0);
    }
}
?>
