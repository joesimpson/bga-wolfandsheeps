<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * WolfAndSheeps implementation : © joesimpson <1324811+joesimpson@users.noreply.github.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * wolfandsheeps.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );

const COLUMNS_LETTERS ="ABCDEFGH";
const LINE_MAX = 8;

const SHEEP_COLOR = "ffffff";//WHITE
const WOLF_COLOR = "000000";//BLACK

const TOKEN_STATE_RESET = 0;
const TOKEN_STATE_MOVED = 1;

const WINNER_SCORE = 1;

class WolfAndSheeps extends Table
{
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array( 
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );        
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "wolfandsheeps";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( ',', $values );
        self::DbQuery( $sql );
        if ($gameinfos['favorite_colors_support']) self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // setup the initial game situation here
        $this->initTables();

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).
        
        $result['board'] = self::getObjectListFromDB(   self::getSQLSelectTOKEN() );
  
        $result['constants'] = array( 
            "TOKEN_STATE_MOVED" => TOKEN_STATE_MOVED,
        );
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        //Each turn Black player MUST advance a pawn on a line : it means each of its 4 pawns can move a maximum of 7 times (LINE_MAX-1).
        $nb_wolves = LINE_MAX/2;
        $nb_wolf_moves = (LINE_MAX-1);
        $nbMaxBlackMoves = $nb_wolves*$nb_wolf_moves;
        
        //So the (theoritical) maximum number of moves is 2 times this (Black + White): a game can always finish before...
        $nbMaxMoves = 2* $nbMaxBlackMoves;

        //predefined  Global value "6"	"playerturn_nbr" :	Player turn number
        $currentMoves = 0;
        try{
            $currentMoves = $this->getGameStateValue('playerturn_nbr');
        } catch ( Exception $e ) {
            $this->error("Fatal error while calling BGA predefined global 'playerturn_nbr'");
            //$this->dump('err', $e);
        }
        
        $progress = $currentMoves / $nbMaxMoves *100;
        
        return $progress;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */
    
    /**
    Init DataBase
    */
    function initTables(){
        try {
            $players = $this->loadPlayersBasicInfos();
            
            // INIT Board with tokens starting positions : no other tokens will be used in the game
            $sql = "INSERT INTO token (token_key,token_location,token_state) VALUES ";
            $sql_values = array();
                $sql_values[] = "('t_".SHEEP_COLOR."_1','E1',0)";
                $sql_values[] = "('t_".WOLF_COLOR."_1','B8',0)";
                $sql_values[] = "('t_".WOLF_COLOR."_2','D8',0)";
                $sql_values[] = "('t_".WOLF_COLOR."_3','F8',0)";
                $sql_values[] = "('t_".WOLF_COLOR."_4','H8',0)";
            $sql .= implode( $sql_values, ',' );
            self::DbQuery( $sql );
        
        } catch ( Exception $e ) {
            // logging does not actually work in game init :(
            // but if you calling from php chat it will work
            $this->error("Fatal error while creating game");
            $this->dump('err', $e);
        }
        
    }

    //////////// Database Utility functions - BEGIN -----------------------------------
    function getSQLSelectTOKEN() { return "SELECT token_key 'key', token_location location, token_state state, 
                    SUBSTRING(token_key FROM 3 FOR 6) color,
                    SUBSTRING(token_location FROM 1 FOR 1) coord_col,
                    SUBSTRING(token_location FROM 2 FOR 1) coord_row 
                FROM token ";
    }
    function dbGetToken($token_key){
        $sql = self::getSQLSelectTOKEN()." WHERE token_key='$token_key'" ;
        return self::getObjectFromDB( $sql ); 
    }
    function dbGetTokenOnLocation($token_location){
        $sql = self::getSQLSelectTOKEN()." WHERE token_location='$token_location'" ;
        return self::getObjectFromDB( $sql ); 
    }
    
    /**
    return all tokens of this color
    */
    function dbGetPlayerTokens($player_color){
        $sql = "SELECT * FROM (".self::getSQLSelectTOKEN().") subquery WHERE color = '$player_color'" ;
        return self::getObjectListFromDB( $sql ); 
    }
     
    
    function dbUpdateTokenLocation($tokenId,$dest){
        $newState = TOKEN_STATE_MOVED;
        self::DbQuery("UPDATE token SET token_location='$dest', token_state='$newState' WHERE token_key = '$tokenId' ");
    }
    
    function dbUpdateAllTokenState($newState){
        self::DbQuery("UPDATE token SET token_state='$newState'");
    }
    
    // set player score
    function dbSetScore($player_id, $score) {
        self::DbQuery("UPDATE player SET player_score='$score' WHERE player_id='$player_id'");
    }
    //////////// Database Utility functions - END -----------------------------------

    /* Get the list of possible moves
    Example Format for 3 wolves (black) tokens : 
     array( 
        "E1" =>  array("D2","F2"), 
        "B8" =>  array("A7","C7"), 
        "H8" =>  array("G7") , 
        );  
    */
    function getPossibleMoves( $player_id )
    {
        $result = array();
    
        //  LOOP ON player TOKENS
        $color = self::getPlayerColorById( $player_id );
        $tokens = $this->dbGetPlayerTokens($color );
        $this->dump("getPossibleMoves($player_id ) color $color:", $tokens);
        
        foreach( $tokens as $token){
            $origin = $token['location'];
            $token_id = $token['key'];
            $moves = $this->getPossibleMovesForToken($token_id);
            if(count($moves) ==0 ) continue;
            $result[$origin] = $moves;
        }
        return $result;
    }
    
    /**
    Get the list of possible moves for this token.
    
    Example format when the sheep (white) is in E3 : 
        array("D2","F2","D4","F4") ; 
    */
    function getPossibleMovesForToken( $tokenId )
    {
        $result = array();
        
        $token = self::dbGetToken($tokenId); 
        if(! $token) throw new BgaVisibleSystemException( ("Unknown token"));
    
        $token_location = $token['location'];
        $token_color = $token['color'];
        $this->dump('getPossibleMovesForToken() :', $token);
        
        if($token_color == SHEEP_COLOR){//Only the sheep can move from row N to N+1 (backward for wolves)
            $this->addPossiblePositionInArray($result, $token_location, 1,1 );
            $this->addPossiblePositionInArray($result, $token_location, 1,-1 );
        }
        //all can move from row N to N-1 (forward for wolves) :
        $this->addPossiblePositionInArray($result, $token_location, -1,1 );
        $this->addPossiblePositionInArray($result, $token_location, -1,-1 );
            
        $this->dump('getPossibleMovesForToken() : result ', $result);
        
        return $result;
    }
    /**
    Directly add the new position in the pArray corresponding to coordinates ORIGIN+[dRow,dCol]
    */
    function addPossiblePositionInArray( &$pArray, $origin_location, $dRow,$dCol )
    {
        $row = substr($origin_location, -1);
        $columnInt = strpos (COLUMNS_LETTERS, substr($origin_location, 0, -1) );
        
        $nextRow = $row + $dRow;
        //dont add new position IF out of board limits
        if($nextRow <= 0) return;
        if($nextRow > LINE_MAX) return;
        
        $nextColumnInt = $columnInt + $dCol;
        if($nextColumnInt < 0) return;
        if($nextColumnInt > strlen(COLUMNS_LETTERS)-1 ) return;
        $nextCol = substr(COLUMNS_LETTERS, $nextColumnInt, 1);
        
        $nextPos = "$nextCol$nextRow";
        $existingToken = $this->dbGetTokenOnLocation($nextPos);
        //Check position is EMPTY !!!!!
        if($existingToken) return;
        
        $this->trace("addPossiblePositionInArray($origin_location, $dRow,$dCol) : $nextPos ");

        //Add corresponding position in array
        $pArray[] = $nextPos;
        
    }
    
