-- TEST  4 : Test ending situation with wolves win
 
 
 
UPDATE token SET token_location='E5', token_state='1' WHERE token_key = 't_ffffff_1'; 

UPDATE token SET token_location='D4', token_state='0' WHERE token_key = 't_000000_1'; 
UPDATE token SET token_location='D6', token_state='0' WHERE token_key = 't_000000_2'; 
UPDATE token SET token_location='F4', token_state='0' WHERE token_key = 't_000000_3'; 
UPDATE token SET token_location='G7', token_state='0' WHERE token_key = 't_000000_4'; 


-- RESET 	Active state id to playerTurn
UPDATE global SET global_value='10' WHERE global_id='1';
-- RESET 	Active player id to BLACK
UPDATE global SET global_value=(select player_id FROM `player` where player_color = '000000') WHERE global_id='2';
