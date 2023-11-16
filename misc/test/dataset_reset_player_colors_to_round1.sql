-- TO COMBINE WITH  BGA Studio Button "Reload State 1" when state 1 is during round 1

UPDATE player SET `player_color` = 'ffffff' WHERE player_no = 1;
UPDATE player SET `player_color` = '000000' WHERE player_no = 2;


SELECT player_no, player_name,player_color FROM `player` ;

