<?php

namespace App\Services;

use App\Models\ApplicationGocardlessAccess;
use Nordigen\NordigenPHP\API\NordigenClient;
use Throwable;

class NordigenService
{
    private NordigenClient $client;

    public static $SANDBOX_INSTITUTION_ID = 'SANDBOXFINANCE_SFIN0000';

    public function __construct(string $secretId, string $secretKey)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
        $this->client = new NordigenClient($secretId, $secretKey);

        try {
            $gc_access_data = ApplicationGocardlessAccess::getAccessData();
            $access_token = $gc_access_data ? ($gc_access_data->access_token ?? '') : '';
            $refresh_token = $gc_access_data ? ($gc_access_data->refresh_token ?? '') : '';

            $this->client->setAccessToken($access_token);
            $this->client->setRefreshToken($refresh_token);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function getListOfInstitutions(string $country)
    {
        return $this->client->institution->getInstitutionsByCountry($country);
    }

    public function getSessionData(string $redirectUri, string $institutionId, int $maxHistoricalDays = 90): array
    {
        return $this->client->initSession($institutionId, $redirectUri, $maxHistoricalDays);
    }

    private function getListOfAccounts(string $requisitionId): array
    {
        return $this->client->requisition->getRequisition($requisitionId)["accounts"];
    }

    public function getFirstAccount(string $requisitionId)
    {
        $accountArray = $this->getListOfAccounts($requisitionId);
        return $accountArray[0];
    }

    public function getAccountData(string $requisitionId): array
    {
        $accountArray = $this->getListOfAccounts($requisitionId);
        $accountData = [];

        foreach ($accountArray as $id) {
            $account = $this->client->account($id);
            $accountData[] = [
                "metaData" => $account->getAccountMetaData(),
                "details" => $account->getAccountDetails(),
                "balances" => $account->getAccountBalances(),
                "transactions" => $account->getAccountTransactions()
            ];
        }

        return $accountData;
    }

    public function createAccessToken()
    {
        $tokens = $this->client->createAccessToken();
        return $tokens;
    }

    public function reload() {
        $gc_access_data = ApplicationGocardlessAccess::getAccessData();
        $access_token = $gc_access_data ? ($gc_access_data->access_token ?? '') : '';
        $refresh_token = $gc_access_data ? ($gc_access_data->refresh_token ?? '') : '';
        $this->client->setAccessToken($access_token);
        $this->client->setRefreshToken($refresh_token);
    }

    public function getClient(): NordigenClient
    {
        return $this->client;
    }
}
