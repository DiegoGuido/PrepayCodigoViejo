<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OcTechConstants
 *
 * @author sdavi
 */
class OTConstants {
    const CONNECTION_FILE = "./admin/cnx.php";
    const DAO_FILE = "_OT_DAO.php";
    const WELCOME_MESSAGE = "B I E N V E N I D O";
    const AUTHENTICATION_FAILED = "Fallo de autenticación.";
    function getPrefix(){
        $prefix="";
        while(!is_file($prefix.".htaccess")){
            $prefix = "../".$prefix;
        }
        return $prefix;
    }
}

