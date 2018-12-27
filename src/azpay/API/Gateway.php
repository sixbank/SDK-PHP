<?php
    /**
     * Created by PhpStorm.
     * User: brunopaz
     * Date: 2018-12-26
     * Time: 22:38
     */

    namespace Azpay\API;


    /**
     * Class Gateway
     *
     * @package Azpay\API
     */
    class Gateway
    {

        /**
         * @var
         */
        public $json;
        /**
         * @var
         */
        private $version;
        /**
         * @var
         */
        private $verification;
        /**
         * @var string
         */
        private $env;
        /**
         * @var
         */
        private $response;


        /**
         * Gateway constructor.
         *
         * @param $env
         */
        public function __construct($env) { $this->env = strtoupper($env); }


        /**
         * @param Transaction $transaction
         * @return $this
         * @throws \Exception
         */
        public function Authorize(Transaction $transaction)
        {
            $authorize = new Authorize($transaction);
            $request = new Request($this->env);

            $this->response = $request->post("/v1/receiver", $authorize->toJSON());

            return $this;
        }

        /**
         * @param Transaction $transaction
         * @return $this
         * @throws \Exception
         */
        public function Sale(Transaction $transaction)
        {
            $sale = new Sale($transaction);
            $request = new Request($this->env);
            $this->response = $request->post("/v1/receiver", $sale->toJSON());

            return $this;
        }

        /**
         * @param Transaction $transaction
         * @param $transactionId
         * @param null $amount
         * @return $this
         * @throws \Exception
         */
        public function Capture(Transaction $transaction, $transactionId, $amount = NULL)
        {
            $sale = new Capture($transaction, $transactionId, $amount);
            $request = new Request($this->env);
            $this->response = $request->post("/v1/receiver", $sale->toJSON());

            return $this;
        }

        /**
         * @param Transaction $transaction
         * @param $transactionId
         * @param null $amount
         * @return $this
         * @throws \Exception
         */
        public function Cancel(Transaction $transaction, $transactionId, $amount = NULL)
        {
            $sale = new Cancel($transaction, $transactionId, $amount);
            $request = new Request($this->env);
            $this->response = $request->post("/v1/receiver", $sale->toJSON());

            return $this;
        }

        /**
         * @param Transaction $transaction
         * @param $transactionId
         * @return $this
         * @throws \Exception
         */
        public function Report(Transaction $transaction, $transactionId)
        {
            $sale = new Report($transaction, $transactionId);
            $request = new Request($this->env);
            $this->response = $request->post("/v1/receiver", $sale->toJSON());

            return $this;
        }


        /**
         * @return mixed
         */
        public function getResponse()
        {
            return $this->response;
        }

        /**
         * @return false|string
         */
        public function getResponseJson()
        {
            return json_encode($this->response, JSON_PRETTY_PRINT);
        }


        /**
         * @return string
         */
        public function getTransactionID()
        {
            if (isset($this->response["transactionId"])) {
                return $this->response["transactionId"];
            }
            return "UNKNOWN";
        }


        /**
         * @return string
         */
        public function getStatus()
        {

            switch ($this->response["status"]) {
                case "0":
                    return "WAITING FOR PAYMENT";
                case "1":
                    return "AUTHENTICATED";
                case "2":
                    return "UNAUTHORIZED";
                case "3":
                    return "AUTHORIZED";
                case "4":
                    return "UNAUTHORIZED";
                case "5":
                    return "IN CANCELLING";
                case "6":
                    return "CANCELLED";
                case "7":
                    return "IN CAPTURING";
                case "8":
                    return "AUTHORIZED";
                case "9":
                    return "UNAUTHORIZED";
                case "10":
                    return "RECURRING DONE";
                case "11":
                    return "BOLETO";
                case "12":
                case "56":
                    return "PARTIAL CANCELLED";
            }
            return "UNKNOWN";

        }

        /**
         * @return bool
         */
        public function isAuthorized()
        {
            if (isset($this->response["status"]) && ($this->response["status"] == 3 || $this->response["status"] == 8)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * @return bool
         */
        public function canCapture()
        {
            if (isset($this->response["status"]) && ($this->response["status"] == 3)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * @return bool
         */
        public function canCancel()
        {
            if (isset($this->response["status"]) && ($this->response["status"] == 3 || $this->response["status"] == 8)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * @return mixed
         */
        public function getVersion()
        {
            return $this->version;
        }


        /**
         * @param $version
         * @return $this
         */
        public function setVersion($version)
        {
            $this->version = $version;
            return $this;
        }

        /**
         * @return Verification
         */
        public function getVerification(): Verification
        {
            return $this->verification;
        }

        /**
         * @param Verification $verification
         * @return Gateway
         */
        public function setVerification(Verification $verification): Gateway
        {
            $this->verification = $verification;
            return $this;
        }


    }