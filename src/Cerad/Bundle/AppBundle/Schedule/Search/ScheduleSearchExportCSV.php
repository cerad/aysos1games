<?php
namespace Cerad\Bundle\AppBundle\Schedule\Search;

/* ============================================
 * Basic referee schedule exporter
 */
class ScheduleSearchExportCSV
{
    public function generate($games)
    {
        $fp = fopen('php://temp','r+');

        // Header
        $row = array(
            "Game","Date","DOW","Time","Field",
            "Group","GT","Home Team","Away Team",
        );
        fputcsv($fp,$row);

        // Games is passed in
        foreach($games as $game)
        {
            // Date/Time
            $dt   = $game->getDtBeg();
            $dow  = $dt->format('D');
            $date = $dt->format('M d');
            $time = $dt->format('g:i A');

            // Build up row
            $row = array();
            $row[] = $game->getNum();
            $row[] = $date;
            $row[] = $dow;
            $row[] = $time;
            $row[] = $game->getField()->getName();

            $row[] = $game->getGroup();
            $row[] = $game->getGroupType();
            $row[] = $game->getHomeTeam()->getName();
            $row[] = $game->getAwayTeam()->getName();

            fputcsv($fp,$row);
        }
        // Return the content
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        //echo $csv; //die();
        return $csv;
    }
}
?>
