<?php

#################
# start edit	#
#################
if ($action=="edit")
{

	if(!$_SESSION['read_only']){

		extract($_POST);
		foreach ($custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']]['custom_table_column_defs'] as $key => $value)
			$_SESSION[$key] = $_POST[$key];

		require("template/".$page_id.".edit.php");
		require("template/footer.php");
		exit();
	}else{
		$errors= "User with Read-Only Rights";
	}
}
#############
# end edit	#
#############

#################
# start modify	#
#################
if ($action=="modify")
{
	$success="";
	$form_error="";
	$id=$_GET['id'];

	if(!$_SESSION['read_only']){

		foreach ($custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']]['custom_table_column_defs'] as $key => $value)
			$_SESSION[$key] = $_POST[$key];

		//initialize

		// Check Primary, Unique and Multiple Keys
		$query = build_unique_check_query($custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']],$table,$_POST,$id);

		if ($query != NULL){
			$count = $link->queryOne($query);
			if(PEAR::isError($count)) {
				$form_error=$count->getMessage();
				require("template/".$page_id.".edit.php");
				require("template/footer.php");
				exit();
			}

			if ($count > 0){
				$form_error="Key Constraint violation - Record with same key(s) already exists";
				require("template/".$page_id.".edit.php");
				require("template/footer.php");
				exit();
			}
		}

		//build update string
		$updatestring="";
		foreach ($custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']]['custom_table_column_defs'] as $key => $value){
			if (isset($_POST[$key])){
	        	$updatestring=$updatestring.$key."='".$_POST[$key]."',";
			}
		}
		//trim the ending comma
		$updatestring = substr($updatestring,0,-1);

		$sql = "UPDATE ".$table." SET ".$updatestring." WHERE ".$custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']]['custom_table_primary_key']."=".$id;
		$result = $link->exec($sql);

		if(PEAR::isError($result)) {
			$form_error=$result->getMessage();
			require("template/".$page_id.".edit.php");
			require("template/footer.php");
			exit();
		}

		$success="The entry has been successfully updated";

		//clear session info
		foreach ($custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']]['custom_table_column_defs'] as $key => $value)
			unset($_SESSION[$key]);

		require("template/".$page_id.".edit.php");
		require("template/footer.php");
		exit();
	}
	else {
		foreach ($custom_config[$module_id][$_SESSION[$module_id]['submenu_item_id']]['custom_table_column_defs'] as $key => $value)
			unset($_SESSION[$key]);

		unset($_POST);
		unset($_GET);
		$form_error= "User with Read-Only Rights";
		require("template/".$page_id.".edit.php");
		require("template/footer.php");
		exit();
	}
}
#################
# end modify	#
#################

?>
