-- TEST  1.B (SIZE 10) : REINIT the game to starting positions
 
 
 
UPDATE token SET token_location='E1', token_state='0' WHERE token_key = 't_ffffff_1'; 

UPDATE token SET token_location='B10', token_state='0' WHERE token_key = 't_000000_1'; 
UPDATE token SET token_location='D10', token_state='0' WHERE token_key = 't_000000_2'; 
UPDATE token SET token_location='F10', token_state='0' WHERE token_key = 't_000000_3'; 
UPDATE token SET token_location='H10', token_state='0' WHERE token_key = 't_000000_4'; 
UPDATE token SET token_location='J10', token_state='0' WHERE token_key = 't_000000_5'; 


-- RESET 	Active state id to playerTurn
UPDATE global SET global_value='10' WHERE global_id='1';
-- RESET 	Active player id to WHITE
UPDATE global SET global_value=(select player_id FROM `player` where player_color = 'ffffff') WHERE global_id='2';
