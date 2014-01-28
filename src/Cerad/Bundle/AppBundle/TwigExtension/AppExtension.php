<?php
namespace Cerad\Bundle\AppBundle\TwigExtension;

use Cerad\Bundle\AppBundle\DataTransformer\AttendingTransformer;

class AppExtension extends \Twig_Extension
{
    protected $env;
    protected $project;

    public function getName()
    {
        return 'cerad_app_extension';
    }
    public function __construct($project)
    {
        $this->project = $project;
    }
    public function initRuntime(\Twig_Environment $env)
    {
        parent::initRuntime($env);
        $this->env = $env;
    }
    protected function escape($string)
    {
        return twig_escape_filter($this->env,$string);
    }
    public function getFilters()
    {
        return array(
            'cerad_plan_attending_desc' => new \Twig_Filter_Method($this, 'attendDesc'),
        );
    }
    public function getFunctions()
    {
        return array(
            'cerad_tourn_is_local' => new \Twig_Function_Method($this, 'isLocal'),
        );
    }
    public function isLocal()
    {
        // Should be a better way than to access $_SERVER directly.
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        if (!$url) return null;

        $parts = parse_url($url);

        $islocal = sprintf('%s://%s/',$parts['scheme'],$parts['host']);

        return $islocal;
    }
    // Return attending text description
    protected $attendingTransformer;

    public function attendDesc($value)
    {
        if (!$this->attendingTransformer) $this->attendingTransformer = new AttendingTransformer();

        return $this->attendingTransformer->transform($value);
    }

}
?>
