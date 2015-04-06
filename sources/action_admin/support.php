<?php

/*
+--------------------------------------------------------------------------
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
|		          http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-03-23 07:34:25 -0500 (Thu, 23 Mar 2006) $
|   > $Revision: 177 $
|   > $Author: brandon $
+---------------------------------------------------------------------------
|
|   > Support Module
|   > Module written by Brandon Farber
|   > Date started: 19th April 2006
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
    print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}


class ad_support
{
	var $base_url;

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "help";

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "support";

	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Помощь и поддержка' );

		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'doctor':
				$this->ipsclass->admin->page_detail = "Воспользовавшись нашей документацией, вы сможете узнать более подробно о том, как использовать ту или иную функцию в Invision Power Board.";
				$this->ipsclass->admin->page_title  = "Документация";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/docs-ipb' );
				break;
			break;

			case 'kb':
				$this->ipsclass->admin->page_detail = "Воспользуйтесь нашей базой знаний для поиска решений общих проблем и проблем, связанных со старыми выпусками Invision Power Board. В данной базе вы также сможете найти описание, как использовать некоторые функции нашего программного обеспечения.";
				$this->ipsclass->admin->page_title  = "База знаний";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/wiki' );
				break;

			case 'support':
				$this->ipsclass->admin->page_detail = "Если у вас возникли проблемы или вопросы при использовании программного обеспечения компании Invision Power Services, то, пожалуйста, воспользуйтесь формой ниже, чтобы получить квалифицированную помощь. Помните, что время ответа в первую очередь зависит от сложности вашего вопроса.<br /><br /><i>Вам необходимо иметь активную лицензию, чтобы воспользоваться клиент-центром.</i>";
				$this->ipsclass->admin->page_title  = "Помощь и поддержка";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/clientarea' );
				break;

			case 'ibresource':
				$this->ipsclass->admin->page_detail = "Форумы IBResource — это ресурс, где можно найти много полезных статей и советов, модификаций и модулей, стилей и дополнительной графики, а так же получить помощь от других клиентов. Помните, вся информация на наших форумах предоставляется «как есть» и не подтверждена техническим отделом компании «Invision Power Services, Inc.» — все действия, описанные другими пользователями, вы будете делать на свой страх и риск.
                                                        <br /><br />Перевести свою учетную запись на наших форумах в группу &laquo;Клиенты&raquo; вы можете через клиент-центр.";
				$this->ipsclass->admin->page_title  = "Помощь и поддержка";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/forums' );
				break;

			case 'contact':
				$this->ipsclass->admin->page_detail = "Если вы хотите связаться с нами, пожалуйста, ознакомьтесь с нашей контактной информацией и часами работы ниже.";
				$this->ipsclass->admin->page_title  = "Помощь и поддержка";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/contact' );
				break;

			case 'features':
				$this->ipsclass->admin->page_detail = "Если вы хотите подсказать нам, как улучшить наши продукты, то просто оставьте свое предложение в одном из форумов ниже.";
				$this->ipsclass->admin->page_title  = "Предложения и пожелания";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/suggestfeatures' );
				break;

			case 'bugs':
				$this->ipsclass->admin->page_detail = "Вы можете отправить отчет о найденной ошибке, а так же узнать об исправлениях других ошибок, используя «Bugtracker» ниже.";
				$this->ipsclass->admin->page_title  = "Помощь и поддержка";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/bugtrack' );
				break;


			//-----------------------------------------
			default:
				$this->ipsclass->admin->page_detail = "Если у вас возникли проблемы или вопросы при использование программного обеспечения компании Invision Power Services, то, пожалуйста, воспользуйтесь формой ниже, чтобы получить квалифицированную помощь. Помните, что время ответа в первую очередь зависит от сложности вашего вопроса.<br /><br /><i>Вам необходимо иметь активную лицензию, чтобы воспользоваться клиент-центром.</i>";
				$this->ipsclass->admin->page_title  = "Помощь и поддержка";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'https://www.ibresource.ru/clientarea/index.php' );
				break;
		}
	}

}


?>