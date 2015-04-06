<?php
 
/*
+--------------------------------------------------------------------------
|   Invision Power Services Kernel [Email Functions]
|   Invision Power Board 2.2.2
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2006 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Tuesday 27th of March 2007 07:00:16 AM
|   Release: 936d62a249c0dc8fd81438cdbc911b98
|   Licence Info: http://www.invisionboard.com/?license
|                  http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD ÍÅ ßÂËßÅÒÑß ÁÅÑÏËÀÒÍÛÌ ÏÐÎÃÐÀÌÌÍÛÌ ÎÁÅÑÏÅ×ÅÍÈÅÌ!
|   Ïðàâà íà ÏÎ ïðèíàäëåæàò Invision Power Services
|   Ïðàâà íà ïåðåâîä IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|
|   > Core Module
|   > Module written by Matt Mecham
|   > Date started: Tuesday 1st March 2005 (11:52)
|
|	> Module Version Number: 2.1.0
+--------------------------------------------------------------------------
*/

/**
 * IPS Kernel Pages: Email
 *
 * This class contains all generic PHP mail and SMTP functions
 * as well as specific functions for IPB emails
 *
 * Example usage:
 * <code>
 * $email = new class_email();
 * $email->email_init();
 * $email->to = 'user@invisionboard.com';
 * $email->from = 'from@user.com';
 * $email->subject = 'Test Email';
 * $email->message = 'Hello there!';
 * $email->send_mail();
 * </code>
 *
 * @package		IPS_KERNEL
 * @author		Matt Mecham
 * @copyright	Invision Power Services, Inc.
 * @version		2.1
 */
 
/**
 *
 */
 
/**
 * Email Class
 *
 * Methods and functions for handling emails
 *
 * @package	IPS_KERNEL
 * @author   Matt Mecham
 * @version	2.1
 * @todo		Separate out IPB content and leave a pure
 *			email class.
 */
class class_email
{
	/**
	* From email address 
	*
	* @var string
	*/
	var $from         = "";
	
	/**
	* To email address
	*
	* @var string
	*/
	var $to           = "";
	
	/**
	* Email subject
	*
	* @var string
	*/
	var $subject      = "";
	
	/**
	* Email message contents
	*
	* @var string
	*/
	var $message      = "";
	
	/**
	* Attachments: Parts
	*
	* @var array
	*/
	var $parts        = array();

	/**
	* BCC Email addresses 
	*
	* @var array
	*/
	var $bcc          = array();
	
	/**
	* Email headers (sep. \r\n)
	*
	* @var string
	*/
	var $mail_headers = "";
	
	/**
	* Attachments: Multi-part
	*
	* @var string
	*/
	var $multipart    = "";
	
	/**
	* Attachments: Boundry
	*
	* @var string
	*/
	var $boundry      = "----=_NextPart_000_0022_01C1BD6C.D0C0F9F0";
	
	/**
	* HTML email flag
	*
	* @var integer
	*/
	var $html_email   = 0;
	
	/**
	* Email character set
	*
	* @var string
	*/
	var $char_set     = 'iso-8859-1';
	
	/**
	* SMTP: 
	*
	* @var boolean
	*/
	var $smtp_fp      = FALSE;
	
	/**
	* SMTP: Message
	*
	* @var string
	*/
	var $smtp_msg     = "";
	
	/**
	* SMTP: Port
	*
	* @var integer
	*/
	var $smtp_port    = 25;
	
	/**
	* SMTP: Host
	*
	* @var string
	*/
	var $smtp_host    = "localhost";
	
	/**
	* SMTP: Username 
	*
	* @var string
	*/
	var $smtp_user    = "";
	
	/**
	* SMTP: Password
	*
	* @var string
	*/
	var $smtp_pass    = "";
	
	/**
	* SMTP: Return code
	*
	* @var string
	*/
	var $smtp_code    = "";
	
	/**
	* SMTP: Wrap email addresses in brackets flag
	*
	* @var integer boolean
	*/
	var $wrap_brackets = 0;
	
	/**
	* Default email method (mail or smtp)
	*
	* @var string
	*/
	var $mail_method  = 'mail';
	
	/**
	* Dump email to flat file for testing
	*
	* @var integer
	*/
	var $temp_dump    = 0;
	
	/**
	* Error message
	*
	* @var string
	*/
	var $error_msg;
	
