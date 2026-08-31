<?php

/**
 *
 * Zoho Books API
 * Version: 2
 *
 * Author: Giuseppe Occhipinti - https://github.com/peppeocchi
 *
 * CHANGELOG v2
 * - extended parameters for invoices and credit notes and contacts
 *
 */

class ZohoBooks
{
	/**
	 * cUrl timeout
	 */
	private $timeout = 30000;

	/**
	 * HTTP code of the cUrl request
	 */
	private $httpCode;

	/**
	 * Zoho Books API authentication
	 */
	private $authtoken;
	private $organizationId;

	/**
	 * Zoho Books API request limit management
	 */
	private $apiRequestsLimit = 1000;
	private $apiRequestsCount;
	private $apiTimeLimit = 6000000;
	private $startTime;

	/**
	 * Zoho Books API urls request
	 */
	private $oauthAccountsUrl = 'https://accounts.zoho.com';
	private $apiUrl = 'https://www.zohoapis.com/books/v3/';
	private $contactsUrl = 'contacts/';
	private $accountsUrl = 'bankaccounts/';
	private $customerpaymentsUrl = 'customerpayments/';
	private $invoicesUrl = 'invoices/';
	private $creditnotesUrl = 'creditnotes/';
    private $currenciesUrl = 'settings/currencies/';
    private $taxesUrl = 'settings/taxes';
    private $ItemsUrl = 'items/';


	

	/**
	 * Init
	 *
	 * @param (string) Zoho Books authentication token
	 * @param (string) Zoho Books organization id
	 */
	public function __construct()
	{
		$this->authtoken = get_option("zoho_access_token");
		$this->organizationId = get_option('zoho_organization_id');
		$this->apiRequestsCount = 0;
		$this->startTime = time();

        $apiDomain = get_option('zoho_api_domain');
        if ($apiDomain != '') {
            $this->apiUrl = rtrim($apiDomain, '/') . '/books/v3/';
        }

        $this->refresh = get_option('zoho_refresh_token');
        $this->accessToken = $this->authtoken;

        if ($this->refresh != '' && $this->shouldRefreshAccessToken()) {
            $this->accessToken = $this->getAccessTokenFromRefreshTocken();
        } elseif (get_option('zoho_auth_code') != '') {
            $this->accessToken = $this->getAccessTokenFromAuthCode();
        }
	}

    private function shouldRefreshAccessToken()
    {
        if ($this->accessToken == '') {
            return true;
        }

        $expiresAt = (int)get_option('zoho_access_token_expires_at');

        if ($expiresAt <= 0) {
            return true;
        }

        return $expiresAt <= (time() + 300);
    }

    public function getAccessTokenFromRefreshTocken()
    {
        $params = [
            'refresh_token' => $this->refresh,
            'client_id' => get_option('zoho_client_id'),
            'client_secret' => get_option('zoho_client_secret'),
            'grant_type' => 'refresh_token',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->oauthAccountsUrl . "/oauth/v2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $response = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();
        $access_tocken_json = json_decode($response);

        if ($this->httpCode == 200 && isset($access_tocken_json->access_token)) {
            update_option('zoho_access_token', $access_tocken_json->access_token);
            update_option(
                'zoho_access_token_expires_at',
                time() + (isset($access_tocken_json->expires_in) ? (int)$access_tocken_json->expires_in : 3600)
            );
            update_option('zoho_last_token_error', '');

            if (isset($access_tocken_json->api_domain) && $access_tocken_json->api_domain != '') {
                update_option('zoho_api_domain', $access_tocken_json->api_domain);
                $this->apiUrl = rtrim($access_tocken_json->api_domain, '/') . '/books/v3/';
            }

            return $access_tocken_json->access_token;
        }

        update_option('zoho_last_token_error', $response);

        return $this->accessToken;
    }

    public function getAccessTokenFromAuthCode()
    {
        $params = [
            'code' => get_option('zoho_auth_code'),
            'client_id' => get_option('zoho_client_id'),
            'client_secret' => get_option('zoho_client_secret'),
            'grant_type' => 'authorization_code',
        ];

        if (get_option('zoho_redirect_uri') != '') {
            $params['redirect_uri'] = get_option('zoho_redirect_uri');
        }

        $url = $this->oauthAccountsUrl . "/oauth/v2/token?" . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $response = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();
        $token = json_decode($response);

        if ($this->httpCode == 200 && isset($token->access_token)) {
            update_option('zoho_access_token', $token->access_token);
            update_option(
                'zoho_access_token_expires_at',
                time() + (isset($token->expires_in) ? (int)$token->expires_in : 3600)
            );
            update_option('zoho_last_token_error', '');

            if (isset($token->refresh_token) && $token->refresh_token != '') {
                update_option('zoho_refresh_token', $token->refresh_token);
                $this->refresh = $token->refresh_token;
            }

            if (isset($token->api_domain) && $token->api_domain != '') {
                update_option('zoho_api_domain', $token->api_domain);
                $this->apiUrl = rtrim($token->api_domain, '/') . '/books/v3/';
            }

            return $token->access_token;
        }

        update_option('zoho_last_token_error', $response);

        return false;
    }
	/**
	 * Get all contacts
	 *
	 * @return (string) json string || false
	 */
	public function allContacts($config = array())
	{
	  ;
		$url = $this->apiUrl . $this->contactsUrl . '?organization_id=' . $this->organizationId;
		if(isset($config['page'])) {
			$url .= '&page=' . $config['page'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
		$contacts = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $contacts : false;
	}

	/**
	 * Get contact details by ID
	 *
	 * @param (int) contact id
	 *
	 * @return (string) json string || false
	 */
	public function getContact($id)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->contactsUrl . $id . '?organization_id=' . $this->organizationId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
		$contact = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $contact : false;
	}


    /**
     * Create an contact
     *
     * @param (string) json encoded
     * @param (bool) send the invoice to the contact associated with the invoice
     *
     * @return (bool)
     */
    public function postContact($contact,$send = false)
    {
        $url = $this->apiUrl . $this->contactsUrl . '?organization_id=' . $this->organizationId;

        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $contact,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json")
        ));

        $contact = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();
        return $contact;
        return $this->httpCode == 201 ? $contact : false;
    }

