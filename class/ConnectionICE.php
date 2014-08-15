<?php

/**
	Copyright (C) août 2014  David Bonnier
	Classe connection murmur
	Classe principale
*/

require_once 'Ice.php';
require_once 'Murmur.php';


abstract class ConnectionICE
{
	//Variable de classe
	protected $m_connectMurmur;
	private $m_ipMumbleICE = "127.0.0.1";
	private $m_portMumbleICE = "6502";

	private $m_timeoutMumbleICE = "20";

	//Mot de passe a utiliser a chaque fois
	protected $m_iceScecet = array('secret' => 'lire');


	protected function __construct()
	{
		$initData = new Ice_InitializationData;
		$initData->properties = Ice_createProperties();
		$initData->properties->setProperty("Ice.MessageSizeMax", "16384");
		
		$ICE = Ice_initialize($initData);
		
		$this->m_connectMurmur = $ICE->stringToProxy('Meta:tcp -h '.$this->m_ipMumbleICE.
			' -p '.$this->m_portMumbleICE. ' -t '.$this->m_timeoutMumbleICE);
	}

	protected function __destruct()
	{
		$this->m_connectMurmur = null;
	}
}

?>
