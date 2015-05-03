#!/usr/bin/php
<?php

class tPlayer
{
   function __construct($name)
   {
      $this->name = $name;
      $this->roomId = 0;
   }

   function setRoom($room)
   {
      $this->setRoomId($room->getId());
   }

   function setRoomId($id)
   {
      $this->roomId = $id;
   }

   function getRoomId()
   {
      return $this->roomId;
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
      $this->items = [];
   }

   function getId()
   {
      return $this->id;
   }

   function addItem($name)
   {
      $this->items[] = $name;
   }

   function connect($direction, tRoom $oth)
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

   function getShortInfo()
   {
      return $this->getId() . ' (' . ($this->name !== null ? '"' . $this->name . '"': 'NULL') . ')';
   }

   function getWhereCanIGo()
   {
      $wcig = [];
      if ($this->westId  !== null) $wcig[] = 'west';
      if ($this->northId !== null) $wcig[] = 'north';
      if ($this->eastId  !== null) $wcig[] = 'east';
      if ($this->southId !== null) $wcig[] = 'south';
      return implode(", ", $wcig);
   }

   function dump()
   {
       print "Room " . $this->getShortInfo() . "\n";
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
      $this->player->setRoomId( 0 );
   }

   function initRooms()
   {
      // test: four connected rooms:
      $root = $this->addRoom('root');
      $root->addItem('a dark green chest');
      $tmp = $this->addRoom('A room', 'east', $root);
      $tmp->addItem('a yellow key');
      $tmp = $this->addRoom('A room', 'north', $tmp);
      $tmp->addItem('a puppet');
      $tmp = $this->addRoom('A room', 'west', $tmp);
      $tmp->connect('south', $root);
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
      $this->rooms[ $r->getId() ] = $r;

      if (($inDirection !== null) && ($ofRoom !== null))
      {
          $ofRoom->connect($inDirection, $r);
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

   function getPlayer()
   {
      return $this->player;
   }

   function getPlayerRoom()
   {
      return $this->rooms[ $this->player->getRoomId() ];
   }
}

$game = new tGame();

print "Hello player!\n";
$cmd = null;
while ($cmd !== 'q')
{
    $pr = $game->getPlayerRoom();
    print "\n";
    print "You are in room " . $pr->getShortInfo() . ".\n";
    if (count($pr->items))
    {
       print "Items in the current room: " . implode(", ", $pr->items) . "\n";
    }
    else
    {
       print "There are no items in this room.\n";
    }
    print "You can go to: " . $pr->getWhereCanIGo() . "\n";
    print "\n";

    $cmd = readline('Enter command (h = help) > ');

    switch ($cmd)
    {
       case "n" :
          if ($pr->northId !== null)
          {
             $game->player->setRoomId($pr->northId);
          }
          break;
       case "s" :
          if ($pr->southId !== null)
          {
             $game->player->setRoomId( $pr->southId );
          }
          break;
       case "e" :
          if ($pr->eastId !== null)
          {
             $game->player->setRoomId( $pr->eastId );
          }
          break;
       case "w" :
          if ($pr->westId !== null)
          {
             $game->player->setRoomId( $pr->westId );
          }
          break;
       case "h":
          print "Go to (n)orth, (s)outh, (e)ast or (w)west or (q)uit\n";
          break;
       case "dump":
          $game->dump();
          break;
    }
}

