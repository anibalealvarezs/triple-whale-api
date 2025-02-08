<?php

namespace Anibalealvarezs\TripleWhaleApi;

use Carbon\Carbon;
use Anibalealvarezs\ApiSkeleton\Clients\BearerTokenClient;
use GuzzleHttp\Exception\GuzzleException;

class TripleWhaleApi extends BearerTokenClient
{
    protected string $shopId;
    protected string $user;
    protected string $shopDomain;

    /**
     * @param string $token
     * @param string $shopId
     * @param string $user
     * @param string $shopDomain
     * @param string $gitSha
     * @param string $datadogParentId
     * @param string $datadogTraceId
     * @param string $datadogOrigin
     * @param string $datadogSamplingPriority
     * @throws GuzzleException
     */
    public function __construct(
        string $token,
        string $shopId,
        string $user,
        string $shopDomain,
        string $gitSha,
        string $datadogParentId,
        string $datadogTraceId,
        string $datadogOrigin = "rum",
        string $datadogSamplingPriority = "1",
    ) {
        $this->shopId = $shopId;
        $this->user = $user;
        $this->shopDomain = $shopDomain;
        return parent::__construct(
            baseUrl: 'https://app.triplewhale.com/api/v2/',
            token: $token,
            authSettings: [
                'location' => 'header',
                'name' => 'Authorization',
                'headerPrefix' => 'Bearer ',
            ],
            defaultHeaders: [
                "x-tw-shop-id" => $shopId,
                "shop_domain" => $shopDomain,
                "user" => $user,
                "Content-Type" => "application/json",
                "Accept" => "application/json, text/plain, */*",
                "x-tw-git-sha" => $gitSha,
                "x-datadog-origin" => $datadogOrigin,
                "x-datadog-parent-id" => $datadogParentId,
                "x-datadog-trace-id" => $datadogTraceId,
                "x-datadog-sampling-priority" => $datadogSamplingPriority,
                "host" => "api.triplewhale.com",
            ],
        );
    }

    /**
     * @param int $page
     * @param string $timezone
     * @return array
     * @throws GuzzleException
     */
    public function getActivities(
        int $page = 0,
        string $timezone = "America/Chicago",
    ): array {
        $body =[
            "page" => $page,
            "timezone" => $timezone,
            "shopId" => $this->shopId,
        ];

        // Request the spreadsheet data
        $response = $this->performRequest(
            method: "POST",
            endpoint: "activities/get-activities",
            body: json_encode($body),
        );
        // Return response
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $timezone
     * @param array[] $accountIds
     * @return array
     * @throws GuzzleException
     */
    public function getAllStats(
        string $startDate,
        string $endDate,
        string $timezone = "America/Chicago",
        array $accountIds = [],
    ): array {
        $body =[
            "shopDomain" => $this->shopId,
            "model" => "lastPlatformClick-v2",
            "dateModel" => "eventDate",
            "startDate" => Carbon::parse($startDate)->toIso8601String(),
            "endDate" => Carbon::parse($endDate)->toIso8601String(),
            "currency" => "USD",
            "shopCurrency" => "USD",
            "accountIds" => array_filter($accountIds, fn($account) => !empty($account)),
            "timezone" => $timezone,
            "includeOneDayFacebookView" => false,
            "attributionWindow" => "lifetime",
            "subscriptionTags" => [
                "oneTime",
                "subscriptionFirstOrder",
                "subscriptionRecurringOrder"
            ],
            "ppsViewsLookbackWindow" => 7,
            "filters" => [
                "shopify" => [],
                "amazon" => [],
                "facebook-ads" => [],
                "google-ads" => [],
                "bing" => [],
                "recharge" => [],
                "stripe" => [],
                "mountain" => [],
                "criteo" => [],
                "taboola" => [],
                "smsbump" => [],
                "postscript" => [],
                "shipstation" => [],
                "shipbob" => [],
                "tiktok-ads" => [],
                "twitter-ads" => [],
                "pinterest-ads" => [],
                "snapchat-ads" => [],
                "influencers" => [],
                "bing-ads" => [],
                "pixel" => [],
                "triple-whale" => [],
                "klaviyo" => [],
                "attentive" => [],
                "GORGIAS" => [],
                "GOOGLE_ANALYTICS" => [],
                "ENQUIRELABS" => [],
                "enquirelabs" => [],
                "kno" => [],
                "triplesurvey" => [],
                "triplesurvey_text" => [],
                "triplesurvey_email" => [],
                "organic" => [],
                "triplesurvey-none" => [],
                "None of the above" => [],
                "instagram" => [],
                "youtube" => [],
                "tw_referrer" => [],
                "organic_and_social" => [],
                "drip" => [],
                "via" => [],
                "mailchimp" => [],
                "omnisend" => [],
                "sms_bump" => [],
                "meta_shop" => [],
                "Excluded" => []
            ],
            "showDirect" => false,
            "useNewModels" => true,
            "includeCustomAdSpend" => true,
            "includeCustomSpend" => true,
            "useNexus" => false,
            "breakdown" => "source",
        ];

        // Request the spreadsheet data
        $response = $this->performRequest(
            method: "POST",
            endpoint: "attribution/get-all-stats",
            body: json_encode($body),
        );
        // Return response
        return json_decode($response->getBody()->getContents(), true);
    }
}