	/**
	* Error description
	*
	* @var string
	*/
	var $error_help;
	
	/**
	* Error flag
	*
	* @var string
	*/
	var $error;
	
	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/
	
	function class_email()
	{
		
	}
	
	/*-------------------------------------------------------------------------*/
	// ADD ATTACHMENT
	/*-------------------------------------------------------------------------*/
	
	/**
	* Add an attachment to the current email
	*
	* @param	string	File data
	* @param	string	File name
	* @param	string	File type (MIME)
	* @return	void
	*/
	
	function add_attachment($data = "", $name = "", $ctype='application/octet-stream')
	{
		$this->parts[] = array( 'ctype'  => $ctype,
								'data'   => $data,
								'encode' => 'base64',
								'name'   => $name
							  );
	}
	
	/*-------------------------------------------------------------------------*/
	// BUILD HEADERS
	/*-------------------------------------------------------------------------*/
	
	/**
	* Build the email headers (MIME, Charset, From, BCC, To, Subject, etc)
	*
	* @return	void
	*/
	
	function build_headers()
	{
		//-----------------------------------------
		// HTML (hitmuhl)
		// If we're sending HTML messages, then
		// we'll add the plain text message along with
		// it for non HTML browsers
		//-----------------------------------------
		
		$this->pt_message = $this->message;
		
		//-----------------------------------------
		// Start mail headers
		//-----------------------------------------
		
		$this->mail_headers .= "Return-Path: ".$this->from."\n";
		$this->mail_headers .= "X-Priority: 3\n";
		$this->mail_headers .= "X-Mailer: IPB PHP Mailer\n";
		
		//-----------------------------------------
		// From and to...
		//-----------------------------------------
		
		$this->mail_headers  .= "From: ".$this->from."\n";
		
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
		
		# Start MIME headers
		$this->mail_headers .= "MIME-Version: 1.0\n";
		
		//-----------------------------------------
		// Attachments?
		//-----------------------------------------
		
		if ( count ($this->parts) > 0 )
		{
			# Format PT message
			if ( $this->html_email )
			{
				$this->pt_message = str_replace( "<br />", "\n", $this->pt_message );
				$this->pt_message = str_replace( "<br>"  , "\n", $this->pt_message );
				$this->pt_message = strip_tags( $this->pt_message );
			}
			
			# Force plain text emails only
			$this->html_email = 0;
			
			# Do headers
			$this->mail_headers .= "Content-type: multipart/mixed;boundary=\"".$this->boundry."\"\n\nThis is a MIME encoded message.\n\n--".$this->boundry;
		}
		
		//-----------------------------------------
		// Sort out HTML / PT emails
		//-----------------------------------------
		
		if ( $this->html_email )
		{
			# Format PT message
			$this->pt_message = str_replace( "<br />", "\n", $this->pt_message );
			$this->pt_message = str_replace( "<br>"  , "\n", $this->pt_message );
			$this->pt_message = strip_tags( $this->pt_message );
		
			# Do headers
			$this->mail_headers .= "Content-type: multipart/alternative;boundary=\"".$this->boundry."\"\n\nThis is a MIME encoded message.\n\n--".$this->boundry;
		}
		
		//-----------------------------------------
		// HTML headers...
		//-----------------------------------------
		
		# Plain text version...
		$this->mail_headers .= "\nContent-Type: text/plain;\n\tcharset=\"".$this->char_set."\"\n\n".$this->pt_message."\n\n--".$this->boundry;
		
		# HTML version
		if ( $this->html_email )
		{
			$this->mail_headers .= "\nContent-Type: text/html;\n\tcharset=\"".$this->char_set."\"\n\n".$this->message."\n\n";
		}
		
		//-----------------------------------------
		// Do the attachments last
		//-----------------------------------------
		
		if ( count ($this->parts) > 0 )
		{
			$this->mail_headers .= $this->build_multipart();
		}
		
		//-----------------------------------------
		// Stop double printing message
		//-----------------------------------------
		
		if ( $this->html_email OR count ($this->parts) > 0 )
		{
			$this->message = "";
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// BUILD MULTIPART
	/*-------------------------------------------------------------------------*/
	
	/**
	* Build the multipart headers for the email
	*
	* @return	void
	*/
	
	function build_multipart() 
	{
		$multipart = "";
		
		for ( $i = sizeof($this->parts) - 1 ; $i >= 0 ; $i-- )
		{
			$multipart .= "\n--" . $this->boundry . "\n" . $this->_encode_attachment($this->parts[$i]);
		}
		
		return $multipart;
	}
	
	
	/*-------------------------------------------------------------------------*/
	// Send_mail:
	// Physically sends the email
	/*-------------------------------------------------------------------------*/
	
	/**
	* Send the mail (mail must be built by this point)
	*
	* @return	boolean On error
	*/
	
	function send_mail()
	{
		//-----------------------------------------
		// Wipe ya face
		//-----------------------------------------
		
		$this->to   = preg_replace( "/[ \t]+/" , ""  , $this->to   );
		$this->from = preg_replace( "/[ \t]+/" , ""  , $this->from );
		
		$this->to   = preg_replace( "/,,/"     , ","  , $this->to );
		$this->from = preg_replace( "/,,/"     , ","  , $this->from );
		
		$this->to     = preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#" , "", $this->to  );
		$this->from   = preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#" , "", $this->from);
		
		//-----------------------------------------
		// Build headers
		//-----------------------------------------
		
		$this->build_headers();
		
		//-----------------------------------------
		// Lets go..
		//-----------------------------------------
		
		if ( ($this->from) and ($this->subject) )
		{
			//-----------------------------------------
			// Tmp dump? (Testing)
			//-----------------------------------------
			
			if ($this->temp_dump == 1)
			{
				$blah = $this->subject."\n------------\n".$this->mail_headers."\n\n".$this->message;
				
				$pathy = str_replace( '//', '/', $this->root_path.'/_mail/'.date("M-j-Y,hi-A").str_replace( '@', '+', $this->to ).".php" );
				$fh = fopen ($pathy, 'w');
				fputs ($fh, $blah, strlen($blah) );
				fclose($fh);
			}
			else
			{
				//-----------------------------------------
				// PHP MAIL()
				//-----------------------------------------
				
				if ($this->mail_method != 'smtp')
				{
					if ( ! @mail( $this->to, $this->subject, $this->message, $this->mail_headers, $this->extra_opts ) )
					{
						# Try without args for safe mode peeps
						if ( ! @mail( $this->to, $this->subject, $this->message, $this->mail_headers ) )
						{
							$this->fatal_error("Could not send the email", "Failed at 'mail' command");
						}
					}
				}
				//-----------------------------------------
				// SMTP
				//-----------------------------------------
				else
				{
					$this->smtp_send_mail();
				}
			}
		}
		else
		{
			$this->fatal_error("From or subject empty");
			return FALSE;
		}
		
		$this->to           = "";
		$this->from         = "";
		$this->message      = "";
		$this->subject      = "";
		$this->mail_headers = "";
	}

	/*-------------------------------------------------------------------------*/
	// SMTP: Get line
	/*-------------------------------------------------------------------------*/
	
	/**
	* SMTP: Get next line
	*
	* @return	void
	*/
	
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
	
	/*-------------------------------------------------------------------------*/
	// SMTP Send CMD
	/*-------------------------------------------------------------------------*/
	
	/**
	* SMTP: Send command
	*
	* @param	string	SMTP command
	* @return	boolean
	*/
	
	function smtp_send_cmd($cmd)
	{
		$this->smtp_msg  = "";
		$this->smtp_code = "";
		
		fputs( $this->smtp_fp, $cmd."\r\n" );
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		return $this->smtp_code == "" ? FALSE : TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// SMTP Error
	/*-------------------------------------------------------------------------*/
	
	/**
	* SMTP: Error handler
	*
	* @param	string	SMTP error
	* @return	boolean
	*/
	
	function smtp_error($err = "")
	{
		$this->smtp_msg = $err;
		$this->fatal_error( $err );
		return FALSE;
	}
	
	/*-------------------------------------------------------------------------*/
	// SMTP: Encode newlines
	/*-------------------------------------------------------------------------*/
	
	/**
	* Encode data and make it safe for SMTP transport
	*
	* @param	string	Raw Data
	* @return	string	CRLF Encoded Data
	*/
	
	function smtp_crlf_encode($data)
	{
		$data .= "\n";
		$data  = str_replace( "\n", "\r\n", str_replace( "\r", "", $data ) );
		$data  = str_replace( "\n.\r\n" , "\n. \r\n", $data );
		
		return $data;
	}
	
	/*-------------------------------------------------------------------------*/
	// SMTP send mail
	/*-------------------------------------------------------------------------*/
	
	/**
	* SMTP: Sends the SMTP email
	*
 	* @return	void
	*/
	
	function smtp_send_mail()
	{
		$this->smtp_fp = @fsockopen( $this->smtp_host, intval($this->smtp_port), $errno, $errstr, 30 );
		
		if ( ! $this->smtp_fp )
		{
			$this->smtp_error("Could not open a socket to the SMTP server");
			return;
		}
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		if ( $this->smtp_code == 220 )
		{
			$data = $this->smtp_crlf_encode( $this->mail_headers."\n" . $this->message);
			
			//-----------------------------------------
			// HELO!, er... HELLO!
			//-----------------------------------------
			
			$this->smtp_send_cmd("HELO ".$this->smtp_host);
			
			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error("HELO");
				return;
			}
			
			//-----------------------------------------
			// Do you like my user!
			//-----------------------------------------
			
			if ($this->smtp_user and $this->smtp_pass)
			{
				$this->smtp_send_cmd("AUTH LOGIN");
				
				if ( $this->smtp_code == 334 )
				{
					$this->smtp_send_cmd( base64_encode($this->smtp_user) );
					
					if ( $this->smtp_code != 334  )
					{
						$this->smtp_error("Username not accepted from the server");
						return;
					}
					
					$this->smtp_send_cmd( base64_encode($this->smtp_pass) );
					
					if ( $this->smtp_code != 235 )
					{
						$this->smtp_error("Password not accepted from the server");
						return;
					}
				}
				else
				{
					$this->smtp_error("This server does not support authorisation");
					return;
				}
			}
			
			//-----------------------------------------
			// We're from MARS!
			//-----------------------------------------
			
			if ( $this->wrap_brackets )
			{
				if ( ! preg_match( "/^</", $this->from ) )
				{
					$this->from = "<".$this->from.">";
				}
			}
			
			$this->smtp_send_cmd("MAIL FROM:".$this->from);
			
			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error();
				return;
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
			
			//-----------------------------------------
			// You are from VENUS!
			//-----------------------------------------
			
			foreach( $to_arry as $to_email )
			{
				if ( $this->wrap_brackets )
				{
						$this->smtp_send_cmd("RCPT TO:<".$to_email.">");
				}
				else
				{
					$this->smtp_send_cmd("RCPT TO:".$to_email);
				}
				
				if ( $this->smtp_code != 250 )
				{
					$this->smtp_error("Incorrect email address: $to_email");
					return;
					break;
				}
			}
			
			//-----------------------------------------
			// SEND MAIL!
			//-----------------------------------------
			
			$this->smtp_send_cmd("DATA");
			
			if ( $this->smtp_code == 354 )
			{
				fputs( $this->smtp_fp, $data."\r\n" );
			}
			else
			{
				$this->smtp_error("Error on write to SMTP server");
				return;
			}
			
			//-----------------------------------------
			// GO ON, NAFF OFF!
			//-----------------------------------------
			
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
			
			//-----------------------------------------
			// Tubby-bye-bye!
			//-----------------------------------------
			
			@fclose( $this->smtp_fp );
		}
		else
		{
			$this->smtp_error();
			return;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// FATAL ERROR : RETURN
	/*-------------------------------------------------------------------------*/
	
	/**
	* Fatal error handler
	*
	* @param	string	Error Message
	* @param	string	Error Help / Description
	* @return	boolean
	*/
	
	function fatal_error($msg, $help="")
	{
		$this->error_msg  = $msg;
		$this->error_help = $help;
		
		return FALSE;
	}
	
	/*-------------------------------------------------------------------------*/
	// ENCODE ATTACHMENT
	/*-------------------------------------------------------------------------*/
	
	/**
	* Encode an attachment
	*
	* @param	string	Raw data
	* @return	string	Processed data
	*/
	
	function _encode_attachment($part)
	{
		$msg = chunk_split(base64_encode($part['data']));
		
		return "Content-Type: ".$part['ctype']. ($part['name'] ? ";\n\tname =\"".$part['name']."\"" : "").
			   "\nContent-Transfer-Encoding: ".$part['encode']."\nContent-Disposition: attachment;\n\tfilename=\"".$part['name']."\"\n\n".$msg."\n";
		
	}

}

?>