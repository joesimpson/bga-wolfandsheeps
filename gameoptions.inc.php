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
 * gameoptions.inc.php
 *
 * WolfAndSheeps game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in wolfandsheeps.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(

    100 => array(
                'name' => totranslate('Board size'),    
                'values' => array(
                            1 => array( 'name' => totranslate('Chess (8x8)'), 'description' => totranslate('classic chess board with 8 rows and 8 columns : 1 white token VS 4 black tokens')),
                            2 => array( 'name' => totranslate('Checkers (10x10)'), 'description' => totranslate('classic checkers board with 10 rows and 10 columns : 1 white token VS 5 black tokens')),
                        ),
                'default' => 1
            ),
);


$game_preferences = array(
    100 => array(
            'name' => totranslate('Board colors'),
            'needReload' => true, // after user changes this preference game interface would auto-reload
            'values' => array(
                    1 => array( 'name' => totranslate( 'Tokens on dark cells' ), 'cssPref' => 'wsh_tokens_on_dark' ),
                    2 => array( 'name' => totranslate( 'Tokens on light cells' ), 'cssPref' => 'wsh_tokens_on_light' )
            ),
            'default' => 1
    ),
    101 => array(
            'name' => totranslate('Interface theme'),
            'needReload' => true, // after user changes this preference game interface would auto-reload
            'values' => array(
                    1 => array( 'name' => totranslate( 'Standard' ), 'cssPref' => 'wsh_theme_standard' ),
                    2 => array( 'name' => totranslate( 'Darker' ), 'cssPref' => 'wsh_theme_dark' )
            ),
            'default' => 1
    ),
    102 => array(
            'name' => totranslate('Auto confirm turn'),
            'needReload' => true, // after user changes this preference game interface would auto-reload
            'values' => array(
                    1 => array( 'name' => totranslate( 'Enabled' ), 'cssPref' => 'wsh_pref_auto_confirm_yes' ),
                    2 => array( 'name' => totranslate( 'Disabled' ), 'cssPref' => 'wsh_pref_auto_confirm_no' )
            ),
            'default' => 1
    ),
);
