<?php
namespace App\Controller;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
/**
* Link to this controller to start the "connect" process
*
* @Route("/connect/facebook", name="connect_facebook_start")
*/
#[Route('/connect/facebook', name: 'connect_facebook_start')]
public function connectAction(ClientRegistry $clientRegistry): Response
{
// will redirect to Facebook!
return $clientRegistry
->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
->redirect([
'public_profile', 'email','instagram_basic','pages_show_list','instagram_manage_insights' // the scopes you want to access
]);
}

/**
* After going to Facebook, you're redirected back here
* because this is the "redirect_route" you configured
* in config/packages/knpu_oauth2_client.yaml
*
* @Route("/connect/facebook/check", name="connect_facebook_check")
*/
#[Route('/connect/facebook/check', name: 'connect_facebook_check')]
public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
{

}
}