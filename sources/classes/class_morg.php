<?php
/*
+---------------------------------------------------------------------------
|
|   > MORGENSHTERN CLASS
|   > Module written by Aliance
|   > Date started: 15th October 2009
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/
if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class morgenshtern
{
    //-----------------------------------------------
	// ПЕРЕМЕННЫЕ
	//-----------------------------------------------

	/**
	 * BK SERVERS
	 *
	 * Сервера Бойцовского Клуба
	 *
	 * @type		array
	 * @version		0.0.1
	 * @since		16-09-2009
	 */
	protected $servers;
	/**
	 * BK SERVERS LENGTH
	 *
	 * Кол-во серверов Бойцовского Клуба
	 *
	 * @type		integer
	 * @version		0.0.1
	 * @since		16-09-2009
	 */
	protected $servers_num;

	//-----------------------------------------------
	// КОНСТРУКТОР
	//-----------------------------------------------

	function __construct()
	{
		$this->servers = array(
			'capital',
			'angels',
			'demons',
			'devils',
			'sun',
			'sand',
			'moon',
			'emeralds',
			'old',
			'dreams',
			'low'
		);
		$this->servers_num = count( $this->servers );
	}
	
	/**
	 * CURL
	 *
	 * Подключается к серверу БК и переходит на страницу чара.
	 * Возвращает ресурс подключения cURL.
	 *
	 * @param		string		ник
	 * @return		resource
	 * @version		0.0.1
	 * @since		16-09-2009
	 */
	public function getChar( $login )
	{
		$random = rand( 0, $this->servers_num - 1 );

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, 'http://' . $this->servers[ $random ] . 'city.combats.com/inf.pl?login=' . urlencode( $login ) . '&short=1' );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
		curl_setopt( $ch, CURLOPT_FAILONERROR, true );
		curl_setopt( $ch, CURLOPT_ENCODING, 'gzip' );
		curl_setopt( $ch, CURLOPT_REFERER, 'http://www.morgenshtern.com' );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)' );

		$info = curl_exec( $ch );

		return $this->curl_error( $ch, $info, $login );
		
	}
	/**
	 * ERROR CURL
	 *
	 * Обрабатывает ошибки при работе с cURL
	 *
	 * @param		resource	ресурс cURL
	 * @param		string		полученный текст инфы чара
	 * @param		string		ник
	 * @return		boolean
	 * @version		0.0.1
	 * @since		16-09-2009
	 */
	public function curl_error( $ch, $info, $name )
	{
		if ( curl_errno( $ch ) )
		{
			return 3;
		}
		if ( preg_match_all( "#Ваш IP временно заблокирован#i", $info, $matches ) )
		{
			return 0;
		}
		if ( preg_match_all( "#Service Unavailable#i", $info, $matches ) )
		{
			return 1;
		}
		if ( preg_match_all( "#Произошла ошибка#i", $info, $matches ) )
		{
			return 2;
		}
		return 4;
	}
	public function check_login( string $login )
	{
		/*
		0 - Сервер БК заблокировал наш IP
		1 - Сервер БК недоступен ( Service Unavailable )
		2 - Чар не найден
		3 - Ошибка cUrl
		4 - Все в порядке
		*/
		return $this->getChar( $login );
	}
	public function get_char_info( int $id, string $login )
	{
		$login = ( empty( $login ) ) ? '<i>невидимка</i>' : $login;
		if ( ! ctype_digit( $id ) OR empty( $id ) OR intval( $id ) <= 0 )
		{
			return array(
				'id'	=> 0,
				'nick'	=> $login
			);
		}

		$sql = $this->ipsclass->DB->simple_select( '*', 'morgenshtern_members', 'id = ' . intval( $id ) );
		$this->ipsclass->DB->exec_query();
		if ( $this->ipsclass->DB->get_num_rows() == 1 )
		{
			return $this->ipsclass->DB->fetch_row();
		}
		else
		{
			return array(
				'id'	=> $id,
				'nick'	=> $login
			);
		}
	}
	public function getInfoImgByCity( $city = '' )
	{
		$city = (string) trim( $city );
		if ( empty( $city ) ) {
			return 'http://img.combats.com/i/inf.gif';
		}

		$sql = $this->ipsclass->DB->simple_select( '*', 'city', 'title = "' . addslashes( $city ) . '"' );
		$this->ipsclass->DB->exec_query();
		if ( $this->ipsclass->DB->get_num_rows() == 1 )
		{
			$row = $this->ipsclass->DB->fetch_row();
			return $row['img'];
		}
		else
		{
			return 'http://img.combats.com/i/inf.gif';
		}
	}
	public function formatChar( $member = array() )
	{
		if ( ! is_array( $member ) OR count( $member ) == 0 ) {
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'morgenshtern_char_format' ) );
		}
		$where = null;
		if ( isset( $member['id'] ) ) {
			$where = 'member_id = ' . $member['id'];
		} else if ( isset( $member['name'] ) ) {
			$where = 'nick = ' . $member['name'];
		}
		if ( null == $where ) {
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'morgenshtern_char_format' ) );
		}

		try {
			$this->ipsclass->DB->simple_select( '*', 'morgenshtern_members', $where );
			$this->ipsclass->DB->simple_limit( 1 );
			$this->ipsclass->DB->exec_query();

			if ( $this->ipsclass->DB->get_num_rows() == 1 ) {
				$result = $this->ipsclass->DB->fetch_row();

				$clan = empty( $result['clan'] ) ? '' : '<a href="http://capitalcity.combats.com/clans_inf.pl?' . $result['clan'] . '" target="_blank"><img src="http://img.combats.com/i/klan/' . $result['clan'] . '.gif" alt="" title="' . $result['clan'] . '" /></a>';
				if ( empty( $result['char_id'] ) ) {
					$inf = '<a href="http://emeraldscity.combats.com/inf.pl?login=' . $member['name'] . '" target="_blank"><img src="http://img.combats.com/i/inf.gif" alt="Информация о ' . $member['name'] . '" /></a>';
				} else {
					if ( empty( $result['native_city'] ) ) {
						$inf = '<a href="http://emeraldscity.combats.com/inf.pl?' . $result['char_id'] . '" target="_blank"><img src="http://img.combats.com/i/inf.gif" alt="Информация о ' . $result['nick'] . '" /></a>';
					} else {
						$inf = '<a href="http://emeraldscity.combats.com/inf.pl?' . $result['char_id'] . '" target="_blank"><img src="' . $this->getInfoImgByCity( $result['native_city'] ) . '" alt="Информация о ' . $result['nick'] . '" /></a>';
					}
				}
				return $clan . '<a href="' . $this->ipsclass->base_url . 'showuser=' . $result['member_id'] . '">' . $result['nick'] . '</a> [' . $result['level'] . ']' . $inf;
			} else {
				throw new Exception( 'Чар не найден в БД.' );
			}
		} catch ( Exception $e ) {
			if ( isset( $member['id'] ) ) {
				return '<a href="' . $this->ipsclass->base_url . 'showuser=' . $member['id'] . '">' . $member['name'] . '</a> <a href="http://emeraldscity.combats.com/inf.pl?login=' . $member['name'] . '" target="_blank"><img src="http://img.combats.com/i/inf.gif" alt="Информация о ' . $member['name'] . '" /></a>';
			} else {
				return $member['name'] . ' <a href="http://emeraldscity.combats.com/inf.pl?login=' . $member['name'] . '" target="_blank"><img src="http://img.combats.com/i/inf.gif" alt="Информация о ' . $member['name'] . '" /></a>';
			}
		}
		
	}
}
?>