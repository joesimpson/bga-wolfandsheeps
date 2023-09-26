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


        /*
        
        // Examples: set the value of some element defined in your tpl file like this: {MY_VARIABLE_ELEMENT}

        // Display a specific number / string
        $this->tpl['MY_VARIABLE_ELEMENT'] = $number_to_display;

        // Display a string to be translated in all languages: 
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::_("A string to be translated");

        // Display some HTML content of your own:
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::raw( $some_html_code );
        
        */
        
        /*
        
        // Example: display a specific HTML block for each player in this game.
        // (note: the block is defined in your .tpl file like this:
        //      <!-- BEGIN myblock --> 
        //          ... my HTML code ...
        //      <!-- END myblock --> 
        

        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "myblock" );
        foreach( $players as $player )
        {
            $this->page->insert_block( "myblock", array( 
                                                    "PLAYER_NAME" => $player['player_name'],
                                                    "SOME_VARIABLE" => $some_value
                                                    ...
                                                     ) );
        }
        
        */
        
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_row_number_left" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_row_number_right" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_col_number" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_col_number_bottom" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_board_cell" );
        $this->page->begin_block( "wolfandsheeps_wolfandsheeps", "wsh_board_column" );
        
        $columns = \COLUMNS_LETTERS;  //"ABCDEFGH";
        //TODO JSA how to reuse game LINE_MAX
        $lines = "12345678";
        $counter = 0;
        foreach (str_split($columns) as $column) {
            $columnInt = strpos ($columns, $column );
            
            $this->page->reset_subblocks( 'wsh_row_number_left' ); 
            $this->page->reset_subblocks( 'wsh_row_number_right' ); 
            $this->page->reset_subblocks( 'wsh_col_number_bottom' ); 
            $this->page->reset_subblocks( 'wsh_board_cell' ); 
            foreach (str_split($lines) as $row) {
                $color = ( $counter % 2 ==0 ) ? "dark" : "light";
                
                if($columnInt == 0){//FIRST COLUMN
                    $this->page->insert_block( "wsh_row_number_left", array( 
                                                        "ROW" => $row,
                                                         ) );
                }     
                else if($columnInt == strlen($columns)-1 ){//LAST COLUMN
                    $this->page->insert_block( "wsh_row_number_right", array( 
                                                        "COLUMN_INT" => $columnInt,
                                                        "ROW" => $row,
                                                         ) );
                }         
                
                if($row == 1){//FIRST ROW
                    
                    $this->page->reset_subblocks( 'wsh_col_number' ); 
                    
                    $this->page->insert_block( "wsh_col_number", array( 
                                                    "COLUMN" => $column,
                                                    "COLUMN_INT" => $columnInt,
                                                         ) );
                } else if($row == \LINE_MAX){//LAST ROW
                    
                    $this->page->insert_block( "wsh_col_number_bottom", array( 
                                                    "COLUMN" => $column,
                                                    "COLUMN_INT" => $columnInt,
                                                    "LINE_MAX" => \LINE_MAX,
                                                         ) );
                }
                
                $this->page->insert_block( "wsh_board_cell", array( 
                                                    "ROW" => $row,
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
