<?php
/*
+--------------------------------------------------------------------------
|   Invision Gallery Module v<#VERSION#>
|   ========================================
|   by Adam Kinder
|   (c) 2001 - 2005 Invision Power Services
|   ========================================
|   
|   Nullfied by SneakerXZ
|   
+---------------------------------------------------------------------------
*/


/**
* Library/Gallery Emailer
*
* NTS: Remove? Not needed anymore
*
* @package		Gallery
* @subpackage 	Library
* @author   	Matt mecham
* @version		<#VERSION#>
* @since 		1.0
*/


class gal_emailer {

        var $ipsclass;
        var $glib;

	var $from         = "";
	var $to           = "";
	var $subject      = "";
	var $message      = "";
	var $header       = "";
	var $footer       = "";
	var $template     = "";
	var $error        = "";
	var $parts        = array();
	var $bcc          = array();
	var $mail_headers = array();
	var $multipart    = "";
	var $boundry      = "";
	
	var $smtp_fp      = FALSE;
	var $smtp_msg     = "";
	var $smtp_port    = "";
	var $smtp_host    = "localhost";
	var $smtp_user    = "";
	var $smtp_pass    = "";
	var $smtp_code    = "";
	
	var $mail_method  = 'mail';
	
	var $temp_dump = 0;
	
	function emailer()
	{		
		//---------------------------------------------------------
		// Assign $from as the admin out email address, this can be
		// over-riden at any time.
		//---------------------------------------------------------
		
		$this->temp_dump = $this->ipsclass->vars['fake_mail'];
		
		//---------------------------------------------------------
		// Set up SMTP if we're using it
		//---------------------------------------------------------
		
		if ( $this->ipsclass->vars['mail_method'] == 'smtp' )
		{ 
			$this->mail_method = 'smtp';
			$this->smtp_port   = ( intval($this->ipsclass->vars['smtp_port']) != "" ) ? intval($this->ipsclass->vars['smtp_port']) : 25;
			$this->smtp_host   = (     $this->ipsclass->vars['smtp_host'] != ""     ) ?     $this->ipsclass->vars['smtp_host']     : 'localhost';
			$this->smtp_user   = $this->ipsclass->vars['smtp_user'];
			$this->smtp_pass   = $this->ipsclass->vars['smtp_pass'];
		}
		
		//---------------------------------------------------------
		// Temporarily assign $header and $footer, this can be over-riden
		// also
		//---------------------------------------------------------
		
		$this->header  = $this->ipsclass->vars['email_header'];
		$this->footer  = $this->ipsclass->vars['email_footer'];
		$this->boundry = "----=_NextPart_000_0022_01C1BD6C.D0C0F9F0";
		
		$this->ipsclass->vars['board_name'] = $this->ipsclass->vars['board_name'];
	}
	
	function add_attachment($data = "", $name = "", $ctype='application/octet-stream')
	{
	
		$this->parts[] = array( 'ctype'  => $ctype,
								'data'   => $data,
								'encode' => 'base64',
								'name'   => $name
							  );
	}
	
    function build_headers()
	{		
		$this->mail_headers  = "From: \"".$this->ipsclass->vars['board_name']."\" <".$this->from.">\n";
		
		if ( $this->mail_method != 'smtp' )
		{
			if ( count( $this->bcc ) > 1 )
			{
				$this->mail_headers .= "Bcc: ".implode( "," , $this->bcc ) . "\n";
			}
		}
		else
		{
			if ( $this->to )
			{
				$this->mail_headers .= "To: ".$this->to."\n";
			}
			$this->mail_headers .= "Subject: ".$this->subject."\n";
		}
		
		$this->mail_headers .= "Return-Path: ".$this->from."\n";
		$this->mail_headers .= "X-Priority: 3\n";
		$this->mail_headers .= "X-Mailer: IPB PHP Mailer\n";
        // html
        $this->mail_headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		
		if ( count ($this->parts) > 0 )
		{
		    
			$this->mail_headers .= "MIME-Version: 1.0\n";
			$this->mail_headers .= "Content-Type: multipart/mixed;\n\tboundary=\"".$this->boundry."\"\n\nThis is a MIME encoded message.\n\n--".$this->boundry;
			$this->mail_headers .= "Content-Transfer-Encoding: quoted-printable\n\n".$this->message."\n\n--".$this->boundry;
			$this->mail_headers .= $this->build_multipart();
			
			$this->message = "";
		}
	
	}
	
