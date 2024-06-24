<?php 

namespace App\Twig;

use App\Service\NavBarService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $navBarService;

    public function __construct(NavBarService $navBarService)
    {
        $this->navBarService = $navBarService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_navbar_data', [$this, 'getNavBarData']),
        ];
    }

    public function getNavBarData()
    {
        return $this->navBarService->getNavBarData();
    }
}
