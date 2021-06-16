<?php

/**
 * Call this method to check if sftp connection is working 
 */
function checkFtp()
{
    $host = 'storage';
    $password = 'storage';
    $username = 'storage';
    $port = 22;

    try {
        $result = isConnected($host, $username, $password, $port);
    } catch(Exception $e) {
        $result = $e->getMessage();
    }

    if($result) {
        echo 'FTP server connected successfully';
    } else {
        echo 'Unable to connect to FTP Server';
    }
}

function isConnected($host, $username, $password, $port = 21, $timeout = 10) 
{
        $con = ftp_connect($host, $port, $timeout);
        if (false === $con) {
            throw new Exception('Unable to connect to FTP Server.');
        }
        $loggedIn = ftp_login($con,  $username,  $password);
        ftp_close($con);
        if (true === $loggedIn) {
            return true;
        } else {
            throw new Exception('Unable to log in.');
        }
}