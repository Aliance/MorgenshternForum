<?php
// Invision Power Board
// LDAP -- LDAP

$LOGIN_CONF = array();

/**
* LDAP SERVER LOCATION
* This is the location of the LDAP server, either by hostname or IP address.
*/
$LOGIN_CONF['ldap_server'] = '';

/**
* LDAP SERVER PORT
* If you require a specific port number, enter it here.
*/
$LOGIN_CONF['ldap_port'] = '';

/**
* LDAP SERVER USERNAME
* If your LDAP server requires a username, enter it here.
*/
$LOGIN_CONF['ldap_server_username'] = '';

/**
* LDAP SERVER PASSWORD
* If your LDAP server requires password authentication, enter it here.
*/
$LOGIN_CONF['ldap_server_password'] = '';

/**
* LDAP UID FIELD
* This is the field which contains the user's authenticate name.
*/
$LOGIN_CONF['ldap_uid_field'] = 'cn';

/**
* LDAP BASE DN
* The part of the world directory that is held on this
* server, which could be "o=My Company,c=US"
*
*/
$LOGIN_CONF['ldap_base_dn'] = '';

/**
* LDAP FILTER
* Specify an LDAP search filter string to return a subset of the search for
* the ldap_uid_field. The string might be used for restricting authentication
* to a subgroup of your organisation, e.g. 'ou=your_department'
*
* It might be useful to list here the operators that work:
*  =xyz   - matches exact value
*  =*xyz  - matches values ending xyz
*  =xyz*  - matches values beginning xyz
*  =*xyz* - matches values containing xyz
*  =*     - matches all values 
* Boolean operators for constructing complex search
*  &(term1)(term2)  - matches term1 AND term2
*  | (term1)(term2) - matches term1 OR term2
*  !(term1) - matches NOT term1 e.g. '!(ou=Student)'
* 
* leave this blank unless you are familiar with the contents of your
* LDAP server entries.
* 
*/
$LOGIN_CONF['ldap_filter'] = '';

/**
* LDAP SERVER VERSION
* Select the relevant major version number for your LDAP server.
* If unknown, try "3"
*
* OPTIONS: 2 = Version 2. 3 = Version 3.
*/
$LOGIN_CONF['ldap_server_version'] = 3;

/**
* LDAP USERNAME SUFFIX
* If you're using Active Directory, you may need to use an account suffix
* such as '@mydomain.local'. This is not always required.
*
*/
$LOGIN_CONF['ldap_username_suffix'] = '';

/**
* LDAP USER REQUIRES PASS?
* This relates to fetching a user's record from the LDAP.
* If the each user does not have a password switch this to 'no' or authentication will fail.
* Naturally, it's highly recommended that the LDAP admin chooses to use password authentication!
*
* OPTIONS: 1 = Yes. 0 = No
*/
$LOGIN_CONF['ldap_user_requires_pass'] = 1;



?>