<?php
namespace Cerad\Bundle\AppBundle\Schedule\Games;

use Symfony\Component\Stopwatch\Stopwatch;

class LowerLoadResults
{
    public $message;
    public $filepath;
    public $basename;
    
    public $countGames = 0;
    
    public $duration;
    public $memory;
    
    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start('load');
    }
    public function __toString()
    {
        // Should probably not be here
        $event = $this->stopwatch->stop('load');
        $this->duration = $event->getDuration();
        $this->memory = $event->getMemory();

        return  sprintf(
            "Load Games %s %s %d\n" . 
            "Duration %.2f %.2fM\n",
            $this->message,
            $this->basename,
            $this->countGames,
            $this->duration / 1000.,
            $this->memory   / 1000000.
        );
    }
}
?>
