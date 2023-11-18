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

//Constants are replaced by global var + static function
//const COLUMNS_LETTERS ="ABCDEFGH";
//const LINE_MAX = 8;

const SHEEP_COLOR = "ffffff";//WHITE
const WOLF_COLOR = "000000";//BLACK

const TOKEN_STATE_RESET = 0;
const TOKEN_STATE_MOVED = 1;

const WINNER_SCORE = 1;

const VICTORY_TYPE_SHEEP_REACH = 1;
const VICTORY_TYPE_PLAYER_BLOCKED = 2;
const VICTORY_TYPE_SHEEP_FREE = 3;

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
            "wsh_line_max" => 10,
            "wsh_victory_type" => 11,
            "wsh_round_max" => 20,
            "wsh_round_number" => 21,
            
            "variant_BoardSize" => 100,
            "variant_RoundNumber" => 101,
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
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, player_no) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            
            if( $color == WOLF_COLOR ){
                $blackplayer_id = $player_id;
                $player_order = 2;
            }
            else {
                $whiteplayer_id = $player_id;
                $player_order = 1; //WHITE PLAYER STARTS !
            }
            
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."','" .	$player_order . "')";
        }
        $sql .= implode( ',', $values );
        self::DbQuery( $sql );
        if ($gameinfos['favorite_colors_support']) self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        
        $variant_BoardSize = self::getGameStateValue( 'variant_BoardSize' );
        switch ($variant_BoardSize){
            case 2:
                $boardsize = 10;
                break;
            case 1:
            default:
                $boardsize = 8;
                break;
        }
        $variant_RoundNumber = self::getGameStateValue( 'variant_RoundNumber' );
        switch ($variant_RoundNumber){
            case 2:
                $roundMax = 2;
                break;
            case 1:
            default:
                $roundMax = 1;
                break;
        }
        
        self::setGameStateInitialValue( 'wsh_line_max', $boardsize );
        self::setGameStateInitialValue( 'wsh_round_max', $roundMax );
        self::setGameStateInitialValue( 'wsh_round_number', 0 );
        self::setGameStateInitialValue( 'wsh_victory_type', 0 );
                
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        self::initStat( 'table', 'turns_number', 0 );    // Init a table statistics
        self::initStat( 'player', 'player_side', 0 );  // Init a player statistics (for all players)
        self::initStat( 'player', 'moves_forward', 0 ); 
        self::initStat( 'player', 'moves_backward', 0 ,$whiteplayer_id); 

        self::setStat(0,'player_side',$whiteplayer_id);
        self::setStat(1,'player_side',$blackplayer_id);
        
        if($roundMax >1){
            //Prepare round 2 with reversed roles
            self::initStat( 'player', 'player_side_round2', 0 );
            self::initStat( 'player', 'moves_forward_round2', 0 ); 
            self::initStat( 'player', 'moves_backward_round2', 0 ,$blackplayer_id); 
            self::setStat(0,'player_side_round2',$blackplayer_id);
            self::setStat(1,'player_side_round2',$whiteplayer_id);
        }

        // setup the initial game situation here

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
        $sql = "SELECT player_id id, player_score score, player_color color FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // Gather all information about current game situation (visible by player $current_player_id).
        
        //May be empty in first few seconds => added in argPlayerTurn because board is not init before new round
        $result['board'] = self::getObjectListFromDB(   self::getSQLSelectTOKEN() );
        $result['round'] = $this->getCurrentRound();
  
        $result['constants'] = array( 
            "TOKEN_STATE_MOVED" => TOKEN_STATE_MOVED,
            "SHEEP_COLOR" => SHEEP_COLOR,
            "WOLF_COLOR" => WOLF_COLOR,
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
        $round = $this->getCurrentRound();
        $maxRound = $this->getMaxRound();
        
        $previousRoundProgress = 0;
        //Previous round progress : 0 on round 1/1, 0 on round 1/2,  1/2 on round 2
        if($maxRound >0) $previousRoundProgress = ($round -1) / $maxRound *100; 
        
        //Each turn Black player MUST advance a pawn on a line : it means each of its 4 pawns can move a maximum of 7 times (LINE_MAX-1).
        $LINE_MAX = self::get_LINE_MAX();
        $nb_wolves = $LINE_MAX/2;
        $nb_wolf_moves = ($LINE_MAX-1);
        $nbMaxBlackMoves = $nb_wolves*$nb_wolf_moves;
        
        //So the (theoritical) maximum number of moves is 2 times this (Black + White): a game can always finish before...
        $nbMaxMoves = 2* $nbMaxBlackMoves;

        $currentMoves = 0;    
        $players = $this->loadPlayersBasicInfos();
        foreach ($players as $player) {
            $currentMoves += $this->getCurrentRoundMoves($player['player_id']);
        }        
        
        $progressRound = $currentMoves / $nbMaxMoves *100;
        if($previousRoundProgress >0 ){
            $progress = $previousRoundProgress + $progressRound / (100 / (100 - $previousRoundProgress));
        }
        else {
            $progress = $progressRound;
        }
        
        //If we want players to be able to concede at any time (like Chess, xiangqi and some others ), it must be >=50
        $minProgress = 50;
        $progress = $minProgress + $progress / (100 / (100 - $minProgress) );
        
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
    function initBoard($boardsize,$round){
        try {
            //Don't delete tokens, we will keep them with the round indicator
            //self::DbQuery("DELETE FROM token" );
            
            // INIT Board with tokens starting positions : no other tokens will be used in the game
            $sql = "INSERT INTO token (token_key,token_location,token_state) VALUES ";
            $sql_values = array();
            if($boardsize == 10){
                $sql_values[] = "('".$round."_".SHEEP_COLOR."_1','E1',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_1','B10',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_2','D10',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_3','F10',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_4','H10',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_5','J10',0)";
            } else { // Default size 8
                $sql_values[] = "('".$round."_".SHEEP_COLOR."_1','E1',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_1','B8',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_2','D8',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_3','F8',0)";
                $sql_values[] = "('".$round."_".WOLF_COLOR."_4','H8',0)";
            }
            $sql .= implode( $sql_values, ',' );
            self::DbQuery( $sql );
        
        } catch ( Exception $e ) {
            $this->error("Fatal error while initializing board tables");
            $this->dump('err', $e);
        }
        
    }
    function getCurrentRound(){
        try{
            $currentRound = $this->getGameStateValue('wsh_round_number');
        } catch ( Exception $e ) {
            $this->trace("Error while reading new global 'wsh_round_number'");
            $currentRound = 1;
        }
        return $currentRound;
    }
    function getMaxRound(){
        try{
            $max = $this->getGameStateValue('wsh_round_max');
        } catch ( Exception $e ) {
            $this->trace("Error while reading new global 'wsh_round_max'");
            $max = 1;
        }
        return $max;
    }
    
    function getCurrentRoundMoves($player_id){
        try{
            $round = $this->getCurrentRound();
            $statMoveBack = ($round == 2) ? 'moves_backward_round2':'moves_backward';
            $statMoveForward = ($round == 2) ? 'moves_forward_round2':'moves_forward';
            $nbMoves = self::getStat($statMoveForward,$player_id) + self::getStat($statMoveBack,$player_id);
        } catch ( Exception $e ) {
            $nbMoves = 0;
        }
        return $nbMoves;
    }
    /**
    Almost constant, but depends on game start variant 
    */
    function get_COLUMNS_LETTERS(){
        $all_letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $line_max = self::getGameStateValue( 'wsh_line_max' );
        $columns = substr($all_letters,0, $line_max) ;
        return $columns;
    }
    /**
    Almost constant, but depends on game start variant 
    Return the maximum row in the board (Sorry for the confusion between row and line in french...)
    */
    function get_LINE_MAX(){
        return self::getGameStateValue( 'wsh_line_max' );;
    }

    //////////// Database Utility functions - BEGIN -----------------------------------
    function getSQLSelectTOKEN() { return "SELECT token_key 'key', token_location location, token_state state, 
                    SUBSTRING(token_key FROM 1 FOR 1) round,
                    SUBSTRING(token_key FROM 3 FOR 6) color,
                    SUBSTRING(token_location FROM 1 FOR 1) coord_col,
                    SUBSTRING(token_location FROM 2 FOR 2) coord_row 
                FROM token ";
    }
    /**
    Return token identified
    */
    function dbGetToken($token_key){
        $sql = self::getSQLSelectTOKEN()." WHERE token_key='$token_key'" ;
        return self::getObjectFromDB( $sql ); 
    }
    /**
    Return token on the specied location if it exists
    */
    function dbGetTokenOnLocation($token_location,$round){
        $sql = "SELECT * FROM (".self::getSQLSelectTOKEN().") subquery WHERE location='$token_location' AND round = '$round' " ;
        return self::getObjectFromDB( $sql ); 
    }
    
    /**
    return all tokens of this color
    */
    function dbGetPlayerTokens($player_color,$round){
        $sql = "SELECT * FROM (".self::getSQLSelectTOKEN().") subquery WHERE color = '$player_color' AND round = '$round' " ;
        return self::getObjectListFromDB( $sql ); 
    }
     
    /**
    Move the token $tokenId to the new location $dest
    */
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
    // increase/decrease player score
    function dbIncScore($player_id, $deltaScore) {
        self::DbQuery("UPDATE player SET player_score= player_score + $deltaScore WHERE player_id='$player_id'");
    }
    // set player aux score
    function dbIncreasePlayerScoreAux($player_id,$deltaScoreAux){
        self::DbQuery( "UPDATE player SET player_score_aux= player_score_aux +$deltaScoreAux WHERE player_id='$player_id'" );
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
        $tokens = $this->dbGetPlayerTokens($color, $this->getCurrentRound() );
       
        //$this->dump("getPossibleMoves($player_id ) color $color:", $tokens); // NOI18N 
        
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
        if(! $token) throw new BgaVisibleSystemException( ("Unknown token")); // NOI18N 
    
        $token_location = $token['location'];
        $token_color = $token['color'];
        
        //$this->dump('getPossibleMovesForToken() :', $token); // NOI18N 
        
        if($token_color == SHEEP_COLOR){//Only the sheep can move from row N to N+1 (backward for wolves)
            $result = array_merge($result, $this->getPossibleForwardMovesForSheep($token_location) );
        }
        //all can move from row N to N-1 (forward for wolves) :
        $this->addPossiblePositionInArray($result, $token_location, -1,1 );
        $this->addPossiblePositionInArray($result, $token_location, -1,-1 );
        
        //$this->dump('getPossibleMovesForToken() : result ', $result); // NOI18N 
        
        return $result;
    }
    
    function getPossibleForwardMovesForSheep( $token_location )
    {
        $this->trace("getPossibleForwardMovesForSheep($token_location)..."); // NOI18N 
        $result = array();
        
        $this->addPossiblePositionInArray($result, $token_location, 1,1 );
        $this->addPossiblePositionInArray($result, $token_location, 1,-1 );
        
        return $result;
    }
    /**
    Return the next position corresponding to coordinates ORIGIN+[dRow,dCol]
    */
    function getNextPosition( $origin_location, $dRow,$dCol )
    {
        $row = $this->parseRowInLocationString($origin_location);
        $columnsLetters = self::get_COLUMNS_LETTERS();
        $columnInt = $this->parseColInLocationString($origin_location);
        
        $nextRow = $row + $dRow;
        //dont add new position IF out of board limits
        if($nextRow <= 0) return;
        if($nextRow > self::get_LINE_MAX()) return;
        
        $nextColumnInt = $columnInt + $dCol;
        if($nextColumnInt < 0) return;
        if($nextColumnInt > strlen($columnsLetters)-1 ) return;
        $nextCol = substr($columnsLetters, $nextColumnInt, 1);
        
        $nextPos = "$nextCol$nextRow";
        
        $this->trace("getNextPosition($origin_location, $dRow,$dCol) : $nextPos ");
        return $nextPos;
    }
    /**
    Directly add the new position in the pArray corresponding to coordinates ORIGIN+[dRow,dCol]
    */
    function addPossiblePositionInArray( &$pArray, $origin_location, $dRow,$dCol )
    {
        $nextPos = $this->getNextPosition($origin_location, $dRow,$dCol);
        if(!isset($nextPos)) return;
        $round = $this->getCurrentRound();
        $existingToken = $this->dbGetTokenOnLocation($nextPos, $round);
        //Check position is EMPTY !!!!!
        if($existingToken) return;
        
        $this->trace("addPossiblePositionInArray($origin_location, $dRow,$dCol) : $nextPos ");

        //Add corresponding position in array
        $pArray[] = $nextPos;
        
    }
    
    /**
    Return true if moving FROM $origin TO $destination BY this player represents a backward move
    */
    function isBackwardMove($origin, $destination, $player_color) {
        $originRow = $this->parseRowInLocationString($origin);
        $destinationRow = $this->parseRowInLocationString($destination);
        
        if($destinationRow > $originRow && $player_color == WOLF_COLOR) return true;
        if($destinationRow < $originRow && $player_color == SHEEP_COLOR) return true;
        
        return false;
    }
    
    /**
    return true if there is no more way for wolves to block the sheep (not exclusive list of checks)
        false otherwise
    */
    function isSheepImpossibleToBlock($sheepToken = null){
        $this->trace("isSheepImpossibleToBlock()...");
        
        $isFree = false;
        
        $token = $sheepToken;
        $tokenId = $token['key'];
        $token_location = $token['location'];
        
        $sheepFrontLineFreeSpaces = $this->getPossibleForwardMovesForSheep($token_location);
        $previousWolfId = array();
        
        foreach($sheepFrontLineFreeSpaces as $positionToBlock){
            $wolfIds = $this->isPossibleToReachPositionByWolves($positionToBlock);
            if(count($wolfIds) == 0){
                $isFree = true;
                break;
            }
            if(count($wolfIds) == 1){
                $newWolfs = array_diff( $wolfIds,$previousWolfId);
                $oldWolfs = array_diff( $previousWolfId,$wolfIds);
                if(count( $newWolfs) == 0 && count( $oldWolfs) == 0){
                    //IF the same wolf is needed for each space, the sheep can go through
                    $isFree = true;
                    break;
                }
            }
            //Else : there is more than 1 wolf to go there, so check next pos
            
            $previousWolfId = $wolfIds;
        }
        
        $this->trace("isSheepImpossibleToBlock() => return ".($isFree ? 'true' : 'false'));
        return $isFree;
    }
    
    /**
    return 
        array of wolfs' ids who can reach the target,
        empty array if none can
    */
    function isPossibleToReachPositionByWolves($targetLocationString){
        $isPossible = array();
        
        $wolves = $this->dbGetPlayerTokens(WOLF_COLOR, $this->getCurrentRound() );
        foreach($wolves as $wolf){
            if($this->isPossibleToReachPositionByWolf($targetLocationString,$wolf)){
                $isPossible[] = $wolf['key'];
            }
        }
        
        //$this->dump("isPossibleToReachPositionByWolves($targetLocationString)...=> return ",$isPossible);
        return $isPossible;
    }
    
    /**
    return 
        false if wolf token cannot reach the target during game
        true otherwise
    */
    function isPossibleToReachPositionByWolf($targetLocationString,$wolf){
        $isPossible = true;
        
        //$this->dump("isPossibleToReachPositionByWolf($targetLocationString) ...  ", $wolf);
        
        $wolf_id = $wolf["key"];
        $coord_row = intval($wolf["coord_row"]);
        $coord_col = 1 + $this->parseColInLocationString($wolf["location"]);
        $targetRow = $this->parseRowInLocationString($targetLocationString);
        $targetCol = 1 + $this->parseColInLocationString($targetLocationString);
        
        if($coord_row <=$targetRow ){
            //IF row is behind wolf (row included), target cannot be reached
            $isPossible = false;
        }
        else {
            //Analysis: if target is not in angle between left diagonal and right diagonal from wolf position, wolf cannot reach it. (Compare this angle with the vehicle blind spot angle VS forward visibility angle).
            //Example : for wolf in B8, left diag is B8-A7 (math defined as row = col +6) And right diag is B8-H2 (math defined as row = 8 - col +1 ) 
            //Watch image "/misc/AnalyzeBoardDiagonals.PNG" for examples
            //If {ROW,COL} under left diag [row = col + ($coord_row - $coord_col) ]
            if($targetCol <= $coord_col && ( $targetRow >  ($targetCol + ($coord_row - $coord_col)) )){
                $isPossible = false;
                $this->trace("isPossibleToReachPositionByWolf($targetLocationString, $wolf_id) => KO because [$targetRow,$targetCol] under left diagonal [row = col + ($coord_row - $coord_col) ]");
            }
            //IF {ROW,COL} under right diag [row = (coord_row + coord_col) - col  ]
            else if($targetCol >= $coord_col && ( $targetRow > ( $coord_row + $coord_col - $targetCol)) ){
                $isPossible = false;
                $this->trace("isPossibleToReachPositionByWolf($targetLocationString, $wolf_id) => KO because [$targetRow,$targetCol] under right diagonal [row = ($coord_row + $coord_col) - $targetCol ]");
            }
        }
        
        $this->trace("isPossibleToReachPositionByWolf($targetLocationString,$wolf_id) => return ".($isPossible ? 'true' : 'false'));
        return $isPossible;
    }
    
    /*
    return column between 0 and max
    */
    function parseColInLocationString ($location) {
        $columnsLetters = self::get_COLUMNS_LETTERS();
        $columnInt = strpos ($columnsLetters, substr($location, 0, 1) );
        return $columnInt;
    }
     /*
    return row between 1 and max
    */
    function parseRowInLocationString ($location) {
        return substr($location,1);
    }
    
    
    /**
    Return true if current player is the "Sheep" (== the white player)
    */
    function isCurrentPlayerSheep () {
        if(self::isSpectator() ) return false;
        return self::getCurrentPlayerColor() == SHEEP_COLOR;
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
        $this->trace("playToken($tokenId, $dest)");
        self::checkAction( 'playToken' ); 
        
        $player_id = self::getActivePlayerId();
        $token = $this->dbGetToken($tokenId);
        
        $token_origin = $token['location'];
        $token_color = $token['color'];
        
        //ANTICHEAT CHECKS :
        $possibleMoves = $this->getPossibleMoves($player_id);
        if (!array_key_exists($token_origin, $possibleMoves))
        {
            throw new BgaVisibleSystemException( ("You cannot move this token")); // NOI18N 
        }
        $possibleDest = $possibleMoves[$token_origin];
        //$this->dump("playToken($tokenId, $dest)...  possibleDest= ",$possibleDest);
        if (array_search($dest, $possibleDest ) === FALSE)
        {
            throw new BgaVisibleSystemException( ("You cannot move that token to this place")); // NOI18N 
        }
        
        //REAL ACTION :
        $this->dbUpdateAllTokenState(TOKEN_STATE_RESET);
        $this->dbUpdateTokenLocation($tokenId,$dest);
        
        $round = $this->getCurrentRound();
        if( $this->isBackwardMove($token_origin, $dest,$token_color)){
            $statMove = ($round == 2) ? 'moves_backward_round2':'moves_backward';
        }
        else {
            $statMove = ($round == 2) ? 'moves_forward_round2':'moves_forward';
        }
        self::incStat(1,$statMove,$player_id);
        
        // Notify all players about the token played
        self::notifyAllPlayers( "tokenPlayed", clienttranslate( '${player_name} moves from ${origin} to ${dest}' ), array(
            'preserve' => [ 'color' ],
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'color' =>  $token_color, 
            'tokenId' => $tokenId,
            'origin' => $token_origin, 
            'dest' => $dest,
            'round' => $round,
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
    
    function argPlayerTurn(){
        $player_id = self::getActivePlayerId();
        self::trace("argPlayerTurn() : ".($player_id));        
        return array( 'possibleMoves' => self::getPossibleMoves($player_id),
            'board' => self::getObjectListFromDB(   self::getSQLSelectTOKEN() ),
            'round' => $this->getCurrentRound(),
        );
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stNewRound()
    { 
        self::trace("stNewRound()");
        
        $roundMax = $this->getMaxRound();
        
        //INCREASE ROund number
        $round = $this->getCurrentRound();
        $round++;
        self::setGameStateValue( 'wsh_round_number', $round );
        
        if($roundMax > 1){ 
            //IF several rounds
            
            if($round == 2){
                //switch colors + notify new colors
                $this->DbQuery("UPDATE player SET `player_color` = '".SHEEP_COLOR."' WHERE player_no = 2");
                $this->DbQuery("UPDATE player SET `player_color` = '".WOLF_COLOR."' WHERE player_no = 1");
                
                $this->reloadPlayersBasicInfos();
            }
            
            $players = $this->loadPlayersBasicInfos();
            foreach ($players as $player) {
                if($player['player_color'] == SHEEP_COLOR ) { 
                    $sheep_player_id = $player['player_id'];
                }
                else if($player['player_color'] == WOLF_COLOR ) { 
                    $wolf_player_id = $player['player_id'];
                }
            }
            
            $this->gamestate->changeActivePlayer( $sheep_player_id );
            
            //NOTIF ALL about new round
            self::notifyAllPlayers( "newRound", clienttranslate( 'The game starts round number ${nb}' ), array( 
                'nb' => $round,
                'sheep_player_id' => $sheep_player_id,
                'wolf_player_id' => $wolf_player_id,
            ) );
            
        }
        
        $this->initBoard(self::get_LINE_MAX(),$round);
        
        self::notifyAllPlayers( "newBoard", '', array( 
                'round' => $round,
                'board' => self::getObjectListFromDB(   self::getSQLSelectTOKEN() ),
            ) );
        
        $this->gamestate->nextState( 'next' );
    }
    
    function stNextPlayer()
    {
        // Active next player
        $player_id = self::activeNextPlayer();

        self::incStat(1,'turns_number');
        
        $round = $this->getCurrentRound();
        
        $sheepToken = $this->dbGetToken($round."_".SHEEP_COLOR."_1");
            
        //CHECK IF SHEEP IS ON the opposite line (row == MAX row) => Sheep wins
        if($sheepToken["coord_row"] == self::get_LINE_MAX()){
            $winner_color = SHEEP_COLOR;
            
            $players = $this->loadPlayersBasicInfos();
            foreach ($players as $player) {
                if($player['player_color'] == $winner_color ) { 
                    $winner_id = $player['player_id'];
                    $winner_name = $player['player_name'];
                }
            }
            //TODO JSA FACTORIZE
            $this->dbIncScore($winner_id, WINNER_SCORE);
            //Tie breaker score :
            $nbMoves = $this->getCurrentRoundMoves($winner_id);
            self::trace("stNextPlayer() nbMoves = $nbMoves");
            //We will consider the lowest number of moves as tie, so count negative :
            $this->dbIncreasePlayerScoreAux($winner_id, 0 - $nbMoves);
            
            self::notifyAllPlayers( "sheepWins", clienttranslate( '${player_name} wins by reaching the other side of the board' ), array(
                'preserve' => [ 'color' ],
                'player_id' => $winner_id,
                'player_name' => $winner_name,
                'winner_score' => WINNER_SCORE,
                'color' =>  $winner_color, 
            ) );
            
            self::setGameStateValue( 'wsh_victory_type', VICTORY_TYPE_SHEEP_REACH );
            
            // Go to end of the game
            $this->gamestate->nextState( 'endRound' );
            return;
        }

        // TODO ? check if we can check it 1 turn before to avoid a useless turn waiting ? Be careful with this idea because computing future possible moves now doesn't consider the next player turn action !
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
                    $winner_color = $player['player_color'];
                }
            }
            $this->dbIncScore($winner_id, WINNER_SCORE);
            //Tie breaker score :
            $nbMoves = $this->getCurrentRoundMoves($winner_id);
            //We will consider the lowest number of moves as tie, so count negative :
            $this->dbIncreasePlayerScoreAux($winner_id, 0 - $nbMoves);
            
            self::notifyAllPlayers( "winByBlocking", clienttranslate( '${player_name} wins because the other player is blocked' ), array(
                'preserve' => [ 'color' ],
                'player_id' => $winner_id,
                'player_name' => $winner_name,
                'winner_score' => WINNER_SCORE,
                'color' =>  $winner_color, 
            ) );
            
            self::setGameStateValue( 'wsh_victory_type', VICTORY_TYPE_PLAYER_BLOCKED );
            
            // Go to end of the game
            $this->gamestate->nextState( 'endRound' );
            return;
        }
        
        if( $this->isSheepImpossibleToBlock($sheepToken)){
            $winner_color = SHEEP_COLOR;
            
            $players = $this->loadPlayersBasicInfos();
            foreach ($players as $player) {
                if($player['player_color'] == $winner_color ) { 
                    $winner_id = $player['player_id'];
                    $winner_name = $player['player_name'];
                }
            }
            $this->dbIncScore($winner_id, WINNER_SCORE);
            //Tie breaker score :
            $nbMoves = $this->getCurrentRoundMoves($winner_id);
            //We will consider the lowest number of moves as tie, so count negative :
            $this->dbIncreasePlayerScoreAux($winner_id, 0 - $nbMoves);
            
            self::notifyAllPlayers( "sheepWinsUnstoppable", clienttranslate( '${player_name} wins because the other player cannot block him anymore' ), array(
                'preserve' => [ 'color' ],
                'player_id' => $winner_id,
                'player_name' => $winner_name,
                'winner_score' => WINNER_SCORE,
                'color' =>  $winner_color, 
            ) );
            
            self::setGameStateValue( 'wsh_victory_type', VICTORY_TYPE_SHEEP_FREE );
            
            // Go to end of the game
            $this->gamestate->nextState( 'endRound' );
            return;
        }
        
        // This player can play. Give them some extra time
        self::giveExtraTime( $player_id );
        $this->gamestate->nextState( 'nextTurn' );
    }

    function stEndRound()
    {  
        self::trace("stEndRound()");
        
        $roundMax = $this->getMaxRound();
        $round = $this->getCurrentRound();
        
        self::notifyAllPlayers( "endRound", '', array( 
                'round' => $round,
            ) );
            
        //CHECK round_number and end if >=MAX
        if($round >= $roundMax ){
            $this->gamestate->nextState( 'endGame' );
            return;
        }
        
        $this->gamestate->nextState( 'newRound' );
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
        
        if( $from_version <= 2311131948 ){
            
            //ADD round number as 1 for old games :
            $result = self::getUniqueValueFromDB("SELECT global_value FROM `global` where global_id='20'");
            if (empty($result)) {
                self::applyDbUpgradeToAllDB("INSERT INTO DBPREFIX_global (`global_id`, `global_value`) VALUES ('20', '1'); ");
            }
            $result = self::getUniqueValueFromDB("SELECT global_value FROM `global` where global_id='21'");
            if (empty($result)){
                self::applyDbUpgradeToAllDB("INSERT INTO DBPREFIX_global (`global_id`, `global_value`) VALUES ('21', '1'); ");
            } 
            
            //replace old 't' with round '1' in token key
            self::applyDbUpgradeToAllDB("UPDATE DBPREFIX_token SET `token_key` = '1_ffffff_1' WHERE `token_key` = 't_ffffff_1'");
            self::applyDbUpgradeToAllDB("UPDATE DBPREFIX_token SET `token_key` = '1_000000_1' WHERE `token_key` = 't_000000_1'");
            self::applyDbUpgradeToAllDB("UPDATE DBPREFIX_token SET `token_key` = '1_000000_2' WHERE `token_key` = 't_000000_2'");
            self::applyDbUpgradeToAllDB("UPDATE DBPREFIX_token SET `token_key` = '1_000000_3' WHERE `token_key` = 't_000000_3'");
            self::applyDbUpgradeToAllDB("UPDATE DBPREFIX_token SET `token_key` = '1_000000_4' WHERE `token_key` = 't_000000_4'");
            self::applyDbUpgradeToAllDB("UPDATE DBPREFIX_token SET `token_key` = '1_000000_5' WHERE `token_key` = 't_000000_5'");
        }

    }    
}
