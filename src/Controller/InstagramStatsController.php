<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

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
            $insightsData = $this->getInstagramInsights($instagramBusinessAccountId, $accessToken, 'impressions,reach,profile_views', 'day');
            $allMedia = $this->getAllMedia($instagramBusinessAccountId, $accessToken);
            $totalLikes = 0;
            $totalComments = 0;

            foreach ($allMedia as &$media) {
                $likes = $this->getTotalLikesForMedia($media['id'], $accessToken);
                $comments = $this->getTotalCommentsForMedia($media['id'], $accessToken);
                $media['like_count'] = $likes;
                $media['comment_count'] = $comments;
                $totalLikes += $likes;
                $totalComments += $comments;
            }

            $latestThreeMedia = $this->getLimitedMedia($instagramBusinessAccountId, $accessToken, 3);
            foreach ($latestThreeMedia as &$media) {
                $media['like_count'] = $this->getTotalLikesForMedia($media['id'], $accessToken);
                $media['comment_count'] = $this->getTotalCommentsForMedia($media['id'], $accessToken);
                $media['media_url'] = $this->getMediaUrl($media['id'], $accessToken);
            }

        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to retrieve Instagram insights: ' . $e->getMessage());
            return $this->redirectToRoute('landing');
        }

        return $this->render('instagram/stats.html.twig', [
            'insights' => $insightsData,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'latestMedia' => $latestThreeMedia,
        ]);
    }

    //Cette méthode retourne l'identifiant de la page Facebook
    private function getFacebookPageId(string $accessToken): string {
        $url = "https://graph.facebook.com/v19.0/me/accounts?access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['data'][0]['id'];  // Assuming the first listed page is the correct one
    }

    //Cette méthode retourne l'identifiant du compte professionnel Instagram
    private function getInstagramBusinessAccountId(string $pageId, string $accessToken): ?string {
        $url = "https://graph.facebook.com/v19.0/{$pageId}?fields=instagram_business_account&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        if (isset($data['instagram_business_account'])) {
            return $data['instagram_business_account']['id'];
        } else {
            return null;
        }
    }

    //Cette méthode retourne les statistiques Instagram
    private function getInstagramInsights(string $id, string $accessToken, string $metrics, string $period): array {
        $url = "https://graph.facebook.com/v19.0/{$id}/insights?metric={$metrics}&period={$period}&access_token={$accessToken}";
        $response = file_get_contents($url);
        return json_decode($response, true)['data'];
    }

    //Cette méthode retourne le nombre total de likes pour un média donné
    private function getTotalLikesForMedia(string $mediaId, string $accessToken): int {
        $url = "https://graph.facebook.com/v19.0/{$mediaId}?fields=like_count&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['like_count'] ?? 0;
    }

    //Cette méthode retourne tous les médias
    private function getAllMedia(string $accountId, string $accessToken): array {
        $url = "https://graph.facebook.com/v19.0/{$accountId}/media?access_token={$accessToken}";
        $response = file_get_contents($url);
        return json_decode($response, true)['data'] ?? [];
    }

    //Cette méthode retourne le nombre total de commentaires pour un média donné
    private function getTotalCommentsForMedia(string $mediaId, string $accessToken): int {
        $url = "https://graph.facebook.com/v19.0/{$mediaId}?fields=comments_count&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['comments_count'] ?? 0;
    }

    //Cette méthode retourne les 3 derniers médias grâce a $limit (voir doc API GRAPH
    private function getLimitedMedia(string $accountId, string $accessToken, int $limit): array {
        $url = "https://graph.facebook.com/v19.0/{$accountId}/media?limit={$limit}&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['data'] ?? [];
    }

    private function getMediaUrl(string $mediaId, string $accessToken): string {
        $url = "https://graph.facebook.com/v19.0/{$mediaId}?fields=media_url&access_token={$accessToken}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data['media_url'] ?? '#';  // Default to '#' if no URL is found
    }
}

