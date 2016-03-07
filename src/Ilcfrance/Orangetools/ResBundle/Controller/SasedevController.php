<?php

namespace Ilcfrance\Orangetools\ResBundle\Controller;

use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialMetas\CharsetMeta;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialMetas\Name\Author;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialLinks\Icon;
use Sasedev\Commons\SharedBundle\HtmlModel\Attributes\Href;
use Sasedev\Commons\SharedBundle\Controller\BaseController;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialLinks\Stylesheet;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialScripts\ExternalJavascript;
use Sasedev\Commons\SharedBundle\HtmlModel\Attributes\Src;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialScripts\InlineJavascript;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialMetas\HttpEquivContentMeta;
use Sasedev\Commons\SharedBundle\HtmlModel\Tags\SpecialMetas\NameContentMeta;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class SasedevController extends BaseController
{



	/**
	 * Constructor
	 */
	public function __construct()
	{
		$metaCharset = new CharsetMeta('utf-8');
		$metaXUACompatible = new HttpEquivContentMeta('X-UA-Compatible', 'IE=edge');
		$metaViewport = new NameContentMeta('viewport', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no');
		$metaAuthor = new Author('ILCFrance');

		$this->addHeadMeta($metaCharset);
		$this->addHeadMeta($metaXUACompatible);
		$this->addHeadMeta($metaViewport);
		$this->addHeadMeta($metaAuthor);


		$favicon = new Icon(new Href('favicon.ico', true));
		$FullTemplateMinCss = new Stylesheet(new Href('css/fulltemplate.min.css', true), 'text/css', 'all');
		$this->addHeadLink($favicon);
		$this->addHeadLink($FullTemplateMinCss);

		$Html5shivRespondJs = new ExternalJavascript(new Src('js/html5shiv.respond.min.js', true));
		$Html5shivRespondJs->setPrepend('<!--[if lt IE 9]><!-->');
		$Html5shivRespondJs->setAppend('<!--<![endif]-->');
		$Jquery1MigrateHincludeJs = new ExternalJavascript(new Src('js/jquery1.migrate.hinclude.min.js', true));
		$Jquery1MigrateHincludeJs->setPrepend('<!--[if lt IE 9]><!-->');
		$Jquery1MigrateHincludeJs->setAppend('<!--<![endif]-->');
		$Jquery2MigrateHincludeJs = new ExternalJavascript(new Src('js/jquery2.migrate.hinclude.min.js', true));
		$Jquery2MigrateHincludeJs->setPrepend('<!--[if (gte IE 9) | (!IE)]><!-->');
		$Jquery2MigrateHincludeJs->setAppend('<!--<![endif]-->');

		$this->addHeadScript($Html5shivRespondJs);
		$this->addHeadScript($Jquery1MigrateHincludeJs);
		$this->addHeadScript($Jquery2MigrateHincludeJs);

		$AppMinJs = new ExternalJavascript(new Src('js/app.min.js', true));

		$EndInlineJsTxt = <<<JSSAFEOUTPUT
		<!--
		$(function() {
			$("a[data-targetblank]").live('click', function(e) {
				window.open($(this)[0].href);
				e.preventDefault();
			});
			$("html").niceScroll({
				scrollspeed: 100,
				mousescrollstep: 30,
				cursorwidth: 9,
				cursorborder: 0,
				cursorcolor: '#ff1c24',
				autohidemode: false,
				zindex: 999999999,
				horizrailenabled: true,
				cursorborderradius: 0
			});
			$('input').iCheck({
				checkboxClass: 'icheckbox_minimal',
				radioClass: 'iradio_minimal'
			});
		});
		-->
JSSAFEOUTPUT;
		$EndInlineJs = new InlineJavascript($EndInlineJsTxt);

		$this->addBodyScript($AppMinJs);
		$this->addBodyScript($EndInlineJs);



		$this->addTwigVar('menu_active', 'home');

	}

}