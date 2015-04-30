#!/usr/bin/php
<?php

class tPlayer
{
   function __construct($name)
   {
      $this->name = $name;
      $this->setRoom(0);
   }

   function setRoom($id)
   {
      $this->roomId = $id;
   }
}

class tRoom
{
   function __construct( $id, $name = null)
   {
      $this->id = $id;
      $this->name = $name;
      $this->northId = null;
      $this->southId = null;
      $this->eastId  = null;
      $this->westId  = null;
   }

   function getId()
   {
      return $this->id;
   }

   function connect($direction, $oth)
   {
      switch ($direction)
      {
         case "east":
            $this->eastId = $oth->getId();
            $oth->westId = $this->getId();
            break;
         case "west":
            $this->westId = $oth->getId();
            $oth->eastId = $this->getId();
            break;
         case "north":
            $this->northId= $oth->getId();
            $oth->southId = $this->getId();
            break;
         case "south":
            $this->southId = $oth->getId();
            $oth->northId = $this->getId();
            break;
         default: throw new Exception("unknown direction '$direction'");
      }
   }

   function dump()
   {
       print "Room ID=" . $this->getId() . "(" . ($this->name === null ? "null" : $this->name) . ")" . "\n";
       print "  east : " . var_export($this->eastId, 1) . "\n";
       print "  west : " . var_export($this->westId, 1) . "\n";
       print "  north: " . var_export($this->northId, 1) . "\n";
       print "  south: " . var_export($this->southId, 1) . "\n";
   }
}


// Create the world:

class tGame
{
   function __construct()
   {
      $this->maxRoomId = -1;
      $this->rooms = []; // id => object
      $this->player = new tPlayer('t3o');
      $this->initRooms();
   }

   function initRooms()
   {
      // test: four connected rooms:
      $root = $this->addRoom('root');
      $tmp = $this->addRoom('A room', 'east', $root);
      //$tmp = $this->addRoom('A room', 'north', $tmp);
      //$tmp = $this->addRoom('A room', 'west', $tmp);
      //$tmp = $this->addRoom();
      //$tmp->connect('south', $root);
   }

   function getNewId()
   {
      $id = $this->maxRoomId + 1;
      $this->maxRoomId = $id;
      return $id;
   }

   function addRoom( $name = null, $inDirection = null, $ofRoom = null)
   {
      $id = $this->getNewId();
      $r = new tRoom($id, $name);
$r->dump();
      $this->rooms[ $r->getId() ] = $r;

      if (($inDirection !== null) && ($ofRoom !== null))
      {
          $ofRoom->connect($inDirection, $ofRoom);
      }
      return $r;
   }

   function dump()
   {
       print "Gamedump:\n";
       print "Max Id: " . $this->maxRoomId . "\n";
       foreach ($this->rooms as $id => $r)
       {
          $r->dump();
       }
   }
}

$game = new tGame();
$game->dump();
