<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.event.plugin');

class plgContentPlg_fen_viewer extends JPlugin {

	private $styleAndScript = false;

	function plgContentPlg_pgn_viewer( &$subject ) {
		parent::__construct( $subject );
	}

	function onContentPrepare($context, $row, $params, $page = 0) {
		$siteurl = JURI::base();
		$regex = "#{fen}(.*?){end-fen}#s";
		$row->text = preg_replace_callback($regex,array($this,"execphp"), $row->text);
		return true;
	}

	function execphp($matches) {
		$siteurl = JURI::base();
		$doc = JFactory::getDocument();
		if(!$this->styleAndScript) {
			$doc->addStyleSheet($siteurl."plugins/content/plg_fen_viewer/css/chess.css");
			$doc->addScript($siteurl."plugins/content/plg_fen_viewer/js/ChessFen.js");
			$this->styleAndScript=true;
		}

		$plugin = JPluginHelper::getPlugin('content', 'plg_fen_viewer');
		$pluginParams = new JRegistry($plugin->params);
		$style 	  	  = $pluginParams->get('style', 'merida');
		if($style!="merida" && $style!="alpha" && $style!="cases" && $style!="leipzig" && $style!="motif" && $style!="smart") {
			$style="merida";
		}

		$groesse	= $pluginParams->get('groesse', 30);
		if(!is_numeric($groesse)) {
			$groesse = 30;
		}
		
		$now = time()+mt_rand();
		$url = $siteurl."plugins/content/plg_fen_viewer/images/";
		$script = "<script>var chessObj = new DHTMLGoodies.ChessFen({ pieceType:'".$style."',squareSize:'".$groesse."' }); chessObj.loadFen('".$matches[1]."','".$now."','".$url."');</script>";
		$script .= '<noscript>You have JavaScript disabled and you are not seeing a graphical interactive chessboard!</noscript>';
		
		// Ausgabe
		$output = '<div id="'.$now.'"></div>'.$script;
		return $output;
	}
}
?>
