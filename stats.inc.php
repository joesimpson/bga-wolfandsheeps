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
 * stats.inc.php
 *
 * WolfAndSheeps game statistics description
 *
 */

/*
    In this file, you are describing game statistics, that will be displayed at the end of the
    game.
    
    !! After modifying this file, you must use "Reload  statistics configuration" in BGA Studio backoffice
    ("Control Panel" / "Manage Game" / "Your Game")
    
    There are 2 types of statistics:
    _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
    _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

    Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean
    
    Once you defined your statistics there, you can start using "initStat", "setStat" and "incStat" method
    in your game logic, using statistics names defined below.
    
    !! It is not a good idea to modify this file when a game is running !!

    If your game is already public on BGA, please read the following before any change:
    http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
    Notes:
    * Statistic index is the reference used in setStat/incStat/initStat PHP method
    * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
    * Statistics IDs must be >=10
    * Two table statistics can't share the same ID, two player statistics can't share the same ID
    * A table statistic can have the same ID than a player statistics
    * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
    * Statistic name is the English description of the statistic as shown to players
    
*/

$stats_type = array(

    // Statistics global to table
    "table" => array(

        "turns_number" => array("id"=> 10,
                    "name" => totranslate("Number of turns"),
                    "type" => "int" ),

    ),
    
    // Statistics existing for each player
    "player" => array(

        "player_side" => array("id"=> 10,
                    "name" => totranslate("Round 1 : Played side"),
                    "type" => "int" ),
        "player_result_round1" => array("id"=> 11,
                    "name" => totranslate("Round 1 : Player result"),
                    "type" => "int" ),
                    
        "moves_forward" => array("id"=> 20,
                    "name" => totranslate("Round 1 : Moves forward"),
                    "type" => "int" ),
    
        "moves_backward" => array("id"=> 21,
                    "name" => totranslate("Round 1 : Moves backward"),
                    "type" => "int" ),

        "player_side_round2" => array("id"=> 50,
                    "name" => totranslate("Round 2 : Played side"),
                    "type" => "int" ),
        "player_result_round2" => array("id"=> 51,
                    "name" => totranslate("Round 2 : Player result"),
                    "type" => "int" ),
                    
        "moves_forward_round2" => array("id"=> 60,
                    "name" => totranslate("Round 2 : Moves forward"),
                    "type" => "int" ),
    
        "moves_backward_round2" => array("id"=> 61,
                    "name" => totranslate("Round 2 : Moves backward"),
                    "type" => "int" ),
    ),
    
    "value_labels" => array(
		10 => array( 
			0 => totranslate("White"),
			1 => totranslate("Black"), 
		),
		11 => array(
            //Maybe no need to display 'looser'
			0 => '',
			1 => totranslate("Winner"), 
		),
        50 => array( 
			0 => totranslate("White"),
			1 => totranslate("Black"), 
		),
		51 => array(
            //Maybe no need to display 'looser'
			0 => '',
			1 => totranslate("Winner"), 
		),
	)

);
