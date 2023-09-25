-- TEST  1 : REINIT the game to starting positions
 
 
 
UPDATE token SET token_location='E1', token_state='0' WHERE token_key = 't_ffffff_1'; 

UPDATE token SET token_location='B8', token_state='0' WHERE token_key = 't_000000_1'; 
UPDATE token SET token_location='D8', token_state='0' WHERE token_key = 't_000000_2'; 
UPDATE token SET token_location='F8', token_state='0' WHERE token_key = 't_000000_3'; 
UPDATE token SET token_location='H8', token_state='0' WHERE token_key = 't_000000_4'; 


-- RESET 	Active state id to playerTurn
UPDATE global SET global_value='10' WHERE global_id='1';
-- RESET 	Active player id to WHITE
UPDATE global SET global_value=(select player_id FROM `player` where player_color = 'ffffff') WHERE global_id='2';
