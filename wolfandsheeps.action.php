<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * WolfAndSheeps implementation : © joesimpson <1324811+joesimpson@users.noreply.github.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * wolfandsheeps.action.php
 *
 * WolfAndSheeps main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/wolfandsheeps/wolfandsheeps/myAction.html", ...)
 *
 */
  
  
  class action_wolfandsheeps extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "wolfandsheeps_wolfandsheeps";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
    
    /* Check Helper, not a real action */
    private function checkVersion()
    {
        $clientVersion = (int) self::getArg('version', AT_int, false);
        $this->game->checkVersion($clientVersion);
    }
    
  	// defines your action entry points there
 
    public function playToken()
    {
        self::setAjaxMode();
        self::checkVersion();      

        $tokenId = self::getArg( "id", AT_alphanum, true );
        $dest = self::getArg( "dest", AT_alphanum, true );
        $this->game->playToken( $tokenId, $dest );

        self::ajaxResponse( );
    }

  }
  

