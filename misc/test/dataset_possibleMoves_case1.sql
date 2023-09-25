-- TEST  2 : Test positions and possible moves : 2 black tokens CANNOT MOVE
 
 
 
UPDATE token SET token_location='G5', token_state='1' WHERE token_key = 't_ffffff_1'; 

UPDATE token SET token_location='C1', token_state='0' WHERE token_key = 't_000000_1'; 
UPDATE token SET token_location='C5', token_state='0' WHERE token_key = 't_000000_2'; 
UPDATE token SET token_location='F8', token_state='0' WHERE token_key = 't_000000_3'; 
UPDATE token SET token_location='H6', token_state='0' WHERE token_key = 't_000000_4'; 


-- RESET 	Active state id to playerTurn
UPDATE global SET global_value='10' WHERE global_id='1';
-- RESET 	Active player id to BLACK
UPDATE global SET global_value=(select player_id FROM `player` where player_color = '000000') WHERE global_id='2';
