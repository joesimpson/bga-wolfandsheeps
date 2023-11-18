DELETE FROM token;

INSERT INTO `token` (`token_key`, `token_location`, `token_state`) 
VALUES 
('1_000000_1', 'C5', '0'), 
('1_000000_2', 'E7', '0'), 
('1_000000_3', 'G7', '0'), 
('1_000000_4', 'H8', '0'), 
('1_ffffff_1', 'C7', '0'); 

INSERT INTO `token` (`token_key`, `token_location`, `token_state`) 
VALUES 
('2_000000_1', 'B8', '0'), 
('2_000000_2', 'D8', '0'), 
('2_000000_3', 'F8', '0'), 
('2_000000_4', 'H8', '0'), 
('2_ffffff_1', 'E1', '0'); 

SELECT * FROM `token` WHERE 1;