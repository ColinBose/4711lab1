<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>  
    <head>
        <style>
            #anthony {
                width: 500px;
                height: 500px;
            }
            td{
                width:166px;
                height: 166px;
                text-align: center;
            }
            .but{
                width:166px;
                height: 166px;
                text-align: center;  
            }
            .but1{
                width:166px;
                height: 166px;
                text-align: center;  
                background-color:pink;
            }nav{
                text-align: center;
            }
    
    </style>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
       
        if(!isset($_GET['board'])){
            $position = '---------';   
        }else{
            $position = $_GET['board'];
            if(strlen($position) != 9){
                $position = '---------';
            }      
        }
    //Make a game object and call constructor handing it the game state from URL
    $game = new Game($position);
    //Allows player to go first 
    if($position != "---------"){
        $game->play();
    }
    $game->display();   
         
       
   //Game class, does all the game logic and stuff
    class Game{
        //holds game state
        var $position;
        //global to hold the current best move
        var $move;
        //global to hold score for genmove
        var $endScore;
        var $over;
        //constructor, set position to the current board state
        function __construct($squares) {
            $this->position = str_split($squares);
            echo '<nav style="height:120px">';
            echo '<h1>Welcome to Tic Tac Toe!!!!!!!!!!!</h1>';

        }
        //check if a tie has occured
         function checkTie($state){

            for($ok = 0; $ok < 9; $ok++){
                if($state[$ok] == '-'){
                    return false;
                }
            }   
            return true;
        }
            //called to do the logic of players moving.
    function play(){
        //check if a winner/tie has happened
        $this->over = false;
        //can never happen
        if($this->winner($this->position,'o')){
            $this->over = true;
            echo "<h2>You cheated.</h2>";
        }
        //player didn't win, so
        else{
            //check if the game is over
            if($this->checkTie($this->position)){
                $link = '?board=---------';
                $this->over = true;
                echo "<h2>Tie game</h2>";
                 //button to reset game

            }
            //game was not over - not a tie, so make a move
            if(!$this->over){
                $this->genMove($this->position, 'x',0);
                $this->position[$this->move] = 'x';
                //move made, check if computer won as a result of move
                if($this->winner($this->position,'x')){
                    echo "<h2>James Wins</h2>";
                    $this->over = true;

                }
            }
        }
        //did computer win or game tie? make link to reset game
        if($this->over){
            $link = '?board=---------';
            
            echo '<a href="'.$link.'"><button class="button">Reset</button></a></br></nav>';
        }
    }

               
            
           //Generate computers move
    function genMove($state, $token, $d){
        //check for win/loss
        $ret = $this->winOrlose($state, $token);
        //win or loss occurred, return that value
        if($ret != 0){
            return $ret;
        //check for a tie, return 0 state if tie has occured
        }if($this->checkTie($state)){
            return 0; 
        }
        //check to a max depth of 6
        if($d >= 6){
            return $ret;
        }
        //array to hold the scores and moves of each possible game state
        $score = [];
        $moves = [];
        
        //generate all possible next moves
        $moves = $this->getFree($state);
        if($token == 'x'){
            //if X (computer), find the max possible score
            $s = -99;
            //recurive call on each possible game state
            foreach($moves as $m){
                $newBoard = $state;
                $newBoard[$m] = 'x';
                array_push($score, $this->genMove($newBoard, 'o', $d+1)); 
            }
            //scores have return, find the max index and set the computers move to the
            //index of that score
            foreach($score as $index=>$sc){

                if($sc > $s){
                    $s = $sc;
                      $this->move = $moves[$index];
                }

            }

            $this->endScore = $s;
            return $s;
        }
        //for player, find the minimum 
        else{
            $s = 99;
            //recursive call for all possible next game states
            foreach($moves as $m){
                $newBoard = $state;
                $newBoard[$m] = 'o';
                array_push($score, $this->genMove($newBoard, 'x',$d+1)); 
            }
            //find the minimum score and set move based off
            foreach($score as $index=>$sc){
                if($sc < $s){
                    $s = $sc;
                    $this->move = $moves[$index];
                }
            }
            $this->endScore = $s;
            return $s;
        }

    }
    //find all open places
    function getFree($state){
        $retArr = [];
        //loop through fid index == '-'
        for($i = 0; $i < 9; $i++){
            if($state[$i] == '-'){
                array_push($retArr, $i);
            }
        }
        return $retArr;
    }
    //return scores for win/loss. positive score if computer wins, negative for players
    function winOrLose($state, $token){
        $opponent = 'o';
        if($this->winner($state, $token)){
            //echo "player wins";
            return 5;
        }
        if($this->winner($state, $opponent)){
            //echo "opponent wins ";
            return -5;
        }

        return 0;
    }
    //function to check winner
    function winner($state, $token){
        //loop through, checking horz/vert wins
        for($row = 0; $row < 3; $row++){
            $verResult = true;
            $horResult = true;
        for($col = 0; $col < 3; $col++){
            if($state[3*$row+$col] != $token){
                $horResult = false;
            }
            if($state[$row+3*$col] != $token){

                $verResult = false;
            }
        }
        //horz/vert winner, return true
        if($horResult || $verResult){
           return true;
        }

        }
        //check diagonals
        if($state[0] == $token && $state[4] == $token 
                && $state[8] == $token)
            return true;
        if($state[2] == $token && $state[4] == $token 
                && $state[6] == $token)
            return true;
    return false;

    }
    //generates what is in the table
    function show_cell($which) {
        $token = $this->position[$which];
        if ($token != '-') {
            //for x, return teachers face
            if($token == 'x'){
                return '<td> <img src="0a197ea.jpg"></img></td>';
            }
            //for o, return Gabby face
            return '<td><img src="spenser.jpg"></img></td>';
        }
        $newposition = $this->position; // copy the original
        $newposition[$which] = 'o'; // this would be their move
        $move = implode($newposition); // make a string from the board 
        $link = '?board='.$move; // this is what we want the link to be
        // so return a cell containing an anchor and showing a hyphen
        
        //for open space, return button with - sign
        if($this->over){
                    return '<td><button class="but" type="button">-</button></td>';

        }
        return '<td><a href="'.$link.'"><button class="but" type="button">-</button></a></td>';
} 

    //display function, loops through table and get the proper button/picture for each space.
    function display() {
        echo '<table id="anthony" cols="3" style="font-size:large;margin: auto; font-weight:bold">';
        echo '<tr>'; // open the first row
        for ($pos=0; $pos<9;$pos++) {
        echo $this->show_cell($pos); 
            if ($pos %3 == 2) 
                echo '</tr>';
            }
        // close the last row
        echo '</table>';
        } 
    }

        ?>
    </body>
</html>
