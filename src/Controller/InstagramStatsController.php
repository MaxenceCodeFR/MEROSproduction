<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\Routing\Attribute\Route;

class InstagramStatsController extends AbstractController
{
    public function stats(Request $request): Response
    {
        $accessToken = $request->getSession()->get('fb_access_token');
        if (!$accessToken) {
            return $this->redirectToRoute('connect_facebook_check');
        }

        try {
            $pageId = $this->getFacebookPageId($accessToken);
            $instagramBusinessAccountId = $this->getInstagramBusinessAccountId($pageId, $accessToken);
            if (!$instagramBusinessAccountId) {
                $this->addFlash('error', 'No Instagram Business Account linked to this Facebook page.');
                return $this->redirectToRoute('landing');
            }


            $totalFollowers = $this->getTotalFollowers($instagramBusinessAccountId, $accessToken);
            $instagramInsightsLast30Days = $this->getInstagramInsightsLast30Days($instagramBusinessAccountId, $accessToken);

            $insightsData = $this->getInstagramInsights($instagramBusinessAccountId, $accessToken, 'reach,profile_views', 'day');

            $totalLikes = 0;


            $latestThreeMedia = $this->getLimitedMedia($instagramBusinessAccountId, $accessToken, 3);

            foreach ($latestThreeMedia as &$media) {
                $media['like_count'] = $this->getTotalLikesForMedia($media['id'], $accessToken);
                $media['media_url'] = $this->getMediaUrl($media['id'], $accessToken);
                $media['insights'] = $this->getMediaInsights($media['id'], $accessToken, ['reach', 'impressions']);
                $totalLikes += $media['like_count'];
            }

        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to retrieve Instagram insights: ' . $e->getMessage());
            return $this->redirectToRoute('landing');
        }

        return $this->render('instagram/stats.html.twig', [
            'insights' => $insightsData,
            'totalLikes' => $totalLikes,
            'latestMedia' => $latestThreeMedia,
            'totalFollowers' => $totalFollowers,
            'instagramInsightsLast30Days' => $instagramInsightsLast30Days,

        ]);
    }

    //Cette méthode retourne l'identifiant de la page Facebook
    private function getFacebookPageId(string $accessToken): string
    {
        $url = "https://graph.facebook.com/v20.0/me/accounts?fields=id,instagram_business_account&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        // Vérifiez si le compte a un compte Instagram professionnel associé
        foreach ($data['data'] as $page) {
            if (isset($page['instagram_business_account'])) {
                return $page['id'];
            }
        }
        // Si aucun compte Instagram professionnel n'est trouvé, renvoyez une chaîne vide
        return '';
    }

    //Cette méthode retourne l'identifiant du compte professionnel Instagram
    private function getInstagramBusinessAccountId(string $pageId, string $accessToken): string
    {
        $url = "https://graph.facebook.com/v20.0/{$pageId}?fields=instagram_business_account&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        if (isset($data['instagram_business_account'])) {
            return $data['instagram_business_account']['id'];
        } else {
            return '';
        }
    }


    //Cette méthode retourne les statistiques Instagram
    // Cette méthode retourne les statistiques Instagram pour une période donnée
    private function getInstagramInsights(string $id, string $accessToken, string $metrics, string $period): array {
        $url = "https://graph.facebook.com/v20.0/{$id}/insights?metric={$metrics}&period={$period}&access_token={$accessToken}";
        $response = file_get_contents($url);
        return json_decode($response, true)['data'];
    }


    //Cette méthode retourne le nombre total de likes pour un média donné
    private function getTotalLikesForMedia(string $mediaId, string $accessToken): int {
        $url = "https://graph.facebook.com/v20.0/{$mediaId}?fields=like_count&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['like_count'] ?? 0;
    }

    //Cette méthode retourne tous les médias
    private function getAllMedia(string $accountId, string $accessToken): array {
        $url = "https://graph.facebook.com/v20.0/{$accountId}/media?access_token={$accessToken}";
        $response = file_get_contents($url);
        return json_decode($response, true)['data'] ?? [];
    }




    //Cette méthode retourne les 3 derniers médias grâce a $limit (voir doc API GRAPH
    private function getLimitedMedia(string $accountId, string $accessToken, int $limit): array {
        $url = "https://graph.facebook.com/v20.0/{$accountId}/media?limit={$limit}&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['data'] ?? [];
    }

    private function getMediaUrl(string $mediaId, string $accessToken): string {
        $url = "https://graph.facebook.com/v20.0/{$mediaId}?fields=media_url&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['media_url'] ?? '#';  // Default to '#' if no URL is found
    }
    // Cette méthode retourne les insights pour une publication spécifique
    private function getMediaInsights(string $mediaId, string $accessToken, array $metrics): array {
        $metricString = implode(',', $metrics);
        $url = "https://graph.facebook.com/v20.0/{$mediaId}/insights?metric={$metricString}&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['data'] ?? [];
    }



    private function getTotalFollowers(string $instagramBusinessAccountId, string $accessToken): int
    {
        $url = "https://graph.facebook.com/v20.0/{$instagramBusinessAccountId}?fields=followers_count&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        return $data['followers_count'] ?? 0;
    }
    private function getInstagramInsightsLast30Days(string $instagramBusinessAccountId, string $accessToken): array
    {
        $since = strtotime('-30 days');
        $until = time();

        $url = "https://graph.facebook.com/v19.0/{$instagramBusinessAccountId}/insights?metric=impressions,reach,profile_views&period=day&since={$since}&until={$until}&access_token={$accessToken}";

        $response = file_get_contents($url);

        $data = json_decode($response, true);

        return $data['data'] ?? [];
    }
}



