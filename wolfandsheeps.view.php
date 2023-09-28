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
 * wolfandsheeps.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in wolfandsheeps_wolfandsheeps.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
require_once( APP_BASE_PATH."view/common/game.view.php" );
  
class view_wolfandsheeps_wolfandsheeps extends game_view
{
    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "wolfandsheeps";
    }
    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );

        /*********** Place your code below:  ************/
        
        $COLUMNS_LETTERS = $this->game->get_COLUMNS_LETTERS();
        $this->tpl['BOARD_SIZE'] = strlen($COLUMNS_LETTERS);
        
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_row_number_left" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_row_number_right" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_col_number" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_col_number_bottom" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_board_cell" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_board_column" );
        
        $columns = $COLUMNS_LETTERS;//"ABCDEFGH";
        //$lines = "12345678";
        $lines = "";
        $tenCharReplacement = "A";
        $LINE_MAX = $this->game->get_LINE_MAX();
        for ($k = 1; $k <= $LINE_MAX; $k++) {
            if($k==10){
                $lines .= $tenCharReplacement;
                continue;
            }
            //TODO DOESN't support >10
            $lines .=$k;
        }
        
        $counter = 0;
        
        $activateRowOffset = false;
        
        if( $this->game->isCurrentPlayerSheep() ){
            //DISPLAY REVERSE BOARD lines/columns in order to have the player pawns always start at bottom of board:
            $lines = strrev($lines); // 87654321
            $columns = strrev($columns);
            $activateRowOffset = true;
        }
        
        foreach (str_split($columns) as $column) {
            $columnInt = strpos ($columns, $column );
            
            $this->page->reset_subblocks( 'wsh_row_number_left' ); 
            $this->page->reset_subblocks( 'wsh_row_number_right' ); 
            $this->page->reset_subblocks( 'wsh_col_number_bottom' ); 
            $this->page->reset_subblocks( 'wsh_board_cell' ); 
            foreach (str_split($lines) as $row) {
                if($row == $tenCharReplacement ) $row = 10;//In order to store 1 char for line 10 in this string (see before)
                $color = ( $counter % 2 ==0 ) ? "dark" : "light";
                $rowOffset = $activateRowOffset ? ( $LINE_MAX-$row +1 ): $row;
                
                if($columnInt == 0){//FIRST COLUMN
                    $this->page->insert_block( "wsh_row_number_left", array( 
                                                        "ROW" => $row,
                                                        "ROW_OFFSET" => $rowOffset,
                                                         ) );
                }     
                else if($columnInt == strlen($columns)-1 ){//LAST COLUMN
                    $this->page->insert_block( "wsh_row_number_right", array( 
                                                        "COLUMN_INT" => $columnInt,
                                                        "ROW" => $row,
                                                        "ROW_OFFSET" => $rowOffset,
                                                         ) );
                }         
                
                if($row == 1){//FIRST ROW
                    
                    $this->page->reset_subblocks( 'wsh_col_number' ); 
                    
                    $this->page->insert_block( "wsh_col_number", array( 
                                                    "COLUMN" => $column,
                                                    "COLUMN_INT" => $columnInt,
                                                         ) );
                } else if($row == $LINE_MAX){//LAST ROW
                    
                    $this->page->insert_block( "wsh_col_number_bottom", array( 
                                                    "COLUMN" => $column,
                                                    "COLUMN_INT" => $columnInt,
                                                    "LINE_MAX" => $LINE_MAX,
                                                         ) );
                }
                
                $this->page->insert_block( "wsh_board_cell", array( 
                                                    "ROW" => $row,
                                                    "ROW_OFFSET" => $rowOffset,
                                                    "COLUMN" => $column,
                                                    "COLUMN_INT" => $columnInt,
                                                    "LIGHT_OR_DARK" => $color,
                                                     ) );
                        
                                         
                $counter++;
            }
             
            $this->page->insert_block( "wsh_board_column", array( 
                                                    "COLUMN" => $column,
                                                     ) );
            $counter++;
         
        }



        /*********** Do not change anything below this line  ************/
  	}
}