	function encode_attachment($part) {
		
		$msg = chunk_split(base64_encode($part['data']));
		
		return "Content-Type: ".$part['ctype']. ($part['name'] ? ";\n\tname =\"".$part['name']."\"" : "").
			  "\nContent-Transfer-Encoding: ".$part['encode']."\nContent-Disposition: attachment;\n\tfilename=\"".$part['name']."\"\n\n".$msg."\n";
		
	}
	
	function build_multipart() {
	
		$multipart = "";
		
		for ($i = sizeof($this->parts) - 1 ; $i >= 0 ; $i--)
		{
			$multipart .= "\n".$this->encode_attachment($this->parts[$i]) . "--".$this->boundry;
		}
		
		return $multipart . "--\n";
		
	}
	
	/*
	90c4dd65
	
	bf0cf730
	
	12087e92
	
	e3fe0f14
	*/
	
	
	//+--------------------------------------------------------------------------
	// send_mail:
	// Physically sends the email
	//+--------------------------------------------------------------------------
	
	function send_mail( $subject, $message )
	{		
		$this->to   = preg_replace( "/[ \t]+/" , ""  , $this->to   );
		$this->from = preg_replace( "/[ \t]+/" , ""  , $this->from );
		
		$this->to   = preg_replace( "/,,/"     , ","  , $this->to );
		$this->from = preg_replace( "/,,/"     , ","  , $this->from );
		
		$this->to     = preg_replace( "#\#\[\]'\"\(\):;/\$!?%\^&\*\{\}#" , "", $this->to  );
		$this->from   = preg_replace( "#\#\[\]'\"\(\):;/\$!?%\^&\*\{\}#" , "", $this->from);

        $this->subject = $subject;
        $this->message = $message;
		
		$this->build_headers();
		
		if ( ($this->from) and ($this->subject) )
		{
			$this->subject .= " ( From ".$this->ipsclass->vars['board_name']." )";
			
			if ($this->temp_dump == 1)
			{
				$blah = $this->subject."\n------------\n".$this->mail_headers."\n\n".$this->message;
				
				$pathy = './_mail/'.date("M-j-Y,h:i-A").".txt";
				$fh = fopen ($pathy, 'w');
				fputs ($fh, $blah, strlen($blah) );
				fclose($fh);
			}
			else
			{
				if ($this->mail_method != 'smtp')
				{
					if ( ! @mail( $this->to, $this->subject, $this->message, $this->mail_headers ) )
					{
						$this->fatal_error("Could not send the email", "Failed at 'mail' command");
					}
				}
				else
				{
					$this->smtp_send_mail();
				}
			}
		}
		else
		{
			return FALSE;
		}
	}
		
	function fatal_error($msg, $help="")
	{
		echo("<h1>Mail Error!</h1><br><b>$msg</b><br>$help");
		exit();
	}
	
	
	//---------------------------------------------------------
	//
	// SMTP methods
	//
	//---------------------------------------------------------
	
	//+------------------------------------
	//| get_line()
	//|
	//| Reads a line from the socket and returns
	//| CODE and message from SMTP server
	//|
	//+------------------------------------
	
	function smtp_get_line()
	{
		$this->smtp_msg = "";
		
		while ( $line = fgets( $this->smtp_fp, 515 ) )
		{
			$this->smtp_msg .= $line;
			
			if ( substr($line, 3, 1) == " " )
			{
				break;
			}
		}
	}
	
	//+------------------------------------
	//| send_cmd()
	//|
	//| Sends a command to the SMTP server
	//| Returns TRUE if response, FALSE if not
	//|
	//+------------------------------------
	
	function smtp_send_cmd($cmd)
	{
		$this->smtp_msg  = "";
		$this->smtp_code = "";
		
		fputs( $this->smtp_fp, $cmd."\r\n" );
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		return $this->smtp_code == "" ? FALSE : TRUE;
	}
	
	//+------------------------------------
	//| error()
	//|
	//| Returns SMTP error to our global
	//| handler
	//|
	//+------------------------------------
	