    public function postPayment($payment, $send = false)
    {

        $url = $this->apiUrl . $this->customerpaymentsUrl. '?organization_id=' . $this->organizationId;

       /* $data = array(
            'authtoken' 		=> $this->authtoken,
            'JSONString' 		=> $payment,
            "organization_id" 	=> $this->organizationId
        );*/

        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $payment,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json")
        ));

        $payment = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();
        return $payment;
        return $this->httpCode == 201 ? $payment : false;
    }


    /**
     * Create an contact
     *
     * @param (string) json encoded
     * @param (bool) send the invoice to the contact associated with the invoice
     *
     * @return (bool)
     */
    public function postItems($items, $send = false)
    {
        $url = $this->apiUrl . $this->ItemsUrl. '?organization_id=' . $this->organizationId;

        /*$data = array(
            'authtoken' 		=> $this->authtoken,
            'JSONString' 		=> $items,
            "organization_id" 	=> $this->organizationId
        );*/

        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $items,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json")
        ));

        $items = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();
        return $items;
    }

    public function getItems($params = [])
    {
        $queryString = '';
        if (!empty($params) && is_array($params)) {
            $queryString = '&' . http_build_query($params);
        }
        $url = $this->apiUrl . $this->ItemsUrl . '?organization_id=' . $this->organizationId . $queryString;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => ["authorization: Zoho-oauthtoken " . $this->accessToken, "Content-Type: application/json"]
        ]);
        $response = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->checkApiRequestsLimit();
        return $response;
    }

    public function getItem($item_id)
    {
        $url = $this->apiUrl . $this->ItemsUrl . trim($item_id) . '?organization_id=' . $this->organizationId;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => ["authorization: Zoho-oauthtoken " . $this->accessToken, "Content-Type: application/json"]
        ]);
        $response = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->checkApiRequestsLimit();
        return $response;
    }

    public function markItemActive($item_id)
    {
        $url = $this->apiUrl . $this->ItemsUrl . trim($item_id) . '/active?organization_id=' . $this->organizationId;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => ["authorization: Zoho-oauthtoken " . $this->accessToken, "Content-Type: application/json"]
        ]);
        $response = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->checkApiRequestsLimit();
        return $response;
    }

    /**
	 * Get all invoices
	 *
	 * @param (date) date start
	 * @param (date) date end
	 *
	 * @return (string) json string || false
	 */
	public function allInvoices($config = array())
	{
		$url = $this->apiUrl . $this->invoicesUrl . '?organization_id=' . $this->organizationId;
		/*if(isset($config['date_start']) && isset($config['date_end'])) {
			$url .= '&date_start=' . $config['date_start'] . '&date_end=' . $config['date_end'];
		}
		if(isset($config['invoice_number_startswith'])) {
			$url .= '&invoice_number_startswith=' . $config['invoice_number_startswith'];
		}
		if(isset($config['page'])) {
			$url .= '&page=' . $config['page'];
		}*/
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
		$invoices = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $invoices : false;
	}


	/**
	 * Get invoice
	 *
	 * @param (int) invoice id
	 *
	 * @return (string) json string || false
	 */
	public function getInvoice($id)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->invoicesUrl . $id . '?organization_id=' . $this->organizationId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
		$invoice = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $invoice : false;
	}

    public function getCurrencies()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->currenciesUrl . '?organization_id=' . $this->organizationId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken " . $this->accessToken, "Content-Type: application/json"));
        $currencies = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();

        return $this->httpCode == 200 ? $currencies : false;
    }

    public function getTaxes()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->taxesUrl . '?organization_id=' . $this->organizationId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken " . $this->accessToken, "Content-Type: application/json"));
        $taxes = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();

        return $this->httpCode == 200 ? $taxes : false;
    }


    /**
	 * Create an invoice
	 *
	 * @param (string) json encoded
	 * @param (bool) send the invoice to the contact associated with the invoice
	 *
	 * @return (bool)
	 */
	public function postInvoice($invoice, $send = false)
	{
		$url = $this->apiUrl . $this->invoicesUrl. '?organization_id=' . $this->organizationId;

		/*$data = array(
			'authtoken' 		=> $this->authtoken,
			'JSONString' 		=> $invoice,
			"organization_id" 	=> $this->organizationId
		);*/

		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $invoice,
			CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json")
		));

		$invoice = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();
        return $invoice;
		return $this->httpCode == 201 ? true : false;
	}
    public function postInvoice_status_sent($invoice_id, $send = false)
    {
        $url = $this->apiUrl . $this->invoicesUrl.$invoice_id.'/status/sent?organization_id=' . $this->organizationId;

      /*  $data = array(
            'authtoken' 		=> $this->authtoken,
            "organization_id" 	=> $this->organizationId
        );*/

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => 1,
           // CURLOPT_POSTFIELDS => $invoice_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json")
        ));

        $invoice = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();
         /*return $invoice;
         return $this->httpCode == 201 ? true : false;*/
    }

	/**
	 * Get all credit notes
	 *
	 * @param (date) date start
	 * @param (date) date end
	 *
	 * @return (string) json string || false
	 */
	public function allCreditNotes($config = array())
	{
		$url = $this->apiUrl . $this->creditnotesUrl . '?organization_id=' . $this->organizationId;
		if(isset($config['date_start']) && isset($config['date_end'])) {
			$url .= '&date_start=' . $config['date_start'] . '&date_end=' . $config['date_end'];
		}
		if(isset($config['page'])) {
			$url .= '&page=' . $config['page'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
		$creditnotes = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $creditnotes : false;
	}


	/**
	 * Get credit note
	 *
	 * @param (int) credit note id
	 *
	 * @return (string) json string || false
	 */
	public function getCreditNote($id)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->creditnotesUrl . $id . '?organization_id=' . $this->organizationId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
		$creditnote = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $creditnote : false;
	}


	/**
	 * Create a credit note
	 *
	 * @param (string) json string
	 *
	 * @return (bool)
	 */
	public function postCreditNote($creditnote)
	{
		$url = $this->apiUrl . $this->creditnotesUrl.'?organization_id=' . $this->organizationId;

		/*$data = array(
			'authtoken' 		=> $this->authtoken,
			'JSONString' 		=> $creditnote,
			"organization_id" 	=> $this->organizationId
		);*/

		$ch = curl_init($url);

		curl_setopt_array($ch, array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $creditnote,
			CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json")
		));

		$creditnote = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 201 ? true : false;
	}


	/**
	 * Get HTTP code
	 */
	public function getHttpCode()
	{
		return $this->httpCode ? $this->httpCode : false;
	}

    /**
     * Get all contacts
     *
     * @return (string) json string || false
     */
    public function allAccounts($config = array())
    {
        $url = $this->apiUrl . $this->accountsUrl . '?organization_id=' . $this->organizationId;
        if(isset($config['page'])) {
            $url .= '&page=' . $config['page'];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Zoho-oauthtoken ".$this->accessToken,"Content-Type: application/json"));
        $accounts = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->checkApiRequestsLimit();

        return $accounts;
    }


	/**
	 * Check API requests limit
	 *
	 */
	private function checkApiRequestsLimit()
	{
		$tempTime = time() - $this->startTime;
		if($this->apiRequestsCount >= $this->apiRequestsLimit && $tempTime < $this->apiTimeLimit) {
			usleep(($this->apiTimeLimit - $tempTime)*1000000);
			$this->apiRequestsCount = 1;
			$this->startTime = time();
		} else {
			$this->apiRequestsCount++;
		}
	}
	
	
	public function getChartOfAccounts()
{
    $url = "https://www.zohoapis.com/books/v3/chartofaccounts?organization_id=" . $this->organization_id;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Zoho-oauthtoken " . $this->access_token,
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

	
	
	
}
