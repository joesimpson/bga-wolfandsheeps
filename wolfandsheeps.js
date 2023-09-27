/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * WolfAndSheeps implementation : © joesimpson <1324811+joesimpson@users.noreply.github.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * wolfandsheeps.js
 *
 * WolfAndSheeps user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.wolfandsheeps", ebg.core.gamegui, {
        constructor: function(){
            console.log('wolfandsheeps constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;
            this.allPossibleMoves = [];
            this.displayedPossibleMovesOrigin = null;
            //this.onSelectMoveOriginHandler = [];
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup",gamedatas );
            
            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
            }
            
            // TODO: Set up your game interface here, according to "gamedatas"
            
            for( let i in gamedatas.board ){
                let token = gamedatas.board[i];
                this.addTokenOnBoard(token.key, token.color,token.location);
                if(token.state == gamedatas.constants.TOKEN_STATE_MOVED){
                    this.updateLastMove(token.key);
                }
            }
            dojo.query( '.wsh_token' ).connect( 'onclick', this, 'onSelectMoveOrigin' );
            dojo.query( '.wsh_cell' ).connect( 'onclick', this, 'onPlayToken' );
            
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            case 'playerTurn':
                console.log( 'possibleMoves: ',args.args.possibleMoves );
                if(args.active_player == this.player_id){
                    this.updatePossibleMoves( args.args.possibleMoves );
                }
                break;
            }
           
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            case 'playerTurn':
                //for(let i in this.onSelectMoveOriginHandler) dojo.disconnect(this.onSelectMoveOriginHandler[i]);
                this.updatePossibleMoves( [] );
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

        
        //// Studio Cookbook Utility methods ------------------------
        
        /**
         * This method will attach mobile to a new_parent without destroying, unlike original attachToNewParent which destroys mobile and
         * all its connectors (onClick, etc)
         */
        attachToNewParentNoDestroy: function (mobile_in, new_parent_in, relation, place_position) {

            const mobile = $(mobile_in);
            const new_parent = $(new_parent_in);

            var src = dojo.position(mobile);
            if (place_position)
                mobile.style.position = place_position;
            dojo.place(mobile, new_parent, relation);
            mobile.offsetTop;//force re-flow
            var tgt = dojo.position(mobile);
            var box = dojo.marginBox(mobile);
            var cbox = dojo.contentBox(mobile);
            var left = box.l + src.x - tgt.x;
            var top = box.t + src.y - tgt.y;

            mobile.style.position = "absolute";
            mobile.style.left = left + "px";
            mobile.style.top = top + "px";
            box.l += box.w - cbox.w;
            box.t += box.h - cbox.h;
            mobile.offsetTop;//force re-flow
            return box;
        },
        ajaxcallwrapper: function(action, args, handler) {
            if (!args) {
                args = {};
            }
            args.lock = true;

            if (this.checkAction(action)) {
                this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", args, this, (result) => { }, handler);
            }
        },
        
        //// Other Utility methods ------------------------
        updateLastMove: function( $tokenId )
        {
            dojo.query( '.wsh_token' ).removeClass( 'wsh_lastMove' );
            dojo.addClass( $tokenId,'wsh_lastMove' ); 
        },
        
        updatePossibleMoves: function( possibleMoves )
        {
            // Remove current possible moves
            dojo.query( '.wsh_possibleMoveFrom' ).removeClass( 'wsh_possibleMoveFrom' ).removeClass( 'wsh_possibleMoveFromHere' );
            //this.disconnect( $('.wsh_possibleMoveTo'), 'click');
            dojo.query( '.wsh_possibleMoveTo' ).removeClass( 'wsh_possibleMoveTo' ) ;
            dojo.query(".wsh_token" ).forEach(  (node)=> { 
                this.removeTooltip(node.id);
                //?? dojo.disconnect(node, 'click');
            })  ; 
            
            this.allPossibleMoves = possibleMoves;
            
            for( let origin in possibleMoves )
            {
                let moves = possibleMoves[origin];
                /*
                let divPlace = "wsh_cell_"+origin;
                if($(divPlace) == null){
                    console.log( "Cannot place possibleMove on not found cell ",divPlace, origin );
                    return null;
                }
                */
                let nodes = dojo.query(".wsh_token[data_location='"+origin+"']");
                let node = nodes[0];
                if(node !=undefined  ) {
                    dojo.addClass( node.id , 'wsh_possibleMoveFrom' ); 
                    //this.onSelectMoveOriginHandler[origin] = dojo.connect(node, 'onclick', this, 'onSelectMoveOrigin' );
                }
            }
            
            //TODO JSA see how to display this tooltip without adding a "click" event listener...
            //this.addTooltipToClass( 'wsh_possibleMoveFrom', '', _('You can move from this place') );
            
        },
        
        addTokenOnBoard: function( id, color,coord ) {
            /*
            let row = coord_row -1;
            let col = "ABCDEFGHIJ".indexOf(coord_col);
            */
            let divPlace = "wsh_cell_"+coord;
            if($(divPlace) == null){
                console.log( "Cannot place token on not found cell ",divPlace, coord );
                return;
            }
        
            dojo.place(  
                this.format_block(
                    'jstpl_wsh_token',
                    {
                        T_ID : id,
                        T_COLOR : color,
                        //T_ROW : row,
                        //T_COLUMN: col,
                        T_LOCATION : coord,
                    }
                ),
                divPlace
            ); 
        },
        
        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        onPlayToken: function( evt )
        {
            console.log("onPlayToken",evt);
            // Stop this event propagation
            evt.preventDefault();
            dojo.stopEvent( evt );

            let token_id = evt.currentTarget.getAttribute("data_token_id") ;
            //let dest= evt.currentTarget.getAttribute("data_location") ;
            //Now it's in cell id "wsh_cell_D5"
            let dest= evt.currentTarget.id.split("_")[2];

            if( ! dojo.hasClass( evt.currentTarget.id, 'wsh_possibleMoveTo' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            
            this.ajaxcallwrapper( "playToken",{id: token_id, dest: dest} );            
        },
        
        
        /**
        Toggle display of possible moves from this place
        */
        onSelectMoveOrigin: function( evt )
        {
            console.log("onSelectMoveOrigin",evt);
            // Stop this event propagation
            evt.preventDefault();
            dojo.stopEvent( evt );
            
            
            let origin= evt.currentTarget.getAttribute("data_location") ;
            let token_id = evt.currentTarget.id;
            if( ! dojo.hasClass( token_id, 'wsh_possibleMoveFrom' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            dojo.query( '.wsh_possibleMoveFromHere' ).removeClass( 'wsh_possibleMoveFromHere' );
            
            let displayedMoves = dojo.query( '.wsh_possibleMoveTo' );
            //this.disconnect(displayedMoves, 'click');
            displayedMoves.removeClass( 'wsh_possibleMoveTo' ); 
            if(this.displayedPossibleMovesOrigin == origin ){
                //IF ALREADY DISPLAYED , hide
                this.displayedPossibleMovesOrigin = null;
                console.log("onSelectMoveOrigin() => Hide :",origin);
                return;
            } //ELSE continue to SHOW
            
            dojo.addClass( token_id,'wsh_possibleMoveFromHere' ); 
            
            this.displayedPossibleMovesOrigin = origin;
            let moves = this.allPossibleMoves[origin];
            for( let i in moves )
            {
                let target = moves[i];
                let targetId = "wsh_cell_"+target;
                if($(targetId) == null){
                    console.log( "Cannot place move on not found cell ",targetId, target );
                    continue;
                }
                dojo.addClass( targetId , 'wsh_possibleMoveTo' ); 
                dojo.attr(targetId, "data_token_id", token_id);
                
                
                //TODO JSA see how to display this tooltip without adding a "click" event listener...
                //this.addTooltipToClass( 'wsh_possibleMoveTo', '', _('You can move TO this place') );
            }
            
            console.log("onSelectMoveOrigin() => moves :",moves);
        },

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your wolfandsheeps.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
            
            dojo.subscribe( 'tokenPlayed', this, "notif_tokenPlayed" );
            dojo.subscribe( 'sheepWins', this, "notif_sheepWins" );
            dojo.subscribe( 'winByBlocking', this, "notif_winByBlocking" );
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        notif_tokenPlayed: function( notif )
        {
            console.log( 'notif_tokenPlayed', notif );
            
            let tokenId = notif.args.tokenId;
            let destination = notif.args.dest;
            let color = notif.args.color;
            
            //Do want to create NEw Token ? 
            //this.addTokenOnBoard(tokenId, color,destination);
            
            //Animation to move existing token to dest :
            let tokenDivId = tokenId;
            let destinationDivId ="wsh_cell_"+destination;
            this.attachToNewParentNoDestroy(tokenDivId, destinationDivId);
            dojo.attr(tokenDivId, "data_location", destination);
            let anim = this.slideToObject(tokenDivId,destinationDivId,1000);
            dojo.connect(anim, 'onEnd', function(node){
                //dojo.attr(node, "data_location", destination); // TOO LATE when playing alone with zombie ?
                //To avoid offset of some px calculated after sliding:
                dojo.style(node,"left","0");
                dojo.style(node,"top","0");
            });
            anim.play();
            this.updateLastMove(tokenId);
        },    
        
        notif_sheepWins: function( notif )
        {
            //Update player panel score
            this.scoreCtrl[notif.args.player_id].toValue( notif.args.winner_score);
        },
        notif_winByBlocking: function( notif )
        {
            //Update player panel score
            this.scoreCtrl[notif.args.player_id].toValue( notif.args.winner_score);
        },
        
   });             
});
//# sourceURL=wolfandsheeps.js