	function smtp_error($err = "")
	{
		
		if ( ($this->smtp_code == 501) AND ( preg_match( "/domain missing/i", $this->smtp_msg ) ) )
		{
			return;
		}
		else
		{
			$this->fatal_error( "SMTP protocol failure!</b><br>Host: ".$this->smtp_host."<br>Return Code: ".$this->smtp_code."<br>Return Msg: ".$this->smtp_msg."<br>Invision Power Board Error: $err", "Check your SMTP settings from the admin control panel" );
		}
	}
	
	//+------------------------------------
	//| crlf_encode()
	//|
	//| RFC 788 specifies line endings in
	//| \r\n format with no periods on a 
	//| new line
	//+------------------------------------
	
	function smtp_crlf_encode($data)
	{
		$data .= "\n";
		$data  = str_replace( "\n", "\r\n", str_replace( "\r", "", $data ) );
		$data  = str_replace( "\n.\r\n" , "\n. \r\n", $data );
		
		return $data;
	}
	
	//+------------------------------------
	//| send_mail
	//|
	//| Does the bulk of the email sending
	//+------------------------------------
		
	function smtp_send_mail()
	{
		$this->smtp_fp = fsockopen( $this->smtp_host, intval($this->smtp_port), $errno, $errstr, 30 );
		
		if ( ! $this->smtp_fp )
		{
			$this->smtp_error("Could not open a socket to the SMTP server");
		}
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		if ( $this->smtp_code == 220 )
		{
			$data = $this->smtp_crlf_encode( $this->mail_headers."\n" . $this->message);
			
			//---------------------
			// HELO!, er... HELLO!
			//---------------------
			
			$this->smtp_send_cmd("HELO ".$this->smtp_host);
			
			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error("HELO");
			}
			
			//---------------------
			// Do you like my user!
			//---------------------
			
			if ($this->smtp_user and $this->smtp_pass)
			{
				$this->smtp_send_cmd("AUTH LOGIN");
				
				if ( $this->smtp_code == 334 )
				{
					$this->smtp_send_cmd( base64_encode($this->smtp_user) );
					
					if ( $this->smtp_code != 334  )
					{
						$this->smtp_error("Username not accepted from the server");
					}
					
					$this->smtp_send_cmd( base64_encode($this->smtp_pass) );
					
					if ( $this->smtp_code != 235 )
					{
						$this->smtp_error("Password not accepted from the server");
					}
				}
				else
				{
					$this->smtp_error("This server does not support authorisation");
				}
			}
			
			//---------------------
			// We're from MARS!
			//---------------------
			
			$this->smtp_send_cmd("MAIL FROM:".$this->from);
			
			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error();
			}
			
			$to_arry = array( $this->to );
			
			if ( count( $this->bcc ) > 0 )
			{
				foreach ($this->bcc as $bcc)
				{
					if ( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", str_replace( " ", "", $bcc ) ) )
					{
						$to_arry[] = $bcc;
					}
				}
			}
			
			//---------------------
			// You are from VENUS!
			//---------------------
			
			foreach( $to_arry as $to_email )
			{
				$this->smtp_send_cmd("RCPT TO:".$to_email);
				
				if ( $this->smtp_code != 250 )
				{
					$this->smtp_error("Incorrect email address: $to_email");
					return;
					break;
				}
			}
			
			//---------------------
			// SEND MAIL!
			//---------------------
			
			$this->smtp_send_cmd("DATA");
			
			if ( $this->smtp_code == 354 )
			{
				//$this->smtp_send_cmd( $data );
				fputs( $this->smtp_fp, $data."\r\n" );
			}
			else
			{
				$this->smtp_error("Error on write to SMTP server");
			}
			
			//---------------------
			// GO ON, NAFF OFF!
			//---------------------
			
			$this->smtp_send_cmd(".");
			
			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error();
				return;
			}
			
			$this->smtp_send_cmd("quit");
			
			if ( $this->smtp_code != 221 )
			{
				$this->smtp_error();
				return;
			}
			
			//---------------------
			// Tubby-bye-bye!
			//---------------------
			
			@fclose( $this->smtp_fp );
		}
		else
		{
			$this->smtp_error();
		}
	}
}
?>
