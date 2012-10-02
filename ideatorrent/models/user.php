<?php

/*
Copyright (C) 2008 Nicolas Deschildre <ndeschildre@gmail.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/




class UserModel extends Model
{
	/**
	 * User id.
	 */
	var $_id = -1;

	/**
	 * UserModel instances.
	 */
	static private $_instances = array();


	/**
	 * Return an instance of an UserModel.
	 */
	static function getInstance($id)
	{
		if(!is_numeric($id))
			return null;

		if(UserModel::$_instances[$id] != null)
			return UserModel::$_instances[$id];
		else
		{
			$user = new UserModel();
			$user->setId($id);
			UserModel::$_instances[$id] = $user;
			return $user;
		}
	}

	//Use getInstance instead!
	private function UserModel()
	{

	}


	function setId($id)
	{
		if(is_numeric($id))
		{
			$this->_id = $id;
			$_data = null;
		}
	}

	function getId()
	{
		return $this->_id;
	}

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		if($this->_id != -1)
		{
			//Get the entrypoint from DB, using the drupal function.
			$user = user_load(array("uid" => $this->_id));

			//Load the permissions.
			//DIRTY HACK: Unfortunately, I can't has cheezburgers: there is no public drupal function to
			//load the list of permissions associated to a user. I just can check if it has one given permission.
			//I could take the list of possible qapoll_permissions and try one after the another to 
			//see if the user has it, but that's no good.
			//So I will directly interact with Drupal tables. To be reviewed when changing drupal versions.
			//Code taken from user.module:378
			$rids = array_keys($user->roles);
			$placeholders = implode(',', array_fill(0, count($rids), '%d'));
			$result = it_query("SELECT DISTINCT(p.perm) FROM {role} r INNER JOIN {permission} p ON p.rid = r.rid WHERE " . 
				"r.rid IN ($placeholders)", $rids);
			while ($row = db_fetch_object($result))
				$user->permlists[] = "$row->perm";

			//Load some extras informations concerning the permissions.
			$permissionlist = new PermissionListModel();
			$permissionlist->setFilterParameters(array("permission_names" => implode(", ", $user->permlists)));
			$permlist = $permissionlist->getData();

			$qapoll_permissions = array();
			foreach($permlist as $permobject)
			{
				//Unserialize and merge the qapoll permissions
				$perms = unserialize($permobject->permissions);
				if(is_array($perms) == false)
					drupal_set_message("SYSTEM ERROR! Permission array failed to unserialize.", 'error_msg');

				$qapoll_permissions = array_merge_recursive($qapoll_permissions, $perms);

				//Load and overlap the name, permission_level, icon_name and description
				//We want here to get the infos of the highest permissions. See qapoll.install for more info.
				$user->perm_name = $permobject->name;
				$user->perm_description = $permobject->description;
				$user->perm_icon_name = $permobject->icon_name;
				$user->perm_level = $permobject->permission_level;
				$user->perm_display_name = $permobject->display_name;
			}
			$user->qapoll_perms = $qapoll_permissions;

			return $user;
		}
		else
			return null;
	}

	/**
	 * Delete all the comments from the user
	 */
	function deleteAllComments()
	{
		if($this->_id == -1)
			return false;

		$commentlist = new ChoiceCommentListModel();
		$commentlist->setFilterParameters(array("userid" => $this->_id));
		$commentlist->deleteEntries();
	}

	/**
	 * Delete all the choices from the user
	 */
	function deleteAllChoices()
	{
		if($this->_id == -1)
			return false;

		$choicelist = new ChoiceListModel($GLOBALS['poll'], 1, 999999);
		$choicelist->setFilterParameters(array("user" => $this->_id));
		$choicelist->deleteEntries();
	}

	/**
	 * Delete all the choice solution from the user
	 */
	function deleteAllSolutions()
	{
		if($this->_id == -1)
			return false;

		$choicesolutionlist = new ChoiceSolutionListModel();
		$choicesolutionlist->setFilterParameters(array("userid" => $this->_id));
		$choicesolutionlist->deleteEntries();
	}

	/**
	 * One of the main permission function. Call _hasPermission. See its description for more info.
	 */
	static function currentUserHasPermission($perm_name, $subsystem = "", $instance_listmodel = null, $model_id = -1)
	{
		return UserModel::_hasPermission(UserModel::getInstance($GLOBALS['user']->uid), $perm_name, $subsystem, $instance_listmodel, $model_id);
	}

	/**
	 * One of the main permission function. Call _hasPermission. See its description for more info.
	 */
	static function userHasPermission($user_id, $perm_name, $subsystem = "", $instance_listmodel = null, $model_id = -1)
	{
		return UserModel::_hasPermission(UserModel::getInstance($user_id), $perm_name, $subsystem, $instance_listmodel, $model_id);
	}	

	/**
	 * One of the main permission function. Call _hasPermission. See its description for more info.
	 */
	function hasPermission($perm_name, $subsystem = "", $instance_listmodel = null, $model_id = -1)
	{
		return UserModel::_hasPermission($this, $perm_name, $subsystem, $instance_listmodel, $model_id);
	}

	/**
	 * The main permission function. Tells if yes or no, you are allowed to do something.
	 * @param user_model: The user we are checking the perm against
	 * @param perm_name: The name of the right we want to know about.
	 * @param subsystem: The namespace of the right name, as described in qapoll.install.
	 * @param instance_listmodel: A instance of a list Model (model containing a list of object).
	 * @param model_id: A id identifying one of the object in the instance_listmodel.
	 * If we are giving an instance_listmodel, that means that we want to check the filtered permissions only on this instance. (see qapoll.install).
	 * If we are giving the pair (instance_listmodel, model_id), that means that we want to check the
	 * permission for the given model id.
	 */
	static function _hasPermission($user_model, $perm_name, $subsystem = "", $instance_listmodel = null, $model_id = -1)
	{
		//User model id not set, returning.
		if($user_model->getId() == -1)
			return false;
		
		//Get the perms data
		$perms = $user_model->getData()->qapoll_perms;

		//If we got the global perm "all" set to true, always return true.
		if(isset($perms["global"]) && $perms["global"]["all"] != null)
			return true;

		//If subsystem is empty, directly look on the global list of perms
		if($subsystem == "")
		{
			if(isset($perms["global"])) 
				return $perms["global"]["$perm_name"];
			else
				return false;
		}
		else
		{		
			//Check if the perm is defined in the list of perms of the subsystem.
			if(isset($perms["$subsystem"]) && $perms["$subsystem"]["$perm_name"] != null) 
				return $perms["$subsystem"]["$perm_name"];

			//Now check if this perm is valid using the filtered perms.
			if($instance_listmodel != null)
			{
				//An instance of listmodel plus a model id was given. Let's check.
				//First the owner permissions.
				if(isset($perms["$subsystem"]) && isset($perms["$subsystem"]["owner_perms"]) &&
					$instance_listmodel->_callback_isOwner($model_id, $user_model->getId()) &&
					$perms["$subsystem"]["owner_perms"]["$perm_name"] != null)
					return $perms["$subsystem"]["owner_perms"]["$perm_name"];

				//Then checks the filtered permissions. Call a callback in the model
				if(isset($perms["$subsystem"]) && isset($perms["$subsystem"]["filtered_perms"])) 
					return $instance_listmodel->_callback_hasFilteredPermissions($perm_name, $model_id,
						$perms["$subsystem"]["filtered_perms"]);
				else
					return false;
			}
		}		
	
		return false;
	}

	/**
	 * Load the stats of the user.
	 */
	function _loadStats()
	{
		if(!is_numeric($this->_id))
			return null;

		//First query the votes of the user ideas
		//20-40ms
		$query = "SELECT COALESCE(SUM(total_minus_votes),0) as minsum, COALESCE(SUM(total_plus_votes),0) as plussum " . 
			"from qapoll_choice_solution where userid = '" .
			$this->_id . "' AND status != -2";
		$result = db_fetch_array(it_query($query));
		$stats->nb_plus_vote_on_user_ideas = $result["plussum"];
		$stats->nb_minus_vote_on_user_ideas = $result["minsum"];

		//Now compute the rank of the quality of the ideas of the users.
		//30-50ms of SQL processing time. ok.
		$query = "DROP SEQUENCE IF EXISTS row_num; CREATE TEMPORARY sequence row_num; DROP TABLE IF EXISTS idea_quality_contributor_ordering; " .
			"CREATE TEMPORARY TABLE idea_quality_contributor_ordering as (SELECT  nextval('row_num') as row_pos, * FROM (SELECT userid, " . 
			"SUM(total_plus_votes) - SUM(total_minus_votes) as sum_user from qapoll_choice_solution WHERE status != -2 " . 
			"GROUP BY userid ORDER BY sum_user DESC) " .
			"as sorting);" .
			"select row_pos from idea_quality_contributor_ordering where userid='" . $this->_id . "'";
		$stats->idea_quality_rank = db_result(it_query($query));

		//Query the nb of votes the user have casted
		//50ms of SQL processing time. Negligible.
		$query = "SELECT COUNT(id) from qapoll_vote where userid='" . $this->_id . 
			"' and value=1";
		$stats->nb_plus_vote_user_casted = db_result(it_query($query));
		$query = "SELECT COUNT(id) from qapoll_vote where userid='" . $this->_id . 
			"' and value='-1'";
		$stats->nb_minus_vote_user_casted = db_result(it_query($query));

		//Finally, query the nb of comments the user made
		//20ms of SQL processing time. Bwah!
		$query = "SELECT COUNT(id) from qapoll_choice_comment where userid='" . $this->_id . "' AND status=0";
		$stats->nb_comments_of_user = db_result(it_query($query));

		return $stats;
	}

}

?>
