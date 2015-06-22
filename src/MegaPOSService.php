<?php

namespace Datastat\MegaPOS;

use Illuminate\Validation\Factory as IlluminateValidator;

class MegaPOSService
{

    private $soapClient;
    private $gateWayIds;
    private $statusUrl;
    private $updateUrl;
    private $redirect;
    private $validator;
    private $storeId;

    private $rules = array(
        'name' => array( 'required', 'alpha_dash', 'max:200' ),
        'surname' => array( 'required', 'alpha_dash', 'max:200' ),
        'email' => array( 'required', 'email', 'min:6', 'max:200' ),
        'language' => array( 'required', 'in:si,en'),
        'gateway' => array( 'required', 'in:ACTIVA_PGW,BANKART_PGW,DINERS,EFUNDS,MONETA,KLIK,ABANET'),
        'tx_type' => array( 'sometimes|required', 'in:PURCHASE,ORDER'),
        'amount' => array( 'required', 'numeric'),
    );

    public function __construct(IlluminateValidator $validator)
    {
        $this->validator = $validator;
        $this->soapClient = $this->getSoapClient();
        $this->gateWayIds = $this->getGatewayIds();
        $this->setStoreId();
        $this->setPageUrls();
        $this->setRedirect();
    }

    public function init($params)
    {
        $this->validate($params);
        $initVars = $this->prepareVars($params);

        if (!isset($redirect) OR $redirect=='1')
        {
            // classic redirect mode, result of init() is url, where we redirect customer
            $this->redirectCall($initVars);
        }
        elseif (isset($redirect) AND $redirect=='0')
        {
            // authorize call - no redirect, merchant takes customer's credit card
            // details and sends it to MegaPOS for authorization
            // here it's merchant's responsibility to validate user input
            // (amount with decimal comma will fail (must be dot),
            // card number with spaces, dashes, ... will fail, ..).
            // This is ommited from this example.

            $this->authorizeCall();
        }

    }

    public function update($txId)
    {
        $idData = [
            'store-id' => $this->storeId,
            'transaction-id'=>trim($txId),
        ];

        try {
            $result = $this->soapClient->load($idData);
            event(new MegaPOSUpdateWasCalledEvent($result));
        } catch (\SoapFault $e) {
            throw new MegaPOSException($e->getMessage());
        }

    }

    public function status($txId)
    {
        event(new MegaPOSStatusWasCalledEvent($txId));
    }

    public function cancel($txId)
    {
        $idData = [
            'store-id' => $this->storeId,
            'transaction-id'=>trim($txId),
        ];

        try {
            $result = $this->soapClient->cancelTransaction($idData);
            event(new MegaPOSCancelTransactionWasCalledEvent($result));
        } catch (\SoapFault $e) {
            throw new MegaPOSException($e->getMessage());
        }

    }

    public function process($params)
    {

        dd('not yet implemented');

//     DOCS SAMPLE:


//        $transaction_id = trim($_POST['transaction-id']);
//        $amount = trim($_POST['amount']);
//        $currency = $_POST['currency'];
//
//        $id_data = array(
//            'store-id'=>$store_id,
//            'transaction-id'=>$transaction_id
//        );
//        $process_data = $id_data;
//        $process_data['amount'] = $amount;
//        if (isset($currency)){
//            $process_data['currency'] = $currency;
//        }
//
//        try {
//            $result = $client->processTransaction($process_data);
//            $result_type = $result->{'active-state'}->type;
//            if ($result_type == 'PROCESSED'){
//                echo "Transakcija uspešno zaključena";
//            }
//            else{
//                echo "ERROR occured - MegaPOS response:<br><pre>";
//                var_dump($result);
//                echo "</pre><br><br>";
//            }
//        } catch (SoapFault $e) {
//            print_r($e->getMessage());
//        }

    }

    public function listGateways()
    {
        try {
            $result =  $this->soapClient->listGateways($this->storeId);
            echo "MegaPOS response:<br><pre>";
            print_r ($result);
        } catch (\SoapFault $e) {
            print_r($e->getMessage());
        }
    }

    private function redirectCall($initVars){
        try {
            $result = $this->soapClient->init($initVars);
            $resultUrl = $result->{'active-state'}->result;

            if (\Config::get('megapos.debug')){
                echo "<br><br>Gateway response:<pre>";
                print_r($result);
                echo "</pre>";
            }
            else {
                header('location:'.$resultUrl);
            }
        }
        catch (\SoapFault $e) {
            throw new MegaPOSException($e->getMessage());
        }
    }

