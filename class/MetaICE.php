<?php

/**
	Copyright (C) août 2014  David Bonnier
	Classe pour toutes les meta de ICE
*/
require_once 'ConnectionICE.php';

class MetaICE extends ConnectionICE
{
	private $m_meta;

	function __construct()
	{
		parent::__construct();
		$this->m_meta = Murmur_MetaPrxHelper::checkedCast($this->m_connectMurmur);
		$this->m_meta = $this->m_meta->ice_context($this->m_iceScecet);
	}

	function __destruct()
	{
		$this->m_meta = Murmur_MetaPrxHelper::uncheckedCast($this->m_connectMurmur);
		$this->m_meta = null;
		parent::__destruct();
	}

	/*
	** Config d'un mumble par défault
	*/
	public function configDefaulf()
	{
		/*Possibiliter fichier cache ICI*/
		return $this->m_meta->getDefaultConf();
	}

	/*
	** Une valeur de la configuration par default
	** Retour de la valeur
	** Retourne null si pas de valeur
	*/
	public function getConfDefaulf($value)
	{
		$default = $this->configDefaulf();
		if(array_key_exists($value, $default))
			return $default[$value];
		else
			return null;
	}

	/*
	** Retourne un serveur
	*/
	public function getServer($id)
	{
		return $this->m_meta->getServer($id);
	}

	/*
	** Retourne une liste des serveurs qui sont démaré
	*/
	public function getBootedServers()
	{
		return $this->m_meta->getBootedServers();
	}

	/*
	** Retourne le nombre de serveur qui sont démaré
	*/
	public function getNbBootedServers()
	{
		return count($this->m_meta->getBootedServers());
	}
}

?>