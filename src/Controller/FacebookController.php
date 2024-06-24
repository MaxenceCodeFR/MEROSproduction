<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{

    #[Route('/connect/facebook', name: 'connect_facebook_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'public_profile', 'email','instagram_basic', 'pages_show_list','business_management'// the scopes you want to access
            ]);
    }


    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {

    }


}