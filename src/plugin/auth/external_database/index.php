<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2012 Charles Rincheval.
	All rights reserved

	Hyla is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	Hyla is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	 02111-1307	 USA
 */

class plugin_auth_external_database extends plugin_auth {

	/**
     *  Initialisation
	 */
	public function __construct() {
		parent::__construct();
	}


    /**
     * open a connection to the database
     *
     * @param $dbEngine the database engine
     * @param $dbServer the database server name
     * @param $dbLogin the database login
     * @param $dbPassword the database password
     * @param $dbName the database name
     * @param $dbCharset the database charset
     * @return the database resource or throw an exception
     */
    public function sqlConnect($dbEngine, $dbServer, $dbLogin, $dbPassword, $dbName, $dbCharset)
    {
        switch ($dbEngine) {
            case 'mysql':
                $dbResource = new PDO("mysql:host=$dbServer;dbname=$dbName", $dbLogin, $dbPassword);
                $dbResource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbResource->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $dbResource->exec("SET CHARACTER SET $dbCharset");
                break;
            case 'sqlite':
                $dbResource = new PDO("sqlite:$dbName");
                $dbResource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                break;
        }

        return $dbResource;
    }

	/**
     *  try to authenticate the user from an external database containing users
     *  @param	string	$login		user login
     *  @param	string	$password	user password
     *  @return	null if an error occurs, or a tUser object
	 */
	public function auth($login, $password) {
		/* at first, returnedUser is set to null */
		$returnedUser = null;
		
		$ini = parse_ini_file('conf.ini', true);

		/* retreiving ini file parameters */
		$autocreate = $ini['main']['autocreate_hyla_user'];
		$cms = $ini['main']['cms'];
		$rawStatement = $ini[$cms]['statement'];

		$dbEngine = $ini['database']['engine'];
		$dbServer = $ini['database']['server'];
		$dbLogin = $ini['database']['login'];
		$dbPassword = $ini['database']['password'];
		$dbName = $ini['database']['name'];
		$dbCharset = $ini['database']['charset'];

		/* opening a connection to the database */
		$dbResource = $this->sqlConnect($dbEngine, $dbServer, $dbLogin, $dbPassword, $dbName, $dbCharset);
        if ($dbResource) {
            /* preparing statement */
            $statement = $dbResource->prepare($rawStatement);
            $statement->bindParam(':login', $login);
            $statement->bindParam(':password', $password);

            /* executing statement and fetching result */
            $statement->execute();
            $userList = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (count($userList) != 1) {
                return null;
            }

            /* checking if authenticated user exists in Hyla */
            $usr = new users();
            if ($usr->testLogin($login) == 1) {
                if ($autocreate) {
                    /* creating user in Hyla if needed */
                    $user_id = $usr->addUser($login, $password);
                } else {
                    /* no automatic user creation configured, authentication fails */
                    return null;
                }

                $returnedUser = new tUser;
                $returnedUser->id = $user_id;
                $returnedUser->name = $login;
                $returnedUser->type = USR_TYPE_USER;

            } else {
                $res = $usr->getUser($login);

                $returnedUser = new tUser;
                $returnedUser->id = $res->id;
                $returnedUser->name = $res->name;
                $returnedUser->type = $res->type;
            }
        }

		return $returnedUser;
	}
}
