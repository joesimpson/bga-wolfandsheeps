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


//debug() LOG MANAGEMENT, as seen on tisaac's BGA Games
var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};


define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.wolfandsheeps", ebg.core.gamegui, {
        constructor: function(){
            debug('wolfandsheeps constructor');
              
            // Here, you can init the global variables of your user interface
            this.allPossibleMoves = [];
            this.displayedPossibleMovesOrigin = null;
            
            this.doAutoConfirm = true;
            this.datasToConfirm = {};
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
            debug( "Starting game setup",gamedatas );
            
            if (this.prefs[100].value == 2){//Board colors set to reverse
                this.toggleCellLight();
            }
            if (this.prefs[102].value == 2){//Auto confirm disabled
                this.doAutoConfirm = false;
            }
            
            // Set up your game interface here, according to "gamedatas"
            
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

            debug( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            debug( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            case 'playerTurn':
                debug( 'possibleMoves: ',args.args.possibleMoves );
                if(args.active_player == this.player_id){
                    this.updatePossibleMoves( args.args.possibleMoves );
                }
                this.cleanChosenMoveToConfirm();
                break;
            }
           
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            debug( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            case 'playerTurn':
                this.updatePossibleMoves( [] );
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            debug( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                    case 'playerTurn':
                        if(!this.doAutoConfirm) {
                            this.addActionButton( 'wsh_button_confirm', _('Confirm'), 'onButtonConfirm' ); 
                        }
                        break;

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
            debug("ajaxcallwrapper()",action,args);
            if (!args) {
                args = {};
            }
            args.lock = true;

            if (this.checkAction(action)) {
                this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", args, this, (result) => { }, handler);
            }
        },
        
        //// Other Utility methods ------------------------
        toggleCellLight: function( )
        {
            debug("toggleCellLight()");
            //The VIEW generate cells style for default option, but we can toggle it if we want
            dojo.query(".wsh_cell_dark").replaceClass("wsh_cell_dark_TMP", "wsh_cell_dark");
            dojo.query(".wsh_cell_light").replaceClass("wsh_cell_dark", "wsh_cell_light"); 
            dojo.query(".wsh_cell_dark_TMP").replaceClass("wsh_cell_light", "wsh_cell_dark_TMP"); 
        },
        
        updateLastMove: function( tokenId )
        {
            debug("updateLastMove()",tokenId);
            dojo.query( '.wsh_token' ).removeClass( 'wsh_lastMove' );
            dojo.addClass( tokenId,'wsh_lastMove' ); 
        },
        
        updatePossibleMoves: function( possibleMoves )
        {
            debug("updatePossibleMoves()",possibleMoves);
            // Remove current possible moves
            dojo.query( '.wsh_possibleMoveFrom' ).removeClass( 'wsh_possibleMoveFrom' ).removeClass( 'wsh_possibleMoveFromHere' );
            dojo.query( '.wsh_possibleMoveTo' ).removeClass( 'wsh_possibleMoveTo' ) ;
            dojo.query(".wsh_token" ).forEach(  (node)=> { 
                this.removeTooltip(node.id);
            })  ; 
            
            this.allPossibleMoves = possibleMoves;
            
            for( let origin in possibleMoves )
            {
                let nodes = dojo.query(".wsh_token[data_location='"+origin+"']");
                let node = nodes[0];
                if(node !=undefined  ) {
                    dojo.addClass( node.id , 'wsh_possibleMoveFrom' ); 
                }
            }
            
            //TODO JSA see how to display this tooltip without adding a "click" event listener...
            //this.addTooltipToClass( 'wsh_possibleMoveFrom', '', _('You can move from this place') );
            
        },
        
        addTokenOnBoard: function( id, color,coord ) {
            debug( "addTokenOnBoard",id, color,coord );
            let divPlace = "wsh_cell_"+coord;
            if($(divPlace) == null){
                debug( "Cannot place token on not found cell ",divPlace, coord );
                return;
            }
        
            dojo.place(  
                this.format_block(
                    'jstpl_wsh_token',
                    {
                        T_ID : id,
                        T_COLOR : color,
                        T_LOCATION : coord,
                    }
                ),
                divPlace
            ); 
        },
        
        cleanChosenMoveToConfirm: function(){
            dojo.query(".wsh_chosenMoveTo").removeClass("wsh_chosenMoveTo wsh_token wsh_token_000000 wsh_token_ffffff");
            this.datasToConfirm = {};
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
            debug("onPlayToken",evt);
            // Stop this event propagation
            evt.preventDefault();
            dojo.stopEvent( evt );

            let token_id = evt.currentTarget.getAttribute("data_token_id") ;
            //Now it's in cell id "wsh_cell_D5"
            let dest= evt.currentTarget.id.split("_")[2];

            if( ! dojo.hasClass( evt.currentTarget.id, 'wsh_possibleMoveTo' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            this.cleanChosenMoveToConfirm();
            
            let playerColor = this.gamedatas.players[this.player_id].color;
            
            evt.currentTarget.classList.add("wsh_chosenMoveTo");
            evt.currentTarget.classList.add("wsh_token");
            evt.currentTarget.classList.add("wsh_token_"+playerColor);
                
            if(!this.doAutoConfirm) {
                this.datasToConfirm = {'token_id':token_id, 'dest':dest };
                return;
            }
            else {
                this.ajaxcallwrapper( "playToken",{id: token_id, dest: dest} );
            }
        },
        /**
        Confirm the action
        */
        onButtonConfirm: function(evt){
            debug("onButtonConfirm",evt);
            // Stop this event propagation
            evt.preventDefault();
            dojo.stopEvent( evt );
            
            if(this.datasToConfirm.token_id == undefined){
                return;
            }
            if(this.datasToConfirm.dest == undefined){
                return;
            }
            
            this.ajaxcallwrapper( "playToken",{id: this.datasToConfirm.token_id, dest: this.datasToConfirm.dest} );
        },
        
        /**
        Toggle display of possible moves from this place
        */
        onSelectMoveOrigin: function( evt )
        {
            debug("onSelectMoveOrigin",evt);
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
            this.cleanChosenMoveToConfirm();
            
            let displayedMoves = dojo.query( '.wsh_possibleMoveTo' );
            displayedMoves.removeClass( 'wsh_possibleMoveTo' ); 
            if(this.displayedPossibleMovesOrigin == origin ){
                //IF ALREADY DISPLAYED , hide
                this.displayedPossibleMovesOrigin = null;
                debug("onSelectMoveOrigin() => Hide :",origin);
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
                    debug( "Cannot place move on not found cell ",targetId, target );
                    continue;
                }
                dojo.addClass( targetId , 'wsh_possibleMoveTo' ); 
                dojo.attr(targetId, "data_token_id", token_id);
                
                //TODO JSA see how to display this tooltip without adding a "click" event listener...
                //this.addTooltipToClass( 'wsh_possibleMoveTo', '', _('You can move TO this place') );
            }
            
            debug("onSelectMoveOrigin() => moves :",moves);
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
            debug( 'notifications subscriptions setup' );
            // here, associate your game notifications with local methods
            
            dojo.subscribe( 'tokenPlayed', this, "notif_tokenPlayed" );
            dojo.subscribe( 'sheepWins', this, "notif_sheepWins" );
            dojo.subscribe( 'winByBlocking', this, "notif_winByBlocking" );
        },  
        
        // from this point and below, you can write your game notifications handling methods
        
        notif_tokenPlayed: function( notif )
        {
            debug( 'notif_tokenPlayed', notif );
            
            let tokenId = notif.args.tokenId;
            let destination = notif.args.dest;
            let color = notif.args.color;
            
            //Animation to move existing token to dest :
            let tokenDivId = tokenId;
            let destinationDivId ="wsh_cell_"+destination;
            this.attachToNewParentNoDestroy(tokenDivId, destinationDivId);
            dojo.attr(tokenDivId, "data_location", destination);
            let anim = this.slideToObject(tokenDivId,destinationDivId,1000);
            dojo.connect(anim, 'onEnd', function(node){
                debug( 'anim onEnd', node );
                //dojo.attr(node, "data_location", destination); // TOO LATE when playing alone with zombie ?
                //To avoid offset of some px calculated after sliding:
                if(node != undefined){// Happens in 3d ?
                    dojo.style(node,"left","0");
                    dojo.style(node,"top","0");
                }
            });
            anim.play();
            this.updateLastMove(tokenId);
        },    
        
        notif_sheepWins: function( notif )
        {
            debug( "notif_sheepWins",notif );
            //Update player panel score
            this.scoreCtrl[notif.args.player_id].toValue( notif.args.winner_score);
        },
        notif_winByBlocking: function( notif )
        {
            debug( "notif_winByBlocking",notif );
            //Update player panel score
            this.scoreCtrl[notif.args.player_id].toValue( notif.args.winner_score);
        },
        
   });             
});
//# sourceURL=wolfandsheeps.js
