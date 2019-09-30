<?php

namespace Drakkar\Exception;

class MatrixErrorFunction
{

    /**
     * @param \Drakkar\Exception\MatrixConnectionException $e
     */
    public static function connectionException($e)
    {
        $error = array();
        $error['status'] = 'ko';
        $error['error']['code'] = $e->getCode();
        $error['error']['title'] = $e->getTitle();
        $error['error']['message'] = $e->getMessage();
        $error['error']['query'] = $e->getQuery();
        $error['error']['params'] = $e->getQueryParameters(true);
        $error['error']['previous'] = $e->getPrevious()->getCode() . ' - ' . $e->getPrevious()->getMessage();

        return $error;
    }

    /**
     * @param \Drakkar\Exception\MatrixException $e
     */
    public static function genericException($e)
    {
        $error = array();
        $error['status'] = 'ko';
        $error['error']['code'] = $e->getCode();
        $error['error']['title'] = $e->getTitle();
        $error['error']['message'] = $e->getMessage();
        $error['error']['query'] = $e->getQuery();
        $error['error']['params'] = $e->getQueryParameters(true);
        $error['error']['previous'] = $e->getPrevious()->getCode() . ' - ' . $e->getPrevious()->getMessage();

        return $error;
    }


    /**
     * @param \Drakkar\Exception\MatrixJsonException $e
     */
    public static function jsonException($e)
    {



        $error = array();
        $error['status'] = 'ko';
        $error['error']['code'] = $e->getCode();
        $error['error']['title'] = $e->getTitle();
        $error['error']['message'] = $e->getMessage();
        $error['error']['query'] = $e->getQuery();
        $error['error']['params'] = $e->getQueryParameters(true);
        $error['error']['previous'] = $e->getPrevious()->getCode() . ' - ' . $e->getPrevious()->getMessage();

        return $error;
    }

    /**
     * @param \Drakkar\Exception\MatrixException $e
     */
    public static function integrityConstrainException($e)
    {
        $error = array();
        $error['status'] = 'ko';
        $error['error']['code'] = $e->getCode();
        $error['error']['title'] = $e->getTitle();

        //Custom Message
        //$error['error']['message']= $e->getMessage();
        $app = explode('entry', $e->getPrevious()->getMessage());
        $app = explode('for key', $app[1]);
        $error['error']['message'] = $app[0] . ' : Valore già presente';

        $error['error']['query'] = $e->getQuery();
        $error['error']['params'] = $e->getQueryParameters(true);
        $error['error']['previous'] = $e->getPrevious()->getCode() . ' - ' . $e->getPrevious()->getMessage();

        return $error;
    }

}

