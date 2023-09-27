-- TEST  3.B (Size 10) : Test ending situation with sheep wins
 
 
 
UPDATE token SET token_location='I9', token_state='0' WHERE token_key = 't_ffffff_1'; 

UPDATE token SET token_location='G1', token_state='0' WHERE token_key = 't_000000_1'; 
UPDATE token SET token_location='H6', token_state='0' WHERE token_key = 't_000000_2'; 
UPDATE token SET token_location='E7', token_state='1' WHERE token_key = 't_000000_3'; 
UPDATE token SET token_location='A7', token_state='0' WHERE token_key = 't_000000_4'; 
UPDATE token SET token_location='J10', token_state='0' WHERE token_key = 't_000000_5'; 


-- RESET 	Active state id to playerTurn
UPDATE global SET global_value='10' WHERE global_id='1';
-- RESET 	Active player id to WHITE
UPDATE global SET global_value=(select player_id FROM `player` where player_color = 'ffffff') WHERE global_id='2';
