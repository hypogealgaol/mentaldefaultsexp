<?php

/*
  ****************************************************************************
  * class time_online                                                        *
  * Version 0.8b2                                                            *
  *                                                                          *
  * A PHP class to help get info how much time users spend on a page or on   *
  * the entire site in a session or the total time                           *
  *                                                                          *
  * Copyright (C) 2003 by Dragos Protung - dragos@protung.ro                 *
  *                                                                          *
  * This PHP class is free software; you can redistribute it and/or          *
  * modify it under the terms of the GNU Lesser General Public               *
  * License as published by the Free Software Foundation; either             *
  * version 2.1 of the License, or (at your option) any later version.       *
  *                                                                          *
  * This PHP class is distributed in the hope that it will be useful,        *
  * but WITHOUT ANY WARRANTY; without even the implied warranty of           *
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU         *
  * Lesser General Public License for more details.                          *
  *                                                                          *
  *                                                                          *
  * Author:                                                                  *
  * Dragos Protung, 500164 Brasov, Romania, dragos@protung.ro                *
  *                                                                          *
  ****************************************************************************
*/


class time_online {


   function time_online() {

      $this -> userID  = $_COOKIE["time_online_ID"]; // user's ID
      $this -> userTT  = $_COOKIE["time_online_TT"]; // user's total time on site
      $this -> userST  = $_COOKIE["time_online_ST"]; // time when user entered the site
      $this -> userTO  = $_COOKIE["time_online_TO"]; // time that the user has been online this sesion
      $this -> displayID = 0; // ID used to generate diferent script in case of multiple call of display() function

      if ($this -> userID != "") {

         if ($this -> userST == "") {

            $this -> userST = time();
            setcookie("time_online_ST", $this -> userST);
         }

         $this -> userLPT = time() - $this -> userST - $this -> userTO;

         $this -> userTT += $this -> userLPT;
         setcookie("time_online_TT", $this -> userTT, time()+60*60*60*60*60);

         $this -> userTO = time() - $this -> userST;
         setcookie("time_online_TO", $this -> userTO);

         $this -> userTO = time_online::normalizare($this -> userTO);
         $this -> userTT = time_online::normalizare($this -> userTT);
      }

      if ($this -> userID == "") {

         time_online::newID();
      }

   }

   function newID() {

         $this -> userID = md5(rand());
         $this -> userST = time();
         $this -> userTO = 0;
         $this -> userTT = 0;
         setcookie("time_online_ID", $this -> userID, time()+(60*60*24*365*10));
         setcookie("time_online_ST", $this -> userST);
         setcookie("time_online_TO", $this -> userTO);
         setcookie("time_online_TT", $this -> userTT, time()+(60*60*24*365*10));
   }

   function normalizare($secunde) {

	     $minute  = $secunde / 60;
	     $secunde = $secunde % 60;
	     $ore     = $minute  / 60;
	     $minute  = $minute  % 60;
      $zile    = $ore     / 24;
	     $ore     = $ore     % 24;

	     return $timp = array("days" => (int)$zile, "hours" => $ore, "minutes" => $minute, "seconds" => $secunde);
   }

   function display_time($type){

      $this -> displayID++;

      if ($type == "current_page") {

         $time_start_multiply = 0;
      }

      if ($type == "current_session") {

         $time_start_multiply = $this -> userTO["days"]*24*60*60 + $this -> userTO["hours"]*60*60 + $this -> userTO["minutes"]*60 + $this -> userTO["seconds"];
      }

      if ($type == "total_time") {

         $time_start_multiply = $this -> userTT["days"]*24*60*60 + $this -> userTT["hours"]*60*60 + $this -> userTT["minutes"]*60 + $this -> userTT["seconds"];
      }

      echo "
	           <script type=\"text/javascript\">
	           document.writeln(\"<span id=\\\"time_online" . $this -> displayID . "\\\"></span>\");

	           zi_inceput" . $this -> displayID . " = new Date();
	           ceas_start" . $this -> displayID . " = zi_inceput" . $this -> displayID . ".getTime();

	           function initStopwatch" . $this -> displayID . "() {

               var timp_pe_pag" . $this -> displayID . " = new Date();
   	           return((timp_pe_pag" . $this -> displayID . ".getTime()+(1000*$time_start_multiply) - ceas_start" . $this -> displayID . ")/1000);
	           }
	           function getSecs" . $this -> displayID . "() {


            	  var tSecs" . $this -> displayID . " = Math.round(initStopwatch" . $this -> displayID . "());
	              var iSecs" . $this -> displayID . " = tSecs" . $this -> displayID . " % 60;
	              var iMins" . $this -> displayID . " = Math.round((tSecs" . $this -> displayID . "-30)/60);
	              var iHour" . $this -> displayID . " = Math.round((iMins" . $this -> displayID . "-30)/60);
	              var iMins" . $this -> displayID . " = iMins" . $this -> displayID . " % 60;
	              var iDays" . $this -> displayID . " = Math.round((iHour" . $this -> displayID . "-11)/24);
               if (iDays" . $this -> displayID . " == -0) {iDays" . $this -> displayID . " *= (-1)}; // Stupid Opera :)
	              var iHour" . $this -> displayID . " = iHour" . $this -> displayID . " % 24;
	              var sSecs" . $this -> displayID . " = \"\" + ((iSecs" . $this -> displayID . " > 9) ? iSecs" . $this -> displayID . " : \"0\" + iSecs" . $this -> displayID . ");
	              var sMins" . $this -> displayID . " = \"\" + ((iMins" . $this -> displayID . " > 9) ? iMins" . $this -> displayID . " : \"0\" + iMins" . $this -> displayID . ");
	              var sHour" . $this -> displayID . " = \"\" + ((iHour" . $this -> displayID . " > 9) ? iHour" . $this -> displayID . " : \"0\" + iHour" . $this -> displayID . ");

               document.getElementById('time_online" . $this -> displayID . "').innerHTML=iDays" . $this -> displayID . "+\":\"+sHour" . $this -> displayID . "+\":\"+sMins" . $this -> displayID . "+\":\"+sSecs" . $this -> displayID . ";
               window.setTimeout('getSecs" . $this -> displayID . "()',1000);

	           }
               window.setTimeout('getSecs" . $this -> displayID . "()',1000)

	           </script>
      ";


   }
}

?>