    private function authorizeCall()
    {
        //DOESNT WORK YET
        if (isset($_POST['pan']) AND isset($_POST['cvc']) AND isset($_POST['expiry-date-year']) AND isset($_POST['expiry-date-month'])){
            $pan = $_POST['pan'];
            $cvc = $_POST['cvc'];
            $expiry_date_year = $_POST['expiry-date-year'];
            $expiry_date_month = $_POST['expiry-date-month'];
            $installments = $_POST['installments'];
            $authorization_data = array(
                'pan'=>$pan,
                'cvc'=>$cvc,
                'expiry-date-year'=>$expiry_date_year,
                'expiry-date-month'=>$expiry_date_month
            );
            if (isset($installments) AND $gateway_id=='DINERS'){
                $authorization_data['installments'] = $installments;
            }
            $authorization_data_merged = array_merge($init_data_merged, $authorization_data);
            try {
                $result = $this->soapClient->authorize($authorization_data_merged);
                $active_state_type = $result->{'active-state'}->type;
                if ($active_state_type=='INITIALIZED' OR $active_state_type=='PROCESSED'){
                    echo "<br>Transaction successful.";
                }
                else {
                    echo "<br>Transaction failed.";
                }
                if ($debug){
                    echo "<br><br>Gateway response:<pre>";
                    print_r($result);
                    echo "</pre>";
                }
            } catch (\SoapFault $e) {
                throw new MegaPOSException($e->getMessage());
            }
        }

        else{
            echo "Missing transaction-data";
        }
    }

    private function getSoapClient()
    {
        return new \SoapClient($this->getWdslFilePath(), array('local_cert' => $this->getCertPemFilePath()));
    }

    private function getWdslFilePath()
    {
        $environment = \Config::get('megapos.enviroment') ? \Config::get('megapos.enviroment') : 'test';
        return __DIR__."/files/megapos_v3_".$environment.".wdsl";
    }

    private function getCertPemFilePath()
    {
        return \Config::get('megapos.cert') ? Config::get('megapos.cert') : __DIR__."/files/test_php.pem";
    }

    private function getGatewayIds()
    {
        if (\Config::get('megapos.gateway_ids'))
            return \Config::get('megapos.gateway_ids');

        return array ("ACTIVA_PGW" => "402883870d9e7520010d9e755a310001",
            "BANKART_PGW" => "2c9185d12f3709c4012f4eb6c792151a",
            "DINERS" => "000000002214c84a01222b95c1a10632",
            "EFUNDS" => "40288387102d176501102f23aa990015",
            "MONETA" => "FD2F2E6D-AD1F-4D55-AADE-835344DF0AE5"
        );
    }

    private function setStoreId(){
        $this->storeId = \Config::get('megapos.store_id') ? Config::get('megapos.store_id') : 'test';
    }

    private function setPageUrls()
    {
        $this->statusUrl = route('megapos.update');
        $this->updateUrl = route('megapos.status');
    }

    private function setRedirect()
    {
        $this->redirect = \Config::get('megapos.redirect', true);
    }

    private function validate($params)
    {
        $validator = $this->validator->make($params, $this->rules);
        if ($validator->fails())
        {
            throw new \InvalidArgumentException(implode(', ',$validator->errors()->all()));
        }
    }

    private function prepareVars($params)
    {
        $idData = array(
            'store-id'=> $this->storeId,
            'transaction-id'=>"txid-".rand(),
        );

        $amountData = array(
            'amount'=> trim($params['amount']),
            'currency'=>'EUR'
        );

        $initData = array(
            'status-url'=>$this->statusUrl,
            'update-url'=>$this->updateUrl,
            'gateway-id'=>trim($params['gateway']),
            'transaction-type'=>isset($params['tx_type']) ? trim($params['tx_type']) : 'PURCHASE',
        );


        $initData['language'] = $params['language'];
        $initData['customer-name'] = $params['name'];
        $initData['customer-surname'] = $params['surname'];
        $initData['email'] = $params['email'];
        if (isset($params['additional_info'])){
            $initData['additional-info'] = $params['additional_info'];
        }

        return array_merge($idData, $amountData, $initData);
    }
}
