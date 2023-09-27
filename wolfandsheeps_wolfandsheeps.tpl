{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- WolfAndSheeps implementation : © joesimpson <1324811+joesimpson@users.noreply.github.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-->

<div id="wsh_game_zone">

    <!-- Inspired by BGA Chess Board implementation by ecolin -->
    
    <div id="wsh_board" class="wsh_board_size_{BOARD_SIZE}">
        <!-- BEGIN wsh_board_column -->
    
            <!-- BEGIN wsh_col_number -->
            <div class="wsh_coordinate wsh_col_number" style="top:calc(-0.5*var(--wsh_cell_size)); left:calc( ( 0.43 + {COLUMN_INT})*var(--wsh_cell_size));">{COLUMN}</div>
            <!-- END wsh_col_number -->
            
            <!-- BEGIN wsh_col_number_bottom -->
            <div class="wsh_coordinate wsh_col_number" style="top:calc( ( 0.20 + {LINE_MAX})*var(--wsh_cell_size)); left:calc( ( 0.43 + {COLUMN_INT})*var(--wsh_cell_size));">{COLUMN}</div>
            <!-- END wsh_col_number_bottom -->
                
            <!-- BEGIN wsh_row_number_left -->
            <div class="wsh_coordinate wsh_row_number" style="left:calc(-0.5*var(--wsh_cell_size)); top:calc( ( {ROW_OFFSET} - 0.65)*var(--wsh_cell_size));">{ROW}</div>
            <!-- END wsh_row_number_left -->
            
            <!-- BEGIN wsh_row_number_right -->
            <div class="wsh_coordinate wsh_row_number" style="left:calc( (1.25 +  {COLUMN_INT})*var(--wsh_cell_size)); top:calc( ({ROW_OFFSET} - 0.65)*var(--wsh_cell_size));">{ROW}</div>
            <!-- END wsh_row_number_right -->
                
            <!-- BEGIN wsh_board_cell -->
            <div class="wsh_cell_holder wsh_cell_{LIGHT_OR_DARK}" style="top: calc(({ROW_OFFSET} - 1)*var(--wsh_cell_size)); left: calc({COLUMN_INT}*var(--wsh_cell_size));">
                <div id="wsh_cell_{COLUMN}{ROW}" class="wsh_cell"></div>
            </div>
            <!-- END wsh_board_cell -->
        
            
        <!-- END wsh_board_column -->
    </div>
    

</div>

<script type="text/javascript">

// Javascript HTML templates

var jstpl_wsh_token='<div id="${T_ID}" class="wsh_token wsh_token_${T_COLOR} " data_location="${T_LOCATION}"></div>';
//data_row="${T_ROW}" data_col="${T_COLUMN}" 

</script>  

{OVERALL_GAME_FOOTER}
