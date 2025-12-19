<?php
namespace JocomRepo;
use Illuminate\Support\ServiceProvider;

class BackendServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('JocomRepo\CampaignInterface', 'JocomRepo\FestivalCampaignRepository');
        // $this->app->bind('JocomRepo\SocialMediaInterface', 'JocomRepo\SocialMediaRepository');
    }
}
