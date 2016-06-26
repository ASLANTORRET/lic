<?php
class KZMnp
{
	/*
	$mnp = new KZMnp();
	$mnp->phone = '77017234213';
	$mnp->is_local = false;
	$mnp->query();
	echo $mnp->mnc;
	*/

	public $uri = 'http://mnp.sms2b.kz/query?'; //production урл
	//public $uri = 'http://mnp.local/query?'; //developer урл
	public $phone;
	public $response;
	public $result;
	public $operator;
	public $mnc;
	public $mccmnc;
	public $route;
	public $portdate;
	public $message = null;
	public $is_local = false; //подключаться напрямую к базе?
	//public $debug = false;
	public $db_name = 'db_mnp_kz';
	public $db_username = 'root';
	public $db_password = '';
	public $db_host = '127.0.0.1';
	private static $operators = array(
		'D02'=>array(
			'operator'=>'mkcell',
			'mnc'=>'02',
			'mccmnc'=>'40102'
			),
		'D77'=>array(
			'operator'=>'mmts',
			'mnc'=>'77',
			'mccmnc'=>'40177'
			),
		'D01'=>array(
			'operator'=>'mkartel',
			'mnc'=>'01',
			'mccmnc'=>'40101'
			),
		'D07'=>array(
			'operator'=>'maltel',
			'mnc'=>'07',
			'mccmnc'=>'40107'
			),
		);

	private function curl_request($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_TIMEOUT, 7);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}

	private function ValidatePhone()
	{
		$result = preg_replace("/[^0-9]/","", $this->phone);
		if (strlen($result) <> strlen($this->phone))
		{
			return false;
		}
		elseif (strlen($this->phone) != 11)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	private function createConnection()
	{
		$dsn = 'mysql:host='.$this->db_host.';dbname='.$this->db_name;
		$pdo = new PDO($dsn, $this->db_username, $this->db_password);
		return $pdo;
	}
	
	private function getRouteToMNC($gate)
	{
		if (array_key_exists($gate, self::$operators))
		{
			return (string)self::$operators[$gate]['mnc'];
		}
	}

	private function getMNCtoRoute($mnc)
	{
		foreach (self::$operators as $key=>$value)
		{
			if ($value['mnc'] == $mnc)
			{
				return (string)$key;
			}
		}
	}

	private function getRouteToMCCMNC($gate)
	{
		if (array_key_exists($gate, self::$operators))
		{
			return (string)self::$operators[$gate]['mccmnc'];
		}
	}

	public function toJson()
	{
		$result = array(
			'number' => $this->phone,
			'result' => $this->result,
			'operator' => $this->operator,
			'portdate' => $this->portdate,
			'mnc'=> $this->mnc,
			'route'=>$this->route,
			'mccmnc'=>$this->mccmnc
		);
		return json_encode($result);
	}

	public function LocalQuery()
	{
		$pdo = self::createConnection();

		$sql = 'select OwnerID, Route, PortDate from tb_mnp where number=:phone limit 1';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':phone', $this->phone, PDO::PARAM_STR);
		$stmt->execute();

		$res = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($res)
		{
			$this->result = true;
			$this->operator = $res['OwnerID'];
			$this->route = $res['Route'];
			$this->message = 'OK';
			$this->portdate = $res['PortDate'];
			$this->mnc = self::getRouteToMNC($this->route);
			$this->mccmnc = self::getRouteToMCCMNC($this->route);
			$this->response = self::toJson();
			return $res;
		}
		else
		{
			$sql = 'select OwnerId, MNC from tb_numbering_plan where :phone between NumberFrom and NumberTo limit 1';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':phone', $this->phone, PDO::PARAM_STR);
			$stmt->execute();

			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($res)
			{
				$this->result = true;
				$this->operator = $res['OwnerId'];
				$this->mnc = $res['MNC'];
				$this->route = self::getMNCToRoute($this->mnc);
				$this->message = 'OK';
				$this->mccmnc = self::getRouteToMCCMNC($this->route);
				$this->response = self::toJson();
			}
			else
			{
				return false;
			}
		}
		
	}

	public function Query()
	{
		if (!self::ValidatePhone())
		{
			$this->message = 'Error: Number not found. Incorrect number?';
			$this->result = false;
			$this->response = self::toJson();
			return;
		}

		if ($this->is_local)
		{
			return self::LocalQuery();
		}
		else
		{
			return self::RemoteQuery();
		}

	}

	public function RemoteQuery()
	{
		$params = array(
			'phone'=>$this->phone
			);
		$url = $this->uri.http_build_query($params);

		//@$res = file_get_contents($url);
		@$res = self::curl_request($url);

		if ($res)
		{
			$res_arr = json_decode($res, true);
			$this->response = $res;
			$this->result = $res_arr['result'];
		}
		else
		{
			return false;
		}

		if ($this->result)
		{
			$this->operator = $res_arr['operator'];
			$this->result = $res_arr['result'];
			$this->mnc = (string)$res_arr['mnc'];
			$this->route = $res_arr['route'];
			$this->mccmnc = self::getRouteToMCCMNC($this->route);
			$this->portdate = $res_arr['portdate'];
			$this->message = 'OK';
		}
		else {
			$this->response = $res;
			$this->message = 'Error: Number not found. Incorrect number?';
		}
		//return $this->response;
		return $this->result;
	}
}
?>