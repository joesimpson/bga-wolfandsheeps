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
    <h1><center>Work in progress :
        <br></center>
    </h1>



    <!-- TODO JSA GENERATE TEMPLATE elements instead of full hard corded -->
    <!-- TODO JSA Display this board from the player point of view (black on top if viewed by white...) -->


    <!-- Inspired by BGA Chess Board implementation by ecolin -->
    <div id="wsh_board" class="wsh_board_size_8">
           <div class="wsh_cell_holder wsh_cell_dark" style="top: 0px; left: 0px;">
			<div id="wsh_cell_A1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">A</div>
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">1</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 62px; left: 0px;">
			<div id="wsh_cell_A2" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">2</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 124px; left: 0px;">
			<div id="wsh_cell_A3" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">3</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 186px; left: 0px;">
            <div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">4</div>
			<div id="wsh_cell_A4" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 248px; left: 0px;">
			<div id="wsh_cell_A5" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">5</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 310px; left: 0px;">
			<div id="wsh_cell_A6" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">6</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 372px; left: 0px;">
			<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">7</div>
			<div id="wsh_cell_A7" class="wsh_cell" data_location="A7">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 434px; left: 0px;">
			<div id="wsh_cell_A8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">A</div>
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:-20px; top:22px;">8</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 0px; left: 62px;">
			<div id="wsh_cell_B1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">B</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 62px; left: 62px;">
			<div id="wsh_cell_B2" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 124px; left: 62px;">
			<div id="wsh_cell_B3" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 186px; left: 62px;">
			<div id="wsh_cell_B4" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 248px; left: 62px;">
			<div id="wsh_cell_B5" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 310px; left: 62px;">
			<div id="wsh_cell_B6" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 372px; left: 62px;">
			<div id="wsh_cell_B7" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 434px; left: 62px;">
			<div id="wsh_cell_B8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">B</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 0px; left: 124px;">
			<div id="wsh_cell_C1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">C</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 62px; left: 124px;">
			<div id="wsh_cell_C2" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 124px; left: 124px;">
			<div id="wsh_cell_C3" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 186px; left: 124px;">
			<div id="wsh_cell_C4" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 248px; left: 124px;">
			<div id="wsh_cell_C5" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 310px; left: 124px;">
			<div id="wsh_cell_C6" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 372px; left: 124px;">
			<div id="wsh_cell_C7" class="wsh_cell" data_location="C7">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 434px; left: 124px;">
			<div id="wsh_cell_C8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">C</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 0px; left: 186px;">
			<div id="wsh_cell_D1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">D</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 62px; left: 186px;">
			<div id="wsh_cell_D2" class="wsh_cell" data_location="D2">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 124px; left: 186px;">
			<div id="wsh_cell_D3" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 186px; left: 186px;">
			<div id="wsh_cell_D4" class="wsh_cell" data_location="D4">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 248px; left: 186px;">
			<div id="wsh_cell_D5" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 310px; left: 186px;">
			<div id="wsh_cell_D6" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 372px; left: 186px;">
			<div id="wsh_cell_D7" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 434px; left: 186px;">
			<div id="wsh_cell_D8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">D</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 0px; left: 248px;">
			<div id="wsh_cell_E1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">E</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 62px; left: 248px;">
			<div id="wsh_cell_E2" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 124px; left: 248px;">
			<div id="wsh_cell_E3" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 186px; left: 248px;">
			<div id="wsh_cell_E4" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 248px; left: 248px;">
			<div id="wsh_cell_E5" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 310px; left: 248px;">
			<div id="wsh_cell_E6" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 372px; left: 248px;">
			<div id="wsh_cell_E7" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 434px; left: 248px;">
			<div id="wsh_cell_E8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">E</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 0px; left: 310px;">
			<div id="wsh_cell_F1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">F</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 62px; left: 310px;">
			<div id="wsh_cell_F2" class="wsh_cell" data_location="F2">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 124px; left: 310px;">
			<div id="wsh_cell_F3" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 186px; left: 310px;">
			<div id="wsh_cell_F4" class="wsh_cell" data_location="F4">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 248px; left: 310px;">
			<div id="wsh_cell_F5" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 310px; left: 310px;">
			<div id="wsh_cell_F6" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 372px; left: 310px;">
			<div id="wsh_cell_F7" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 434px; left: 310px;">
			<div id="wsh_cell_F8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">F</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 0px; left: 372px;">
			<div id="wsh_cell_G1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">G</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 62px; left: 372px;">
			<div id="wsh_cell_G2" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 124px; left: 372px;">
			<div id="wsh_cell_G3" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 186px; left: 372px;">
			<div id="wsh_cell_G4" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 248px; left: 372px;">
			<div id="wsh_cell_G5" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 310px; left: 372px;">
			<div id="wsh_cell_G6" class="wsh_cell">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 372px; left: 372px;">
			<div id="wsh_cell_G7" class="wsh_cell" data_location="G7">
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 434px; left: 372px;">
			<div id="wsh_cell_G8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">G</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 0px; left: 434px;">
			<div id="wsh_cell_H1" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:-24px; left:27px;">H</div>
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">1</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 62px; left: 434px;">
			<div id="wsh_cell_H2" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">2</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 124px; left: 434px;">
			<div id="wsh_cell_H3" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">3</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 186px; left: 434px;">
			<div id="wsh_cell_H4" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">4</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 248px; left: 434px;">
			<div id="wsh_cell_H5" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">5</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 310px; left: 434px;">
			<div id="wsh_cell_H6" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">6</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_light" style="top: 372px; left: 434px;">
			<div id="wsh_cell_H7" class="wsh_cell">
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">7</div>
			</div>
		</div>
		<div class="wsh_cell_holder wsh_cell_dark" style="top: 434px; left: 434px;">
			<div id="wsh_cell_H8" class="wsh_cell">
				<div class="wsh_coordinate wsh_col_number" style="display:block; top:72px; left:27px;">H</div>
				<div class="wsh_coordinate wsh_row_number" style="display:block; left:77px; top:22px;">8</div>
			</div>
		</div>
    
    </div>

</div>

<script type="text/javascript">

// Javascript HTML templates

var jstpl_wsh_token='<div id="${T_ID}" class="wsh_token wsh_token_${T_COLOR} " data_location="${T_LOCATION}"></div>';
//data_row="${T_ROW}" data_col="${T_COLUMN}" 

</script>  

{OVERALL_GAME_FOOTER}
