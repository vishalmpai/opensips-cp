<?php
/*
* Copyright (C) 2018 OpenSIPS Project
*
* This file is part of opensips-cp, a free Web Control Panel Application for
* OpenSIPS SIP server.
*
* opensips-cp is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* opensips-cp is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

require("template/header.php");
require("lib/".$page_id.".main.js");
require ("../../../common/mi_comm.php");
require("../../../common/cfg_comm.php");
include("lib/db_connect.php");

$table=$config->table_rtpengine;
$current_page="current_page_rtpengine";

if (isset($_POST['action'])) $action=$_POST['action'];
else if (isset($_GET['action'])) $action=$_GET['action'];
else $action="";

if (isset($_GET['page'])) $_SESSION[$current_page]=$_GET['page'];
else if (!isset($_SESSION[$current_page])) $_SESSION[$current_page]=1;

###############################
# 		state change		  #
###############################

if ($action=="change_state"){

	$state= $_GET['state'];
	$sock = $_GET['sock'];

	$mi_connectors=get_proxys_by_assoc_id($talk_to_this_assoc_id);
	for ($i=0;$i<count($mi_connectors);$i++) {
		if ($state=="0") {
			mi_command("rtpengine_enable $sock 0" , $mi_connectors[$i], $errors , $status);
		} else {
			mi_command("rtpengine_enable $sock 1" , $mi_connectors[$i], $errors , $status);
		}
	}

} 

#################
# start add new #
#################

if ($action=="add")
{
	extract($_POST);
	if(!$_SESSION['read_only'])
	{
		require("template/".$page_id.".add.php");
		require("template/footer.php");
		exit();
	}else {
		$errors= "User with Read-Only Rights";
	}

}

#################
# end add new   #
#################


####################
# start add verify #
####################
if ($action=="add_verify")
{
	$info="";
	$errors="";

	if(!$_SESSION['read_only']){

		$rtpengine_sock=$_POST['rtpengine_sock'];
		$set_id=$_POST['set_id'];

		$sql = "SELECT * FROM ".$table." WHERE set_id=" .$set_id . " AND socket='".$rtpengine_sock."'";
		$row = $link->queryAll($sql);
		if(PEAR::isError($row)) {
			 die('Failed to issue query, error message : ' . $row->getMessage());
		}
		if (count($row)>0) {
			$errors="Duplicate RTPEngine entry";
		} else {
			$sql_command = "INSERT INTO ".$table."
				(set_id, socket) VALUES 
				(".$set_id.", '".$rtpengine_sock."') ";
			$result = $link->prepare($sql_command);
			$result->execute();
			$result->free();

			$info="The new RTPengine was added";
		}
		$link->disconnect();	
	}else{
		$errors= "User with Read-Only Rights";
	}

}

##################
# end add verify #
##################

#################
# start edit	#
#################
if ($action=="edit")
{

	if(!$_SESSION['read_only']){

		extract($_POST);

		require("template/".$page_id.".edit.php");
		require("template/footer.php");
		exit();
	}else{
		$errors= "User with Read-Only Rights";
	}
}
#############
# end edit  #
#############

#################
# start modify	#
#################
if ($action=="modify")
{

	$info="";
	$errors="";

	if(!$_SESSION['read_only']){

		$set_id=$_POST['set_id'];
		$rtpengine_sock=$_POST['rtpengine_sock'];
		$id=$_GET['id'];

		$sql = "SELECT * FROM ".$table." WHERE set_id=" .$set_id. " AND socket='".$rtpengine_sock."' AND id!=".$id;
		$row = $link->queryAll($sql);
		if(PEAR::isError($row)) {
	       		die('Failed to issue query, error message : ' . $row->getMessage());
		}

		if (count($row)>0) {
			$errors="Duplicate RTPEngine socket";
		} else {

			$sql_command = "UPDATE ".$table." SET set_id=".$set_id.", socket = '".$rtpengine_sock.
				"' WHERE id=".$id;
			 $result = $link->prepare($sql_command);
			 $result->execute();
			 $result->free();

			$info="The RTPEngine socket was modified";
		}
		$link->disconnect();
	}else{

		$errors= "User with Read-Only Rights";
	}

}
#################
# end modify	#
#################



################
# start delete #
################
if ($action=="delete")
{
	if(!$_SESSION['read_only']){

		$id=$_GET['id'];
		$sql = "DELETE FROM ".$table." WHERE id=".$id;
		$link->exec($sql);
		$link->disconnect();
	}else{

		$errors= "User with Read-Only Rights";
	}
}
##############
# end delete #
##############


################
# start search #
################
if ($action=="search")
{
	$query="";
	$_SESSION['rtpengine_sock']  = $_POST['rtpengine_sock'];
	$_SESSION['rtpengine_setid']= $_POST['rtpengine_setid'];

	$_SESSION[$current_page]=1;
	extract($_POST);
	if ($show_all=="Show All") {
		$_SESSION['rtpengine_setid']="";
		$_SESSION['rtpengine_sock']="";
	} else if($search=="Search"){
		$_SESSION['rtpengine_setid']=$_POST['rtpengine_setid'];
		$_SESSION['rtpengine_sock'] =$_POST['rtpengine_sock'];
	} else if($_SESSION['read_only']){

		$errors= "User with Read-Only Rights";

	}else if ($delete=="Delete RTPproxy Sock"){
		$set_id = $_POST['rtpengine_setid'];
		$rtpengine_sock = $_POST['rtpengine_sock'];
		if($rtpengine_sock =="") { 
			$query .= " AND socket like %";
		}else {
			$query .= " AND socket like '%" . $rtpengine_sock."%'"; 
		}
		if ($set_id!=""){
			$query .=" AND set_id=".$set_id;
		}
		$sql = "SELECT * FROM ".$table.
			" WHERE (1=1) ". $query;
		$row = $link->queryAll($sql);
		if(PEAR::isError($row)) {
			 die('Failed to issue query, error message : ' . $row->getMessage());
		}

		if (count($row)==0) {
			$errors="No Rule with such RTPEngine Sock ID";
			$_SESSION['rtpengine_setid']="";
			$_SESSION['rtpengine_sock']="";

		}else{
			$sql = "DELETE FROM ".$table." WHERE (1=1) " .$query;
			$link->exec($sql);				
		}
		$link->disconnect();
		print $result;
	}
}
##############
# end search #
##############

##############
# start main #
##############

require("template/".$page_id.".main.php");
if($errors)
echo('!!! ');echo($errors);
require("template/footer.php");
exit();

##############
# end main   #
##############
?>
