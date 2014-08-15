<?php

/**
	Copyright (C) août 2014  David Bonnier
	Classe pour toutes les serveur virtuel de ICE
*/
require_once 'MetaICE.php';

class VirtualServeurICE extends ConnectionICE
{
	private $m_serveurVirtuel;
	private $m_channel;
	private $m_users;
	private $m_nbUsers;

	function __construct($serveurVirtuel)
	{
		parent::__construct();
		$this->m_serveurVirtuel = $serveurVirtuel;
	}

	function __destruct()
	{
		$this->m_serveurVirtuel = Murmur_ServerPrxHelper::uncheckedCast($this->m_connectMurmur);
		$this->m_serveurVirtuel = null;
		parent::__destruct();
	}

	/*
	** Retourne uune instance en fonction de l'ID du serveur
	** Retourne null si il n'y a pas de serveur
	*/
	public static function instance($id)
	{
		$meta = new MetaICE();
		$serveur = $meta->getServer($id);
		$meta = null;
		if($serveur != "")
			return new static($serveur);
		else
			return 0;
	}

	/*
	** Retourne la ou les config soit defaut, soit serveur
	** null si c'est une key absurbe
	*/
	public function getConf($keyConf = '')
	{
		$meta = new MetaICE();
		$this->m_serveurVirtuel = $this->m_serveurVirtuel->ice_context($this->m_iceScecet);
		if ($keyConf == '')
		{
			$default = $meta->configDefaulf();
			$default['port'] = $default['port'] + $this->m_serveurVirtuel->id() - 1;
			$meta = null;
			$confServeur = $this->m_serveurVirtuel->getAllConf();
			return array_replace($default, $confServeur);			
		}	
		else 
		{
			$config = $this->m_serveurVirtuel->getConf($keyConf);
			if(! $config)
			{
				$config =  $meta->getConfDefaulf($keyConf);
				if($keyConf == 'port')
					$config =  $config + $this->m_serveurVirtuel->id() - 1;
			}
			$meta = null;
			return $config;
		}			
	}

	/*
	** Retourne le nombre d'utlisateur
	*/
	public function getNbUser()
	{
		$this->m_serveurVirtuel = $this->m_serveurVirtuel->ice_context($this->m_iceScecet);
		if($this->ouvert())
			return count($this->m_serveurVirtuel->getUsers());
		else
			return 0;
	}

	/*
	** Retourne le nombre de Channel
	*/
	public function getNbChannel()
	{
		$this->m_serveurVirtuel = $this->m_serveurVirtuel->ice_context($this->m_iceScecet);
		return count($this->m_serveurVirtuel->getChannels());
	}

	/*
	** Return true si le serveur est open, false sinon
	*/
	public function ouvert()
	{
		$this->m_serveurVirtuel = $this->m_serveurVirtuel->ice_context($this->m_iceScecet);
		return $this->m_serveurVirtuel->isRunning();
	}

	/*
	** getTree dans l'ordre
	*/
	public function getTree()
	{
		$this->m_serveurVirtuel = $this->m_serveurVirtuel->ice_context($this->m_iceScecet);
		$tree = $this->m_serveurVirtuel->getTree();

		$tree->c->name = $this->getConf('registername');
		if($tree->c->name == "")
			$tree->c->name = "Root";
		
		$retour = array($tree->c->position => $tree->c,
			'users' => $tree->users,
			'children' => array());

		$retour['children'] = $this->creationTree($tree, $retour['children']);
		ksort($retour['children']);

		return $retour;
	}

	/*
	** Creation de l'arbre dans toutes les possibilités
	*/
	private function creationTree($channel, $tableau)
	{
		foreach ($channel->children as $key) 
		{
			if($key->c->position<10)
				$numero = "000000000";
			elseif ($key->c->position<100) 
				$numero = "00000000";
			elseif ($key->c->position<1000) 
				$numero = "0000000";
			elseif ($key->c->position<10000) 
				$numero = "000000";
			elseif ($key->c->position<100000) 
				$numero = "00000";
			elseif ($key->c->position<1000000) 
				$numero = "0000";
			elseif ($key->c->position<10000000) 
				$numero = "000";
			elseif ($key->c->position<100000000) 
				$numero = "00";
			elseif ($key->c->position<1000000000) 
				$numero = "0";

			$numero = $numero.$key->c->position.strtolower($key->c->name);

			$tableau[$numero] = array($key->c);
			$tableau[$numero]['users'] = $key->users;
			$tableau[$numero]['children'] = array();
			$tableau[$numero]['children'] = $this->creationTree($key, $tableau[$numero]['children']);
			ksort($tableau[$numero]['children']);
		}
		return $tableau;
	}
}

?>