//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in wolfandsheeps.action.php)
    */

    
    /**
      Player choose a token and wants to move it to "dest"
    */
    public function playToken($tokenId, $dest)
    {
        $player_id = self::getActivePlayerId();
        
        $token = $this->dbGetToken($tokenId);
        $this->dbUpdateAllTokenState(TOKEN_STATE_RESET);
        $this->dbUpdateTokenLocation($tokenId,$dest);
        
        $token_origin = $token['location'];
        
        // Notify all players about the token played
        self::notifyAllPlayers( "tokenPlayed", clienttranslate( '${player_name} moves from ${origin} to ${dest}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'color' =>  $token['color'], 
            'tokenId' => $tokenId,
            'origin' => $token_origin, 
            'dest' => $dest,
        ) );
          
          
        //----------------------------------------------------------------------------
        // Then, go to the next state
        $this->gamestate->nextState( 'moveToken' );
    }


    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */
    
    function argPlayerTurn(){
        $player_id = self::getActivePlayerId();
        self::trace("argPlayerTurn() : ".($player_id));        
        return array( 'possibleMoves' => self::getPossibleMoves($player_id),
        );
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stNextPlayer()
    {
        // Active next player
        $player_id = self::activeNextPlayer();

        $sheepToken = $this->dbGetToken("t_".SHEEP_COLOR."_1");
        //CHECK IF SHEEP IS ON the opposite line (row == MAX row) => Sheep wins
        if($sheepToken["coord_row"] == LINE_MAX){
            $winner_color = SHEEP_COLOR;
            
            $players = $this->loadPlayersBasicInfos();
            foreach ($players as $player) {
                if($player['player_color'] == $winner_color ) { 
                    $winner_id = $player['player_id'];
                    $winner_name = $player['player_name'];
                }
            }
            $this->dbSetScore($winner_id, WINNER_SCORE);
            
            self::notifyAllPlayers( "sheepWins", clienttranslate( '${player_name} wins by reaching the other side of the board' ), array(
                'player_id' => $winner_id,
                'player_name' => $winner_name,
                'winner_score' => WINNER_SCORE,
            ) );
            
            // Go to end of the game
            $this->gamestate->nextState( 'endGame' );
            return;
        }

        // TODO JSA check if we can check it 1 turn before to avoid a useless turn waiting ?
        //TODO JSA Check if only sheep's 0 movements get LOOSING
        // Can this player play?
        $possibleMoves = self::getPossibleMoves( $player_id );
        if( count( $possibleMoves ) == 0 )
        {
            
            //add 1 score to other player :
            $players = $this->loadPlayersBasicInfos();
            foreach ($players as $player) {
                if($player['player_id'] != $player_id ) { //ONLY 2 players here so let's find any other
                    $winner_id = $player['player_id'];
                    $winner_name = $player['player_name'];
                }
            }
            $this->dbSetScore($winner_id, WINNER_SCORE);
            
            self::notifyAllPlayers( "winByBlocking", clienttranslate( '${player_name} wins because the other player is blocked' ), array(
                'player_id' => $winner_id,
                'player_name' => $winner_name,
                'winner_score' => WINNER_SCORE,
            ) );
            
            // Go to end of the game
            $this->gamestate->nextState( 'endGame' );
            return;
        }
        
        // This player can play. Give them some extra time
        self::giveExtraTime( $player_id );
        $this->gamestate->nextState( 'nextTurn' );
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
